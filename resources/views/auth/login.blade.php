@extends('layouts.app')

@section('title', 'Login')

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500&display=swap');

        body {
            background: #0a0a0f !important;
            overflow: hidden;
            position: relative;
        }

        #particle-canvas {
            position: fixed;
            inset: 0;
            z-index: 1;
            pointer-events: none;
        }

        .login-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(ellipse at 20% 50%, #1a1000 0%, #0a0a0f 60%),
                radial-gradient(ellipse at 80% 20%, #120d00 0%, transparent 50%);
        }

        .login-outer {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: linear-gradient(145deg, rgba(30, 22, 5, 0.95) 0%, rgba(15, 10, 2, 0.98) 100%);
            border: 1px solid rgba(212, 175, 55, 0.25);
            border-radius: 20px;
            padding: 44px 40px 36px;
            box-shadow:
                0 0 60px rgba(212, 175, 55, 0.08),
                0 30px 60px rgba(0, 0, 0, 0.6),
                inset 0 1px 0 rgba(212, 175, 55, 0.15);
            backdrop-filter: blur(20px);
        }

        .logo-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .logo-coin {
            width: 40px;
            height: 40px;
            background: conic-gradient(from 0deg, #c8952a, #f5d77e, #daa035, #f5d77e, #c8952a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 700;
            color: #5a3a00;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.5), 0 2px 8px rgba(0, 0, 0, 0.4);
            animation: spinCoin 8s linear infinite;
        }

        @keyframes spinCoin {
            0% {
                box-shadow: 0 0 20px rgba(212, 175, 55, 0.5), 0 2px 8px rgba(0, 0, 0, 0.4);
                transform: rotateY(0deg);
            }

            25% {
                box-shadow: 0 0 30px rgba(212, 175, 55, 0.8);
            }

            50% {
                box-shadow: 0 0 10px rgba(212, 175, 55, 0.3);
                transform: rotateY(360deg);
            }

            75% {
                box-shadow: 0 0 25px rgba(212, 175, 55, 0.6);
            }

            100% {
                box-shadow: 0 0 20px rgba(212, 175, 55, 0.5), 0 2px 8px rgba(0, 0, 0, 0.4);
                transform: rotateY(720deg);
            }
        }

        .logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            background: linear-gradient(135deg, #c8952a 0%, #f5d77e 50%, #daa035 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 1px;
        }

        .login-subtitle {
            text-align: center;
            color: rgba(212, 175, 55, 0.4);
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 32px;
            font-family: 'DM Sans', sans-serif;
        }

        .gold-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }

        .gold-divider-line {
            flex: 1;
            height: 1px;
            background: rgba(212, 175, 55, 0.15);
        }

        .gold-divider-diamond {
            width: 6px;
            height: 6px;
            background: rgba(212, 175, 55, 0.4);
            transform: rotate(45deg);
        }

        .login-card .form-label {
            display: block;
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(212, 175, 55, 0.6);
            margin-bottom: 8px;
            font-weight: 500;
            font-family: 'DM Sans', sans-serif;
        }

        .login-card .form-control {
            width: 100%;
            padding: 14px 16px;
            background: rgba(212, 175, 55, 0.04);
            border: 1px solid rgba(212, 175, 55, 0.15);
            border-radius: 10px;
            color: #f5e6b8;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.3s ease;
            outline: none;
            -webkit-appearance: none;
        }

        .login-card .form-control::placeholder {
            color: rgba(212, 175, 55, 0.2);
        }

        .login-card .form-control:focus {
            border-color: rgba(212, 175, 55, 0.5);
            background: rgba(212, 175, 55, 0.07);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.08);
        }

        .login-card .form-group {
            margin-bottom: 20px;
        }

        .form-error {
            color: #f5a623;
            font-size: 12px;
            margin-top: 6px;
            font-family: 'DM Sans', sans-serif;
        }

        .btn-gold {
            width: 100%;
            padding: 15px;
            margin-top: 8px;
            background: linear-gradient(135deg, #c8952a 0%, #f5d77e 50%, #daa035 100%);
            border: none;
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 500;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #1a0f00;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.3);
        }

        .btn-gold::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .btn-gold:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 30px rgba(212, 175, 55, 0.45);
        }

        .btn-gold:hover::before {
            opacity: 1;
        }

        .btn-gold:active {
            transform: translateY(0);
        }

        .shimmer-bar {
            height: 1px;
            margin: 28px 0 0;
            background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.5), transparent);
            background-size: 200% 100%;
            animation: shimmerBar 2.5s ease infinite;
        }

        @keyframes shimmerBar {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        .login-register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: rgba(212, 175, 55, 0.3);
            font-family: 'DM Sans', sans-serif;
        }

        .login-register-link a {
            color: rgba(212, 175, 55, 0.7);
            text-decoration: none;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
            padding-bottom: 1px;
            transition: all 0.2s;
        }

        .login-register-link a:hover {
            color: #f5d77e;
            border-color: rgba(212, 175, 55, 0.5);
        }
    </style>
