<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'status'         => $this->status,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'subtotal'       => $this->subtotal,
            'shipping_cost'  => $this->shipping_cost,
            'discount'       => $this->discount,
            'total'          => $this->total,
            'notes'          => $this->notes,
            'is_cancellable' => $this->isCancellable(),
            'shipping_address' => [
                'full_name'   => $this->shipping_full_name,
                'phone'       => $this->shipping_phone,
                'street'      => $this->shipping_street,
                'city'        => $this->shipping_city,
                'country'     => $this->shipping_country,
                'postal_code' => $this->shipping_postal_code,
            ],
            'items'      => OrderItemResource::collection($this->whenLoaded('items')),
            'user'       => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at->toDateString(),
        ];
    }
}