<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <h2 class="text-2xl font-bold text-center mb-6">{{ __('Register Account') }}</h2>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Full Name -->
                <div>
                    <x-input-label for="full_name" :value="__('Full Name')" />
                    <x-text-input id="full_name" class="block mt-1 w-full" 
                        type="text" 
                        name="full_name" 
                        :value="old('full_name')" 
                        required 
                        autofocus />
                    <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" 
                        type="email" 
                        name="email" 
                        :value="old('email')" 
                        required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Date of Birth -->
                <div class="mt-4">
                    <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                    <x-text-input id="date_of_birth" class="block mt-1 w-full" 
                        type="date" 
                        name="date_of_birth" 
                        :value="old('date_of_birth')" 
                        required 
                        max="{{ date('Y-m-d') }}" />
                    <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                </div>

                <!-- Phone -->
                <div class="mt-4">
                    <x-input-label for="phone" :value="__('Phone')" />
                    <x-text-input id="phone" class="block mt-1 w-full" 
                        type="tel" 
                        name="phone" 
                        :value="old('phone')" 
                        pattern="[0-9]{10}" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    <p class="mt-1 text-sm text-gray-500">Format: 0123456789</p>
                </div>

                <!-- Role -->
                <div class="mt-4">
                    <x-input-label for="role_id" :value="__('Role')" />
                    <select id="role_id" name="role_id" 
                        class="block mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        required>
                        <option value="">{{ __('Select Role') }}</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
                </div>

                <!-- Unit -->
                <div class="mt-4" id="unit-section">
                    <x-input-label for="unit_id" :value="__('Unit')" />
                    <select id="unit_id" name="unit_id" 
                        class="block mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('Select Unit') }}</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('unit_id')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full"
                        type="password"
                        name="password"
                        required 
                        autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full"
                        type="password"
                        name="password_confirmation" 
                        required />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-6">
                    <a class="text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" 
                        href="{{ route('login') }}">
                        {{ __('Already registered?') }}
                    </a>

                    <x-primary-button class="ml-4">
                        {{ __('Register') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
