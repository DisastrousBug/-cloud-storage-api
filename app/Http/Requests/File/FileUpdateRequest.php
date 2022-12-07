<?php

namespace App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class FileUpdateRequest extends FormRequest
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
            'folder_id' => 'nullable|integer',
            'delete_at' => 'string|nullable', //can use after:now() to validate
            'name' => [
		'string',
                Rule::unique('files')->where(fn ($query) => $query->where('model_id', Auth::user()?->getAuthIdentifier())->where('folder_id', $this->folder_id)
				->where('id', '!=', $this->file->id)),
		'required'
            ]
        ];
    }
}
