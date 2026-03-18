<?php

if (! function_exists('render_placeholders')) {
    function render_placeholders(?string $template, array $data = []): string
    {
        $output = (string) $template;

        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value)) {
                continue;
            }

            $output = str_replace('{{' . $key . '}}', (string) $value, $output);
        }
        return $output;
    }
}