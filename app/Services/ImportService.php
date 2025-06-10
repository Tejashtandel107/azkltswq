<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImportService
{
    public static function getStoragePath($value)
    {
        // if(Storage::exists($value)){
        if (strlen($value) > 0) {
            return Storage::url($value);
        } else {
            return '';
        }
    }

    public function isEmptyValue($value)
    {
        return empty($value);
    }

    public function WriteImportCsv($path)
    {
        $data_arr = [];
        $counter = 0;
        $row = 1;
        $temp_path = $this->getStoragePath($path);
        try {
            if (($handle = fopen($temp_path, 'r')) !== false) {
                ini_set('auto_detect_line_endings', true);
                while (($data = fgetcsv($handle, 0, ',')) !== false) {
                    $num = count($data);
                    for ($c = 0; $c < $num; $c++) {
                        if ($row == 1) {
                            $key_arr[] = trim($data[$c]);
                        } else {
                            $data_arr[$counter][$c] = trim($data[$c]);
                        }
                    }
                    if ($row != 1) {
                        $counter++;
                    }
                    $row++;
                }
            }
            fclose($handle);
        } catch (Exception $e) {
            trigger_error('file_get_contents_chunked::'.$e->getMessage(), E_USER_NOTICE);

            return false;
        }

        return [$data_arr, $key_arr];
    }
}
