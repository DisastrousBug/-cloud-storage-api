<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $table = 'folders';

    protected $fillable = [
        'name',
        'path',
        'user_id',
    ];

    protected $hidden = [
//        'path'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function files(){
        return $this->hasMany(File::class, 'folder_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return mixed
     */
    public function getSizeOfFiles(){
        $sizes = $this->files->pluck('size');
        $total = 0;
        foreach ($sizes as $size){
            $total += $size;
        }

        $this->size = $total;
        $this->save();
        return $this->save();
    }
}
