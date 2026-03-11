<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
        /**
         *             'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'occurence' => $this->occurence,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at

         */
    }
}
