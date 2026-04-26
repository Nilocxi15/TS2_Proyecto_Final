<nav class="navbar sticky-top">
    <div class="container d-flex justify-content-between align-items-center">

        {{-- IZQUIERDA: botón + logo --}}
        <div class="d-flex align-items-center gap-2">

            @auth
                <button class="btn"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#sidebarOffcanvas">
                    <i class="fas fa-bars"></i>
                </button>
            @endauth

            <a class="navbar-brand m-0" href="{{ url('/redirect-by-role') }}">
                <i class="fas fa-brain me-2"></i>IngeniaMath
            </a>

        </div>

        {{-- DERECHA: perfil --}}
        <div>

            @auth
                <div class="dropdown">

                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown">

                        <i class="fas fa-user-circle fs-5"></i>
                        {{ auth()->user()->nombre }}

                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4">

                        <li>
                            <a class="dropdown-item py-2" href="{{ route('profile') }}">
                                <i class="fas fa-user me-2"></i> Mi Perfil
                            </a>
                        </li>

                        <li><hr class="dropdown-divider"></li>

                        <li>
                            <form method="POST" action="/logout">
                                @csrf
                                <button class="dropdown-item py-2 text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    Cerrar sesión
                                </button>
                            </form>
                        </li>

                    </ul>

                </div>
            @else

                <a class="nav-link" href="/">
                    <i class="fas fa-right-to-bracket me-1"></i> Iniciar sesión
                </a>

            @endauth

        </div>

    </div>
</nav>
