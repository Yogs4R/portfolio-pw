<?php
// ==========================================================================
// Halaman Kelola Proyek (CRUD Lengkap) - Praktikum 3 Pemrograman Web
// Mendukung Create, Read, Update, Delete dengan $_REQUEST & Upload/Ganti Gambar
// ==========================================================================

session_start();
require_once "../config/koneksi.php";

// 1. Proteksi Halaman: Hanya dapat diakses jika session login aktif
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

$success_msg  = '';
$error_msg    = '';
$is_edit_mode = false;
$edit_data    = [
    'id'          => '',
    'title'       => '',
    'description' => '',
    'image'       => 'images/project1.jpeg',
    'link'        => '#'
];

// 2. Operasi DELETE: Menghapus data proyek berdasarkan parameter GET id
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = (int)$_GET['id'];
    
    // Cegah query jika ID tidak valid
    if ($delete_id > 0) {
        $del_query = "DELETE FROM projects WHERE id = $delete_id";
        if (mysqli_query($conn, $del_query)) {
            $success_msg = "Proyek berhasil dihapus dari database!";
        } else {
            $error_msg = "Gagal menghapus proyek: " . mysqli_error($conn);
        }
    }
}

// 3. Operasi PREPARE EDIT: Mengambil data lama proyek yang akan diedit
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $edit_id = (int)$_GET['id'];
    $find_res = mysqli_query($conn, "SELECT * FROM projects WHERE id = $edit_id LIMIT 1");
    if ($find_res && mysqli_num_rows($find_res) === 1) {
        $edit_data    = mysqli_fetch_assoc($find_res);
        $is_edit_mode = true;
    } else {
        $error_msg = "Data proyek untuk diedit tidak ditemukan.";
    }
}

// 4. Operasi SIMPAN (CREATE & UPDATE): Dipicu saat form dikirimkan (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id  = isset($_POST['project_id']) && !empty($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
    
    // Menangkap input data menggunakan variabel superglobal $_REQUEST sesuai kriteria Praktikum
    $title       = isset($_REQUEST['title']) ? mysqli_real_escape_string($conn, trim($_REQUEST['title'])) : '';
    $description = isset($_REQUEST['description']) ? mysqli_real_escape_string($conn, trim($_REQUEST['description'])) : '';
    $link        = isset($_REQUEST['link']) && !empty(trim($_REQUEST['link'])) 
                   ? mysqli_real_escape_string($conn, trim($_REQUEST['link'])) 
                   : '#';
    $custom_url  = isset($_REQUEST['custom_image_url']) ? trim($_REQUEST['custom_image_url']) : '';

    // Validasi sederhana
    if (empty($title) || empty($description)) {
        $error_msg = "Judul proyek dan deskripsi wajib diisi!";
    } else {
        $image = '';

        // Prioritas 1: Jika pengguna mengunggah file gambar baru dari komputer
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES['image_file']['tmp_name'];
            $file_name = $_FILES['image_file']['name'];
            $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed   = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($file_ext, $allowed)) {
                $new_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file_name);
                $destination  = '../images/' . $new_filename;

                if (move_uploaded_file($file_tmp, $destination)) {
                    $image = 'images/' . $new_filename;
                } else {
                    $error_msg = "Gagal mengunggah file gambar ke folder server.";
                }
            } else {
                $error_msg = "Format gambar tidak didukung. Harap gunakan format JPG, PNG, GIF, atau WEBP.";
            }
        }

        // Prioritas 2: Jika tidak upload file, cek URL gambar custom
        if (empty($image) && !empty($custom_url)) {
            $image = mysqli_real_escape_string($conn, $custom_url);
        }

        // Prioritas 3: Pilihan aset gambar bawaan atau pertahankan gambar sebelumnya
        if (empty($image)) {
            if (!empty($_REQUEST['existing_image'])) {
                $image = mysqli_real_escape_string($conn, $_REQUEST['existing_image']);
            } elseif ($project_id > 0 && isset($_POST['current_image'])) {
                // Pertahankan gambar lama saat update jika tidak ada perubahan
                $image = mysqli_real_escape_string($conn, $_POST['current_image']);
            } else {
                $image = 'images/project1.jpeg';
            }
        }

        // Jalankan Query ke Database jika tidak ada error
        if (empty($error_msg)) {
            if ($project_id > 0) {
                // UPDATE: Memperbarui data proyek yang sudah ada
                $update_query = "UPDATE projects SET 
                                    title = '$title', 
                                    description = '$description', 
                                    image = '$image', 
                                    link = '$link' 
                                 WHERE id = $project_id";

                if (mysqli_query($conn, $update_query)) {
                    $success_msg  = "Proyek \"$title\" berhasil diperbarui!";
                    $is_edit_mode = false; // Kembalikan ke mode create
                } else {
                    $error_msg = "Gagal memperbarui proyek: " . mysqli_error($conn);
                }
            } else {
                // CREATE: Menyimpan data proyek baru
                $insert_query = "INSERT INTO projects (title, description, image, link) 
                                 VALUES ('$title', '$description', '$image', '$link')";

                if (mysqli_query($conn, $insert_query)) {
                    $success_msg = "Proyek baru \"$title\" berhasil ditambahkan!";
                } else {
                    $error_msg = "Gagal menyimpan proyek: " . mysqli_error($conn);
                }
            }
        }
    }
}

