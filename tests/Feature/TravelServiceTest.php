<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Paket;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\Wisata;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TravelServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_and_published_package_pages_are_available_to_guests(): void
    {
        $wisata = Wisata::create([
            'name' => 'Bali',
            'slug' => 'bali',
            'image_path' => 'https://example.test/images/bali.jpg',
        ]);
        $paket = Paket::create([
            'wisata_id' => $wisata->id,
            'title' => 'Wisata Bali',
            'slug' => 'wisata-bali',
            'category' => 'Domestik',
            'duration_days' => 3,
            'duration_nights' => 2,
            'price' => 1500000,
            'quota' => 20,
            'description' => 'Paket wisata Bali.',
            'published' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Destinasi')
            ->assertSee('Budaya &amp; Sejarah', false)
            ->assertSee(route('editorial.culture'), false)
            ->assertSee(route('editorial.culinary'), false)
            ->assertSee('Paket wisata unggulan')
            ->assertSee('Cerita dan inspirasi perjalanan')
            ->assertSee('Pesan paket sekarang')
            ->assertSee('package-art-image');
        $this->get(route('packages.index'))
            ->assertOk()
            ->assertSee('Destinasi dan paket wisata')
            ->assertSee('Wisata Bali')
            ->assertSee('category-filter')
            ->assertSee('https://example.test/images/bali.jpg')
            ->assertSee('package-art-image');
        $this->get(route('packages.index', ['kategori' => 'Domestik']))
            ->assertOk()
            ->assertSee('Wisata Bali');
        $this->get(route('packages.index', ['destinasi' => $wisata->id]))
            ->assertOk()
            ->assertSee('Wisata Bali');
        $this->get(route('packages.show', $paket))->assertOk()->assertSee('Wisata Bali');
    }

    public function test_customer_registration_assigns_customer_role(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Pelanggan Uji',
            'email' => 'pelanggan@example.test',
            'phone' => '08123456789',
            'password' => 'password-kuat',
            'password_confirmation' => 'password-kuat',
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'pelanggan@example.test', 'role' => 'customer']);
    }

    public function test_customer_can_book_published_package_and_invoice_is_created(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $wisata = Wisata::create(['name' => 'Yogyakarta', 'slug' => 'yogyakarta']);
        $paket = Paket::create([
            'wisata_id' => $wisata->id,
            'title' => 'Study Tour',
            'slug' => 'study-tour',
            'category' => 'Study tour',
            'duration_days' => 2,
            'duration_nights' => 1,
            'price' => 1000000,
            'quota' => 30,
            'description' => 'Perjalanan edukasi.',
            'published' => true,
        ]);

        $response = $this->actingAs($customer)->post(route('bookings.store', $paket), [
            'departure_date' => now()->addMonth()->toDateString(),
            'travelers' => 3,
            'customer_notes' => 'Mohon hubungi melalui email.',
        ]);

        $booking = Booking::firstOrFail();
        $response->assertRedirect(route('invoices.show', $booking->invoice));
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'user_id' => $customer->id,
            'status' => 'pending_negotiation',
            'proposed_price' => 3000000,
        ]);
        $this->assertDatabaseHas('invoices', [
            'booking_id' => $booking->id,
            'amount' => 3000000,
            'status' => 'pending',
        ]);
    }

    public function test_customer_cannot_open_admin_panel(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->get(route('admin.dashboard'))->assertRedirect(route('home'));
    }

    public function test_guest_is_redirected_to_homepage_from_admin_panel(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('home'));
    }

    public function test_admin_can_open_management_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        foreach ([
            'admin.dashboard',
            'admin.packages.index',
            'admin.destinations.index',
            'admin.bookings.index',
            'admin.customers.index',
            'admin.mous.index',
            'admin.settings.index',
        ] as $routeName) {
            $this->actingAs($admin)->get(route($routeName))->assertOk();
        }
    }

    public function test_payment_is_verified_by_admin_before_booking_is_confirmed(): void
    {
        Storage::fake('local');
        $customer = User::factory()->create(['role' => 'customer']);
        $admin = User::factory()->create(['role' => 'admin']);
        $wisata = Wisata::create(['name' => 'Bali', 'slug' => 'bali']);
        $paket = Paket::create([
            'wisata_id' => $wisata->id,
            'title' => 'Liburan Bali',
            'slug' => 'liburan-bali',
            'category' => 'Domestik',
            'duration_days' => 3,
            'duration_nights' => 2,
            'price' => 1000000,
            'quota' => 20,
            'description' => 'Liburan keluarga.',
            'published' => true,
        ]);
        $booking = Booking::create([
            'user_id' => $customer->id,
            'paket_id' => $paket->id,
            'departure_date' => now()->addMonth()->toDateString(),
            'travelers' => 2,
            'status' => 'pending_negotiation',
            'proposed_price' => 2000000,
        ]);
        $invoice = $booking->invoice()->create([
            'invoice_number' => 'INV-TEST-0001',
            'amount' => 2000000,
            'status' => 'pending',
            'issued_at' => now(),
        ]);
        $method = PaymentMethod::create([
            'name' => 'Transfer Bank',
            'account_name' => 'PT Innes Arsen Wisata',
            'account_number' => '1234567890',
            'active' => true,
        ]);

        $this->actingAs($admin)->put(route('admin.bookings.negotiate', $booking), [
            'decision' => 'awaiting_payment',
            'proposed_price' => 2500000,
            'admin_notes' => 'Harga disepakati.',
        ])->assertRedirect();

        PaymentMethod::create([
            'name' => 'DANA QR',
            'instructions' => 'Pindai kode QR DANA.',
            'active' => true,
        ]);
        $this->actingAs($customer)->get(route('invoices.show', $invoice))
            ->assertOk()
            ->assertSee('images/qris-dana.png')
            ->assertSee('DANA QR')
            ->assertSee('kode profil DANA');

        $this->actingAs($customer)->post(route('payments.submit', $invoice), [
            'payment_method_id' => $method->id,
            'proof' => UploadedFile::fake()->image('bukti.jpg'),
            'reference' => 'REF-001',
        ])->assertRedirect(route('invoices.show', $invoice));

        $payment = $invoice->payments()->firstOrFail();
        Storage::disk('local')->assertExists($payment->proof_path);
        $this->actingAs($customer)->get(route('documents.payment-proof', $payment))->assertOk();
        $this->actingAs($admin)->put(route('admin.payments.review', $payment), ['decision' => 'paid'])->assertRedirect();

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'paid']);
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'confirmed']);
    }

    public function test_customer_can_simulate_payment_only_in_enabled_local_testing_mode(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $wisata = Wisata::create(['name' => 'Bali', 'slug' => 'bali']);
        $paket = Paket::create([
            'wisata_id' => $wisata->id,
            'title' => 'Liburan Bali',
            'slug' => 'simulasi-bali',
            'category' => 'Domestik',
            'duration_days' => 3,
            'duration_nights' => 2,
            'price' => 1000000,
            'quota' => 20,
            'description' => 'Liburan keluarga.',
            'published' => true,
        ]);
        $booking = Booking::create([
            'user_id' => $customer->id,
            'paket_id' => $paket->id,
            'departure_date' => now()->addMonth()->toDateString(),
            'travelers' => 2,
            'status' => 'awaiting_payment',
            'proposed_price' => 2000000,
        ]);
        $invoice = $booking->invoice()->create([
            'invoice_number' => 'INV-SIM-0001',
            'amount' => 2000000,
            'status' => 'pending',
            'issued_at' => now(),
        ]);
        PaymentMethod::create(['name' => 'DANA QR', 'active' => true]);
        config(['travel.payment.simulation_enabled' => true]);

        $this->app->instance('env', 'production');
        $this->actingAs($customer)->get(route('invoices.show', $invoice))->assertDontSee('Simulasi Bayar Berhasil');
        $this->actingAs($customer)->postJson(route('payments.simulate', $invoice))->assertNotFound();

        $this->app->instance('env', 'testing');
        $this->actingAs($customer)->get(route('invoices.show', $invoice))
            ->assertOk()
            ->assertSee('Simulasi Bayar Berhasil (Test)');

        $this->actingAs($customer)->postJson(route('payments.simulate', $invoice))
            ->assertOk()
            ->assertJsonPath('invoice_status', 'paid')
            ->assertJsonPath('booking_status', 'confirmed')
            ->assertJsonPath('payment.description', 'Pembayaran simulasi terverifikasi');

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'paid',
        ]);
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'confirmed']);
        $this->assertDatabaseHas('payments', [
            'invoice_id' => $invoice->id,
            'status' => 'paid',
            'reference' => 'SIM-INV-SIM-0001',
        ]);
        $this->assertNotNull(Invoice::findOrFail($invoice->id)->paid_at);
        $this->actingAs($customer)->postJson(route('payments.simulate', $invoice))->assertStatus(422);
    }

    public function test_customer_can_submit_mou_request(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->post(route('mous.store'), [
            'organization_name' => 'SMA Contoh',
            'contact_name' => 'Narahubung',
            'email' => 'kontak@sma.test',
            'phone' => '08123456789',
            'description' => 'Pengajuan study tour.',
            'document' => UploadedFile::fake()->create('surat.pdf', 20, 'application/pdf'),
        ]);

        $response->assertRedirect(route('mous.index'));
        $this->assertDatabaseHas('mous', [
            'user_id' => $customer->id,
            'organization_name' => 'SMA Contoh',
            'status' => 'submitted',
        ]);
    }
}
