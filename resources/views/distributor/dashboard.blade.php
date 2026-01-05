<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Distributor Dashboard') }} - {{ $distributor->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Pending -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="text-sm font-medium text-gray-500">Pending Orders</div>
                        <div class="mt-2 text-3xl font-bold text-yellow-600">{{ $stats['pending_orders'] }}</div>
                    </div>
                </div>

                <!-- Processing -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="text-sm font-medium text-gray-500">Processing</div>
                        <div class="mt-2 text-3xl font-bold text-blue-600">{{ $stats['processing_orders'] }}</div>
                    </div>
                </div>

                <!-- Completed -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="text-sm font-medium text-gray-500">Completed</div>
                        <div class="mt-2 text-3xl font-bold text-green-600">{{ $stats['completed_orders'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Quick Actions</h3>
                    <div class="flex gap-4">
                        <a href="{{ route('distributor.orders.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Manage Orders</a>
                        <a href="{{ route('distributor.shipping.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Shipping Settings</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