// 5. READ: Mengambil seluruh daftar proyek terbaru untuk tabel di bawah
$projects_query = "SELECT * FROM projects ORDER BY id DESC";
$projects_result = mysqli_query($conn, $projects_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit_mode ? 'Edit Proyek' : 'Kelola Proyek'; ?> | Praktikum 3</title>
    <link rel="stylesheet" href="../style.css?v=<?php echo filemtime('../style.css'); ?>">
</head>
<body>

    <!-- Container Dialog Utama Form Input Retro -->
    <div class="auth-wrapper admin-layout">
        <div class="auth-window admin-window">
            <div class="window-titlebar">
                <span class="window-title">
                    ProjectManager.exe - <?php echo $is_edit_mode ? 'Edit Proyek (Update Mode)' : 'Kelola Data Proyek (CRUD)'; ?>
                </span>
                <span class="window-user">Admin: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>

            <div class="auth-body">
                <!-- Bar Navigasi Admin -->
                <div class="admin-topbar">
                    <div>
                        <strong>Praktikum 3:</strong> Operasi Lengkap CRUD via <code>$_REQUEST</code> & MySQLi
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

                <!-- Form Input / Edit Proyek (Mendukung File Upload multipart/form-data) -->
                <form action="input-project.php" method="POST" enctype="multipart/form-data" class="retro-form">
                    <!-- Hidden ID untuk membedakan Mode CREATE atau UPDATE -->
                    <input type="hidden" name="project_id" value="<?php echo $is_edit_mode ? htmlspecialchars($edit_data['id']) : ''; ?>">
                    <input type="hidden" name="current_image" value="<?php echo $is_edit_mode ? htmlspecialchars($edit_data['image']) : ''; ?>">

                    <div class="form-group">
                        <label for="title">Judul Proyek / Karya:</label>
                        <input type="text" id="title" name="title" class="retro-input" 
                               placeholder="Contoh: Sistem Informasi Kasir Web" 
                               value="<?php echo $is_edit_mode ? htmlspecialchars($edit_data['title']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi Proyek:</label>
                        <textarea id="description" name="description" class="retro-input retro-textarea" rows="3" 
                                  placeholder="Jelaskan ringkasan proyek, teknologi yang digunakan, serta fungsinya..." required><?php echo $is_edit_mode ? htmlspecialchars($edit_data['description']) : ''; ?></textarea>
                    </div>

                    <!-- Sistem Pengelolaan Gambar Proyek (Upload / Template / Ganti) -->
                    <div class="form-group">
                        <label>Thumbnail / Gambar Proyek:</label>
                        
                        <?php if ($is_edit_mode): ?>
                            <div class="current-img-preview" style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px; padding: 6px 10px; background-color: #e0e0e0; border: 1px solid #808080;">
                                <img src="../<?php echo htmlspecialchars($edit_data['image']); ?>" 
                                     alt="Current Thumbnail"
                                     style="width: 80px; height: 55px; max-width: 80px; max-height: 55px; object-fit: cover; border: 1px solid #000000; flex-shrink: 0;"
                                     onerror="this.src='../images/project1.jpeg';">
                                <div>
                                    <p style="margin-bottom: 4px;"><strong>Gambar Saat Ini:</strong> <code><?php echo htmlspecialchars($edit_data['image']); ?></code></p>
                                    <small class="form-hint">Kosongkan upload jika tidak ingin mengganti gambar yang sudah ada.</small>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Opsi 1: Upload File Gambar Baru -->
                        <div style="margin-bottom: 8px;">
                            <label for="image_file" style="font-weight: normal;">
                                <strong>Opsi 1:</strong> Upload file gambar baru dari komputer:
                            </label>
                            <input type="file" id="image_file" name="image_file" accept="image/*" class="retro-input" style="padding: 3px;">
                        </div>

                        <!-- Opsi 2: Pilih Aset Template -->
                        <div>
                            <label for="existing_image" style="font-weight: normal;">
                                <strong>Opsi 2:</strong> Atau pilih dari aset template gambar:
                            </label>
                            <select id="existing_image" name="existing_image" class="retro-input">
                                <option value="">-- Gunakan Pilihan / Tetap Gambar Saat Ini --</option>
                                <option value="images/project1.jpeg" <?php echo ($edit_data['image'] === 'images/project1.jpeg') ? 'selected' : ''; ?>>
                                    images/project1.jpeg (Thumbnail Default 1)
                                </option>
                                <option value="images/project2.jpeg" <?php echo ($edit_data['image'] === 'images/project2.jpeg') ? 'selected' : ''; ?>>
                                    images/project2.jpeg (Thumbnail Default 2)
                                </option>
                                <option value="images/profile.jpg" <?php echo ($edit_data['image'] === 'images/profile.jpg') ? 'selected' : ''; ?>>
                                    images/profile.jpg (Foto Profil)
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="link">Tautan / URL Proyek:</label>
                        <input type="text" id="link" name="link" class="retro-input" 
                               placeholder="https://... atau #" 
                               value="<?php echo $is_edit_mode ? htmlspecialchars($edit_data['link']) : '#'; ?>">
                        <small class="form-hint">Masukkan URL tujuan (misal: https://research.fuenzer.web.id).</small>
                    </div>

                    <div class="form-actions">
                        <?php if ($is_edit_mode): ?>
                            <button type="submit" class="btn-action">Simpan Perubahan (Update)</button>
                            <a href="input-project.php" class="btn-action">Batal Edit</a>
                        <?php else: ?>
                            <button type="submit" class="btn-action">Simpan Proyek</button>
                            <button type="reset" class="btn-action">Reset Form</button>
                        <?php endif; ?>
                    </div>
                </form>

                <!-- Daftar Proyek yang Ada di Database (Tabel CRUD) -->
                <div class="section-titlebar" style="margin-top: 25px; margin-bottom: 12px;">
                    Daftar Proyek di Database Saat Ini
                </div>
                <div class="retro-table-wrapper">
                    <table class="retro-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 35px; text-align: center;">No</th>
                                <th style="width: 70px; text-align: center;">Foto</th>
                                <th style="width: 160px;">Judul Proyek</th>
                                <th>Deskripsi</th>
                                <th style="width: 150px;">Tautan</th>
                                <th style="width: 120px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($projects_result && mysqli_num_rows($projects_result) > 0): ?>
                                <?php $no = 1; while ($row = mysqli_fetch_assoc($projects_result)): ?>
                                    <tr>
                                        <td style="text-align: center; vertical-align: middle;"><?php echo $no++; ?></td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <img src="../<?php echo htmlspecialchars($row['image']); ?>" 
                                                 alt="Thumbnail" 
                                                 class="table-thumbnail"
                                                 style="width: 50px; height: 35px; max-width: 50px; max-height: 35px; object-fit: cover; border: 1px solid #000000; display: block; margin: 0 auto;"
                                                 onerror="this.src='../images/project1.jpeg';">
                                        </td>
                                        <td style="vertical-align: middle;"><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                                        <td style="word-break: break-word; vertical-align: middle;"><?php echo htmlspecialchars($row['description']); ?></td>
                                        <td style="word-break: break-all; vertical-align: middle;">
                                            <a href="<?php echo htmlspecialchars($row['link']); ?>" target="_blank" rel="noopener noreferrer" style="color: #000080;">
                                                <small><?php echo htmlspecialchars($row['link']); ?></small>
                                            </a>
                                        </td>
                                        <td style="text-align: center; vertical-align: middle; white-space: nowrap;">
                                            <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                                <a href="input-project.php?action=edit&id=<?php echo $row['id']; ?>" class="btn-action btn-sm">Edit</a>
                                                <a href="input-project.php?action=delete&id=<?php echo $row['id']; ?>" 
                                                   class="btn-action btn-sm btn-danger" 
                                                   onclick="return confirm('Apakah Anda yakin ingin menghapus proyek ini?');">Hapus</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">Belum ada data proyek di database.</td>
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
