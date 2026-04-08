<?php

if (! function_exists('form_render_attributes')) {
    function form_render_attributes(array $attrs = []): string
    {
        $html = '';

        foreach ($attrs as $key => $val) {
            if (is_bool($val)) {
                if ($val) {
                    $html .= ' ' . esc($key);
                }
            } elseif ($val !== null) {
                $html .= ' ' . esc($key) . '="' . esc((string) $val) . '"';
            }
        }

        return $html;
    }
}