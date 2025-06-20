<!-- @format -->

# 🚀 Laravel Reimbursement System

This is a Laravel-based Reimbursement Management System.

## 📦 Requirements

- PHP >= 8.1
- Composer
- MySQL / MariaDB
- Node.js & NPM (for frontend assets)
- Git

---

## 🔧 Installation Steps

### 1. BE Laravel

-Open terminal

    composer install

-Copy .env File and Generate App Key

    cp .env.example .env
    php artisan key:generate

-Configurasi .env

    APP_NAME=Laravel
    APP_ENV=local
    APP_KEY=base64:...
    APP_DEBUG=true
    APP_URL=http://127.0.0.1:8000

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=root
    DB_PASSWORD=

-Run Database Migration

    php artisan migrate

-Start Laravel Server

    php artisan serve

-Open New Terminal for Laravel Queue Checking

    php artisan queue:work

### 2. FE Vue3/Vite

-Open Terminal

    npm install
    npm run dev

### 2. Colection Postman

    -Open Postman
    -Klik Import
    -Pilih tab "File"
    -Klik "Upload Files" lalu pilih file .json Postman-mu
    -Klik "Import"
    -Jika collection kamu menggunakan variable seperti {{base_url}}, kamu juga perlu:
    -Klik Folder Reimbursement API lalu klik Variables
    -Masukkan variable misalnya:
    base_url = http://127.0.0.1:8000/
    -Disetiap request yang perlu token, Buka Authorization Auth Type Bearer Token
    -Masukan Token dari token login
    -Klik Save

Silahkan Tambahkan Usernya terlebih dahulu lewat Postman
Bisa menggunakan yang Register atau pun yang user -> add

Enjoy
