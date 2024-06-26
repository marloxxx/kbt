<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'image' => $this->image ? asset('images/drivers/' . $this->image) : null,
            'car' => $this->whenLoaded('car', function () {
                return new CarResource($this->car);
            }),
            'links' => $this->links,
        ];
    }
}
