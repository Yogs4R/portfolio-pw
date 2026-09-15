<?php
// ==========================================================================
// Portofolio Dinamis - Praktikum 3 Pemrograman Web
// Mengambil data proyek dari MySQL menggunakan mysqli
// ==========================================================================

session_start();
require_once "config/koneksi.php";

// Mengambil seluruh data proyek dari database, diurutkan dari yang terbaru
$query_projects = "SELECT * FROM projects ORDER BY id DESC";
$result_projects = mysqli_query($conn, $query_projects);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portofolio Personal | Praktikum 3</title>
    <!-- File CSS eksternal -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Header -->
    <header class="site-header">
        <div class="window-titlebar">
            <span class="window-title">Portfolio.exe - Ridwan Yoga Suryantara</span>
            <span class="window-status">
                <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
                    [Mode Admin: <?php echo htmlspecialchars($_SESSION['username']); ?>]
                <?php endif; ?>
            </span>
        </div>
        <div class="header-container header-gif-container">
            <h1 class="sr-only">Portofolio Personal - Ridwan Yoga Suryantara</h1>
            <img src="images/gif/spinning-computer.gif" alt="Spinning Computer" class="retro-header-gif computer-gif">
            <img src="images/gif/welcome.gif" alt="Welcome to my Homepage" class="retro-header-gif welcome-gif">
            <img src="images/gif/spinning-computer.gif" alt="Spinning Computer" class="retro-header-gif computer-gif">
        </div>
        <!-- Running text / Marquee -->
        <div class="running-text-wrapper">
            <div class="running-text-content">
                Selamat datang di website portofolio praktikum pemrograman web | Terbuka untuk kolaborasi proyek dan eksplorasi teknologi terkini.
            </div>
        </div>
    </header>

    <!-- Layout Utama -->
    <div class="main-layout">

        <!-- Sidebar -->
        <aside class="site-aside">

            <!-- Navigasi Menu Utama di dalam Sidebar -->
            <nav class="aside-widget">
                <div class="widget-header">Navigation</div>
                <ul class="aside-nav-menu">
                    <li><a href="#profil">About Me</a></li>
                    <li><a href="#portofolio">Projects</a></li>
                    <li><a href="#keahlian">Skills</a></li>
                    <li><a href="#kontak">Contact</a></li>
                </ul>
            </nav>

            <!-- Panel Pengelola / Admin Praktikum 3 -->
            <div class="aside-widget">
                <div class="widget-header">Kelola Portofolio</div>
                <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
                    <p style="margin-bottom: 8px;">Halo, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</p>
                    <a href="admin/input-project.php" class="btn-action full-width" style="margin-bottom: 6px;">+ Tambah Proyek</a>
                    <a href="admin/logout.php" class="btn-action full-width" onclick="return confirm('Yakin ingin logout?');">Logout</a>
                <?php else: ?>
                    <p style="margin-bottom: 8px; font-size: 12px;">Khusus pengelola untuk input data karya baru via form:</p>
                    <a href="admin/login.php" class="btn-action full-width">Login Admin</a>
                <?php endif; ?>
            </div>

            <!-- Bagian Keahlian Teknis -->
            <div id="keahlian" class="aside-widget">
                <div class="widget-header">Technical Skills</div>
                <ul class="skills-list">
                    <li>HTML5 & Semantic Web</li>
                    <li>CSS3 (Retro & Beveled UI)</li>
                    <li>JavaScript Dasar</li>
                    <li>PHP & MySQL (mysqli)</li>
                    <li>Git & Version Control</li>
                </ul>
            </div>

            <!-- Bagian Kontak Singkat -->
            <div id="kontak" class="aside-widget">
                <div class="widget-header">Contact Me</div>
                <p>Email: fuenzerofficial@gmail.com</p>
                <p>Location: Semarang, Indonesia</p>
                <a href="#kontak" class="btn-action full-width" id="btn-send-message">Send Message</a>
            </div>

        </aside>

        <!-- Main Content -->
        <main class="site-main">

            <!-- Konten Utama (Profil & Bio) -->
            <article id="profil" class="content-section">
                <div class="section-titlebar">About Me</div>
                <div class="profile-layout">
                    <figure class="profile-avatar">
                        <img src="images/profile.jpg" alt="Foto Profil">
                    </figure>
                    <div class="profile-card">
                        <p>
                            Halo, saya adalah mahasiswa Sistem Informasi yang tertarik pada bidang pengembangan web front-end dan komputasi modern. Saya juga tertarik pada bidang komputasi awan untuk mempelajari layanan-layanan di Google Cloud, AWS Cloud, Microsoft Azure, dll. Halaman ini dirancang sebagai wadah portofolio pengenalan diri serta dokumentasi hasil belajar di mata kuliah Pemrograman Web.
                        </p>
                        <div class="badge-group">
                            <span class="badge">Status: Mahasiswa Aktif</span>
                            <span class="badge">Fokus: Web Development & Cloud Computing</span>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Konten Proyek / Portofolio Dinamis dari MySQL -->
            <section id="portofolio" class="content-section">
                <div class="section-titlebar">Portofolio Proyek (Dinamis via MySQL)</div>
                <p class="section-desc">Berikut adalah beberapa proyek latihan dan karya yang telah dikerjakan (data diambil langsung dari database):</p>
                
                <!-- Grid Thumbnail Proyek -->
                <div class="portfolio-grid">
                    <?php if ($result_projects && mysqli_num_rows($result_projects) > 0): ?>
                        <?php while ($project = mysqli_fetch_assoc($result_projects)): ?>
                            <article class="project-card">
                                <figure class="thumbnail">
                                    <img src="<?php echo htmlspecialchars($project['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($project['title']); ?>"
                                         onerror="this.src='images/project1.jpeg';">
                                </figure>
                                <div class="project-info">
                                    <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($project['description']); ?></p>
                                    <a href="<?php echo htmlspecialchars($project['link']); ?>" class="btn-action">Lihat Detail</a>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <!-- Fallback jika belum ada data atau koneksi bermasalah -->
                        <div style="grid-column: 1 / -1; padding: 20px; background: #ffffff; border: 1px solid #808080;">
                            <p>Belum ada proyek yang ditampilkan. Silakan login dan input data proyek terlebih dahulu.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

        </main>

    </div>

    <!-- FOOTER -->
    <footer class="site-footer">
        <p>© 2026 Portofolio Personal. All Rights Reserved.</p>
        <p class="footer-note">Dirancang untuk memenuhi tugas praktikum 3 di mata kuliah Pemrograman Web (PHP & MySQL)</p>
    </footer>

    <!-- JavaScript simple (dengan cache buster agar browser selalu memuat script terbaru) -->
    <script src="script.js?v=<?php echo filemtime('script.js'); ?>"></script>
</body>
</html>
