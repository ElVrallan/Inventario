@push('styles')
<link rel="stylesheet" href="{{ asset('css/pendiente.css') }}">
@endpush

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta Pendiente - Sistema de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div class="pendiente-container">
        <!-- Fondo animado -->
        <div class="background-animation"></div>
        
        <!-- Contenido principal -->
        <div class="content-card">
            <!-- Icono principal -->
            <div class="icon-container">
                <div class="clock-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
            
            <!-- Título -->
            <h1 class="main-title">Cuenta Pendiente</h1>
            
            <!-- Mensaje principal -->
            <div class="message-container">
                @if(Auth::check())
                <p class="main-message">
                    <i class="bi bi-check-circle text-green-500 mr-2"></i>
                    Tu cuenta fue creada exitosamente
                </p>
                <p class="sub-message">
                    Necesita autorización de un administrador para acceder al sistema de inventario
                </p>
                @else
                <p class="main-message">
                    <i class="bi bi-exclamation-triangle text-yellow-500 mr-2"></i>
                    Sesión expirada por seguridad
                </p>
                <p class="sub-message">
                    Tu cuenta sigue pendiente de autorización. Inicia sesión para verificar tu estado actual.
                </p>
                @endif
            </div>
            
            <!-- Indicador de progreso -->
            <div class="progress-container">
                <div class="progress-step completed">
                    <div class="step-circle">
                        <i class="bi bi-person-plus"></i>
                    </div>
                    <span class="step-label">Registro</span>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step pending">
                    <div class="step-circle">
                        <i class="bi bi-person-check"></i>
                    </div>
                    <span class="step-label">Autorización</span>
                </div>
                <div class="progress-line disabled"></div>
                <div class="progress-step disabled">
                    <div class="step-circle">
                        <i class="bi bi-box"></i>
                    </div>
                    <span class="step-label">Acceso</span>
                </div>
            </div>
            
            <!-- Información adicional -->
            <div class="info-box">
                @if(Auth::check())
                <i class="bi bi-info-circle text-blue-500"></i>
                <div class="info-text">
                    <p class="info-title">¿Qué sigue?</p>
                    <p class="info-description">
                        Un administrador revisará tu solicitud y te notificaremos por email cuando tu cuenta sea activada.
                    </p>
                </div>
                @else
                <i class="bi bi-shield-exclamation text-orange-500"></i>
                <div class="info-text">
                    <p class="info-title">Sesión por seguridad</p>
                    <p class="info-description">
                        Por tu seguridad, las sesiones expiran automáticamente. Inicia sesión nuevamente para verificar si tu cuenta ya fue autorizada.
                    </p>
                </div>
                @endif
            </div>
            
            <!-- Usuario actual -->
            @if(Auth::check())
            <div class="user-info">
                <i class="bi bi-person-circle"></i>
                <span>{{ Auth::user()->name }} ({{ Auth::user()->email }})</span>
            </div>
            @else
            <div class="user-info">
                <i class="bi bi-person-circle"></i>
                <span>Sesión expirada - Debes iniciar sesión nuevamente</span>
            </div>
            @endif
            
            <!-- Botones de acción -->
            <div class="action-buttons">
                @if(Auth::check())
                <button id="refreshBtn" class="refresh-button">
                    <i class="bi bi-arrow-clockwise"></i>
                    Verificar Estado
                </button>
                <a href="{{ route('logout') }}" 
                   class="logout-button"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                   <i class="bi bi-box-arrow-right"></i>
                   Cerrar Sesión
                </a>
                @else
                <a href="{{ route('login') }}" class="refresh-button">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Iniciar Sesión
                </a>
                <a href="{{ route('register') }}" class="logout-button">
                    <i class="bi bi-person-plus"></i>
                    Registrarse
                </a>
                @endif
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="footer">
            <p>&copy; {{ date('Y') }} Sistema de Inventario. Todos los derechos reservados.</p>
        </footer>
        
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animación de entrada
            const contentCard = document.querySelector('.content-card');
            contentCard.style.opacity = '0';
            contentCard.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                contentCard.style.transition = 'all 0.6s ease-out';
                contentCard.style.opacity = '1';
                contentCard.style.transform = 'translateY(0)';
            }, 100);
            
            // Botón de verificar estado (solo si existe y el usuario está autenticado)
            const refreshBtn = document.getElementById('refreshBtn');
            if (refreshBtn && refreshBtn.tagName === 'BUTTON') {
                refreshBtn.addEventListener('click', function() {
                    const btn = this;
                    const icon = btn.querySelector('i');
                    
                    // Animación de carga
                    icon.style.animation = 'spin 1s linear infinite';
                    btn.disabled = true;
                    
                    // Simular verificación
                    setTimeout(() => {
                        icon.style.animation = '';
                        btn.disabled = false;
                        
                        Swal.fire({
                            icon: 'info',
                            title: 'Estado verificado',
                            text: 'Tu cuenta sigue pendiente de autorización. Te notificaremos cuando sea aprobada.',
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#3b82f6'
                        });
                    }, 1500);
                });
            }
            
            // Animación del reloj
            const clockIcon = document.querySelector('.clock-icon i');
            setInterval(() => {
                clockIcon.style.transform = 'rotate(360deg)';
                setTimeout(() => {
                    clockIcon.style.transform = 'rotate(0deg)';
                }, 500);
            }, 3000);
            
            // Mostrar alerta de sesión si existe
            @if(session('pendiente'))
            Swal.fire({
                icon: 'warning',
                title: 'Cuenta pendiente',
                text: '{{ session('pendiente') }}',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#3b82f6',
                background: '#fff',
                customClass: {
                    popup: 'rounded-lg'
                }
            });
            @endif
            
            // Mostrar alerta si la sesión ha expirado
            @if(!Auth::check())
            Swal.fire({
                icon: 'warning',
                title: 'Sesión expirada',
                text: 'Tu sesión ha expirado por seguridad. Debes iniciar sesión nuevamente para verificar el estado de tu cuenta.',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#ef4444',
                background: '#fff',
                customClass: {
                    popup: 'rounded-lg'
                }
            });
            @endif
        });
    </script>
</body>
</html>
