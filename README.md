# 🏢 Sistem Manajemen Booking Aula

Aplikasi web untuk mengelola pemesanan aula dengan fitur lengkap untuk admin dan user.

## 📋 Overview

Sistem manajemen booking aula yang memudahkan admin dalam mengelola jadwal, jenis acara, sesi waktu, dan konfirmasi booking dari user. User dapat melihat jadwal tersedia, membuat booking, dan melacak status pemesanan mereka.

## ✨ Fitur Utama

### 👤 **User**
- ✅ Dashboard dengan quick actions per jenis acara
- ✅ Filter jadwal (bulan, jenis acara, sesi) dengan auto-filter dari dashboard
- ✅ Booking aula dengan Terms & Conditions wajib
- ✅ Pilih catering (opsional)
- ✅ Tracking status booking (pending/active/inactive)
- ✅ Notifikasi expired booking (2 minggu)
- ✅ Profile management

### 🔐 **Admin**
- ✅ Dashboard dengan statistik lengkap
- ✅ Manajemen Master Data:
  - Jenis Acara
  - Sesi Waktu
  - Catering
  - User Management
- ✅ Buka/Tutup Jadwal Aula
- ✅ Konfirmasi Booking
- ✅ Update Status Pembayaran DP
- ✅ Filter & Search di semua halaman

## 🛠️ Tech Stack

- **Framework:** Laravel 10.x
- **Database:** MySQL
- **Frontend:** Blade Templates + Tailwind CSS
- **Icons:** Font Awesome
- **Authentication:** Laravel Breeze

## 📦 Installation

### Prerequisites
```
PHP >= 8.1
Composer
MySQL
Node.js & NPM
```

### Setup
```bash
# 1. Clone repository
git clone <repository-url>
cd sistem-booking-aula

# 2. Install dependencies
composer install
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Configure database di .env
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# 5. Run migrations
php artisan migrate

# 6. Seed data (optional)
php artisan db:seed

# 7. Build assets
npm run dev

# 8. Run server
php artisan serve
```

## 👥 User Roles

### Admin
- **Username:** admin@example.com
- **Password:** password
- **Akses:** Full control semua fitur

### User
- **Register:** Halaman register publik
- **Role:** Otomatis "user" saat register
- **Akses:** Booking & tracking

## 📱 User Flow

### Booking Flow
```
1. User login
2. Lihat dashboard → Klik jenis acara
3. Halaman jadwal (auto-filter)
4. Klik "Book Sekarang"
5. Baca Terms & Conditions → Checklist
6. Isi form booking (catering optional)
7. Submit → Status: Pending
8. Admin konfirmasi → Status: Active/Inactive
```

### Admin Flow
```
1. Admin login
2. Dashboard (lihat statistik)
3. Buka jadwal baru (pilih jenis, sesi, tanggal)
4. User booking masuk
5. Admin review & konfirmasi
6. Update status DP (jika sudah bayar)
7. Booking active!
```

## 📊 Database Schema

### Core Tables
- **users** - Admin & User accounts
- **jenis_acaras** - Jenis acara (Wedding, Seminar, dll)
- **sesis** - Sesi waktu (Pagi, Siang)
- **caterings** - Data catering
- **buka_jadwals** - Jadwal tersedia
- **bookings** - Data booking user

### Key Relationships
```
User 1:N Bookings
JenisAcara 1:N BukaJadwal
Sesi 1:N BukaJadwal
BukaJadwal 1:N Bookings
Catering 1:N Bookings (optional)
```

## 🎨 Fitur Khusus

### Auto-Filter dari Dashboard
User klik jenis acara di dashboard → Otomatis filter di halaman jadwal
```
Dashboard: Click "Wedding"
↓
Jadwal: Filter bulan=Nov, jenis=Wedding, sesi=Semua
```

### Terms & Conditions Modal
- Wajib baca & setuju sebelum booking
- 37 poin peraturan komprehensif
- Button disabled sampai checklist
- Responsive & mobile-friendly

### Booking Expiration
- Auto-inactive setelah 2 minggu jika tidak ada DP
- Notifikasi visual di dashboard
- Status otomatis berubah

### Smart Filtering
- Filter kombinasi: Bulan + Jenis Acara + Sesi
- Real-time counter jadwal
- URL parameter support

## 📁 Struktur Folder Penting

```
app/
├── Http/Controllers/
│   ├── Admin/          # Admin controllers
│   └── User/           # User controllers
├── Models/             # Eloquent models
resources/
├── views/
│   ├── admin/          # Admin views
│   ├── user/           # User views
│   └── layouts/        # Layout templates
routes/
└── web.php             # Route definitions
database/
├── migrations/         # Database migrations
└── seeders/            # Seeder files
```

## 🚀 Quick Commands

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Run migrations
php artisan migrate:fresh --seed

# Create admin manually
php artisan tinker
> User::create([
    'nama' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin'
  ]);
```

## 🔐 Default Credentials

Setelah seeding:
```
Admin:
Email: admin@example.com
Password: password

User Test:
Email: user@example.com  
Password: password
```

## 📝 Important Notes

1. **Terms & Conditions:** Konten T&C ada di JavaScript variable, bisa dipindah ke database untuk management lebih mudah
2. **File Uploads:** Belum ada fitur upload dokumen, bisa ditambahkan jika diperlukan
3. **Email Notifications:** Belum ada email notifikasi, bisa diintegrasikan dengan Laravel Mail
4. **Payment Gateway:** Belum ada integrasi payment, masih manual update DP oleh admin
5. **Multi-Aula:** Saat ini 1 sistem untuk 1 aula, bisa dikembangkan untuk multiple aula

## 🐛 Troubleshooting

### Modal tidak muncul
```bash
# Clear browser cache
# Clear Laravel cache
php artisan view:clear
# Check console for JS errors
```

### Form tidak submit
```bash
# Check CSRF token
# Check validation errors
# Check browser console
```

### Database error
```bash
# Re-run migrations
php artisan migrate:fresh --seed
```

## 📞 Support

Untuk pertanyaan atau issues:
- Check documentation di `/docs` folder
- Review code comments
- Contact: admin@example.com

## 📄 License

[Your License Here]

## 👏 Credits

Developed with ❤️ using Laravel & Tailwind CSS

donarazhar@gmail.com
IG : https://www.instagram.com/donsiyos/

**Version:** 1.0.0  
**Last Updated:** November 2025  
**Status:** Production Ready ✅
