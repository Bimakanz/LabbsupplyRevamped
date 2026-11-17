# LabSupply Rebuild (Laravel + Tailwind CSS)

**LabSupply (labsupply.id)** adalah toko online milik **Lichem** yang menjual bahan kimia, alat, dan perlengkapan laboratorium — mulai dari glassware, consumables, alat ukur (measurement), PPE & safety, hingga peralatan mikrobiologi, sterilisasi, dan gas supply — untuk kebutuhan laboratorium, institusi pendidikan, dan industri.

Situs saat ini dibangun dengan **WordPress** (WooCommerce + Elementor). Project ini membangun ulang LabSupply menggunakan **Laravel + Tailwind CSS** dengan tujuan:

- Performa yang lebih baik (loading lebih cepat, lebih ringan dibanding stack WordPress/WooCommerce/Elementor)
- Kode yang lebih mudah dikembangkan dan dirawat (struktur MVC yang jelas, tanpa ketergantungan plugin pihak ketiga)
- Fitur yang disesuaikan dengan kebutuhan bisnis (katalog produk, kategori lab yang spesifik, request quote, instalasi & training, service alat lab)

## Website

🔗 https://labsupply.id/

## Tech Stack

- **Backend:** Laravel

## Fitur Utama

- Katalog produk per kategori (Chemistry, Glassware, Measurement, PPE, Safety, Microbiology, Sterilization, Gas Supply, dll.)
- Request Penawaran Harga (quote request)
- Diskusi Produk
- Instalasi & Training
- Service Alat Lab
- Keranjang & Checkout
- Wishlist & Compare produk

## Admin Panel (CMS)

Dashboard admin untuk mengelola seluruh konten website tanpa perlu akses ke kode, meliputi:

- **Manajemen Produk** — tambah, edit, hapus produk; atur harga, stok, gambar, dan kategori (Chemistry, Glassware, PPE, Safety, dll.)
- **Manajemen Kategori** — tambah/edit kategori & sub-kategori produk
- **Manajemen Halaman/Konten** — edit konten halaman statis (Tentang Kami, Cara Order, Privacy Policy, Terms & Conditions, Disclaimer, dll.)
- **Manajemen Banner/Homepage** — atur banner, produk unggulan ("New Products"), dan tampilan halaman utama
- **Manajemen Pesanan (Orders)** — lihat & update status pesanan/checkout
- **Manajemen Request Quote & Diskusi Produk** — kelola pengajuan penawaran harga dan pertanyaan dari pelanggan
- **Manajemen Layanan** — atur konten untuk Instalasi & Training serta Service Alat Lab
- **Manajemen User/Role** — atur akun admin dan hak akses (role-based access, misal superadmin, staff produk, staff order)
- **Manajemen Media** — upload & kelola gambar/file produk
- **SEO Settings** — atur meta title, meta description per halaman/produk (menggantikan fungsi plugin SEO di WordPress)

> Catatan: fitur-fitur di atas menggantikan fungsi admin dashboard WordPress + WooCommerce yang selama ini dipakai, namun dibangun native di Laravel agar lebih ringan dan sesuai kebutuhan LabSupply.
