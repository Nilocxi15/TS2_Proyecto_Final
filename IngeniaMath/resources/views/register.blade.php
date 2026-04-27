<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse | IngeniaMath</title>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>

<body>
    <div class="register-page">
        <div class="ambient ambient-left" aria-hidden="true"></div>
        <div class="ambient ambient-right" aria-hidden="true"></div>

        <main class="register-shell">
            <section class="brand-column" aria-label="Introduccion">
                <p class="eyebrow">IngeniaMath</p>
                <h1>Crea tu cuenta y empieza a aprender</h1>
                <p class="subtitle">
                    Accede a ejercicios, retos y rutas de estudio en una sola plataforma.
                </p>
            </section>

            <section class="form-column" aria-label="Formulario de registro">
                <form class="register-form" method="POST" action="{{ route('register.store') }}">
                    @csrf

                    <h2>Registro</h2>

                    @error('register')
                        <p class="error">{{ $message }}</p>
                    @enderror

                    <div class="field-group">
                        <label for="name">Nombre</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            autocomplete="name"
                            required
                            placeholder="Tu nombre">
                        @error('name')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="lastname">Apellidos</label>
                        <input
                            id="lastname"
                            name="lastname"
                            type="text"
                            value="{{ old('lastname') }}"
                            autocomplete="family-name"
                            required
                            placeholder="Tus apellidos">
                        @error('lastname')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="email">Correo electrónico</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required
                            placeholder="tu-correo@ejemplo.com">
                        @error('email')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group password-group">
                        <label for="password">Contraseña</label>
                        <div class="input-wrapper">
                            <input type="password" name="password" id="password" autocomplete="new-password" required placeholder="Mínimo 8 caracteres">

                            <button type="button" class="toggle-password" onclick="togglePassword()">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        @error('password')
                        <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group password-group">
                        <label for="password_confirmation">Confirmar contraseña</label>
                        <div class="input-wrapper">
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                required
                                placeholder="Confirma tu contraseña">
                            <button type="button" class="toggle-password" onclick="toggleConfirmPassword()">
                                <i class="bi bi-eye" id="eyeIcon2"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Crear cuenta</button>
                        <a href="{{ url('/') }}" class="btn-secondary">Regresar al login</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        function toggleConfirmPassword() {
            const input = document.getElementById('password_confirmation');
            const icon = document.getElementById('eyeIcon2');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>

</body>

</html>
