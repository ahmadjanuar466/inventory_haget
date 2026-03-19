<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Libary yang dibutuhkan

libarry yang dibutuhkan laravel yaitu:

- library ZIP

## Langkah-langkah installasi

- Jalankan command : composer install.
- Apabila ada library yang kurang jalankan command : composer update. 
- Buatkan database dengan nama "staterkit"
- Buatkan file .env bedasarkan .env.example edit yang mengandung unsur DB_*.
- Jalankan Command : php artisan key:generate
- Jalankan Command : php artisan migrate
- Jalankan Command : php artisan db:seed
- Tambahkan frankenphp (opsional) : php artisan octane:install

## Alur Pengerjaan

- Buatkan file service seperti UserService.php .
- Buatkan file implement seperti Implement/UserImpl.php untuk impement UserService.php
- Buatkan file provider seperti UserProvider.php untuk mendaftarkan [UserServices::class => UserImpl::class, DataTableService::class => DataTableImpl::class];
- Controller sudah tidak digunakan tetapi services di akses melaui livewire 
- dokumentasi livewire ada di <a href="https://laravel-livewire.com/docs/2.x/quickstart">livewire</a>
- role dan permission menggunakan spaties dokumentasi ada di <a href="https://spatie.be/docs/laravel-permission/v6/introduction">Spatie</a>
- UI menggunakan Flux dokumentasi bisa digunakan di <a href="https://fluxui.dev/">Flux</a>


