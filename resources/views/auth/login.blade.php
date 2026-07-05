<x-guest-layout>
    <h1 style="font-family:Inter,sans-serif;font-size:20px;font-weight:600;color:var(--cms-text-primary);margin:0 0 4px;">{{ __('Sign in') }}</h1>
    <p style="font-family:Inter,sans-serif;font-size:12px;color:var(--cms-text-muted);margin:0 0 20px;">{{ __('Welcome back. Enter your credentials to continue.') }}</p>

    <!-- Session Status -->
    <x-auth-session-status style="display:block;margin-bottom:16px;" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" style="display:flex;flex-direction:column;gap:16px;">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Remember Me -->
        <label for="remember_me" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
            <input id="remember_me" type="checkbox" name="remember" style="width:16px;height:16px;accent-color:var(--cms-accent);">
            <span style="font-size:12px;color:var(--cms-text-muted);">{{ __('Remember me') }}</span>
        </label>

        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
            @if (Route::has('password.request'))
                <a style="font-size:12px;color:var(--cms-text-muted);text-decoration:underline;" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
