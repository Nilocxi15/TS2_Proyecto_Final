<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse | IngeniaMath</title>

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
                <form class="register-form" method="POST" action="{{ url('/register') }}">
                    @csrf

                    <h2>Registro</h2>

                    <div class="field-group">
                        <label for="name">Name</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            autocomplete="name"
                            required
                            placeholder="Tu nombre completo">
                        @error('name')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="email">Email</label>
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

                    <div class="field-group">
                        <label for="password">Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="new-password"
                            required
                            placeholder="Minimo 8 caracteres">
                        @error('password')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="password_confirmation">Confirm password</label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            required
                            placeholder="Confirma tu contrasena">
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

</body>

</html>