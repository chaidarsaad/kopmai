<?php

namespace App\Livewire;

use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Cart;
use App\Models\Store;
use App\Models\Order;
use App\Models\User;
use App\Services\BiteshipService;
use App\Notifications\NewOrderNotification;
use App\Services\MidtransService;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class Checkout extends Component
{
    public $carts = [];
    public $total = 0;
    public $shippingCost = 0;
    public $store;
    public $kelasList = [];
    public $studentList = [];
    public $totalItems = 0;
    public $shopsWithShipping = [];
    public $searchStudent = '';
    public $filteredStudents = [];
    public $showStudentDropdown = false;
    protected $midtrans;
    public $showStudentModal = false;
    public $isEditingStudent = false;
    public $studentForm = [
        'id' => null,
        'nomor_induk_santri' => '',
        'nama_santri' => '',
        'nama_wali_santri' => '',
    ];


    public $shippingData = [
        'nomor_induk_santri' => '',
        'nama_wali' => '',
        'student_id' => '',
        'phone' => '',
        'notes' => ''
    ];

    public $rules = [
        'shippingData.student_id' => 'required',
        'shippingData.nama_wali' => 'required|min:3',
        'shippingData.phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
    ];

    public $messages = [
        'shippingData.nama_wali.required' => 'Nama BIN / BINTI wajib diisi.',
        'shippingData.student_id.required' => 'Nama Santri wajib diisi.',
        'shippingData.nama_wali.min' => 'Nama BIN / BINTI minimal 3 karakter.',
        'shippingData.phone.required' => 'Nomor telepon wajib diisi.',
        'shippingData.phone.regex' => 'Format nomor telepon tidak valid.',
        'shippingData.phone.min' => 'Nomor telepon minimal 10 digit.',
    ];


    public function boot(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    public function mount()
    {
        $this->shippingData['phone'] = Auth::user()->phone_number ?? '';

        $this->loadCarts();
        if ($this->carts->isEmpty()) {
            return redirect()->route('home');
        }
        $this->store = Store::first();
        $this->studentList = Student::orderBy('id', 'asc')->pluck('nama_santri', 'id');

        $this->filteredStudents = Student::inRandomOrder()
            ->limit(10)
            ->get()
            ->toArray();

    }

    public function openAddStudentModal()
    {
        $this->resetErrorBag();  // Bersihkan pesan error lama
        $this->resetValidation();
        $this->resetStudentForm();
        $this->isEditingStudent = false;
        $this->showStudentModal = true;
    }

    public function openEditStudentModal($id)
    {
        $this->resetErrorBag();  // Bersihkan pesan error lama
        $this->resetValidation();
        $this->resetStudentForm();
        $student = Student::findOrFail($id);
        $this->studentForm = [
            'id' => $student->id,
            'nomor_induk_santri' => $student->nomor_induk_santri,
            'nama_santri' => $student->nama_santri,
            'nama_wali_santri' => $student->nama_wali_santri,
        ];
        $this->isEditingStudent = true;
        $this->showStudentModal = true;
    }

    public function saveStudent()
    {
        $this->validate([
            'studentForm.nama_santri' => 'required|min:3|unique:students,nama_santri,' . ($this->studentForm['id'] ?? 'null'),
            'studentForm.nama_wali_santri' => 'required|min:3',
        ], [
            'studentForm.nama_santri.unique' => 'Nama Santri ini sudah digunakan.',

            'studentForm.nama_santri.required' => 'Nama Santri wajib diisi.',
            'studentForm.nama_santri.min' => 'Nama Santri minimal 3 karakter.',

            'studentForm.nama_wali_santri.required' => 'Nama BIN/BINTI wajib diisi.',
            'studentForm.nama_wali_santri.min' => 'Nama BIN/BINTI minimal 3 karakter.',
        ]);

        $student = Student::updateOrCreate(
            ['id' => $this->studentForm['id']],
            [
                'nomor_induk_santri' => strtoupper(uniqid()),
                'nama_santri' => $this->studentForm['nama_santri'],
                'nama_wali_santri' => $this->studentForm['nama_wali_santri'],
            ]
        );

        $this->shippingData['student_id'] = $student->id;
        $this->searchStudent = $student->nama_santri;
        $this->shippingData['nomor_induk_santri'] = $student->nomor_induk_santri;
        $this->shippingData['nama_wali'] = $student->nama_wali_santri;

        $this->showStudentModal = false;
        $this->showStudentDropdown = false;
        $this->loadCarts();
    }


    public function resetStudentForm()
    {
        $this->studentForm = [
            'id' => null,
            'nomor_induk_santri' => '',
            'nama_santri' => '',
            'nama_wali_santri' => '',
        ];
    }


    public function loadCarts()
    {
        $this->carts = Cart::where('user_id', auth()->id())
            ->whereHas('product', function ($query) {
                $query->where('is_active', 1);
            })
            ->with([
                'product' => function ($query) {
                    $query->where('is_active', 1);
                }
            ])
            ->whereHas('product.shop', function ($q) {
                $q->where('is_active', 1);
            })->get();

        $this->calculateTotal();
    }


    public function calculateTotal()
    {
        $this->total = 0;
        $this->totalItems = 0;
        $this->shippingCost = 0;
        $this->shopsWithShipping = [];  // Menyimpan daftar toko dengan ongkir

        foreach ($this->carts as $cart) {
            $this->total += $cart->product->price * $cart->quantity;
            $this->totalItems += $cart->quantity;

            $shop = $cart->product->shop;
            if ($shop && $shop->is_ongkir) {
                if (!isset($this->shopsWithShipping[$shop->id])) {
                    $this->shopsWithShipping[$shop->id] = [
                        'name' => $shop->name,
                        'ongkir' => $shop->ongkir,
                    ];
                }
            }
        }

        // Total ongkir dihitung dari semua shop yang memiliki ongkir aktif
        $this->shippingCost = array_sum(array_column($this->shopsWithShipping, 'ongkir'));
    }

    public function showStudents()
    {
        $this->showStudentDropdown = true;

        // Jika tidak ada pencarian, tampilkan 10 santri teratas
        if (empty($this->searchStudent)) {
            $this->filteredStudents = Student::inRandomOrder()
                ->limit(10)
                ->get()
                ->toArray();
        }

    }

    public function updatedSearchStudent()
    {
        $this->shippingData['student_id'] = null;

        if (strlen($this->searchStudent) === 0) {
            $this->clearSelectedStudent();  // Hapus semua data terkait santri
            return;
        }

        if (strlen($this->searchStudent) > 1) {
            $this->filteredStudents = Student::where('nama_santri', 'like', '%' . $this->searchStudent . '%')
                ->limit(10)
                ->get()
                ->toArray();
        } else {
            $this->filteredStudents = Student::inRandomOrder()
                ->limit(10)
                ->get()
                ->toArray();
        }

        $this->dispatch('show-student-dropdown');
    }


    public function clearSelectedStudent()
    {
        $this->shippingData['student_id'] = null;
        $this->searchStudent = '';
        $this->shippingData['nomor_induk_santri'] = '';
        $this->shippingData['nama_wali'] = '';
        $this->filteredStudents = Student::inRandomOrder()->limit(10)->get()->toArray();
    }


    public function selectStudent($id, $name): void
    {
        $this->shippingData['student_id'] = $id;
        $this->searchStudent = $name;
        $this->showStudentDropdown = false;  // Sembunyikan dropdown setelah memilih

        $student = Student::find($id);
        $this->shippingData['nomor_induk_santri'] = $student?->nomor_induk_santri ?? '';
        $this->shippingData['nama_wali'] = $student?->nama_wali_santri ?? '';
    }

    public function render()
    {
        if ($this->carts->isEmpty()) {
            return redirect()->route('home');
        }
        return view('livewire.checkout')
            ->layout('components.layouts.app', ['hideBottomNav' => true]);
    }

    public function createOrder()
    {
        $this->validate();
        if (!$this->carts->isEmpty()) {
            try {
                $user = Auth::user();
                $inputPhone = $this->shippingData['phone'];

                if (empty($user->phone_number) || $user->phone_number !== $inputPhone) {
                    $user->update(['phone_number' => $inputPhone]);
                }

                $order = Order::create([
                    'user_id' => auth()->id(),
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'subtotal' => $this->total,
                    'total_amount' => $this->total + $this->shippingCost,
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                    'student_id' => $this->shippingData['student_id'],
                    'nama_wali' => $this->shippingData['nama_wali'],
                    'phone' => $this->shippingData['phone'],
                    'notes' => $this->shippingData['notes']
                ]);

                foreach ($this->carts as $cart) {
                    $order->items()->create([
                        'product_id' => $cart->product_id,
                        'product_name' => $cart->product->name,
                        'quantity' => $cart->quantity,
                        'price' => $cart->product->price
                    ]);
                }

                Cart::where('user_id', auth()->id())->delete();

                // Notification::route('mail', $this->store->email_notification)->notify(new NewOrderNotification($order));

                if ($this->store->is_use_payment_gateway) {
                    $result = $this->midtrans->createTransaction($order, $order->items);

                    if (!$result['success']) {
                        throw new \Exception($result['message']);
                    }

                    $order->update(['payment_gateway_transaction_id' => $result['token']]);

                    $this->dispatch('payment-success', [
                        'payment_gateway_transaction_id' => $result['token'],
                        'order_id' => $order->order_number
                    ]);
                } else {
                    $admin = User::role(['owner_tenant', 'pengelola_web'])->get();
                    $title = "Ada pesanan baru dari wali santri: {$order->user->name}";
                    $body = "Untuk santri: {$order->student->nama_santri}";

                    Notification::make()
                        ->title($title)
                        ->body($body)
                        ->actions([
                            Action::make('view')
                                ->label('Lihat')
                                ->url(fn() => route('filament.pengelola.resources.pesanan.index'))
                                ->button()
                                ->markAsRead(),
                        ])
                        ->sendToDatabase($admin);

                    // return redirect()->route('order-detail', ['orderNumber' => $order->order_number]);
                    $this->redirectRoute('order-detail', ['orderNumber' => $order->order_number], navigate: true);
                }
            } catch (\Exception $e) {
                $this->dispatch('showAlert', [
                    'message' => $e->getMessage(),
                    'type' => 'error'
                ]);
            }
        } else {
            $this->dispatch('showAlert', [
                'message' => 'Keranjang belanja kosong',
                'type' => 'error'
            ]);
        }
    }
}
