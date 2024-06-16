<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return 
        [
            'id'=>$this->id,
            'post'=>$this->post,
            'user'=>$this->user,
            'content'=>$this->content,
            'image'=>$this->img

        ];
    }
}
