<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|email|max:255|unique:users')]
    public string $email = '';

    #[Validate('required|string|min:8')]
    public string $password = '';

    public function register(): void
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        Auth::login($user);

        $this->redirect(appUrl('/dashboard'), navigate: true);
    }

    public function continueAsGuest(): void
    {
        // For now, let's just redirect to home with a 'guest' flag in session
        // or we could create a temporary guest user.
        // Let's just redirect to home for now as "guest users" are allowed.
        session(['is_guest' => true]);
        $this->redirect(appUrl('/'), navigate: true);
    }
};
?>

<div class="flex flex-col items-center justify-center min-h-[60vh]">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Create an Account</h2>

        <form wire:submit="register" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input wire:model="name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input wire:model="email" type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input wire:model="password" type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-150 ease-in-out">
                Register
            </button>
        </form>

        <div class="mt-6 text-center space-y-4">
            <p class="text-sm text-gray-600">
                Already have an account? <a href="{{ appUrl('/login') }}" class="text-blue-600 hover:underline">Login</a>
            </p>
            
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <span class="w-full border-t border-gray-300"></span>
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-white px-2 text-gray-500">Or</span>
                </div>
            </div>

            <button wire:click="continueAsGuest" class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-200 transition duration-150 ease-in-out font-medium">
                Continue as Guest
            </button>
        </div>
    </div>
</div>
