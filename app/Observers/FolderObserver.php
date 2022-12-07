<?php

namespace App\Observers;

use App\Helpers\FileHelper;
use App\Models\Folder;

class FolderObserver
{
    /**
     * Handle the Folder "created" event.
     *
     * @param  \App\Models\Folder  $folder
     * @return void
     */
    public function created(Folder $folder)
    {
        //
    }

    /**
     * Handle the Folder "updated" event.
     *
     * @param  \App\Models\Folder  $folder
     * @return void
     */
    public function updated(Folder $folder)
    {
        //
    }

    /**
     * Handle the Folder "deleted" event.
     *
     * @param  \App\Models\Folder  $folder
     * @return void
     */
    public function deleted(Folder $folder)
    {
        $files = $folder->files;
        $fileHelper = new FileHelper(null, $folder->user);

        foreach($files as $file){
            $data['folder_id'] = null;

            $res = $fileHelper->updateFile($file, $data);
        }

        exec('rm -rf ' . $folder->path);
    }

    /**
     * Handle the Folder "restored" event.
     *
     * @param  \App\Models\Folder  $folder
     * @return void
     */
    public function restored(Folder $folder)
    {
        //
    }

    /**
     * Handle the Folder "force deleted" event.
     *
     * @param  \App\Models\Folder  $folder
     * @return void
     */
    public function forceDeleted(Folder $folder)
    {
        //
    }
}
