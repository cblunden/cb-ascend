<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public static $wrap = false;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'authors' => AuthorResource::collection($this->authors),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
