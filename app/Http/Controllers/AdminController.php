<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CompanyGalleryImage;
use App\Models\CompanyProfile;
use App\Models\ManagementMember;
use App\Models\Mou;
use App\Models\Paket;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Models\Wisata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'counts' => [
                'customers' => User::where('role', 'customer')->count(),
                'packages' => Paket::count(),
                'bookings' => Booking::count(),
                'payments' => Payment::where('status', 'submitted')->count(),
                'mous' => Mou::where('status', 'submitted')->count(),
            ],
            'recentBookings' => Booking::with('user', 'paket', 'invoice')->latest()->take(8)->get(),
        ]);
    }

    public function packages(): View
    {
        return view('admin.packages', [
            'packages' => Paket::with('wisata')->latest()->get(),
            'wisatas' => Wisata::orderBy('name')->get(),
        ]);
    }

    public function storePackage(Request $request): RedirectResponse
    {
        $data = $this->validatedPackage($request);
        $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(6));
        Paket::create($data);

        return back()->with('success', 'Paket wisata berhasil ditambahkan.');
    }

    public function updatePackage(Request $request, Paket $paket): RedirectResponse
    {
        $data = $this->validatedPackage($request);
        if ($data['title'] !== $paket->title) {
            $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(6));
        }
        $paket->update($data);

        return back()->with('success', 'Paket wisata berhasil diperbarui.');
    }

    public function deletePackage(Paket $paket): RedirectResponse
    {
        abort_if($paket->bookings()->exists(), 422, 'Paket yang sudah dipesan tidak dapat dihapus.');
        $paket->delete();

        return back()->with('success', 'Paket wisata berhasil dihapus.');
    }

    public function destinations(): View
    {
        return view('admin.destinations', ['wisatas' => Wisata::withCount('packages')->orderBy('name')->get()]);
    }

    public function storeDestination(Request $request): RedirectResponse
    {
        $data = $this->validatedDestination($request);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('destinations', 'public');
        }
        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(6));
        Wisata::create($data);

        return back()->with('success', 'Destinasi berhasil ditambahkan.');
    }

    public function updateDestination(Request $request, Wisata $wisata): RedirectResponse
    {
        $data = $this->validatedDestination($request);
        if ($data['name'] !== $wisata->name) {
            $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(6));
        }
        $previousImage = $wisata->image_path;
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('destinations', 'public');
        }
        $wisata->update($data);
        if (isset($data['image_path']) && $previousImage !== $data['image_path']) {
            $this->deleteStoredDestinationImage($previousImage);
        }

        return back()->with('success', 'Destinasi berhasil diperbarui.');
    }

    public function deleteDestination(Wisata $wisata): RedirectResponse
    {
        abort_if($wisata->packages()->exists(), 422, 'Destinasi yang masih digunakan oleh paket tidak dapat dihapus.');
        $imagePath = $wisata->image_path;
        $wisata->delete();
        $this->deleteStoredDestinationImage($imagePath);

        return back()->with('success', 'Destinasi berhasil dihapus.');
    }

    public function bookings(): View
    {
        return view('admin.bookings', [
            'bookings' => Booking::with('user', 'paket.wisata', 'invoice.payments.paymentMethod')->latest()->get(),
        ]);
    }

    public function negotiate(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless(in_array($booking->status, ['pending_negotiation', 'awaiting_payment'], true), 422, 'Status pesanan ini tidak dapat dinegosiasikan.');

        $data = $request->validate([
            'decision' => ['required', 'in:awaiting_payment,cancelled'],
            'proposed_price' => ['required_if:decision,awaiting_payment', 'nullable', 'numeric', 'min:1', 'max:999999999999'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($booking, $data): void {
            $booking->update([
                'status' => $data['decision'],
                'proposed_price' => $data['proposed_price'] ?? $booking->proposed_price,
                'admin_notes' => $data['admin_notes'] ?? null,
            ]);

            if ($data['decision'] === 'awaiting_payment') {
                $booking->invoice()->update([
                    'amount' => $data['proposed_price'],
                    'status' => 'pending',
                ]);
            }
        });

        return back()->with('success', 'Hasil negosiasi pesanan berhasil disimpan.');
    }

    public function reviewPayment(Request $request, Payment $payment): RedirectResponse|JsonResponse
    {
        $data = $request->validate(['decision' => ['required', 'in:paid,rejected']]);

        DB::transaction(function () use ($payment, $data): void {
            $payment = Payment::query()->lockForUpdate()->findOrFail($payment->getKey());
            abort_unless($payment->status === 'submitted', 422, 'Pembayaran ini sudah ditinjau.');
            $invoice = $payment->invoice()->lockForUpdate()->firstOrFail();
            $booking = $invoice->booking()->lockForUpdate()->firstOrFail();

            $payment->update(['status' => $data['decision'], 'reviewed_at' => now()]);
            if ($data['decision'] === 'paid') {
                $invoice->update(['status' => 'paid', 'paid_at' => now()]);
                $booking->update(['status' => 'confirmed']);
            } else {
                $invoice->update(['status' => 'pending']);
                $booking->update(['status' => 'awaiting_payment']);
            }
        });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Status pembayaran berhasil diperbarui.',
                'decision' => $data['decision'],
            ]);
        }

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    public function customers(): View
    {
        return view('admin.customers', ['customers' => User::where('role', 'customer')->withCount('bookings')->latest()->get()]);
    }

    public function mous(): View
    {
        return view('admin.mous', ['mous' => Mou::with('user')->latest()->get()]);
    }

    public function reviewMou(Request $request, Mou $mou): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:under_review,approved,rejected'],
            'admin_notes' => ['nullable', 'string', 'max:3000'],
            'admin_document' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        if ($request->hasFile('admin_document')) {
            $data['admin_document_path'] = $request->file('admin_document')->store('mou-documents');
        }
        unset($data['admin_document']);
        $mou->update($data);

        return back()->with('success', 'Status pengajuan MoU berhasil diperbarui.');
    }

    public function settings(): View
    {
        return view('admin.settings', [
            'profile' => CompanyProfile::firstOrCreate(
                ['name' => 'PT. Innes Arsen Wisata'],
                ['address' => config('profile.address')],
            ),
            'managers' => ManagementMember::orderBy('sort_order')->get(),
            'paymentMethods' => PaymentMethod::orderBy('name')->get(),
            'galleryImages' => CompanyGalleryImage::orderBy('sort_order')->latest()->get(),
        ]);
    }

    public function storeGalleryImage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'caption' => ['nullable', 'string', 'max:500'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'published' => ['nullable', 'boolean'],
        ]);
        $data['image_path'] = $this->storeGalleryImageFile($request);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['published'] = $request->boolean('published');
        unset($data['image']);
        CompanyGalleryImage::create($data);

        return back()->with('success', 'Foto dokumentasi berhasil ditambahkan ke galeri.');
    }

    public function updateGalleryImage(Request $request, CompanyGalleryImage $galleryImage): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'caption' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'published' => ['nullable', 'boolean'],
        ]);
        $previousImagePath = $galleryImage->image_path;
        if ($request->hasFile('image')) {
            $data['image_path'] = $this->storeGalleryImageFile($request);
        }
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['published'] = $request->boolean('published');
        unset($data['image']);
        $galleryImage->update($data);

        if (isset($data['image_path']) && $previousImagePath !== $galleryImage->image_path) {
            $this->deleteGalleryImageFile($previousImagePath);
        }

        return back()->with('success', 'Foto dokumentasi berhasil diperbarui.');
    }

    public function deleteGalleryImage(CompanyGalleryImage $galleryImage): RedirectResponse
    {
        $imagePath = $galleryImage->image_path;
        $galleryImage->delete();
        $this->deleteGalleryImageFile($imagePath);

        return back()->with('success', 'Foto dokumentasi berhasil dihapus dari galeri.');
    }

    private function storeGalleryImageFile(Request $request): string
    {
        $image = $request->file('image');
        if (!$image instanceof UploadedFile) {
            throw new RuntimeException('Berkas foto galeri tidak tersedia.');
        }

        $path = $image->storePublicly('profile-gallery', 'public');
        if ($path === false) {
            throw new RuntimeException('Foto galeri gagal disimpan ke penyimpanan publik.');
        }

        return $path;
    }

    private function deleteGalleryImageFile(string $path): void
    {
        $disk = Storage::disk('public');
        if ($disk->exists($path) && !$disk->delete($path)) {
            throw new RuntimeException('Berkas foto galeri gagal dihapus dari penyimpanan publik.');
        }
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:5000'],
            'address' => ['nullable', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
        ]);

        $profile = CompanyProfile::first() ?? new CompanyProfile;
        $profile->fill($data)->save();

        return back()->with('success', 'Profil perusahaan berhasil diperbarui.');
    }

    public function storeManager(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'position' => ['required', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        ManagementMember::create($data);

        return back()->with('success', 'Data manajemen berhasil ditambahkan.');
    }

    public function deleteManager(ManagementMember $manager): RedirectResponse
    {
        $manager->delete();

        return back()->with('success', 'Data manajemen berhasil dihapus.');
    }

    public function updateManager(Request $request, ManagementMember $manager): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'position' => ['required', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $manager->update($data);

        return back()->with('success', 'Data manajemen berhasil diperbarui.');
    }

    public function storePaymentMethod(Request $request): RedirectResponse
    {
        PaymentMethod::create($request->validate([
            'name' => ['required', 'string', 'max:80'],
            'account_name' => ['nullable', 'string', 'max:120'],
            'account_number' => ['nullable', 'string', 'max:80'],
            'instructions' => ['nullable', 'string', 'max:2000'],
        ]) + ['active' => $request->boolean('active')]);

        return back()->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    public function updatePaymentMethod(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $paymentMethod->update($request->validate([
            'name' => ['required', 'string', 'max:80'],
            'account_name' => ['nullable', 'string', 'max:120'],
            'account_number' => ['nullable', 'string', 'max:80'],
            'instructions' => ['nullable', 'string', 'max:2000'],
        ]) + ['active' => $request->boolean('active')]);

        return back()->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    public function deletePaymentMethod(PaymentMethod $paymentMethod): RedirectResponse
    {
        abort_if($paymentMethod->payments()->exists(), 422, 'Metode pembayaran yang sudah digunakan tidak dapat dihapus.');
        $paymentMethod->delete();

        return back()->with('success', 'Metode pembayaran berhasil dihapus.');
    }

    private function validatedPackage(Request $request): array
    {
        $data = $request->validate([
            'wisata_id' => ['required', 'exists:wisatas,id'],
            'title' => ['required', 'string', 'max:180'],
            'category' => ['required', Rule::in(config('travel.package_categories'))],
            'duration_days' => ['required', 'integer', 'min:1', 'max:365'],
            'duration_nights' => ['required', 'integer', 'min:0', 'max:364', 'lte:duration_days'],
            'price' => ['required', 'numeric', 'min:1', 'max:999999999999'],
            'quota' => ['required', 'integer', 'min:1', 'max:10000'],
            'description' => ['required', 'string', 'max:5000'],
            'includes' => ['nullable', 'string', 'max:3000'],
            'excludes' => ['nullable', 'string', 'max:3000'],
        ]);
        $data['published'] = $request->boolean('published');

        return $data;
    }

    private function validatedDestination(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'province' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:3000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        unset($data['image']);

        return $data;
    }

    private function deleteStoredDestinationImage(?string $imagePath): void
    {
        if ($imagePath && Str::startsWith($imagePath, 'destinations/')) {
            Storage::disk('public')->delete($imagePath);
        }
    }
}
