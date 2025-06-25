<?php
// File: app/Services/QueueService.php

namespace App\Services;

use App\Models\Counter;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class QueueService
{
    public function addQueue($serviceId, $userId = null)
    {
        $number = $this->generateNumber($serviceId);
        
        // Gunakan user yang sedang login jika userId tidak diberikan
        $userId = $userId ?? Auth::id();

        return Queue::create([
            'service_id' => $serviceId,
            'user_id' => $userId, // GUNAKAN user_id, bukan patient_id
            'number' => $number,
            'status' => 'waiting',
        ]);
    }

    public function generateNumber($serviceId)
    {
        $service = Service::findOrFail($serviceId);

        $lastQueue = Queue::where('service_id', $serviceId)
            ->whereDate('created_at', today())
            ->orderByDesc('id')
            ->first();

        $currentDate = now()->format('Y-m-d');
        $lastQueueDate = $lastQueue ? $lastQueue->created_at->format('Y-m-d') : null;
        $isSameDate = $currentDate === $lastQueueDate;

        $lastQueueNumber = $lastQueue ? intval(
            substr($lastQueue->number, strlen($service->prefix))
        ) : 0;

        $maximumNumber = pow(10, $service->padding) - 1;
        $isMaximumNumber = $lastQueueNumber === $maximumNumber;

        if ($isSameDate && !$isMaximumNumber) {
            $newQueueNumber = $lastQueueNumber + 1;
        } else {
            $newQueueNumber = 1;
        }

        return $service->prefix . str_pad($newQueueNumber, $service->padding, "0", STR_PAD_LEFT);
    }

    public function callNextQueue($counterId)
    {
        $counter = Counter::findOrFail($counterId);

        $nextQueue = Queue::where('status', 'waiting')
            ->where('service_id', $counter->service_id)
            ->where(function ($query) use ($counterId) {
                $query->whereNull('counter_id')->orWhere('counter_id', $counterId);
            })
            ->whereDate('created_at', now()->format('Y-m-d'))
            ->orderBy('id')
            ->first();

        if ($nextQueue && !$nextQueue->counter_id) {
            $nextQueue->update([
                'counter_id' => $counterId,
                'called_at' => now()
            ]);
        }

        return $nextQueue;
    }
    
    public function serveQueue(Queue $queue)
    {
        if ($queue->status !== 'waiting') {
            return;
        }

        $queue->update([
            'status' => 'serving',
            'served_at' => now()
        ]);
    }

    public function finishQueue(Queue $queue)
    {
        if ($queue->status !== 'serving') {
            return;
        }

        $queue->update([
            'status' => 'finished',
            'finished_at' => now()
        ]);
    }

    public function cancelQueue(Queue $queue)
    {
        if (!in_array($queue->status, ['waiting', 'serving'])) {
            return;
        }

        $queue->update([
            'status' => 'canceled',
            'canceled_at' => now()
        ]);
    }
}