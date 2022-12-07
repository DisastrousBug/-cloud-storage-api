<?php

namespace App\Helpers;

use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use App\Traits\HasErrorCollection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

final class FolderHelper
{
    use HasErrorCollection;

    /**
     * @var File
     */
    public Folder $folderInstance;

    public function __construct(protected Authenticatable $authModel)
    {
    }

    /**
     * @param $folderData
     * @return bool|void
     */
    public function storeFolder($folderData){
        if($this->authModel instanceof User){
            $this->folderInstance = new Folder();

            $this->folderInstance->name = $folderData['name'];
            $this->folderInstance->path = storage_path('app/public/'.$this->authModel->base_path).'/'.$this->folderInstance->name;

            exec('mkdir ' . $this->folderInstance->path);

            $this->folderInstance->user_id = $this->authModel->id;

            return $this->folderInstance->save();
        }
    }

    /**
     * @param Folder $folder
     * @param $folderData
     * @return bool
     */
    public function updateFolder(Folder $folder, $folderData): bool
    {
        if($this->authModel instanceof User){
            $this->folderInstance = $folder;

            if(array_key_exists('name', $folderData) && $folderData['name'] !== $this->folderInstance->name){
                $this->folderInstance->name = $folderData['name'];
            }

            return $this->folderInstance->save();
        }

        $this->addError($this->authModel,'Model is not User');
        return false;
    }
}
