<?php

namespace App\Http\Resources\User;

use App\Models\File;
use App\Http\Resources\Folder\FolderCollection;
use App\Http\Resources\Folder\FolderShortResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'model_type' => 'user',
            'email' => $this->email,
            'total_size' => is_null($this->total_size) ? File::getHumanReadableSize($this->getTotalSizeOfFiles()) : File::getHumanReadableSize($this->total_size ?? 0),
            'name' => $this->name,
            'first_name' => $this->first_name,
            'last_name' => $this->first_name,
            'patronymic' => $this->patronymic,
            'folders' => new FolderCollection($this->folders),
        ];
    }
}
