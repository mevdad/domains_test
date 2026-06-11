<?php

namespace App\Http\Requests\Settings;

use App\Concerns\ProfileValidationRules;
use App\Contracts\DomainNotificationChannel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    use ProfileValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $channelRules = [];

        foreach (app()->tagged('domain-notification-channels') as $channel) {
            /** @var DomainNotificationChannel $channel */
            $name = $channel->channelName();
            $channelRules["notification_settings.{$name}.enabled"] = ['boolean'];

            foreach ($channel->validationRules() as $field => $rules) {
                $channelRules["notification_settings.{$name}.{$field}"] = $rules;
            }
        }

        return array_merge($this->profileRules($this->user()->id), [
            'notification_settings' => ['nullable', 'array'],
            ...$channelRules,
        ]);
    }
}
