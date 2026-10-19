<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Murid - Tahfizd Tracker At-Taubah Karawang</title>

    <link rel="stylesheet" href="/css/login.css">
</head>
<body>

    <!-- NAVBAR (Full Width 100%, Height: 115px) -->
    <header class="navbar">
        <div class="navbar-brand">
            <img src="/gambar/logo.png" alt="Logo At-Taubah" class="navbar-logo">
            <div class="navbar-text">
                <div class="navbar-title">Tahfizd Tracker</div>
                <div class="navbar-subtitle">At-Taubah Karawang</div>
            </div>
        </div>
    </header>

    <!-- MAIN FORM CARD AREA -->
    <main class="content-area">
        <div class="login-card">
            
            <img src="/gambar/logo.png" alt="Logo At-Taubah" class="card-logo">

            <div class="card-header-texts">
                <h1 class="card-title">Selamat datang</h1>
                <p class="card-subtitle">Masuk untuk melihat perkembangan hafalan Anda</p>
            </div>

            @if($errors->has('login') || $errors->any())
                <div class="alert-error">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span>{{ $errors->first('login') ?? $errors->first() }}</span>
                </div>
            @endif

            <!-- Form Login Murid -->
            <form action="{{ route('login.post') }}" method="POST" style="width: 100%; display: flex; flex-direction: column; align-items: center; gap: 20px;">
                @csrf

                <!-- Field 1: Email (461 x ~70) -->
                <div class="form-group">
                    <label for="email" class="form-label">Email  :</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        class="form-input" 
                        placeholder="bilalhakim@gmail.com" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="email"
                    >
                </div>

                <!-- Field 2: Password (461 x ~70 dengan Eye Icon Toggle) -->
                <div class="form-group">
                    <label for="password" class="form-label">Password :</label>
                    <div class="password-container">
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="password-input" 
                            placeholder="••••••••" 
                            required
                        >
                        <img 
                            src="/icon/icon_mata_disable.png" 
                            alt="Toggle Password" 
                            id="togglePasswordIcon" 
                            class="toggle-password" 
                            title="Lihat Password"
                            onclick="togglePasswordVisibility()"
                        >
                    </div>
                </div>

                <!-- Field 3: NISN (461 x ~70) -->
                <div class="form-group">
                    <label for="nip" class="form-label">NISN :</label>
                    <input 
                        type="text" 
                        name="nip" 
                        id="nip" 
                        class="form-input" 
                        placeholder="1234567890" 
                        value="{{ old('nip') }}" 
                        required
                    >
                </div>

                <!-- Button Masuk (461 x 44, Background #0E7651) -->
                <button type="submit" class="btn-submit">
                    Masuk
                </button>
            </form>

        </div>
    </main>

    <!-- FOOTER DECORATIVE SHAPE (Full Width 100%) -->
    <div class="footer-shape"></div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('togglePasswordIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.src = '/icon/akar_mata_open.png';
                eyeIcon.title = 'Sembunyikan Password';
            } else {
                passwordInput.type = 'password';
                eyeIcon.src = '/icon/icon_mata_disable.png';
                eyeIcon.title = 'Lihat Password';
            }
        }
    </script>
</body>
</html>
