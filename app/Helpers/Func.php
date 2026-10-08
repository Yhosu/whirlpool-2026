<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class Func
{
    public static function getModel($node)
    {
        $className = 'App\\Models\\'.\Str::studly(\Str::singular($node));

        return $className;
    }

    public static function getTypeField($table_name, $field)
    {
        return \DB::getSchemaBuilder()->getColumnType($table_name, $field) ?? 'extra';
    }

    public static function getColumns($table_name, $lang)
    {
        $arraySubtLang = ['es' => 1, 'en' => -1];
        $database = config('database.connections.mysql.database');
        $langCode = $arraySubtLang[$lang];
        $enum = 'enum(';
        $result = \DB::select("
            select 
                `column_name` as name, 
                CASE
                    when RIGHT(`column_name`,3) = '_id' then 'select'
                    when LOCATE('widget_booking', `column_name`) > 0 then 'textarea'                    
                    when LOCATE('detail', `column_name`) > 0 then 'textarea'
                    when LOCATE('{$enum}', `column_type`) >  0 then 'select'
                    when LOCATE('description', `column_name`) > 0 then 'froala'
                    when LOCATE('image', `column_name`) > 0 then 'image'
                    when LOCATE('file', `column_name`) > 0 then 'file'
                    when LOCATE('varchar(', `column_type`) > 0 then 'text'
                    when LOCATE('tinyint(', `column_type`) > 0 then 'boolean'
                    when LOCATE('int(', `column_type`) > 0 then 'integer'
                    when LOCATE('double', `column_type`) > 0 then 'double'
                    else `column_type`
                end as type,
                SUBSTRING_INDEX(`column_comment`, '|', '$langCode')  as comment,
                CASE when LOCATE('{$enum}', `column_type`) > 0 then REPLACE( REPLACE( REPLACE(`column_type`, 'enum(', ''), ')', '' ), '\'', '' ) else '{}' end as options
            from 
                `information_schema`.`COLUMNS`
            where 
                `column_name` not in ('amenity_call_outs') 
                    and
                `table_name` = '$table_name' and `column_name` not in ('created_at', 'updated_at', 'id') and `table_schema` = '$database'
            "
        );
        foreach ($result as $item) {
            if ($item->options == '{}' && substr($item->name, -3) != '_id') {
                $item->options = [];

                continue;
            }
            $item->options = substr($item->name, -3) == '_id'
                ? \Func::getOptionsRelation($item->name, $table_name)
                : \Func::getEnumOptions($item->options);
        }

        return $result;
    }

    public static function getOptionsRelation($column, $table)
    {
        $class = "App\Models\\".ucfirst(str_replace('_id', '', $column));
        $elements = $class::get();
        \Log::info($column);
        $arrayOptions = [];
        foreach ($elements as $element) {
            $key = config('nodes.'.$table.'.'.$column.'.key');
            $arrayOptions[] = ['key' => $element->id, 'value' => $element->$key];
        }

        return $arrayOptions;
    }

    public static function getEnumOptions($options)
    {
        $options = explode(',', $options);
        $arrayOptions = [];
        foreach ($options as $option) {
            $arrayOptions[] = ['key' => $option, 'value' => __('table.subjects.'.$option)];
        }

        return $arrayOptions;
    }

    public static function getColumnsWithTimestamps($table_name, $lang)
    {
        $arraySubtLang = ['es' => 1, 'en' => -1];
        $database = config('database.connections.mysql.database');
        $langCode = $arraySubtLang[$lang];
        $enum = 'enum(';

        return \DB::select("
            select 
                `column_name` as name, 
                CASE
                    when RIGHT(`column_name`,3) = '_id' then 'select'
                    when LOCATE('detail', `column_name`) > 0 then 'textarea'
                    when LOCATE('{$enum}', `column_type`) >  0 then 'select'
                    when LOCATE('description', `column_name`) > 0 then 'froala'
                    when LOCATE('image', `column_name`) > 0 then 'image'
                    when LOCATE('varchar(', `column_type`) > 0 then 'text'
                    when LOCATE('tinyint(', `column_type`) > 0 then 'boolean'
                    when LOCATE('int(', `column_type`) > 0 then 'integer'
                    when LOCATE('double', `column_type`) > 0 then 'double'
                    else `column_type`
                end as type,
                SUBSTRING_INDEX(`column_comment`, '|', '$langCode')  as comment,
                CASE when LOCATE('{$enum}', `column_type`) > 0 then REPLACE( REPLACE( REPLACE(`column_type`, 'enum(', ''), ')', '' ), '\'', '' ) else '' end as options
            from 
                `information_schema`.`COLUMNS`
            where 
                `column_name` not in ('amenity_call_outs') 
                    and
                `table_name` = '$table_name' and `column_name` not in ('id') and `table_schema` = '$database'
            "
        );
    }

    public static function getFilters($table_name, $lang)
    {
        $arraySubtLang = ['es' => 1, 'en' => -1];
        $database = config('database.connections.mysql.database');
        $langCode = $arraySubtLang[$lang];
        $enum = 'enum(';
        $result = \DB::select("
            select 
                `column_name` as name, 
                CASE
                    when RIGHT(`column_name`,3) = '_id' then 'select'
                    when LOCATE('detail', `column_name`) > 0 then 'textarea'
                    when LOCATE('{$enum}', `column_type`) >  0 then 'select'
                    when LOCATE('description', `column_name`) > 0 then 'froala'
                    when LOCATE('image', `column_name`) > 0 then 'image'
                    when LOCATE('varchar(', `column_type`) > 0 then 'text'
                    when LOCATE('tinyint(', `column_type`) > 0 then 'boolean'
                    when LOCATE('int(', `column_type`) > 0 then 'integer'
                    when LOCATE('double', `column_type`) > 0 then 'double'
                    else `column_type`
                end as type,
                SUBSTRING_INDEX(`column_comment`, '|', '$langCode')  as comment,
                CASE when LOCATE('{$enum}', `column_type`) > 0 then REPLACE( REPLACE( REPLACE(`column_type`, 'enum(', ''), ')', '' ), '\'', '' ) else '{}' end as options
            from 
                `information_schema`.`COLUMNS`
            where 
                `table_name` = '$table_name' 
                    and 
                `column_name` not in ('created_at', 'updated_at', 'id', 'amenity_call_outs', 'widget_booking', 'thumb_medium', 'file', 'ci_number_complement', 'ci_number_expedition', '', 'total_amount', 'date', 'time', 'birth_date', 'price_id') 
                    and 
                `table_schema` = '$database' 
                    and 
                LOCATE('detail', `column_name`) = 0 
                    and 
                LOCATE('description', `column_name`) = 0
                    and 
                LOCATE('image', `column_name`) = 0
                    and 
                LOCATE('int(', `column_type`) = 0
                    and 
                LOCATE('double', `column_type`) = 0
            "
        );
        $result[] = (object) [
            'name' => 'created_at_from',
            'type' => 'date',
            'comment' => __('table.created_at_from'),
            'options' => '{}',
        ];
        $result[] = (object) [
            'name' => 'created_at_to',
            'type' => 'date',
            'comment' => __('table.created_at_to'),
            'options' => '{}',
        ];
        if ($table_name == 'participant_coupons') {
            $result[] = (object) [
                'name' => 'xt_ci_number',
                'type' => 'text',
                'comment' => 'Carnet de identidad',
                'options' => '{}',
            ];
        }
        foreach ($result as $item) {
            if ($item->options == '{}' && substr($item->name, -3) != '_id') {
                $item->options = [];

                continue;
            }
            $item->options = substr($item->name, -3) == '_id'
                ? \Func::getOptionsRelation($item->name, $table_name)
                : \Func::getEnumOptions($item->options);
        }
        if (method_exists('App\Helpers\Func', 'extra_filters_'.$table_name)) {
            $fn = 'extra_filters_'.$table_name;
            \Func::$fn($result);
        }

        return $result;
    }

    public static function createItem($class, $id)
    {
        return ['process' => true, 'item' => null, 'message' => ''];
    }

    public static function editItem($class, $id)
    {
        $item = $class::find($id);
        if (! $item) {
            return ['process' => false, 'message' => __('table.item_not_found')];
        }

        return ['process' => true, 'item' => $item, 'message' => __('table.item_found')];
    }

    public static function readItem($class, $id)
    {
        $item = $class::find($id);
        if (! $item) {
            return ['process' => false, 'message' => __('table.item_not_found')];
        }

        return ['process' => true, 'item' => $item, 'message' => __('table.item_found')];
    }

    public static function deleteItem($class, $id)
    {
        $item = $class::find($id);
        if (! $item) {
            return ['process' => false, 'message' => __('table.item_not_found')];
        }
        $item->delete();

        return ['process' => true, 'item' => $item, 'message' => __('table.item_eliminated'), 'redirect' => true];
    }

    public static function validateImageUrl($value, $folder, $file, $image)
    {
        return $value != 'null' && ! empty($value) && (\Asset::get_image_path($folder, 'mini', $image) != \Asset::get_image_path($folder, 'mini', $file));
    }

    public static function validateFileUrl($value, $folder, $file, $image)
    {
        return $value != 'null' && ! empty($value) && (\Asset::get_file($folder, $image) != \Asset::get_file($folder, $file));
    }

    public static function renderMenuHtml($items, $isFirstLevel = true) {}

    public static function getNameRelation($fields, $column, $key)
    {
        $elemCollect = collect($fields);
        $elemRelation = (array) $elemCollect->firstWhere('name', $column) ?? [];
        $item = collect($elemRelation['options'] ?? [])->firstWhere('key', $key);

        return $item['value'] ?? '';
    }

    public static function extra_filters_menu_items(&$result)
    {
        $result[] = (object) [
            'name' => 'xt_with_image',
            'type' => 'select',
            'comment' => __('table.with_image'),
            'options' => [
                [
                    'key' => 'false',
                    'value' => 'No',
                ],
                [
                    'key' => 'true',
                    'value' => 'Si',
                ],
            ],
        ];
    }

    public static function clearFroalaText($txt)
    {
        return str_replace('<p data-f-id="pbf" style="text-align: center; font-size: 14px; margin-top: 30px; opacity: 0.65; font-family: sans-serif;">Powered by <a href="https://www.froala.com/wysiwyg-editor?pb=1" title="Froala Editor">Froala Editor</a></p>', '', $txt);
    }

    public static function generateCode($model, $field, $digits = 8, $type = 'number')
    {
        $code = self::generateRawCode($digits, $type);
        $exists = $model::where($field, $code)->exists();
        if ($exists) {
            return self::generateCode($model, $field, $digits, $type);
        }

        return $code;
    }

    public static function generateRawCode(int $digits, string $type): string
    {
        if ($digits <= 0) {
            return '';
        }

        if ($digits === 1) {
            return '1';
        }

        $remainingDigits = $digits - 1;

        if ($type === 'alphanumeric') {
            return '1'.self::generateAlphanumericCode($remainingDigits);
        }

        return '1'.self::generateNumericCode($remainingDigits);
    }

    private static function generateNumericCode(int $digits): string
    {
        $code = '';
        for ($i = 0; $i < $digits; $i++) {
            $code .= random_int(0, 9);
        }

        return $code;
    }

    private static function generateAlphanumericCode(int $digits): string
    {
        return Str::upper(Str::random($digits));
    }

    public static function randomProbability($percentage)
    {
        // Genera un número aleatorio entre 1 y 100
        $num = mt_rand(1, 100);

        // Retorna true si el número está dentro del 8% inicial
        return $num <= $percentage;
    }
}
