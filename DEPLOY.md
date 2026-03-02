# 🚀 Deploy Civic Connect ke Fly.io

## Prasyarat

1. **Install Fly CLI** (flyctl):

    ```bash
    # macOS
    brew install flyctl

    # atau cara universal
    curl -L https://fly.io/install.sh | sh
    ```

2. **Buat akun Fly.io** (gratis):
    ```bash
    fly auth signup
    ```
    > Catatan: Fly.io memerlukan credit card untuk verifikasi, tapi ada free tier (3 shared VM 256MB + 3GB storage).

---

## Langkah Deploy

### 1. Login ke Fly.io

```bash
fly auth login
```

### 2. Launch Aplikasi (pertama kali)

```bash
cd civic-connect-laravel
fly launch --no-deploy
```

- Ketika ditanya nama app, masukkan: `civic-connect` (atau nama lain yang kamu mau)
- Pilih region: **Singapore (sin)** — paling dekat ke Indonesia
- Jawab **No** untuk database (kita pakai SQLite)

### 3. Buat Persistent Volume untuk SQLite

```bash
fly volumes create data --region sin --size 1
```

> Volume 1GB gratis untuk menyimpan database SQLite secara persisten.

### 4. Set Secret (APP_KEY)

```bash
# Generate key dan set sebagai secret
fly secrets set APP_KEY=$(php artisan key:generate --show)
```

Atau jika sudah punya key:

```bash
fly secrets set APP_KEY=base64:YOUR_KEY_HERE
```

### 5. Set App URL

```bash
fly secrets set APP_URL=https://civic-connect.fly.dev
```

### 6. Deploy! 🎉

```bash
fly deploy
```

Tunggu beberapa menit hingga build selesai. Setelah sukses, app akan tersedia di:

```
https://civic-connect.fly.dev
```

---

## Perintah Berguna

| Perintah           | Deskripsi                     |
| ------------------ | ----------------------------- |
| `fly status`       | Cek status aplikasi           |
| `fly logs`         | Lihat log aplikasi            |
| `fly ssh console`  | SSH ke dalam container        |
| `fly deploy`       | Deploy ulang                  |
| `fly open`         | Buka app di browser           |
| `fly secrets list` | Lihat daftar secrets          |
| `fly scale show`   | Lihat resource yang digunakan |

### Menjalankan Artisan Command

```bash
fly ssh console -C "php /app/artisan migrate:status"
fly ssh console -C "php /app/artisan db:seed"
fly ssh console -C "php /app/artisan tinker"
```

### Melihat Database

```bash
fly ssh console -C "sqlite3 /data/database.sqlite"
```

---

## Biaya Estimasi (Free Tier)

| Resource              | Gratis | Keterangan                   |
| --------------------- | ------ | ---------------------------- |
| Shared CPU 1x (256MB) | 3 VM   | Cukup untuk development/demo |
| Persistent Volume     | 3GB    | Untuk SQLite database        |
| Bandwidth             | ~100GB | Lebih dari cukup             |
| SSL Certificate       | ✅     | Otomatis via Let's Encrypt   |
| Custom Domain         | ✅     | Gratis                       |

**Total: $0/bulan** untuk penggunaan ringan.

---

## Custom Domain (Opsional)

```bash
# Tambahkan custom domain
fly certs create your-domain.com

# Dapatkan IP address
fly ips list
```

Lalu arahkan DNS domain kamu ke IP yang diberikan.

---

## Troubleshooting

### App tidak bisa diakses

```bash
fly status
fly logs
```

### Database hilang setelah deploy

Pastikan volume sudah di-mount dengan benar. Cek `fly.toml` bagian `[mounts]`.

### Build terlalu lama

Cek `.dockerignore` untuk memastikan folder yang tidak perlu (node_modules, vendor) tidak ikut di-upload.
