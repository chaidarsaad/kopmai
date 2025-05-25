<?php

namespace App\Livewire;

use App\Models\Request;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateRequest extends Component
{
    public $createRequestData = [
        'tanggal_permohonan' => '',
        'nama_pemesan' => '',
        'kelas_divisi' => '',
        'nama_barang' => '',
        'jumlah_barang' => '',
        'tujuan' => '',
        'sumber_dana' => '',
        'budget' => '',
        'deadline' => '',
    ];

    public $rules = [
        'createRequestData.tanggal_permohonan' => 'required|date',
        'createRequestData.nama_pemesan' => 'required|string|max:255',
        'createRequestData.kelas_divisi' => 'required|string|max:255',
        'createRequestData.nama_barang' => 'required|string|max:255',
        'createRequestData.jumlah_barang' => 'required|string|max:255',
        'createRequestData.tujuan' => 'required|string|max:500',
        'createRequestData.sumber_dana' => 'required|string|max:255',
        'createRequestData.budget' => 'required|numeric|min:1',
        'createRequestData.deadline' => 'required|date|after_or_equal:createRequestData.tanggal_permohonan',
    ];

    public $messages = [
        'createRequestData.tanggal_permohonan.required' => 'Tanggal permohonan wajib diisi.',
        'createRequestData.nama_pemesan.required' => 'Nama pemesan wajib diisi.',
        'createRequestData.kelas_divisi.required' => 'Kelas/Divisi wajib diisi.',
        'createRequestData.nama_barang.required' => 'Nama barang wajib diisi.',
        'createRequestData.jumlah_barang.required' => 'Jumlah barang wajib diisi.',
        'createRequestData.tujuan.required' => 'Tujuan wajib diisi.',
        'createRequestData.sumber_dana.required' => 'Sumber dana wajib diisi.',
        'createRequestData.budget.required' => 'Budget wajib diisi.',
        'createRequestData.budget.numeric' => 'Budget harus berupa angka.',
        'createRequestData.budget.min' => 'Budget tidak boleh kurang dari 0.',
        'createRequestData.deadline.required' => 'Deadline wajib diisi.',
        'createRequestData.deadline.date' => 'Deadline harus berupa tanggal yang valid.',
        'createRequestData.deadline.after_or_equal' => 'Deadline harus setelah atau sama dengan tanggal permohonan.',
    ];

    public function createRequest()
    {
        $this->validate();

        Request::create([
            'request_number' => 'REQ-' . strtoupper(uniqid()),
            'user_id' => Auth::id(),
            'tanggal_permohonan' => $this->createRequestData['tanggal_permohonan'],
            'nama_pemesan' => $this->createRequestData['nama_pemesan'],
            'kelas_divisi' => $this->createRequestData['kelas_divisi'],
            'nama_barang' => $this->createRequestData['nama_barang'],
            'jumlah_barang' => $this->createRequestData['jumlah_barang'],
            'tujuan' => $this->createRequestData['tujuan'],
            'sumber_dana' => $this->createRequestData['sumber_dana'],
            'budget' => $this->createRequestData['budget'],
            'deadline' => $this->createRequestData['deadline'],
            'status' => 'Menunggu Verifikasi',
        ]);

        $this->reset('createRequestData');

        $admins = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['super_admin', 'pengelola_web']);
        })->get();

        Notification::make()
            ->title('Ada permohonan baru dari: ' . Auth::user()->name)
            ->actions([
                Action::make('view')
                    ->label('Lihat')
                    ->url(fn() => route('filament.pengelola.resources.permohonan.index'))
                    ->button()
                    ->markAsRead(),
            ])
            ->sendToDatabase($admins);


        session()->flash('alert_message', 'Permohonan berhasil disimpan!');
        session()->flash('alert_type', 'success');

        $this->redirectRoute('permohonan', navigate: true);
    }

    public function mount()
    {
        $user = Auth::user();
        $this->createRequestData['nama_pemesan'] = $user->name;
        $this->createRequestData['tanggal_permohonan'] = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.create-request')
            ->layout('components.layouts.app', ['hideBottomNav' => true]);
    }
}
