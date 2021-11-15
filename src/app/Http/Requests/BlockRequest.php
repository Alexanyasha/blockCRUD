<?php

namespace Backpack\BlockCRUD\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:2|max:255',
            'slug' => 'required|min:2|max:255|unique:block_items,slug,' . $this->id,
            'type' => 'required',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Название обязательно для заполнения',
            'slug.required' => 'Обозначение обязательно для заполнения',
            'slug.unique'   => 'Обозначение уже занято',
            'type.required' => 'Тип обязателен для заполнения',
        ];
    }
}
