<?php

namespace App\Http\Requests\Admin;

use App\Services\Auth\PasswordService;
use Illuminate\Foundation\Http\FormRequest;

class PostConfigurationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return PasswordService::check($this->password);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => [
                'required',
                'string',
            ],
            'config_file' => [
                'required',
                'mimes:xlsx',
            ]
        ];
    }
}
