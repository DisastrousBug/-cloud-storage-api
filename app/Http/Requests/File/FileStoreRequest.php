<?php

namespace App\Http\Requests\File;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FileStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => 'required|file|max:20480',
            'folder_id' => 'nullable|integer',
            'delete_at' => 'string|nullable', //can use after:now() to validate
            'name' => [
                'string',
                Rule::unique('files')->where(fn ($query) => $query->where('model_id', Auth::user()?->getAuthIdentifier())->where('folder_id', $this->folder_id)),
            ]
        ];
    }
}
