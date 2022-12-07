<?php

namespace App\Helpers;

use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use App\Traits\HasErrorCollection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

final class FileHelper
{
    use HasErrorCollection;

    /**
     * @var File
     */
    public File $fileInstance;

    /**
     * @param UploadedFile|null $file
     * @param Authenticatable $authModel
     */
    public function __construct(protected UploadedFile|null $file, protected Authenticatable $authModel)
    {
    }

    /**
     * @param Folder|null $folder
     * @param string|null $deleteAt
     * @return bool
     */
    public function storeFile(Folder $folder = null, string $deleteAt = null, $newName){

        if($this->authModel instanceof User && $this->file instanceof UploadedFile){
            $this->fileInstance = new File();
            $this->fileInstance->original_name = $this->file->getClientOriginalName();

            $newFilePath = is_null($folder) ?  storage_path('app/public/'.$this->authModel->base_path) :  storage_path('app/public/'.$this->authModel->base_path).'/'.$folder->name;

            $name = Str::random();
            $size = $this->file->getSize();
            if( ($this->authModel->total_size + $size) > User::MAX_TOTAL_SIZE){
                $this->addError($this->fileInstance, 'File size is above total limits per user');
                return false;
            }
            $uploadedFile = $this->file->move($newFilePath,$name.'.'.$this->file->guessExtension());



            $this->authModel->total_size += $size;
            $this->fileInstance->file_name = $name.'.'.$uploadedFile->guessExtension();
	        $this->fileInstance->name = $newName;
            $this->fileInstance->size = $size;
	        $this->fileInstance->model_id = $this->authModel->id;
            $this->fileInstance->model_type = $this->authModel->getMorphClass();
            $this->fileInstance->mime_type = $uploadedFile->getMimeType();
            $this->fileInstance->extension = $uploadedFile->guessExtension();
            $this->fileInstance->disk = 'public';
            $this->fileInstance->path = $uploadedFile->getPath();
            $this->fileInstance->uuid = Str::uuid();
            if(!is_null($deleteAt)){
                $this->fileInstance->delete_at = Carbon::parse($deleteAt)->toDateTimeString();

            }

            if($folder){
                $folder->size += $size;
                $folder->save();
                $this->fileInstance->folder_id = $folder->id;
            }

            return $this->fileInstance->save() && $this->authModel->save();
        }

        $this->addError($this->authModel, 'Model is not Authenticatable');
        return false;
    }


    public function updateFile(File $file, $fileData){

        $bool = true;
        if($this->authModel instanceof User){
            if(array_key_exists('folder_id', $fileData) && $file->folder_id !== $fileData['folder_id']){
                $oldFolderId = $file->folder_id;
                $oldFolder = Folder::find($file->folder_id);

                $file->folder_id = $fileData['folder_id'];

                $file->save();

                if(($oldFolder && !$oldFolder->getSizeOfFiles()) && (!is_null($file->folder_id) && !$file->folder->getSizeOfFiles())){
                    $folders = $this->authModel->folder;
                    foreach($folders as $folder){
                        $folder->getSizeOfFiles();
                    }
                    $file->folder_id = $oldFolderId;
                    $this->addError($oldFolder, 'folder could not be changed');
                    return false;
                }

		if(is_null($file->folder_id)){
			$path = storage_path('app/public/'.$this->authModel->base_path);
			$bool = exec('mv ' . $file->path.'/'.$file->file_name. ' '. $path.'/'.$file->file_name);
			$file->path = $path;
			$oldFolder?->getSizeOfFiles();
		} else{
			$path = $file->folder->path;
                	$bool = exec('mv ' . $file->path.'/'.$file->file_name. ' '. $path.'/'.$file->file_name);
			$file->path = $path;
			$file->folder->getSizeOfFiles();
			if($oldFolder){
			    $oldFolder->getSizeOfFiles();
			}
		}
            }

            if(array_key_exists('delete_at', $fileData)){
                $file->delete_at = Carbon::parse($fileData['delete_at'])->toDateTimeString();
            }

            if(array_key_exists('name', $fileData)){
                $file->name = $fileData['name'];
            }
        }

	$file->save();
        $this->fileInstance = $file;
        return !$bool;
    }
}
