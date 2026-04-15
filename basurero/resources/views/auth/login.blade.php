<x-guest-layout>
    <!-- Mensaje de sesión -->
    @if(session('status'))
        <div style="background:#ecfdf5;border:1px solid #6ee7b7;color:#065f46;padding:10px 14px;border-radius:10px;font-size:.82rem;margin-bottom:18px;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-check-circle"></i>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Correo electrónico -->
        <div class="field-wrap">
            <label for="email" class="field-label">Correo electrónico</label>
            <div style="position:relative;">
                <span class="field-icon"><i class="fas fa-envelope"></i></span>
                <input id="email"
                    class="field-input"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required autofocus autocomplete="username"
                    placeholder="usuario@ejemplo.com">
            </div>
            @error('email')
                <span class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
            @enderror
        </div>

        <!-- Contraseña -->
        <div class="field-wrap">
            <label for="password" class="field-label">Contraseña</label>
            <div style="position:relative;">
                <span class="field-icon"><i class="fas fa-lock"></i></span>
                <input id="password"
                    class="field-input"
                    type="password"
                    name="password"
                    required autocomplete="current-password"
                    placeholder="••••••••"
                    style="padding-right:42px;">
                <button type="button" class="toggle-pass" onclick="togglePassword()" id="toggleBtn">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </button>
            </div>
            @error('password')
                <span class="field-error"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
            @enderror
        </div>

        <!-- Recuérdame -->
        <div class="remember-row">
            <label class="remember-label">
                <input type="checkbox" name="remember">
                Recordar sesión
            </label>
        </div>

        <!-- Botón ingresar -->
        <button type="submit" class="btn-login">
            <i class="fas fa-sign-in-alt mr-2"></i>Ingresar al sistema
        </button>
    </form>

    <script>
    function togglePassword() {
        const pwd  = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            pwd.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
    </script>
</x-guest-layout>
