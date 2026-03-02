<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - CIVICConnect</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; flex-direction: row; background: #fff; color: #111827; -webkit-font-smoothing: antialiased; }

        /* ── Left Panel ── */
        .auth-left {
            display: none;
            width: 50%;
            position: relative;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px;
            color: #fff;
            background: linear-gradient(135deg, rgba(30,58,138,.95), rgba(30,64,175,.85)),
                         url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
            background-blend-mode: multiply;
            overflow: hidden;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(30,58,138,.20);
            pointer-events: none;
        }
        .auth-left > * { position: relative; z-index: 1; }

        .auth-logo { display: flex; align-items: center; gap: 12px; }
        .auth-logo-icon {
            width: 40px; height: 40px;
            background: rgba(255,255,255,.2);
            backdrop-filter: blur(4px);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }
        .auth-logo-icon .material-symbols-outlined { font-size: 22px; color: #fff; }
        .auth-logo-text { font-size: 20px; font-weight: 700; letter-spacing: .5px; }

        .auth-hero { display: flex; flex-direction: column; gap: 24px; margin: auto 0; padding-top: 40px; }
        .auth-hero h2 { font-size: 48px; font-weight: 700; line-height: 1.15; }
        .auth-hero h2 span { color: #bfdbfe; }
        .auth-hero p { font-size: 18px; line-height: 1.7; color: rgba(191,219,254,.9); max-width: 480px; }

        .auth-quote {
            margin-top: auto;
            padding-top: 32px;
            font-style: italic;
            font-weight: 300;
            font-size: 17px;
            line-height: 1.6;
            color: #bfdbfe;
            border-left: 4px solid #60a5fa;
            padding-left: 16px;
        }

        /* ── Right Panel ── */
        .auth-right {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 24px;
            overflow-y: auto;
        }
        .auth-form-wrap { width: 100%; max-width: 448px; }

        /* Tabs */
        .auth-tabs { display: flex; border-bottom: 1px solid #e5e7eb; margin-bottom: 32px; }
        .auth-tab {
            padding: 10px 16px;
            font-size: 15px; font-weight: 500;
            color: #6b7280;
            text-decoration: none;
            border-bottom: 2px solid transparent;
            transition: color .2s, border-color .2s;
        }
        .auth-tab:hover { color: #374151; }
        .auth-tab.active { color: #1d5dec; border-bottom-color: #1d5dec; pointer-events: none; }

        /* Form */
        .auth-form { display: flex; flex-direction: column; gap: 24px; }
        .auth-field label {
            display: block;
            font-size: 14px; font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }
        .auth-input-wrap {
            position: relative;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            transition: border-color .2s;
        }
        .auth-input-wrap:focus-within { border-color: #1d5dec; }
        .auth-input-wrap .material-symbols-outlined {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            font-size: 20px; color: #9ca3af; pointer-events: none;
        }
        .auth-input-wrap .toggle-pw {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            font-size: 20px; color: #9ca3af; cursor: pointer; pointer-events: auto;
            user-select: none;
        }
        .auth-input-wrap .toggle-pw:hover { color: #6b7280; }
        .auth-input {
            width: 100%; border: none; outline: none; background: transparent;
            padding: 12px 12px 12px 40px;
            font-size: 15px; font-family: inherit; color: #111827;
        }
        .auth-input::placeholder { color: #9ca3af; }
        .auth-input.has-toggle { padding-right: 40px; }

        .auth-forgot {
            display: flex; justify-content: flex-end;
            margin-top: -8px;
        }
        .auth-forgot a {
            font-size: 14px; font-weight: 500;
            color: #1d5dec; text-decoration: none;
        }
        .auth-forgot a:hover { color: #164bbd; }

        .auth-btn {
            width: 100%; padding: 14px;
            background: #1d5dec; color: #fff;
            border: none; border-radius: 8px;
            font-size: 15px; font-weight: 500; font-family: inherit;
            cursor: pointer; transition: background .2s;
            display: flex; align-items: center; justify-content: center;
        }
        .auth-btn:hover { background: #164bbd; }

        .auth-helper {
            text-align: center;
            font-size: 13px; line-height: 1.6;
            color: #6b7280;
            padding: 0 16px;
        }

        /* Footer */
        .auth-footer { margin-top: 40px; text-align: center; }
        .auth-footer-copy { font-size: 13px; color: #6b7280; margin-bottom: 8px; }
        .auth-footer-links { display: flex; justify-content: center; gap: 24px; }
        .auth-footer-links a { font-size: 13px; color: #6b7280; text-decoration: none; }
        .auth-footer-links a:hover { color: #374151; }

        /* Alerts */
        .auth-alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            line-height: 1.5;
        }
        .auth-alert--error { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
        .auth-alert--success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }

        /* ── Responsive ── */
        @media (min-width: 768px) {
            .auth-left { display: flex; }
            .auth-right { width: 58.333%; padding: 48px 64px; }
        }
        @media (min-width: 1024px) {
            .auth-left { width: 50%; }
            .auth-right { width: 50%; }
        }
        @media (max-width: 767px) {
            .auth-right { min-height: 100vh; }
        }
    </style>
</head>
<body>
    <!-- ════ Left Panel ════ -->
    <div class="auth-left">
        <div class="auth-logo">
            <div class="auth-logo-icon">
                <span class="material-symbols-outlined">verified_user</span>
            </div>
            <span class="auth-logo-text">CIVICConnect</span>
        </div>

        <div class="auth-hero">
            <h2>Cipta Intelektual<br>Visioner<br><span>Indonesia Cerdas</span></h2>
            <p>Platform digital terintegrasi untuk membentuk mahasiswa yang kritis, analitis, dan solutif dalam menghadapi era disrupsi informasi.</p>
        </div>

        <div class="auth-quote">
            "Mengubah paradigma gerakan mahasiswa dari reaktif-emosional menjadi solutif-ilmiah"
        </div>
    </div>

    <!-- ════ Right Panel ════ -->
    <div class="auth-right">
        <div class="auth-form-wrap">
            <!-- Tabs -->
            <div class="auth-tabs">
                <a href="{{ route('login') }}" class="auth-tab active">Masuk</a>
                <a href="{{ route('register') }}" class="auth-tab">Daftar</a>
            </div>

            <!-- Alerts -->
            @if ($errors->any())
                <div class="auth-alert auth-alert--error" style="margin-bottom:20px">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
            @if (session('success'))
                <div class="auth-alert auth-alert--success" style="margin-bottom:20px">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="auth-form">
                @csrf

                <!-- Email -->
                <div class="auth-field">
                    <label for="email">Email Universitas</label>
                    <div class="auth-input-wrap">
                        <span class="material-symbols-outlined">mail</span>
                        <input id="email" type="email" name="email" class="auth-input"
                               placeholder="nama@universitas.ac.id"
                               value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <!-- Password -->
                <div class="auth-field">
                    <label for="password">Password</label>
                    <div class="auth-input-wrap">
                        <span class="material-symbols-outlined">lock</span>
                        <input id="password" type="password" name="password"
                               class="auth-input has-toggle"
                               placeholder="Masukkan password Anda" required>
                        <span class="material-symbols-outlined toggle-pw" onclick="togglePw('password', this)">visibility_off</span>
                    </div>
                </div>

                <!-- Forgot Password -->
                <div class="auth-forgot">
                    <a href="#">Lupa Password?</a>
                </div>

                <!-- Submit -->
                <button type="submit" class="auth-btn">Masuk Sekarang</button>
            </form>

            <!-- Divider -->
            <div style="display:flex;align-items:center;gap:12px;margin:20px 0 16px;">
                <div style="flex:1;height:1px;background:#e5e7eb"></div>
                <span style="font-size:13px;color:#9ca3af;white-space:nowrap">atau</span>
                <div style="flex:1;height:1px;background:#e5e7eb"></div>
            </div>

            <!-- Anonymous Login -->
            <form method="POST" action="{{ route('login.anonim') }}">
                @csrf
                <button type="submit" style="width:100%;padding:12px;border:1px solid #e5e7eb;border-radius:8px;background:#fff;color:#374151;font-size:15px;font-family:inherit;font-weight:500;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .2s,border-color .2s;" onmouseover="this.style.background='#f9fafb';this.style.borderColor='#d1d5db'" onmouseout="this.style.background='#fff';this.style.borderColor='#e5e7eb'">
                    <span class="material-symbols-outlined" style="font-size:20px;color:#9ca3af">visibility</span>
                    Masuk sebagai Pengunjung
                </button>
            </form>
            <p style="text-align:center;font-size:12px;color:#9ca3af;margin-top:8px;">Hanya dapat melihat feed dan policy brief yang sudah dipublikasikan.</p>

            <!-- Footer -->
            <div class="auth-footer">
                <div class="auth-footer-copy">CIVIC-Connect © 2026</div>
                <div class="auth-footer-links">
                    <a href="#">Tentang</a>
                    <a href="#">Panduan</a>
                    <a href="#">Privasi</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePw(id, el) {
            const input = document.getElementById(id);
            if (input.type === 'password') {
                input.type = 'text';
                el.textContent = 'visibility';
            } else {
                input.type = 'password';
                el.textContent = 'visibility_off';
            }
        }
    </script>
</body>
</html>
