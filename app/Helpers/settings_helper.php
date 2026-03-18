<?php

use App\Models\SettingModel;

if (! function_exists('setting')) {

    function setting($key, $lang='en')
    {
        static $cache = [];

        $index = $key.'_'.$lang;

        if(isset($cache[$index])) {
            return $cache[$index];
        }

        $model = new SettingModel();

        $value = $model->getSetting($key,$lang);

        $cache[$index] = $value;

        return $value;
    }
}