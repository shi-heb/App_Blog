<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsergetTheMoreActifUsersRequest extends FormRequest
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
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
        ];
    }

    public function messages()
    {
        return [
            "start_date.required" => "vous devez specifier le debut periode ",
            "end_date.required" => "vous devez specifier la fin de la periode",
        ];
    }
}
