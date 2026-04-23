<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $maxSize = config('app.max_upload_size', 10240); // KB

        return [
            'body'          => ['required', 'string', 'min:1', 'max:5000'],
            'attachments'   => ['nullable', 'array', 'max:4'],
            'attachments.*' => [
                'file',
                "max:{$maxSize}",
                'mimes:jpg,jpeg,png,gif,webp,mp4,mov,pdf,doc,docx,zip',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required'          => 'Please write something before posting.',
            'attachments.max'        => 'You can attach up to 4 files per post.',
            'attachments.*.max'      => 'Each file must be under 10MB.',
            'attachments.*.mimes'    => 'Allowed file types: images, videos, PDFs, Word docs, ZIP.',
        ];
    }
}
