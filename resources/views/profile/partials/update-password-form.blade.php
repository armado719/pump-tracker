<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">Cambiar Contraseña</h2>
        <p class="mt-1 text-sm text-gray-600">
            Usa una contraseña larga y segura para proteger tu cuenta.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-gray-700">
                Contraseña actual
            </label>
            <input id="update_password_current_password" name="current_password" type="password"
                   autocomplete="current-password"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-2 focus:ring-blue-500 px-3 py-2 border">
            @foreach($errors->updatePassword->get('current_password') as $msg)
            <p class="mt-1 text-sm text-red-600">{{ $msg }}</p>
            @endforeach
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-medium text-gray-700">
                Nueva contraseña
            </label>
            <input id="update_password_password" name="password" type="password"
                   autocomplete="new-password"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-2 focus:ring-blue-500 px-3 py-2 border">
            @foreach($errors->updatePassword->get('password') as $msg)
            <p class="mt-1 text-sm text-red-600">{{ $msg }}</p>
            @endforeach
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700">
                Confirmar nueva contraseña
            </label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                   autocomplete="new-password"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm focus:ring-2 focus:ring-blue-500 px-3 py-2 border">
            @foreach($errors->updatePassword->get('password_confirmation') as $msg)
            <p class="mt-1 text-sm text-red-600">{{ $msg }}</p>
            @endforeach
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                    class="px-5 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg text-sm font-bold transition">
                Guardar contraseña
            </button>
            @if(session('status') === 'password-updated')
            <p x-data="{ show: true }" x-show="show" x-transition
               x-init="setTimeout(() => show = false, 2000)"
               class="text-sm text-green-600 font-medium">
                ✓ Contraseña actualizada
            </p>
            @endif
        </div>
    </form>
</section>
