<x-guest-layout>
    <div class="text-center space-y-4">
        <div class="text-4xl">🔒</div>
        <h2 class="text-lg font-semibold text-gray-800">Registro no disponible</h2>
        <p class="text-sm text-gray-500">
            Los accesos son creados por el administrador del sistema.<br>
            Contacta a tu supervisor para solicitar una cuenta.
        </p>
        <a href="{{ route('login') }}"
           class="inline-block mt-2 text-sm text-indigo-600 hover:underline">
            ← Volver al inicio de sesión
        </a>
    </div>
</x-guest-layout>
