<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>PLAN SEMANAL TIC</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

        <style>
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
            body {
                font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                background: url('{{ asset('images/fd.jpg') }}') no-repeat center center fixed;
                background-size: 100%;
                color: #e2e8f0;
                overflow: hidden;
                position: relative;
            }

            /* Animated background grid */
            body::before {
                content: '';
                position: absolute;
                inset: 0;
                background-image:
                    linear-gradient(rgba(59, 130, 246, 0.05) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(59, 130, 246, 0.05) 1px, transparent 1px);
                background-size: 50px 50px;
                animation: gridMove 20s linear infinite;
            }

            /* Floating glow */
            body::after {
                content: '';
                position: absolute;
                width: 600px;
                height: 600px;
                background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, transparent 70%);
                top: -200px;
                right: -200px;
                border-radius: 50%;
                animation: pulse 6s ease-in-out infinite;
            }

            @keyframes gridMove {
                0% { transform: translate(0, 0); }
                100% { transform: translate(50px, 50px); }
            }

            @keyframes pulse {
                0%, 100% { opacity: 0.5; transform: scale(1); }
                50% { opacity: 1; transform: scale(1.1); }
            }

            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .card {
                position: relative;
                z-index: 10;
                background: rgba(15, 23, 42, 0.8);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(59, 130, 246, 0.2);
                border-radius: 1.5rem;
                padding: 3rem 2.5rem;
                max-width: 480px;
                width: 100%;
                text-align: center;
                box-shadow:
                    0 0 60px rgba(59, 130, 246, 0.1),
                    0 25px 50px rgba(0, 0, 0, 0.3);
                animation: fadeInUp 0.8s ease-out;
            }

            .icon-container {
                width: 90px;
                height: 90px;
                margin: 0 auto 1.5rem;
                background: linear-gradient(135deg, #1e40af, #3b82f6);
                border-radius: 1.25rem;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 8px 30px rgba(59, 130, 246, 0.3);
                animation: fadeInUp 0.8s ease-out 0.2s both;
            }

            .icon-container svg {
                width: 48px;
                height: 48px;
                color: white;
            }

            h1 {
                font-size: 2rem;
                font-weight: 800;
                letter-spacing: 0.05em;
                background: linear-gradient(135deg, #93c5fd, #3b82f6, #60a5fa);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                margin-bottom: 0.75rem;
                animation: fadeInUp 0.8s ease-out 0.3s both;
            }

            .subtitle {
                font-size: 1.05rem;
                color: #94a3b8;
                line-height: 1.6;
                margin-bottom: 2rem;
                animation: fadeInUp 0.8s ease-out 0.4s both;
            }

            .btn-login {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.875rem 2.5rem;
                background: linear-gradient(135deg, #2563eb, #1d4ed8);
                color: white;
                font-weight: 700;
                font-size: 1rem;
                border: none;
                border-radius: 0.75rem;
                text-decoration: none;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 4px 20px rgba(37, 99, 235, 0.4);
                animation: fadeInUp 0.8s ease-out 0.5s both;
            }

            .btn-login:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 30px rgba(37, 99, 235, 0.5);
                background: linear-gradient(135deg, #3b82f6, #2563eb);
            }

            .btn-login:active {
                transform: translateY(0);
            }

            .btn-login svg {
                width: 20px;
                height: 20px;
            }

            .features {
                display: flex;
                justify-content: center;
                gap: 2rem;
                margin-top: 2rem;
                animation: fadeInUp 0.8s ease-out 0.6s both;
            }

            .feature {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.4rem;
            }

            .feature-icon {
                width: 36px;
                height: 36px;
                background: rgba(59, 130, 246, 0.1);
                border: 1px solid rgba(59, 130, 246, 0.2);
                border-radius: 0.5rem;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .feature-icon svg {
                width: 18px;
                height: 18px;
                color: #60a5fa;
            }

            .feature span {
                font-size: 0.75rem;
                color: #64748b;
                font-weight: 500;
            }

            .glow-line {
                width: 60%;
                height: 1px;
                margin: 1.5rem auto;
                background: linear-gradient(90deg, transparent, rgba(59,130,246,0.4), transparent);
            }

            @media (max-width: 520px) {
                .card { margin: 1rem; padding: 2rem 1.5rem; }
                h1 { font-size: 1.5rem; }
                .features { gap: 1rem; }
            }
        </style>
    </head>
    <body>
        <div class="card">
            <!-- Icon -->
            <div class="icon-container">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                    <line x1="8" y1="21" x2="16" y2="21"/>
                    <line x1="12" y1="17" x2="12" y2="21"/>
                    <polyline points="7 8 10 11 7 14"/>
                    <line x1="13" y1="14" x2="17" y2="14"/>
                </svg>
            </div>

            <!-- Title -->
            <h1>PLAN SEMANAL TIC</h1>

            <!-- Subtitle -->
            <p class="subtitle">Registro de actividades para cumplimiento del Área</p>

            <!-- Glow separator -->
            <div class="glow-line"></div>

            <!-- Login Button -->
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/activities') }}" class="btn-login">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                        Ir al Panel
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-login">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                        Iniciar Sesión
                    </a>
                @endauth
            @endif

            <!-- Features -->
            <div class="features">
                <div class="feature">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="8.5" cy="7" r="4"/>
                            <polyline points="17 11 19 13 23 9"/>
                        </svg>
                    </div>
                    <span>Usuarios</span>
                </div>
                <div class="feature">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <span>Planificación</span>
                </div>
                <div class="feature">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                        </svg>
                    </div>
                    <span>Seguimiento</span>
                </div>
            </div>
        </div>
    </body>
</html>
