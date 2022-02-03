<?php

namespace App\Http\Requests\User;

use App\Models\User;
use App\Support\PremiumKeyVerifier;

use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest
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
           
            'title'=> 'max:190|email|unique:users,email,' . auth()->id(),
            'description' => ['required', 'string', 'max:255'],
            'source' => ['required', 'string', 'max:255'],
            
        ];
    }
}