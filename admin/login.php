<?php

// Halaman Login - Praktikum 3 Pemrograman Web
// Menggunakan konsep Cookie & Session

session_start();
require_once "../config/koneksi.php";

// Jika user sudah memiliki session login aktif, langsung arahkan ke form input proyek
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: input-project.php");
    exit;
}

// Cek apakah ada cookie "remember_user" untuk mengisi otomatis input username
$saved_username = isset($_COOKIE['remember_user']) ? $_COOKIE['remember_user'] : '';
$error = '';

// Proses saat tombol login ditekan (metode POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']);

    if (empty($username) || empty($password)) {
        $error = "Silakan masukkan username dan password.";
    } else {
        // Cari akun pengguna di tabel users
        $query  = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            // Verifikasi password (mendukung password_hash dan fallback teks biasa)
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                // SET SESSION: Menyimpan status otentikasi pengguna
                $_SESSION['login'] = true;
                $_SESSION['username'] = $user['username'];

                // SET COOKIE: Menyimpan username jika checkbox "Ingat Saya" dicentang
                if ($remember) {
                    // Cookie berlaku selama 7 hari (86400 * 7 detik)
                    setcookie('remember_user', $user['username'], time() + (86400 * 7), '/');
                } else {
                    // Hapus cookie jika checkbox tidak dicentang
                    if (isset($_COOKIE['remember_user'])) {
                        setcookie('remember_user', '', time() - 3600, '/');
                    }
                }

                // Arahkan ke laman form input proyek setelah berhasil login
                header("Location: input-project.php");
                exit;
            } else {
                $error = "Password yang dimasukkan salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pengelola Portofolio | Praktikum 3</title>
    <link rel="stylesheet" href="../style.css?v=<?php echo filemtime('../style.css'); ?>">
</head>
<body>

    <!-- Container Dialog Login Retro -->
    <div class="auth-wrapper">
        <div class="auth-window">
            <div class="window-titlebar">
                <span class="window-title">Login.exe - Sistem Otentikasi Portofolio</span>
            </div>

            <div class="auth-body">
                <div class="auth-header-note">
                    <p><strong>Praktikum 3:</strong> Otentikasi menggunakan Session & Cookie.</p>
                </div>

                <!-- Notifikasi Pesan Error jika login gagal -->
                <?php if (!empty($error)): ?>
                    <div class="alert-retro alert-danger">
                        <strong>Perhatian:</strong> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST" class="retro-form">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" class="retro-input" 
                               value="<?php echo htmlspecialchars($saved_username); ?>" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" class="retro-input" required>
                    </div>

                    <div class="form-group form-checkbox-group">
                        <input type="checkbox" id="remember" name="remember" class="retro-checkbox" 
                               <?php echo !empty($saved_username) ? 'checked' : ''; ?>>
                        <label for="remember">Ingat Saya (Simpan Username via Cookie)</label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-action">Masuk / Login</button>
                        <a href="../index.php" class="btn-action">Batal & Kembali</a>
                    </div>
                </form>

                <div class="auth-footer-help">
                    <p><small>Akun default: <code>admin</code> / <code>admin123</code></small></p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
