<?php

namespace App\Models;

use finfo;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\FileHelpers;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory, FileHelpers;

    public $fileContents;

    protected $table = 'files';

    protected $fillable = [
        'uuid',
        'model_id',
        'model_type',
        'name',
        'file_name',
        'extension',
        'disk',
        'path',
        'folder_id',
        'size',
        'order_column',
        'delete_at',
        'name'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'delete_at' => 'datetime',
    ];

    public $timestamps = [
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
//        'path',
	    'disk',
    ];

    public function folder(){
        return $this->belongsTo(Folder::class, 'folder_id');
    }

    /**
     * @return MorphTo
     */
    public function fileable(): MorphTo
    {
        return $this->morphTo('model');
    }

    /**
     * @param int $sizeInBytes
     * @return string
     */
    public static function getHumanReadableSize(int $sizeInBytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        if ($sizeInBytes == 0) {
            return '0 '.$units[1];
        }

        for ($i = 0; $sizeInBytes > 1024; $i++) {
            $sizeInBytes /= 1024;
        }

        return round($sizeInBytes, 2).' '.$units[$i];
    }

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return (new Finfo(FILEINFO_MIME_TYPE))->file($this->path);
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return Storage::get($this->path);
    }


    /**
     * @param ...$arg
     * @return string
     */
    public function getWithSlashes(...$arg): string
    {
        return implode('/', $arg);
    }
}
