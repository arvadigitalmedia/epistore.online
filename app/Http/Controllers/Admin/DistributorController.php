<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\User;
use App\Http\Requests\StoreDistributorRequest;
use App\Http\Requests\UpdateDistributorRequest;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DistributorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $distributors = Distributor::latest()->paginate(10);
        return view('admin.distributors.index', compact('distributors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.distributors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDistributorRequest $request)
    {
        DB::transaction(function () use ($request) {
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('distributors', 'public');
            }

            // Create Distributor
            $distributor = Distributor::create([
                'name' => $request->name,
                'code' => $request->code,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => $request->status,
                'level' => $request->level,
                'logo' => $logoPath,
                'config' => [], // Default empty config
            ]);

            // Create User for Distributor Owner
            $user = User::create([
                'name' => $distributor->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'distributor_id' => $distributor->id,
                'email_verified_at' => now(),
            ]);

            // Assign Role
            $user->assignRole('distributor_owner');
        });

        return redirect()->route('admin.distributors.index')
            ->with('success', 'Distributor and Owner Account created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Distributor $distributor)
    {
        return view('admin.distributors.show', compact('distributor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Distributor $distributor)
    {
        return view('admin.distributors.edit', compact('distributor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDistributorRequest $request, Distributor $distributor)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($distributor->logo) {
                Storage::disk('public')->delete($distributor->logo);
            }
            $data['logo'] = $request->file('logo')->store('distributors', 'public');
        }

        $distributor->update($data);
        return redirect()->route('admin.distributors.index')
            ->with('success', 'Distributor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Distributor $distributor)
    {
        DB::transaction(function () use ($distributor) {
            // 1. Load related data for snapshot
            $distributor->load(['users']);

            // 2. Create Comprehensive Audit Log
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'HARD_DELETE_DISTRIBUTOR',
                'model_type' => get_class($distributor),
                'model_id' => $distributor->id,
                'old_values' => $distributor->toArray(), // Contains distributor data + users
                'new_values' => null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // 3. Force Delete Related Users (Owner/Staff)
            // This ensures emails are freed up for reuse
            foreach ($distributor->users as $user) {
                // Optional: Log individual user deletion if needed, but the main log covers it.
                $user->forceDelete();
            }

            // 4. Force Delete Distributor
            // Database cascading will handle orders, shipping logs, etc. if configured.
            // Since we want "no trace", forceDelete is mandatory.
            $distributor->forceDelete();
        });

        // 5. Clear System Cache (Optional/General)
        // If there are specific caches for distributors, clear them here.
        // For now, we assume standard Laravel caching.
        // Cache::tags(['distributors'])->flush(); // Example if using tags

        return redirect()->route('admin.distributors.index')
            ->with('success', 'Distributor and all related data have been permanently deleted.');
    }
}
