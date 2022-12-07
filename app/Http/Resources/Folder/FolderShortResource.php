<?php

namespace App\Http\Resources\Folder;

use App\Http\Resources\File\FileCollection;
use App\Http\Resources\User\UserResource;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class FolderShortResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'model_type' => 'folder',
            'name' => $this->name,
            'size' => !is_null($this->size) ? File::getHumanReadableSize($this->size) : File::getHumanReadableSize($this->getSizeOfFiles()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user_id' => $this->user_id,
            'files' => new FileCollection($this->files)
        ];
    }
}
