<?php

namespace App\Observers;

use App\Models\File;
use App\Models\User;

class FileObserver
{
    /**
     * Handle the File "created" event.
     *
     * @param  \App\Models\File  $file
     * @return void
     */
    public function created(File $file)
    {
        //
    }

    /**
     * Handle the File "updated" event.
     *
     * @param  \App\Models\File  $file
     * @return void
     */
    public function updated(File $file)
    {
        //
    }

    /**
     * Handle the File "deleted" event.
     *
     * @param  \App\Models\File  $file
     * @return void
     */
    public function deleted(File $file)
    {
        exec('rm ' . $file->path.'/'.$file->file_name);
        if($file->folder){
            $file->folder->size -= $file->folder->size > 0 ? $file->size : 0;
            $file->folder->save();
        }

        if($file->fileable instanceof User){
            $file->fileable->total_size -= $file->fileable->total_size > 0 ? $file->size : 0;
            $file->fileable->save();
        }
    }

    /**
     * Handle the File "retored" event.
     *
     * @param  \App\Models\File  $file
     * @return void
     */
    public function restored(File $file)
    {
        //
    }

    /**
     * Handle the File "force deleted" event.
     *
     * @param  \App\Models\File  $file
     * @return void
     */
    public function forceDeleted(File $file)
    {
        //
    }
}
