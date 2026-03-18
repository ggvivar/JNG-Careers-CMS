<?php

namespace App\Libraries;

use App\Models\MessageTemplateModel;

class MessageTemplateRenderer
{
    public static function renderByKey(string $templateKey, array $data = [], ?string $channel = null): ?array
    {
        $model = new MessageTemplateModel();
     
        $builder = $model->where('template_key', trim($templateKey))
            ->where('date_deleted', null);

        if ($channel !== null && $channel !== '') {
            $builder->where('channel', trim($channel));
        }

        $template = $builder->first();
        // dd(self::renderTemplate($template, $data));
        if (! $template) {
            return null;
        }

        return self::renderTemplate($template, $data);
    }

    public static function renderTemplate(array $template, array $data = []): array
    {
        helper('template');

        $subject = render_placeholders((string) ($template['subject'] ?? ''), $data);
        $body = render_placeholders((string) ($template['body_template'] ?? ''), $data);
        // dd($body);

        return [
            'id' => $template['id'] ?? null,
            'name' => $template['name'] ?? null,
            'template_key' => $template['template_key'] ?? null,
            'channel' => $template['channel'] ?? 'email',
            'subject' => $subject,
            'body' => $body,
            'available_vars' => self::parseAvailableVars($template['available_vars'] ?? ''),
            'raw' => $template,
        ];
    }

    public static function parseAvailableVars(?string $availableVars): array
    {
        $raw = trim((string) $availableVars);
        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return array_values(array_unique(array_filter(array_map('trim', $decoded))));
        }

        return array_values(array_unique(array_filter(array_map('trim', explode(',', $raw)))));
    }
}