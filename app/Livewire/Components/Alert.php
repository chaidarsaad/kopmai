<?php

namespace App\Livewire\Components;

use Livewire\Component;

class Alert extends Component
{
    public $message = '';
    public $type = 'success';
    public $show = false;

    protected $listeners = ['showAlert'];

    public function mount()
    {
        if (session()->has('alert_message')) {
            $this->message = session('alert_message');
            $this->type = session('alert_type', 'info');
            $this->show = true;

            // Delay hide langsung dari Livewire
            $this->dispatch('hideAlert')->self();
        }
    }

    public function showAlert($params)
    {
        $this->message = $params['message'] ?? '';
        $this->type = $params['type'] ?? 'success';
        $this->show = true;

        $this->dispatch('hideAlert');
    }

    public function render()
    {
        return view('livewire.components.alert');
    }
}
