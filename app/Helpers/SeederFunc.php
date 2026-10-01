<?php

namespace App\Helpers;

class SeederFunc
{
    public static function loadData()
    {
        return;
        $files = scandir(public_path('assets/seed'));
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($files as $file) {
            $rp = preg_replace("#\?.*#", '', pathinfo($file, PATHINFO_EXTENSION));
            if ($rp == 'json') {
                $fileNameArray = explode('.', $file);
                $items = json_decode(file_get_contents(public_path('assets/seed/'.$file)), true);
                $modelName = $fileNameArray[1];
                $modelClass = sprintf('\App\Models\%s', $modelName);
                collect($items)->each(function ($item) use ($modelClass, $modelName, &$tempReferences) {
                    if (isset($item['image'])) {
                        $item['image'] = \Asset::upload_image(config('hideaways.website_url').'/'.$item['image'], strtolower(str_replace(' ', '-', trim(preg_replace('/(?<!\ )[A-Z]/', ' $0', $modelName)))).'-image');
                    }
                    $tempId = $item['temp_id'] ?? null;
                    unset($item['temp_id']);
                    foreach ($item as $key => $value) {
                        if (str_ends_with($key, '_temp_id') && isset($tempReferences[$value])) {
                            $realField = str_replace('_temp_id', '_id', $key);
                            $item[$realField] = $tempReferences[$value];
                            unset($item[$key]);
                        }
                    }
                    $model = new $modelClass;
                    $model->unsetEventDispatcher();
                    $model = $model->create($item);
                    if ($model->getKey() && $model->exists) {
                        try {
                            $model->refresh();
                        } catch (\Exception $e) {
                            \Log::warning('No se pudo hacer refresh del modelo', [
                                'model' => $modelClass,
                                'id' => $model->getKey(),
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                    if ($tempId) {
                        $tempReferences[$tempId] = $model->id;
                    }
                });
            }
        }
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public static function loadTestKeys() {}

    public static function loadPartners() {}
}
