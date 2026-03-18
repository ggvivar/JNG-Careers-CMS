<?php

namespace App\Libraries;

use App\Models\DocumentTemplateModel;

class DocumentTemplateRenderer
{
    public function renderByKey(string $templateKey, array $data = []): ?array
    {
        $model = new DocumentTemplateModel();

        $template = $model->where('template_key', trim($templateKey))
            ->where('date_deleted', null)
            ->first();

        if (! $template) {
            return null;
        }

        return $this->renderTemplate($template, $data);
    }

    public function renderTemplate(array $template, array $data = []): array
    {
        helper('template');

        $fileNamePattern = render_placeholders((string) ($template['file_name_pattern'] ?? ''), $data);

        return [
            'id' => $template['id'] ?? null,
            'name' => $template['name'] ?? null,
            'template_key' => $template['template_key'] ?? null,
            'template_type' => $template['template_type'] ?? 'docx',
            'file_name' => $this->sanitizeFileName($fileNamePattern),
            'source_file_path' => $template['source_file_path'] ?? null,
            'available_vars' => $this->parseAvailableVars($template['available_vars'] ?? ''),
            'raw' => $template,
        ];
    }

    public function parseAvailableVars(?string $availableVars): array
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

    private function sanitizeFileName(string $name): string
    {
        $name = trim($name);
        if ($name === '') {
            return 'document';
        }

        $name = preg_replace('/[\\\\\\/:"*?<>|]+/', '-', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return trim($name);
    }
}