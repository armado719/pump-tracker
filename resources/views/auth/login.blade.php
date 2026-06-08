<x-guest-layout>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if(session('error'))
    <div style="margin-bottom:1rem;padding:0.75rem 1rem;border-radius:0.5rem;
                background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);
                color:#fca5a5;font-size:0.875rem;">
        {{ session('error') }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}" style="display:flex;flex-direction:column;gap:1.25rem;">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" style="display:block;color:#6b9e82;font-size:0.8rem;
                   font-weight:600;letter-spacing:0.06em;text-transform:uppercase;margin-bottom:0.5rem;">
                Correo electrónico
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   required autofocus autocomplete="username"
                   style="width:100%;padding:0.75rem 1rem;border-radius:0.5rem;font-size:0.95rem;
                          background:#0b1622;border:1px solid rgba(74,222,128,0.2);
                          color:#f0ede8;outline:none;box-sizing:border-box;
                          transition:border-color 0.2s;"
                   onfocus="this.style.borderColor='rgba(74,222,128,0.6)'"
                   onblur="this.style.borderColor='rgba(74,222,128,0.2)'"
                   placeholder="usuario@grs.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div>
            <label for="password" style="display:block;color:#6b9e82;font-size:0.8rem;
                   font-weight:600;letter-spacing:0.06em;text-transform:uppercase;margin-bottom:0.5rem;">
                Contraseña
            </label>
            <input id="password" type="password" name="password"
                   required autocomplete="current-password"
                   style="width:100%;padding:0.75rem 1rem;border-radius:0.5rem;font-size:0.95rem;
                          background:#0b1622;border:1px solid rgba(74,222,128,0.2);
                          color:#f0ede8;outline:none;box-sizing:border-box;
                          transition:border-color 0.2s;"
                   onfocus="this.style.borderColor='rgba(74,222,128,0.6)'"
                   onblur="this.style.borderColor='rgba(74,222,128,0.2)'"
                   placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Remember + Forgot --}}
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
                <input id="remember_me" type="checkbox" name="remember"
                       style="width:16px;height:16px;accent-color:#4ade80;cursor:pointer;">
                <span style="color:#4a8a9e;font-size:0.85rem;">Recordarme</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   style="color:#4a8a9e;font-size:0.85rem;text-decoration:none;"
                   onmouseover="this.style.color='#4ade80'"
                   onmouseout="this.style.color='#4a8a9e'">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        {{-- Botón --}}
        <button type="submit"
                style="width:100%;padding:0.85rem;border-radius:0.5rem;border:none;cursor:pointer;
                       background:linear-gradient(135deg,#16a34a,#4ade80);
                       color:#0b1622;font-size:1rem;font-weight:700;letter-spacing:0.05em;
                       transition:opacity 0.2s,transform 0.1s;"
                onmouseover="this.style.opacity='0.9';this.style.transform='translateY(-1px)'"
                onmouseout="this.style.opacity='1';this.style.transform='translateY(0)'">
            Iniciar sesión
        </button>

    </form>

</x-guest-layout>
