<?php

namespace App\Notifications;

use App\Models\Sale;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeliveryChallanAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Sale $sale)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $challanDetail = $this->sale->challanDetail;

        return [
            'title' => 'New Delivery Challan Assigned',
            'message' => sprintf(
                'Delivery challan %s has been assigned to you for %s.',
                $this->sale->bill_number ?? ('#' . $this->sale->id),
                $this->sale->party?->name ?? 'selected party'
            ),
            'sale_id' => $this->sale->id,
            'bill_number' => $this->sale->bill_number,
            'party_name' => $this->sale->party?->name,
            'warehouse_name' => $challanDetail?->warehouse_name,
            'destination' => $challanDetail?->destination,
            'vehicle_number' => $challanDetail?->vehicle_number,
            'url' => route('delivery-challan.edit', $this->sale),
        ];
    }
}
