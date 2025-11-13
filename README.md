# 🏢 Sistem Manajemen Aula YPI Al Azhar

Aplikasi web untuk mengelola pemesanan aula dengan sistem multi-cabang, multi-role, dan pembayaran bertahap.

## 🎯 Fitur Utama

- **Multi-Role**: Super Admin, Admin Cabang, Pimpinan, dan User/Customer
- **Multi-Cabang**: Kelola beberapa cabang dalam satu sistem
- **Booking Aula**: Pilih jadwal, jenis acara, dan catering
- **Pembayaran Bertahap**: DP (30%), Termin 1-4, dan Pelunasan
- **Dashboard & Laporan**: Analytics real-time dan laporan per cabang/global
- **Responsive Design**: Dapat diakses dari desktop dan mobile

## 🛠️ Teknologi

- Laravel 12.x + PHP 8.2+
- MySQL 8.0+
- Tailwind CSS + Alpine.js
- Chart.js

## 📦 Instalasi
```bash
# Clone repository
git clone <repository-url>
cd sistem-manajemen-aula

# Install dependencies
composer install
npm install && npm run build

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database (sesuaikan .env)
php artisan migrate --seed
php artisan storage:link

# Run aplikasi
php artisan serve
```

Akses: `http://localhost:8000`

## 👤 Default Login

| Role | Email | Password |
|------|-------|----------|
| Super Admin | superadmin@aula.com | password |
| Admin Jakarta | admin.jakarta@aula.com | password |
| Pimpinan Jakarta | pimpinan.jakarta@aula.com | password |
| User | user@example.com | password |

## 📋 Role & Akses

| Role | Akses |
|------|-------|
| **Super Admin** | Kelola semua cabang, master data, laporan global |
| **Admin Cabang** | Kelola jadwal, booking, dan pembayaran per cabang |
| **Pimpinan** | Lihat dashboard dan laporan per cabang (read-only) |
| **User** | Booking aula, upload bukti bayar, tracking status |

## 🔄 Alur Booking
```
User Booking → Admin Approve → User Upload DP 30% → 
Admin Validasi → User Upload Termin/Pelunasan → 
Admin Validasi → Booking Selesai
```

## 📊 Database Utama

- `roles` - Role sistem
- `cabangs` - Data cabang
- `users` - Data pengguna
- `jenis_acaras` - Jenis acara
- `sesis` - Waktu sesi
- `buka_jadwals` - Jadwal tersedia
- `transaksi_bookings` - Data booking
- `transaksi_pembayarans` - Data pembayaran

## 🔧 Troubleshooting
```bash
# Clear cache
php artisan optimize:clear

# Fix storage link
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

## 📝 License

MIT License © 2025

---

**Dikembangkan oleh Donar Azhar IG https://www.instagram.com/donsiyos dengan ❤️ menggunakan Laravel**