<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'date_time' => ['required', 'date_format:Y-m-d H:i:s'],
            'task_list_id' => ['required', 'integer']
        ];
    }
}
