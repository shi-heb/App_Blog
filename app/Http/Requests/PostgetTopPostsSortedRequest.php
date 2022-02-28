<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostgetTopPostsSortedRequest extends FormRequest
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
            'type' => ['required', 'integer', 'between:0,3'],
            'value' => ['required', 'integer', 'between:0,1'],
        ];
    }

    public function messages()
    {
        return [
            "type.required" => "vous devez specifier la periode ",
            "value.required" => "vous devez specifier la periode courante ou precedente",
        ];
    }
}
