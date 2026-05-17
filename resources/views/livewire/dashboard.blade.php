<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public function getUserName(): string
    {
        return auth()->user()->name;
    }
};
?>

<div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-2xl font-semibold mb-4">Welcome back, {{ $this->getUserName() }}!</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
                <!-- Activity Placeholder -->
                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50">
                    <h3 class="font-bold text-gray-700 mb-2">Recent Activity</h3>
                    <p class="text-gray-500 text-sm">No recent activity found.</p>
                </div>

                <!-- Collections Placeholder -->
                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50">
                    <h3 class="font-bold text-gray-700 mb-2">My Collections</h3>
                    <p class="text-gray-500 text-sm">You haven't created any collections yet.</p>
                    <button class="mt-4 text-sm text-blue-600 font-medium hover:underline">+ Create Collection</button>
                </div>

                <!-- Environment Placeholder -->
                <div class="border border-gray-100 rounded-lg p-4 bg-gray-50">
                    <h3 class="font-bold text-gray-700 mb-2">Environments</h3>
                    <p class="text-gray-500 text-sm">Default Environment active.</p>
                    <button class="mt-4 text-sm text-blue-600 font-medium hover:underline">Manage Environments</button>
                </div>
            </div>

            <div class="mt-12">
                <h3 class="text-xl font-bold mb-4">HTTP Client</h3>
                <div class="bg-gray-100 h-64 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300">
                    <div class="text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                        <p class="text-gray-500">HTTP Client Interface coming soon...</p>
                        <a href="{{ appUrl('/') }}" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Open Quick Request</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
