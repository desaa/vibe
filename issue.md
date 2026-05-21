# Panduan Implementasi: Autentikasi Shield CI4 & Integrasi Template Velzone

Dokumen ini berisi panduan langkah demi langkah (step-by-step) untuk mengimplementasikan sistem autentikasi menggunakan **CodeIgniter 4 Shield** dan mengintegrasikannya dengan template **Velzone**. Panduan ini dirancang agar mudah diikuti oleh junior programmer atau model AI lainnya.

---

## 1. Persiapan Database & Environment (`.env`)

Konfigurasikan database dan SMTP Google pada file `.env` di root proyek.

### Database
Sesuaikan konfigurasi database dengan detail berikut:
```env
database.default.hostname = localhost
database.default.database = vibe
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
```

### SMTP Email (Google/Gmail)
Gunakan *Google App Password* (Sandi Aplikasi) untuk keamanan, jangan gunakan password akun Google utama Anda.
```env
# Email Config
email.fromEmail = "your-email@gmail.com"
email.fromName = "Vibe App"
email.protocol = "smtp"
email.SMTPHost = "smtp.gmail.com"
email.SMTPUser = "your-email@gmail.com"
email.SMTPPass = "your-google-app-password" # Sandi Aplikasi Google 16 digit
email.SMTPPort = 587
email.SMTPCrypto = "tls"
email.mailType = "html"
```

---

## 2. Instalasi & Setup CodeIgniter Shield

Jalankan perintah-perintah berikut di terminal proyek:

1. **Install Library Shield via Composer:**
   ```bash
   composer require codeigniter4/shield
   ```

2. **Jalankan Setup Wizard Shield:**
   ```bash
   php spark shield:setup
   ```
   *Catatan: Ketik `y` jika ditanya untuk menimpa file atau menjalankan migrasi.*

3. **Jalankan Migrasi Database:**
   Jika migrasi belum berjalan otomatis saat setup, jalankan:
   ```bash
   php spark migrate
   ```

---

## 3. Konfigurasi Fitur Email & Verifikasi

### A. Aktifkan Email Verification (Registrasi)
Secara default, verifikasi email setelah registrasi dinonaktifkan di Shield. Untuk mengaktifkannya:
1. Buka file `app/Config/Auth.php`.
2. Cari bagian `$actions` dan pastikan kelas `EmailActivator` aktif:
   ```php
   public array $actions = [
       'register' => \CodeIgniter\Shield\Actions\EmailActivator::class,
       'login'    => null, // Ubah jika ingin 2FA
   ];
   ```

### B. Konfigurasi Email di CodeIgniter
Buka file `app/Config/Email.php` dan hubungkan dengan konfigurasi `.env`:
```php
public string $fromEmail  = '';
public string $fromName   = '';
public string $protocol   = 'smtp';
public string $SMTPHost   = '';
public string $SMTPUser   = '';
public string $SMTPPass   = '';
public int    $SMTPPort   = 587;
public string $SMTPCrypto = 'tls';
public string $mailType   = 'html';

public function __construct()
{
    parent::__construct();

    $this->fromEmail  = env('email.fromEmail', $this->fromEmail);
    $this->fromName   = env('email.fromName', $this->fromName);
    $this->protocol   = env('email.protocol', $this->protocol);
    $this->SMTPHost   = env('email.SMTPHost', $this->SMTPHost);
    $this->SMTPUser   = env('email.SMTPUser', $this->SMTPUser);
    $this->SMTPPass   = env('email.SMTPPass', $this->SMTPPass);
    $this->SMTPPort   = (int) env('email.SMTPPort', $this->SMTPPort);
    $this->SMTPCrypto = env('email.SMTPCrypto', $this->SMTPCrypto);
    $this->mailType   = env('email.mailType', $this->mailType);
}
```

---

## 4. Role Based Access Control (RBAC)

Shield menyediakan grup bawaan (roles) dan perizinan (permissions) yang dapat dikonfigurasi di `app/Config/AuthGroups.php`.

