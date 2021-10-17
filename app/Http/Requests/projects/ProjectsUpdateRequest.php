<?php

namespace App\Http\Requests\projects;

use Illuminate\Foundation\Http\FormRequest;

class ProjectsUpdateRequest extends FormRequest
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
            "title" => "required",
            "description" => "required",
            "image" => "required"
        ];
    }

    public function messages()
    {
        return [
            "title.required" => "Titulo es requerido",
            "description.required" => "Descripción es requerida",
            "image.required" => "Imagen es requerida"
        ];
    }
}
