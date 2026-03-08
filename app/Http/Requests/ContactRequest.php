<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|min:2|max:50',
            'email' => ['required', 'min:5', 'max:100', 'email'],
            'subject' => ['required', 'min:5', 'max:100'],
            'message' => ['required', 'min:5', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Пожалуйста, введите имя',
            'email.required' => 'Пожалуйста, введите email',
            'subject.required' => 'Пожалуйста, введите тему',
            'message.required' => 'Пожалуйста, введите сообщение',
            'name.min' => 'Имя должно быть не менее 2 символов',
            'name.max' => 'Имя должно быть не более 100 символов',
            'email.min' => 'Email должен быть не менее 5 символов',
            'email.max' => 'Email должен быть не более 100 символов',
            'email.email' => 'Ваш Email не похож на почту',
            'subject.min' => 'Тема сообщения должна быть не менее 5 символов',
            'subject.max' => 'Тема сообщения должна быть не более 100 символов',
            'message.min' => 'Сообщение должно быть не менее 5 символов',
            'message.max' => 'Сообщение должно быть не более 10000 символов',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name),
            'email' => trim($this->email),
            'subject' => trim($this->subject),
            'message' => trim($this->message),
        ]);
    }
}
