<?php

namespace App\Http\Requests\Announcement;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAnnouncementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $announcementId = $this->route('announcementId');
        
        return [
            'title' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('announcements')->ignore($announcementId),
            ],
            'content' => 'sometimes',
            'start_date' => 'sometimes|date|before_or_equal:end_date',
            'end_date' => 'sometimes|date|after_or_equal:start_date'
        ];
    }
}
