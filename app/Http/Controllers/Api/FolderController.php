<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FolderHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Folder\FolderStoreRequest;
use App\Http\Requests\Folder\FolderUpdateRequest;
use App\Http\Resources\Folder\FolderCollection;
use App\Http\Resources\Folder\FolderResource;
use App\Models\Folder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $sortBy = $request->string('sort_by', '');
        $sortOrder = $request->string('sort_order', 'desc');

        $user = $request->user();

        return FolderCollection::collection($user->folders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FolderStoreRequest $request
     * @return FolderResource|Application|ResponseFactory|Response
     */
    public function store(FolderStoreRequest $request): Response|Application|ResponseFactory|FolderResource
    {
        $user = $request->user();

        $data = $request->validated();

        $folderHelper = new FolderHelper($user);

        $res = $folderHelper->storeFolder($data);

        if(!$res){
            return response($folderHelper->error, 400);
        }

        return FolderResource::make($folderHelper->folderInstance);
    }

    /**
     * Display the specified resource.
     *
     * @param Folder $folder
     * @return FolderResource|Response
     */
    public function show(Folder $folder, Request $request)
    {
        $user = $request->user();

        if($folder->user_id !== $user->id){
            return response('Forbidden', 403);
        }

        return FolderResource::make($folder);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Folder $folder
     * @param FolderUpdateRequest $request
     * @return FolderResource|Application|ResponseFactory|Response
     */
    public function update(Folder $folder, FolderUpdateRequest $request)
    {
        $user = $request->user();

        $data = $request->validated();

        $folderHelper = new FolderHelper($user);

        $res = $folderHelper->updateFolder($data);

        if(!$res){
            return response($folderHelper->error, 400);
        }

        return FolderResource::make($folderHelper->folderInstance);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Folder $folder
     * @param Request $request
     * @return bool|Response
     */
    public function destroy(Folder $folder, Request $request): Response|bool
    {
        $user = $request->user();

        if($folder->user_id !== $user->id){
            return response('Forbidden', 403);
        }

        return $folder->delete();
    }
}
