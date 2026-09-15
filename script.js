// ==========================================================================
// Step 6: JavaScript Interaktif Sederhana
// Prinsip: Ringkas, bersih, tanpa library eksternal, mudah dipahami untuk UTS.
// ==========================================================================

document.addEventListener("DOMContentLoaded", function () {
    
    // 1. Fitur Scroll Halus (Smooth Scroll) untuk Menu Navigasi
    // Membaca semua tautan menu yang mengarah ke ID section (diawali tanda #)
    const navLinks = document.querySelectorAll('a[href^="#"]');

    navLinks.forEach(function (link) {
        link.addEventListener("click", function (event) {
            const targetId = this.getAttribute("href");
            
            // Hanya proses jika targetId bukan hanya "#"
            if (targetId.length > 1) {
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    event.preventDefault(); // Mencegah loncatan instan bawaan browser
                    
                    // Gulir halaman secara mulus ke posisi elemen yang dituju
                    targetElement.scrollIntoView({
                        behavior: "smooth",
                        block: "start"
                    });
                }
            }
        });
    });

    // 2. Interaktivitas Tombol "Kirim Pesan" pada Sidebar
    const contactBtn = document.getElementById("btn-send-message") || document.querySelector("#kontak .btn-action");
    if (contactBtn) {
        contactBtn.addEventListener("click", function (event) {
            event.preventDefault();
            // Menampilkan dialog pesan sederhana
            alert("Terima kasih telah berkunjung. Fitur pengiriman pesan langsung akan segera hadir!");
        });
    }

    // 3. Interaktivitas Tombol "Lihat Detail" pada Kartu Proyek
    const projectButtons = document.querySelectorAll(".project-info .btn-action");
    projectButtons.forEach(function (btn) {
        btn.addEventListener("click", function (event) {
            event.preventDefault();
            // Mengambil judul proyek dari kartu yang diklik
            const cardTitle = this.closest(".project-info").querySelector("h3").textContent;
            alert("Informasi detail untuk \"" + cardTitle + "\" sedang dalam tahap penyusunan dokumentasi.");
        });
    });

});
