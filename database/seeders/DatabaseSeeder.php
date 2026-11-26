<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use App\Models\ManagementMember;
use App\Models\Paket;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\Wisata;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(EditorialArticleSeeder::class);

        CompanyProfile::firstOrCreate(
            ['name' => 'PT. Innes Arsen Wisata'],
            [
                'description' => 'Mitra perjalanan Anda untuk menjelajahi destinasi pilihan di Indonesia.',
                'address' => config('profile.address'),
            ],
        );

        ManagementMember::firstOrCreate(
            ['name' => 'Tim Innes Arsen Wisata', 'position' => 'Layanan Pelanggan'],
            ['sort_order' => 1],
        );

        $destinations = [
            [
                'name' => 'Bali',
                'slug' => 'bali',
                'province' => 'Bali',
                'description' => 'Wisata budaya, alam, dan pantai di Pulau Dewata.',
                'image_path' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1000&q=85',
                'package_title' => 'Jelajah Bali',
                'package_description' => 'Nikmati wisata budaya, alam, dan pantai di Pulau Dewata bersama perjalanan yang dirancang nyaman.',
                'price' => 2500000,
            ],
            [
                'name' => 'Yogyakarta',
                'slug' => 'yogyakarta',
                'province' => 'Daerah Istimewa Yogyakarta',
                'description' => 'Wisata sejarah, budaya, dan edukasi.',
                'image_path' => 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?auto=format&fit=crop&w=1000&q=85',
                'package_title' => 'Jelajah Yogyakarta',
                'package_description' => 'Jelajahi kekayaan sejarah, budaya, dan edukasi di Daerah Istimewa Yogyakarta.',
                'price' => 2200000,
            ],
            [
                'name' => 'Magelang & Candi Borobudur',
                'slug' => 'magelang-borobudur',
                'province' => 'Jawa Tengah',
                'description' => 'Wisata warisan budaya dan candi Buddha terbesar di dunia.',
                'image_path' => 'https://images.unsplash.com/photo-1604999333679-b86d54738315?auto=format&fit=crop&w=1000&q=85',
                'package_title' => 'Paket Wisata Magelang & Candi Borobudur',
                'package_description' => 'Kunjungi Candi Borobudur dan jelajahi pesona budaya serta alam Magelang.',
                'price' => 1850000,
            ],
            [
                'name' => 'Dieng',
                'slug' => 'dieng-golden-sunrise',
                'province' => 'Jawa Tengah',
                'description' => 'Dataran tinggi dengan telaga, kawah, dan panorama matahari terbit.',
                'image_path' => 'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=1000&q=85',
                'package_title' => 'Paket Wisata Dieng & Golden Sunrise',
                'package_description' => 'Nikmati udara sejuk dataran tinggi Dieng dan pengalaman menyaksikan golden sunrise.',
                'price' => 1650000,
            ],
            [
                'name' => 'Sidoarjo & Sekitarnya',
                'slug' => 'sidoarjo-sekitarnya',
                'province' => 'Jawa Timur',
                'description' => 'Perjalanan mengenal wisata, budaya, dan kuliner khas Sidoarjo.',
                'image_path' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=1000&q=85',
                'package_title' => 'Paket Wisata Sidoarjo & Sekitarnya',
                'package_description' => 'Jelajahi destinasi pilihan dan kuliner lokal Sidoarjo serta kawasan sekitarnya.',
                'price' => 1350000,
            ],
            [
                'name' => 'Lombok & Gili Trawangan',
                'slug' => 'lombok-gili-trawangan',
                'province' => 'Nusa Tenggara Barat',
                'description' => 'Wisata pantai, pulau, dan alam tropis di Lombok dan Kepulauan Gili.',
                'image_path' => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=1000&q=85',
                'package_title' => 'Paket Wisata Lombok & Gili Trawangan',
                'package_description' => 'Temukan pesona pantai Lombok dan suasana tropis Gili Trawangan dalam satu perjalanan.',
                'price' => 3250000,
            ],
        ];

        foreach ($destinations as $destination) {
            $wisata = Wisata::firstOrCreate(
                ['slug' => $destination['slug']],
                [
                    'name' => $destination['name'],
                    'province' => $destination['province'],
                    'description' => $destination['description'],
                    'image_path' => $destination['image_path'],
                ],
            );
            if (blank($wisata->image_path)) {
                $wisata->update(['image_path' => $destination['image_path']]);
            }

            Paket::firstOrCreate(
                ['slug' => $destination['slug'].'-jelajah'],
                [
                    'wisata_id' => $wisata->id,
                    'title' => $destination['package_title'],
                    'category' => 'Domestik',
                    'duration_days' => 3,
                    'duration_nights' => 2,
                    'price' => $destination['price'],
                    'quota' => 20,
                    'description' => $destination['package_description'].' Hubungi admin untuk detail jadwal dan fasilitas.',
                    'includes' => 'Konsultasi rencana perjalanan dan pendampingan pemesanan.',
                    'excludes' => 'Pengeluaran pribadi dan layanan di luar kesepakatan.',
                    'published' => true,
                ],
            );
        }

        $adminEmail = config('travel.admin.email');
        $adminPassword = config('travel.admin.password');
        if ($adminEmail && $adminPassword) {
            User::updateOrCreate(
                ['email' => $adminEmail],
                ['name' => config('travel.admin.name'), 'password' => Hash::make($adminPassword), 'role' => 'admin'],
            );
        }

        $accountNumber = config('travel.payment.account_number');
        if ($accountNumber) {
            PaymentMethod::updateOrCreate(
                ['name' => config('travel.payment.method_name')],
                [
                    'account_name' => config('travel.payment.account_name'),
                    'account_number' => $accountNumber,
                    'instructions' => 'Pastikan nama penerima sesuai dengan rekening resmi perusahaan.',
                    'active' => true,
                ],
            );
        }

        $danaMethod = PaymentMethod::firstOrNew(['name' => 'DANA QR']);
        if (blank($danaMethod->account_name)) {
            $danaMethod->account_name = config('travel.payment.account_name');
        }
        if (blank($danaMethod->account_number)) {
            $danaMethod->account_number = '62-896****8728';
        }
        if (blank($danaMethod->instructions)) {
            $danaMethod->instructions = 'Pindai QR DANA pada halaman pembayaran. Nomor akun yang ditampilkan tersamarkan; cocokkan nama penerima dan nominal pada aplikasi sebelum mengirim. Admin memverifikasi pembayaran secara manual.';
        }
        if (!$danaMethod->exists) {
            $danaMethod->active = true;
        }
        $danaMethod->save();
    }
}
