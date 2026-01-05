<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Domain Management') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: 'subdomain' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Tabs -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="activeTab = 'subdomain'" 
                        :class="{ 'border-blue-500 text-blue-600': activeTab === 'subdomain', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'subdomain' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Subdomain Settings
                    </button>
                    <button @click="activeTab = 'custom'" 
                        :class="{ 'border-blue-500 text-blue-600': activeTab === 'custom', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'custom' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Custom Domains
                    </button>
                </nav>
            </div>

            <!-- Subdomain Tab -->
            <div x-show="activeTab === 'subdomain'" class="space-y-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">EPI Store Subdomain</h3>
                        <p class="text-sm text-gray-500 mb-6">
                            Set your unique subdomain for your store. Your store will be accessible at: 
                            <span class="font-mono bg-gray-100 px-2 py-1 rounded">
                                <span x-text="subdomain || 'your-name'"></span>.{{ parse_url(config('app.url'), PHP_URL_HOST) }}
                            </span>
                        </p>

                        <form method="POST" action="{{ route('distributor.domains.update-subdomain') }}" x-data="{ subdomain: '{{ $distributor->subdomain }}' }">
                            @csrf
                            <div class="max-w-xl">
                                <x-input-label for="subdomain" :value="__('Subdomain')" />
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="text" name="subdomain" id="subdomain" x-model="subdomain"
                                        class="flex-1 min-w-0 block w-full px-3 py-2 rounded-l-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="my-store">
                                    <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                        .{{ parse_url(config('app.url'), PHP_URL_HOST) }}
                                    </span>
                                </div>
                                <x-input-error :messages="$errors->get('subdomain')" class="mt-2" />
                            </div>

                            <div class="mt-6">
                                <x-primary-button>{{ __('Save Subdomain') }}</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Custom Domain Tab -->
            <div x-show="activeTab === 'custom'" class="space-y-6" style="display: none;">
                <!-- Add New Domain -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Add Custom Domain</h3>
                        <form method="POST" action="{{ route('distributor.domains.store') }}" class="flex gap-4 items-end">
                            @csrf
                            <div class="flex-1 max-w-md">
                                <x-input-label for="domain" :value="__('Domain Name')" />
                                <x-text-input id="domain" class="block mt-1 w-full" type="text" name="domain" placeholder="example.com" required />
                                <x-input-error :messages="$errors->get('domain')" class="mt-2" />
                            </div>
                            <x-primary-button>{{ __('Add Domain') }}</x-primary-button>
                        </form>
                    </div>
                </div>

                <!-- Domain List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Your Custom Domains</h3>
                        
                        @if($distributor->domains->isEmpty())
                            <p class="text-gray-500 italic">No custom domains added yet.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Domain</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verification</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Primary</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($distributor->domains as $domain)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $domain->domain }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    @if($domain->status === 'verified')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Verified</span>
                                                    @elseif($domain->status === 'pending')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending Verification</span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ ucfirst($domain->status) }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    @if($domain->status !== 'verified')
                                                        <div class="text-xs">
                                                            <p class="mb-1">Add TXT record:</p>
                                                            <code class="bg-gray-100 p-1 rounded block w-full overflow-x-auto">{{ $domain->dns_verification_record }}</code>
                                                        </div>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    @if($domain->is_primary)
                                                        <span class="text-green-600 font-bold">✓ Primary</span>
                                                    @elseif($domain->status === 'verified')
                                                        <form method="POST" action="{{ route('distributor.domains.primary', $domain) }}">
                                                            @csrf
                                                            <button type="submit" class="text-blue-600 hover:text-blue-900">Set as Primary</button>
                                                        </form>
                                                    @else
                                                        <span class="text-gray-400">Verify first</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <div class="flex space-x-2">
                                                        @if($domain->status !== 'verified')
                                                            <form method="POST" action="{{ route('distributor.domains.verify', $domain) }}">
                                                                @csrf
                                                                <button type="submit" class="text-indigo-600 hover:text-indigo-900">Verify</button>
                                                            </form>
                                                        @endif
                                                        <form method="POST" action="{{ route('distributor.domains.destroy', $domain) }}" onsubmit="return confirm('Are you sure?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">How to configure Custom Domain</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ol class="list-decimal pl-5 space-y-1">
                                    <li>Add your domain above (e.g., mystore.com).</li>
                                    <li>Log in to your domain registrar (GoDaddy, Namecheap, etc.).</li>
                                    <li>Add a <strong>CNAME</strong> record pointing <code>@</code> or <code>www</code> to <code>{{ parse_url(config('app.url'), PHP_URL_HOST) }}</code>.</li>
                                    <li>Add a <strong>TXT</strong> record with the verification code shown in the table.</li>
                                    <li>Click "Verify" button here after DNS propagation (may take 1-24 hours).</li>
                                    <li>Once verified, click "Set as Primary" to activate it.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
