<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'text'=>$this->text,
            'image'=>$this->image,
            'video'=>$this->video,
            'user_id'=>$this->user_id,
            'tag'=>$this->users,
            'comments'=>$this->comment
        ];
    }
}
