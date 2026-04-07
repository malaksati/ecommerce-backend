<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'slug'          => $this->slug,
            'description'   => $this->description,
            'price'         => $this->price,
            'sale_price'    => $this->sale_price,
            'current_price' => $this->current_price,  // uses accessor
            'is_on_sale'    => $this->is_on_sale,      // uses accessor
            'stock'         => $this->stock,
            'in_stock'      => $this->isInStock(),
            'sku'           => $this->sku,
            'is_featured'   => $this->is_featured,
            'category'      => new CategoryResource($this->whenLoaded('category')),
            'images'        => ProductImageResource::collection($this->whenLoaded('images')),
            'created_at'    => $this->created_at->toDateString(),
        ];
    }
}
