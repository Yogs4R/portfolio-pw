<?php
// ==========================================================================
// Halaman Input Proyek Portofolio - Praktikum 3 Pemrograman Web
// Menangkap data input dengan variabel superglobal $_REQUEST
// dan menyimpannya ke MySQL via mysqli
// ==========================================================================

session_start();
require_once "../config/koneksi.php";

// Proteksi Halaman: Hanya dapat diakses jika session login aktif
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

$success_msg = '';
$error_msg   = '';

// Memproses data formulir saat dikirimkan (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Menggunakan variabel superglobal $_REQUEST sesuai kriteria Praktikum 3
    // mysqli_real_escape_string digunakan untuk keamanan dasar terhadap SQL injection
    $title       = isset($_REQUEST['title']) ? mysqli_real_escape_string($conn, trim($_REQUEST['title'])) : '';
    $description = isset($_REQUEST['description']) ? mysqli_real_escape_string($conn, trim($_REQUEST['description'])) : '';
    $image       = isset($_REQUEST['image']) && !empty(trim($_REQUEST['image'])) 
                   ? mysqli_real_escape_string($conn, trim($_REQUEST['image'])) 
                   : 'images/project1.jpeg';
    $link        = isset($_REQUEST['link']) && !empty(trim($_REQUEST['link'])) 
                   ? mysqli_real_escape_string($conn, trim($_REQUEST['link'])) 
                   : '#';

    // Validasi sederhana
    if (empty($title) || empty($description)) {
        $error_msg = "Judul proyek dan deskripsi wajib diisi!";
    } else {
        // Query INSERT ke tabel projects
        $query = "INSERT INTO projects (title, description, image, link) 
                  VALUES ('$title', '$description', '$image', '$link')";

        if (mysqli_query($conn, $query)) {
            $success_msg = "Proyek baru berhasil disimpan ke database!";
        } else {
            $error_msg = "Gagal menyimpan proyek: " . mysqli_error($conn);
        }
    }
}

// Mengambil daftar proyek yang tersimpan di database untuk ditampilkan
$projects_query = "SELECT * FROM projects ORDER BY id DESC";
$projects_result = mysqli_query($conn, $projects_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Proyek Portofolio | Praktikum 3</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <!-- Container Dialog Utama Form Input Retro -->
    <div class="auth-wrapper admin-layout">
        <div class="auth-window admin-window">
            <div class="window-titlebar">
                <span class="window-title">ProjectManager.exe - Tambah Proyek Portofolio</span>
                <span class="window-user">User: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>

            <div class="auth-body">
                <!-- Bar Navigasi Admin -->
                <div class="admin-topbar">
                    <div>
                        <strong>Praktikum 3:</strong> Input Data Karya/Proyek via <code>$_REQUEST</code>
                    </div>
                    <div class="admin-nav-buttons">
                        <a href="../index.php" class="btn-action">Lihat Website Portofolio</a>
                        <a href="logout.php" class="btn-action" onclick="return confirm('Apakah Anda yakin ingin logout?');">Logout</a>
                    </div>
                </div>

                <!-- Notifikasi Status -->
                <?php if (!empty($success_msg)): ?>
                    <div class="alert-retro alert-success">
                        <strong>Sukses:</strong> <?php echo htmlspecialchars($success_msg); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_msg)): ?>
                    <div class="alert-retro alert-danger">
                        <strong>Perhatian:</strong> <?php echo htmlspecialchars($error_msg); ?>
                    </div>
                <?php endif; ?>

                <!-- Form Input Proyek -->
                <form action="input-project.php" method="POST" class="retro-form">
                    <div class="form-group">
                        <label for="title">Judul Proyek / Karya:</label>
                        <input type="text" id="title" name="title" class="retro-input" 
                               placeholder="Contoh: Sistem Informasi Kasir Web" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi Proyek:</label>
                        <textarea id="description" name="description" class="retro-input retro-textarea" rows="4" 
                                  placeholder="Jelaskan ringkasan proyek, teknologi yang digunakan, serta fungsinya..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image">Path / Nama File Thumbnail:</label>
                        <select id="image" name="image" class="retro-input">
                            <option value="images/project1.jpeg">images/project1.jpeg (Thumbnail Default 1)</option>
                            <option value="images/project2.jpeg">images/project2.jpeg (Thumbnail Default 2)</option>
                            <option value="images/profile.jpg">images/profile.jpg (Foto Profil)</option>
                        </select>
                        <small class="form-hint">Dapat memilih aset gambar yang telah tersedia di folder images.</small>
                    </div>

                    <div class="form-group">
                        <label for="link">Tautan / URL Proyek (Opsional):</label>
                        <input type="text" id="link" name="link" class="retro-input" 
                               placeholder="https://github.com/... atau #" value="#">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-action">Simpan Proyek</button>
                        <button type="reset" class="btn-action">Reset Form</button>
                    </div>
                </form>

                <!-- Daftar Proyek yang Ada di Database -->
                <div class="section-titlebar" style="margin-top: 25px; margin-bottom: 12px;">
                    Daftar Proyek di Database Saat Ini
                </div>
                <div class="retro-table-wrapper">
                    <table class="retro-table">
                        <thead>
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Judul Proyek</th>
                                <th>Deskripsi</th>
                                <th style="width: 120px;">Thumbnail</th>
                                <th style="width: 140px;">Waktu Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($projects_result && mysqli_num_rows($projects_result) > 0): ?>
                                <?php $no = 1; while ($row = mysqli_fetch_assoc($projects_result)): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                                        <td>
                                            <code><?php echo htmlspecialchars($row['image']); ?></code>
                                        </td>
                                        <td><small><?php echo htmlspecialchars($row['created_at']); ?></small></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center;">Belum ada data proyek di database.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</body>
</html>