1. **Definisikan Roles & Permissions** di `app/Config/AuthGroups.php` sesuai kebutuhan bisnis aplikasi Vibe.
2. **Proteksi Route** di file `app/Config/Routes.php` menggunakan filter bawaan Shield:
   - Proteksi login umum: `filter => 'session'`
   - Proteksi berdasarkan grup/role: `filter => 'group:admin'` atau `filter => 'group:admin,superadmin'`
   - Proteksi berdasarkan izin spesifik: `filter => 'permission:users.manage'`

Contoh di `app/Config/Routes.php`:
```php
$routes->group('admin', ['filter' => 'group:admin'], static function ($routes) {
    $routes->get('dashboard', 'AdminController::index');
});
```

---

## 5. Kustomisasi Tampilan Menggunakan Template Velzone

Secara default, Shield memiliki tampilan bawaan. Kita perlu melakukan kustomisasi agar menggunakan template Velzone.

### A. Publish View Shield
Jalankan perintah ini untuk menyalin file tampilan Shield ke folder `app/Views/Shield`:
```bash
php spark auth:publish
```
*Pilih opsi untuk mempublikasikan views.*

### B. Custom Halaman Login
1. Buka file `/public/assets/velzone/auth-signin-cover.html` untuk melihat struktur HTML template login Velzone.
2. Edit file `app/Views/Shield/login.php`. 
3. Gunakan layout dan gaya dari `auth-signin-cover.html`. Pastikan:
   - Seluruh tag link CSS dan script JS menggunakan path absolut yang benar (contoh: `<?= base_url('assets/velzone/assets/css/bootstrap.min.css') ?>`).
   - Form method menggunakan `post` dan action mengarah ke `<?= url_to('login') ?>`.
   - Input username/email memiliki name `email` atau `username` (sesuai konfigurasi Shield).
   - Input password memiliki name `password`.
   - Menyertakan checkbox "Remember me" dengan name `remember`.
   - Menampilkan error dan pesan sukses menggunakan helper session bawaan Shield.

### C. Custom Halaman Register & Reset Password
Lakukan hal yang sama untuk halaman register (`app/Views/Shield/register.php`) dan halaman reset/lupa password (`app/Views/Shield/magic_link_form.php` / `magic_link_message.php`).
*Gunakan `/public/assets/velzone/auth-signup-cover.html` dan `/public/assets/velzone/auth-pass-reset-cover.html` sebagai referensi template.*

---

## 6. Kustomisasi Dashboard Utama

Setelah berhasil login, arahkan user ke Dashboard utama.

1. Buat Controller baru, misal `DashboardController.php`:
   ```bash
   php spark make:controller DashboardController
   ```
2. Buat method `index` di controller tersebut dan kembalikan view dashboard:
   ```php
   public function index()
   {
       return view('dashboard/index');
   }
   ```
3. Buat file view `app/Views/dashboard/index.php`.
4. Salin kode HTML dari `/public/assets/velzone/index.html` ke file tersebut.
5. Sesuaikan semua path aset (CSS, JS, Images) menggunakan helper `base_url()` agar mengarah ke folder `/public/assets/velzone/assets/...` secara absolut.
6. Tambahkan tombol logout yang mengarah ke `<?= url_to('logout') ?>`.

---

## 7. Checklist Pengujian (Testing)

Sebelum menyatakan tugas ini selesai, pastikan skenario berikut berjalan tanpa error:
*   [ ] Halaman login tampil dengan desain Velzone cover style.
*   [ ] Pengguna baru dapat mendaftar (register) dan mendapatkan email aktivasi di inbox/spam email mereka.
*   [ ] Pengguna tidak bisa login sebelum melakukan aktivasi link/kode dari email verifikasi.
*   [ ] Fitur Lupa Password / Reset Password mengirimkan email berisi tautan reset dan dapat mengubah password dengan sukses.
*   [ ] Filter route berfungsi (pengguna biasa tidak bisa mengakses halaman bertanda khusus admin/group).
*   [ ] Setelah login berhasil, pengguna diarahkan ke dashboard utama yang menggunakan layout Velzone `index.html`.
*   [ ] Tombol logout bekerja dan mengakhiri session dengan benar.
