<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\File\FileStoreRequest;
use App\Http\Requests\File\FileUpdateRequest;
use App\Http\Resources\File\FileCollection;
use App\Http\Resources\File\FileResource;
use App\Http\Resources\Folder\FolderCollection;
use App\Http\Resources\Folder\FolderResource;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    /**
     * @param Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function index(Request $request)
    {
        $sortBy = $request->string('sort_by', 'name');
        $sortOrder = $request->string('sort_order', 'desc');
        $folderId = $request->integer('folder_id', null);
        $treeView = $request->boolean('tree_view', false);
        $perPage = $request->input('per_page', $this->perPage);

        $user = $request->user();

        if(!$treeView){
            return new FileCollection($user->files()->paginate($perPage));
        }

        $files = $user->files()->where('folder_id', null)->get();
        $folders = $user->folders()->get();

        //return (new FolderCollection($folders))->additional(['first_depth_files' => (new FileCollection($files))->toArray($request)]);

        return array_merge((new FolderCollection($folders))->toArray($request),(new FileCollection($files))->toArray($request) );
    }

    /**
     * @param FileStoreRequest $request
     * @return FileResource|Application|ResponseFactory|Response
     */
    public function store(FileStoreRequest $request): Response|FileResource|Application|ResponseFactory
    {
        $user = $request->user();

        $data = $request->validated();

        $folder = null;
        if(array_key_exists('folder_id', $data) && !is_null($data['folder_id'])){
            $folder = Folder::find($data['folder_id']);
            if(is_null($folder) || ($folder->user_id !== $user->id)){
                return response("Folder does not exists or doesn't belong to current User", 400);
            }
	}

        $fileHelper = new FileHelper($data['file'], $user);

        $res = $fileHelper->storeFile($folder, $data['delete_at'] ?? null,  $data['name']);
        if(!$res){
            return response($fileHelper->error, 400);
        }

        return FileResource::make($fileHelper->fileInstance);
    }

    /**
     * @param File $file
     * @param Request $request
     * @return FileResource
     */
    public function show(File $file, Request $request): FileResource
    {
        return FileResource::make($file);
    }

    /**
     * @param File $file
     * @param FileUpdateRequest $request
     * @return Response|FileResource|Application|ResponseFactory
     */
    public function update(File $file, FileUpdateRequest $request): Response|FileResource|Application|ResponseFactory
    {
        $user = $request->user();

        $data = $request->validated();
	$data =  $validated = $request->safe()->only(['folder_id', 'name','delete_at']);

        if($file->model_id !== $user->id){
            return response('Forbidden', 403);
        }

        $fileHelper = new FileHelper(null, $user);

	$data['folder_id'] = $request->input('folder_id');

        $res = $fileHelper->updateFile($file, $data);

        if(!$res){
            return response($fileHelper->error, 400);
        }

        return FileResource::make($fileHelper->fileInstance);
    }

    /**
     * @param File $file
     * @param Request $request
     * @return Response|BinaryFileResponse|Application|ResponseFactory
     */
    public function download(File $file, Request $request): Response|\Symfony\Component\HttpFoundation\BinaryFileResponse|Application|ResponseFactory
    {
        $user = $request->user();

        if($file->model_id !== $user->id){
            return response('Forbidden', 403);
        }

        return response()->download($file->path.'/'.$file->file_name);
    }

    /**
     * @param File $file
     * @param string $hash
     * @param Request $request
     * @return Response|BinaryFileResponse|Application|ResponseFactory
     */
    public function publicDownload(File $file, string $hash, Request $request): Response|BinaryFileResponse|Application|ResponseFactory
    {
        $testHash = hash("md5",$file->fileable->id.$file->uuid);

        if($testHash !== $hash){
            return response('Wrong link, try again with another one', 400);
        }

        return response()->download($file->path.'/'.$file->file_name);
    }

    /**
     * @param File $file
     * @param Request $request
     * @return Response|Application|ResponseFactory
     */
    public function destroy(File $file, Request $request): Response|Application|ResponseFactory
    {
        $user = $request->user();

        if(!$user && $file->user_id !== $user->id){
            return response('Forbidden', 403);
        }

        return $file->delete() ? response('File was deleted', 200) : response('Bad request', 400);
    }

    /**
     * @param File $file
     * @param Request $request
     * @return Response|Application|ResponseFactory
     */
    public function generatePublicLink(File $file, Request $request): Response|Application|ResponseFactory
    {
        $user = $request->user();

        $hash = hash("md5",$user->id.$file->uuid);

        return response([ 'link' => route('files.public.link', ['file' => $file->uuid, 'hash' => $hash])], 200);
    }
}
