<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Classes\HateoasClass;
use \App\Hateoas\Books;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'author' => $this->author,
            'publish_date' => $this->publish_date,
        ];

        $links = new HateoasClass(new Books($this->id));
        
        return $links->getLinks() ? array_merge($data, $links->getLinks()) : $data;
    }
}
