<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryTreeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => data_get($this->resource, 'id'),
            'name' => data_get($this->resource, 'name'),

            'children' => self::collection(
                collect(data_get($this->resource, 'children', []))
            ),
        ];

    }
}
