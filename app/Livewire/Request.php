<?php

namespace App\Livewire;

use App\Services\RequestStatusService;
use Livewire\Component;

class Request extends Component
{
    public function getStatusClass($status)
    {
        return RequestStatusService::getStatusColor($status);
    }

    public function getRequests()
    {
        return auth()->user()->requests()
            ->with('user')
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function render()
    {
        return view('livewire.request', [
            'requests' => $this->getRequests(),
            'statusLabels' => array_combine(
                [
                    RequestStatusService::STATUS_WAITING,
                    RequestStatusService::STATUS_PROCESSING,
                    RequestStatusService::STATUS_COMPLETED,
                    RequestStatusService::STATUS_REJECTED
                ],
                array_map(
                    fn($status) => RequestStatusService::getStatusLabel($status),
                    [
                        RequestStatusService::STATUS_WAITING,
                        RequestStatusService::STATUS_PROCESSING,
                        RequestStatusService::STATUS_COMPLETED,
                        RequestStatusService::STATUS_REJECTED
                    ]
                )
            )
        ])->layout('components.layouts.app', ['hideBottomNav' => true]);
    }
}
