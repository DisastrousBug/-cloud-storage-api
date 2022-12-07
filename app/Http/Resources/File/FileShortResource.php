<?php

namespace App\Http\Resources\File;

use App\Http\Resources\Folder\FolderShortResource;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\UserShortResource;

class FileShortResource extends JsonResource
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
            'model_type' => 'file',
            'uuid' => $this->uuid,
            'file_name' => $this->file_name,
            'extension' => $this->extension,
            'name' => $this->name,
            'size' => File::getHumanReadableSize($this->size),
            'order_column' => $this->order_column,
            'delete_at' => $this->delete_at,
            'folder_id' => $this->folder_id,
//            'user' => ($this->fileable instanceof User )? UserShortResource::make($this->fileable) : []
	    'user_id' => $this->user_id
        ];
    }
}
