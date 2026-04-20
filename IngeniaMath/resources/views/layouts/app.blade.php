<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>IngeniaMath - @yield('title', 'Banco de Ejercicios')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- MathJax para fórmulas matemáticas -->
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js" id="MathJax-script" async></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

body {
    background: #f5f7fa;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: bold;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-size: 1.5rem;
        }

        .page {
            min-height: calc(100vh - 56px); 
            display: grid; 
            grid-template-columns: 1fr; 
            align-items: start;
            gap: 26px;
            max-width: 1400px;
            margin: 0 auto; 
            padding: 2rem;
        }

        /* Para vistas que necesitan dos columnas (como formularios grandes) */
        .page-two-columns {
            min-height: calc(100vh - 56px); 
            display: grid; 
            grid-template-columns: 1.02fr 1fr; 
            align-items: start;
            gap: 26px;
            max-width: 1400px;
            margin: 0 auto; 
            padding: 2rem;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 10px;
            padding: 10px 25px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8, #6b46a0);
            transform: scale(1.02);
        }

        .table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }

        .table thead {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding: 10px 15px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .alert {
            border-radius: 15px;
            border: none;
        }

        /* Para el editor de enunciados */
        .math-editor {
            font-family: monospace;
            background: #f8f9fa;
        }

        /* Para previsualización de matemáticas */
        .math-preview {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: white;
            background: rgba(0,0,0,0.1);
            margin-top: 40px;
        }
    </style>
    
    @stack('styles')
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('ejercicios.index') }}">
                <i class="fas fa-brain me-2"></i>IngeniaMath
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('ejercicios.index') }}">
                            <i class="fas fa-database me-1"></i> Ejercicios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('ejercicios.create') }}">
                            <i class="fas fa-plus-circle me-1"></i> Nuevo Ejercicio
                        </a>
                    </li>
                    <!-- Temporal: sin autenticación -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> Tutor Demo
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Mi Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; 2026 IngeniaMath - Plataforma de Aprendizaje Adaptativo para Aspirantes a Ingeniería USAC</p>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Función para previsualizar MathJax
        function previewMath() {
            if (window.MathJax) {
                MathJax.typesetPromise();
            }
        }
        
        // Escuchar cambios en los campos con contenido matemático
        document.addEventListener('DOMContentLoaded', function() {
            const mathFields = document.querySelectorAll('.math-field');
            mathFields.forEach(field => {
                field.addEventListener('input', function() {
                    if (window.MathJax) {
                        MathJax.typesetPromise();
                    }
                });
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>