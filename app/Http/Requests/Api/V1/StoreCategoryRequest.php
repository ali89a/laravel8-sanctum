<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'name' => ['required'],
            'parent_id' => ['nullable'],
            'created_by' => ['required'],
        ];

    }
    public function prepareForValidation()
    {

        $this->merge([
            'created_by' => authUser(true),
        ]);
    }
    public function messages()
    {
        return [
            'name.required' => 'Name Cannot be Empty',
        ];
    }
}
