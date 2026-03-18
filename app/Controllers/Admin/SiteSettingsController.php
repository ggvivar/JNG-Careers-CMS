<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class SiteSettingsController extends BaseController
{
    public function index()
    {
        $model = new SettingModel();
        // var_dump("deym");
        // die();
        $selectedLanguage = trim((string) ($this->request->getGet('key_1') ?? 'en'));

        $settings = $model->where('key_1', $selectedLanguage)
            ->orderBy('setting_key', 'ASC')
            ->findAll();

        $languageRows = $model->select('key_1')
            ->distinct()
            ->orderBy('key_1', 'ASC')
            ->findAll();

        $languageOptions = [];
        foreach ($languageRows as $row) {
            $languageOptions[] = $row['key_1'];
        }

        if (! in_array($selectedLanguage, $languageOptions, true)) {
            $languageOptions[] = $selectedLanguage;
            sort($languageOptions);
        }

        return view('admin/site_settings/index', [
            'settings' => $settings,
            'selectedLanguage' => $selectedLanguage,
            'languageOptions' => $languageOptions,
        ]);
    }

    public function save()
    {
        $model = new SettingModel();

        $rows = $this->request->getPost('rows') ?? [];
        $languageCode = trim((string) ($this->request->getPost('language_code') ?? 'en'));
        $now = date('Y-m-d H:i:s');

        foreach ($rows as $row) {
            $id = (int) ($row['id'] ?? 0);

            if ($id <= 0) {
                continue;
            }

            $model->update($id, [
                'setting_value' => (string) ($row['setting_value'] ?? ''),
                'setting_type' => (string) ($row['setting_type'] ?? 'string'),
                'autoload' => (int) ($row['autoload'] ?? 1),
                'description' => (string) ($row['description'] ?? ''),
                'key_1' => $languageCode,
                // 'key_2' => $languageCode,
                // 'key_3' => $languageCode,
                'date_updated' => $now,
            ]);
        }

        return redirect()->to('/admin/site-settings?language_code=' . urlencode($languageCode))
            ->with('success', 'Settings updated.');
    }
}