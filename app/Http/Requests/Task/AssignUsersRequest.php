<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 認可は UseCase 側で行う
    }

    public function rules(): array
    {
        return [
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id'],
        ];
    }
}