@endpush

@section('content')
    <div class="login-bg"></div>
    <canvas id="particle-canvas"></canvas>

    <div class="login-outer">
        <div class="login-card">

            <div class="logo-row">
                <div class="logo-coin">₿</div>
                <div class="logo-text">FinTrack</div>
            </div>
            <p class="login-subtitle">Manajemen Keuangan</p>

            <div class="gold-divider">
                <div class="gold-divider-line"></div>
                <div class="gold-divider-diamond"></div>
                <div class="gold-divider-line"></div>
            </div>

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="nama@email.com"
                        value="{{ old('email') }}" required>
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-gold">Masuk</button>
            </form>

            <div class="shimmer-bar"></div>

            <p class="login-register-link">
                Belum punya akun?
                <a href="{{ route('register') }}">Daftar Sekarang</a>
            </p>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            const canvas = document.getElementById('particle-canvas');
            const ctx = canvas.getContext('2d');

            function resize() {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            }
            resize();
            window.addEventListener('resize', resize);

            const GOLD = ['#f5d77e', '#daa035', '#c8952a', '#f0c040', '#e8b820'];

            function rand(a, b) {
                return a + Math.random() * (b - a);
            }

            // ── Coin ──────────────────────────────────────────────────────────────────
            class Coin {
                constructor() {
                    this.reset(true);
                }
                reset(init) {
                    this.x = rand(0, canvas.width);
                    this.y = init ? rand(-canvas.height, canvas.height) : canvas.height + 30;
                    this.r = rand(8, 18);
                    this.speed = rand(0.4, 1.2);
                    this.drift = rand(-0.5, 0.5);
                    this.flip = rand(0, Math.PI * 2);
                    this.flipSpeed = rand(0.02, 0.06);
                    this.alpha = rand(0.15, 0.55);
                    this.color = GOLD[Math.floor(rand(0, GOLD.length))];
                    this.sym = ['$', '€', '¥', '₿', 'Rp'][Math.floor(rand(0, 5))];
                }
                update() {
                    this.y -= this.speed;
                    this.x += this.drift;
                    this.flip += this.flipSpeed;
                    if (this.y < -40) this.reset(false);
                }
                draw() {
                    ctx.save();
                    ctx.globalAlpha = this.alpha;
                    ctx.translate(this.x, this.y);
                    const sx = Math.abs(Math.cos(this.flip));
                    ctx.scale(sx < 0.05 ? 0.05 : sx, 1);

                    ctx.beginPath();
                    ctx.ellipse(0, 0, this.r, this.r, 0, 0, Math.PI * 2);
                    const g = ctx.createRadialGradient(-this.r * .3, -this.r * .3, 1, 0, 0, this.r);
                    g.addColorStop(0, '#fff8dc');
                    g.addColorStop(0.4, this.color);
                    g.addColorStop(1, '#7a5000');
                    ctx.fillStyle = g;
                    ctx.fill();
                    ctx.strokeStyle = 'rgba(255,220,80,0.6)';
                    ctx.lineWidth = 1.5;
                    ctx.stroke();

                    ctx.beginPath();
                    ctx.ellipse(-this.r * .2, -this.r * .3, this.r * .35, this.r * .18, -.5, 0, Math.PI * 2);
                    ctx.fillStyle = 'rgba(255,255,200,0.4)';
                    ctx.fill();

                    ctx.fillStyle = 'rgba(90,50,0,0.7)';
                    ctx.font = `bold ${this.r * .9}px sans-serif`;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(this.sym, 0, 0);
                    ctx.restore();
                }
            }

            // ── Bill ──────────────────────────────────────────────────────────────────
            class Bill {
                constructor() {
                    this.reset(true);
                }
                reset(init) {
                    this.x = rand(0, canvas.width);
                    this.y = init ? rand(-canvas.height, canvas.height) : canvas.height + 60;
                    this.w = rand(50, 90);
                    this.h = this.w * .45;
                    this.speed = rand(0.3, 0.9);
                    this.drift = rand(-0.4, 0.4);
                    this.rot = rand(-0.4, 0.4);
                    this.rotDrift = rand(-0.005, 0.005);
                    this.alpha = rand(0.08, 0.3);
                    this.hue = rand(30, 50);
                }
                update() {
                    this.y -= this.speed;
                    this.x += this.drift;
                    this.rot += this.rotDrift;
                    if (this.y < -80) this.reset(false);
                }
                draw() {
                    ctx.save();
                    ctx.globalAlpha = this.alpha;
                    ctx.translate(this.x, this.y);
                    ctx.rotate(this.rot);

                    ctx.beginPath();
                    ctx.roundRect(-this.w / 2, -this.h / 2, this.w, this.h, 3);
                    ctx.fillStyle = `hsl(${this.hue},60%,35%)`;
                    ctx.fill();
                    ctx.strokeStyle = `hsl(${this.hue},70%,55%)`;
                    ctx.lineWidth = 0.8;
                    ctx.stroke();

                    ctx.strokeStyle = `hsla(${this.hue},60%,70%,0.5)`;
                    ctx.lineWidth = 0.5;
                    ctx.strokeRect(-this.w / 2 + 4, -this.h / 2 + 4, this.w - 8, this.h - 8);

                    ctx.beginPath();
                    ctx.ellipse(0, 0, this.w * .2, this.h * .35, 0, 0, Math.PI * 2);
                    ctx.strokeStyle = `hsla(${this.hue},50%,70%,0.4)`;
                    ctx.stroke();

                    ctx.restore();
                }
            }

            // ── Sparkle ───────────────────────────────────────────────────────────────
            class Sparkle {
                constructor() {
                    this.reset(true);
                }
                reset(init) {
                    this.x = rand(0, canvas.width);
                    this.y = init ? rand(0, canvas.height) : canvas.height + 10;
                    this.size = rand(1, 3.5);
                    this.speed = rand(0.2, 0.7);
                    this.alpha = rand(0.3, 0.9);
                    this.life = rand(0.5, 2);
                    this.maxLife = this.life;
                    this.color = GOLD[Math.floor(rand(0, GOLD.length))];
                }
                update() {
                    this.y -= this.speed;
                    this.life -= 0.008;
                    this.alpha = (this.life / this.maxLife) * rand(0.4, 0.9);
                    if (this.life <= 0 || this.y < 0) this.reset(false);
                }
                draw() {
                    ctx.save();
                    ctx.globalAlpha = this.alpha;
                    ctx.translate(this.x, this.y);
                    ctx.fillStyle = this.color;
                    for (let i = 0; i < 4; i++) {
                        ctx.save();
                        ctx.rotate(i * Math.PI / 2);
                        ctx.beginPath();
                        ctx.moveTo(0, -this.size);
                        ctx.lineTo(this.size * .25, -this.size * .25);
                        ctx.lineTo(this.size, 0);
                        ctx.lineTo(this.size * .25, this.size * .25);
                        ctx.lineTo(0, this.size);
                        ctx.lineTo(-this.size * .25, this.size * .25);
                        ctx.lineTo(-this.size, 0);
                        ctx.lineTo(-this.size * .25, -this.size * .25);
                        ctx.closePath();
                        ctx.fill();
                        ctx.restore();
                    }
                    ctx.restore();
                }
            }

            const coins = Array.from({
                length: 22
            }, () => new Coin());
            const bills = Array.from({
                length: 14
            }, () => new Bill());
            const sparkles = Array.from({
                length: 35
            }, () => new Sparkle());

            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                bills.forEach(b => {
                    b.update();
                    b.draw();
                });
                coins.forEach(c => {
                    c.update();
                    c.draw();
                });
                sparkles.forEach(s => {
                    s.update();
                    s.draw();
                });
                requestAnimationFrame(animate);
            }
            animate();
        })();
    </script>
@endpush
