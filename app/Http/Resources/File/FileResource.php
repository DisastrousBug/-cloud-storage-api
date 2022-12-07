<?php

namespace App\Http\Resources\File;

use App\Http\Resources\Folder\FolderShortResource;
use App\Http\Resources\User\UserShortResource;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
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
            'name' => $this->name,
            'extension' => $this->extension,
            'size' => File::getHumanReadableSize($this->size),
            'order_column' => $this->order_column,
            'delete_at' => $this->delete_at,
            'folder' => FolderShortResource::make($this->folder),
            'user' => ($this->fileable instanceof User )? UserShortResource::make($this->fileable) : []
        ];
    }
}
