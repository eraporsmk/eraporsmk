<p align="center"><img src="http://portal.erapor-smk.net/logo3.png" width="600"></p>

## Cara Install

- Clone Repositori ini
```bash
git clone --depth=1 https://github.com/eraporsmk/eraporsmk.git project_name
cd project_name
```
- Install Dependencies
```bash
composer install
```

## Koneksi ke database
```bash
cp .env.example .env
nano .env
```

- Database Utama
```bash
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

- Database eRaporSMK Versi 4 (jika ada)
```bash
DB_ERAPOR4_HOST=127.0.0.1
DB_ERAPOR4_PORT=5432
DB_ERAPOR4_DATABASE=postgres
DB_ERAPOR4_USERNAME=postgres
DB_ERAPOR4_PASSWORD=postgres
```

## Generate App Key
```bash
php artisan key:generate
```

## Migration
- Membuat struktur table
```bash
php artisan migrate
```

- Jalankan seeder
```bash
php artisan db:seed
```
