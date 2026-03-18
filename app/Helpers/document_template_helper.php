<?php

use App\Models\DocumentTemplateModel;
use PhpOffice\PhpWord\TemplateProcessor;

if (! function_exists('render_document_template')) {

    function render_document_template(string $templateKey, array $vars): ?string
    {
        $model = new DocumentTemplateModel();

        $template = $model
            ->where('template_key', $templateKey)
            ->where('date_deleted', null)
            ->first();

        if (! $template) {
            log_message('error', 'Document template not found: ' . $templateKey);
            return null;
        }

        $templatePath = FCPATH . $template['source_file_path'];
        if (! file_exists($templatePath)) {
            log_message('error', 'Template file missing: ' . $templatePath);
            return null;
        }

        // generate output filename
        $fileName = 'generated_' . time() . '.docx';
        $outputDir = FCPATH . 'generated_docs/';

        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0775, true);
        }

        $outputPath = $outputDir . $fileName;

        $processor = new TemplateProcessor($templatePath);

        foreach ($vars as $key => $value) {
            $processor->setValue($key, $value);
        }
        dd($vars);
        $processor->saveAs($outputPath);

        return $outputPath;
    }

}