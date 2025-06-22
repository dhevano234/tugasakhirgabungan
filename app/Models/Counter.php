<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Service;
use App\Models\Queue;

class Counter extends Model
{
    protected $fillable = ['name', 'service_id', 'is_active'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    public function activeQueue()
    {
        return $this->hasOne(Queue::class)->whereIn('status', ['waiting','serving']);
    }

    public function getHasNextQueueAttribute()
    {
        return Queue::where('service_id', $this->service_id)
            ->where('status', 'waiting')
            ->where(function ($query) {
                $query->where('counter_id', $this->id)
                      ->orWhereNull('counter_id');
            })->exists() && $this->is_available;
    }

    public function getIsAvailableAttribute()
    {
        $hasServingQueue = $this->queues()->where('status', 'serving')->exists();

        return !$hasServingQueue && $this->is_active;
    }
}
