<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User as UserResource;
use App\Tweet as TweetModel;

class Tweet extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'body' => $this->body,
            'user' => new UserResource(TweetModel::find($this->id)->user),
            'created_at' => $this->created_at
        ];
    }
}
