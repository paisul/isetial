# iSetial Wisdom MVP

Fondasi Laravel 12 untuk website iSetial Wisdom, portal lima masjid, portal anggota, admin scoped-masjid, dan pemesanan Jersey.

## Kebutuhan

- PHP 8.2+ dengan extensions: mbstring, openssl, pdo_mysql, fileinfo, curl
- Composer 2
- MySQL 8/MariaDB 10.6+
- Node.js hanya diperlukan bila aset Vite dikembangkan; tampilan MVP memakai Tailwind CDN sehingga production tidak menjalankan Node server.

## Instalasi lokal

```bash
git clone <repository-url> isetial
cd isetial
composer install
cp .env.example .env
php artisan key:generate
```

Buat database dan isi `.env`:

```dotenv
APP_ENV=local
APP_DEBUG=true
APP_URL=http://isetial.test
APP_DOMAIN=isetial.test
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=isetial
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan test
php artisan serve
```

Portal masjid dapat diuji tanpa DNS melalui `/m/darulhikmah`, `/m/jamihakim`, `/m/fatimah`, `/m/naim`, dan `/m/aman`. Untuk hostname lokal, arahkan `isetial.test` dan subdomainnya ke `127.0.0.1` lewat hosts/dnsmasq.

## Super Admin aman

Seeder hanya membuat Super Admin ketika tiga environment berikut tersedia. Jangan commit nilainya:

```dotenv
SUPER_ADMIN_NAME="Administrator"
SUPER_ADMIN_EMAIL=admin@example.com
SUPER_ADMIN_PASSWORD="kata-sandi-kuat"
```

Jalankan `php artisan db:seed`. Setelah akun terbentuk, hapus password dari `.env` atau rotasi nilainya.

## Deploy Hostinger Shared Hosting

1. Tambahkan repository melalui Git/SSH Hostinger, atau clone dari GitHub ke folder aplikasi di luar `public_html` bila panel mengizinkan.
2. Pilih PHP 8.2+ dan aktifkan extension yang disebutkan di atas.
3. Jalankan `composer install --no-dev --optimize-autoloader`.
4. Buat database MySQL dan user dengan privilege hanya untuk database tersebut; isi `.env` production. Gunakan `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://isetial.com`, `APP_DOMAIN=isetial.com`, dan HTTPS.
5. Jalankan `php artisan key:generate` hanya untuk instalasi baru, lalu `php artisan migrate --force` dan `php artisan db:seed --force`.
6. Jalankan `php artisan storage:link`. Pastikan `storage` dan `bootstrap/cache` dapat ditulis oleh PHP (umumnya 775; jangan 777 jika tidak diperlukan).
7. Arahkan document root domain utama ke `<project>/public`, bukan root project. Buat wildcard `*.isetial.com` atau lima subdomain eksplisit dan arahkan semuanya ke folder `public` yang sama.
8. Pasang SSL untuk domain utama dan seluruh subdomain. Pastikan wildcard/subdomain DNS mengarah ke hosting yang sama.
9. Optimalkan production: `php artisan config:cache`, `php artisan route:cache`, dan `php artisan view:cache`.

Jika Hostinger memaksa document root `public_html`, tempatkan source di luar web root dan isi `public_html` dengan isi folder `public`, lalu sesuaikan path `vendor/autoload.php` dan `bootstrap/app.php` di `index.php`. Opsi document root langsung ke `public` lebih aman dan lebih disarankan.

## Update dari GitHub

```bash
git pull --ff-only
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Aktifkan maintenance mode (`php artisan down --retry=60`) untuk migrasi berisiko, lalu `php artisan up`.

## Backup

- Jadwalkan backup MySQL harian (`mysqldump`) dan simpan terenkripsi di lokasi berbeda.
- Backup `.env` secara aman, `storage/app/public`, dan terutama `storage/app/private/jersey-proofs`.
- Uji restore berkala. Bukti pembayaran berada di disk private dan hanya bisa diunduh user berwenang.

## Arsitektur dan keamanan

- Satu Laravel + satu database. Tenant masjid ditentukan middleware hostname atau fallback path lokal.
- `people` dipisah dari `users`; akun opsional bagi person.
- Role dapat global atau scoped ke `masjid_id`. Endpoint admin memverifikasi scope di server, bukan hanya menyembunyikan menu.
- Aktivitas pusat memakai `masjid_id = null`; aktivitas/pengumuman portal selalu difilter `masjid_id` aktif.
- Saldo Jersey dihitung dari total pembayaran `verified`, tidak disimpan manual.
- CSRF, validation, password hashing, upload image/size validation, throttle lookup/order, dan private payment proofs diterapkan.

## Modul tahap berikutnya

Skema sekarang sengaja menyediakan konteks person, masjid, periode, posisi, divisi, dan tugas. Modul berikutnya belum dibangun: workflow usulan-keputusan-evaluasi, keuangan organisasi, Bantu Sesama, serta rapat/keuangan/inventaris/dokumen/laporan DKM. Tambahkan sebagai modul pada project dan database yang sama tanpa tabel per-masjid.
