<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | IngeniaMath</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>

<body>
    <main class="page">
        <section class="brand-logo-wrap" aria-label="Identidad visual IngeniaMath">
            <div class="brand-logo-card">
                <img src="{{ asset('assets/images/IngeniaMath.png') }}" alt="Logo IngeniaMath" class="brand-logo">
            </div>
        </section>

        <section class="login-panel" aria-labelledby="login-heading">
            <div class="login-card">
                <div class="login-header">
                    <h2 id="login-heading">Inicia sesion</h2>
                    <p>Accede con tu correo institucional o cuenta registrada.</p>
                </div>

                <form class="login-form" method="POST" action="{{ url('/login') }}" novalidate>
                    @csrf

                    <label for="email" class="field-label">Correo electronico</label>
                    <input id="email" name="email" type="email" class="field-input" placeholder="nombre@correo.com"
                        required>

                    <label for="password" class="field-label">Contrasena</label>
                    <input id="password" name="password" type="password" class="field-input"
                        placeholder="Ingresa tu contrasena" required>

                    <div class="form-row">
                        <a href="{{ route('register') }}" class="aux-link">No tengo cuenta</a>
                        <a href="#" class="aux-link">Olvidé mi contrasena</a>
                    </div>

                    <button type="submit" class="btn-primary">Entrar</button>
                </form>

                <div class="feedback error" role="alert">
                    Si tu acceso falla, valida correo y contrasena.
                </div>
            </div>
        </section>
    </main>
</body>

</html>