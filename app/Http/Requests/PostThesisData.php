<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostThesisData extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'double_degree' => 'bool|required',
            'semester' => 'int|required',
            'study_mode' => 'int|required',
        ];
    }
}
