<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImportService;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Test1Controller extends Controller
{
    public function __construct(ImportService $obj_importservices)
    {
        $this->importservices_obj = $obj_importservices;
    }

    public function index(Request $request)
    {
        error_reporting(0);
        set_time_limit(0);

        $temp_dir = config('constant.TEMP_FOLDER_PATH');

        if (Auth::guest()) {
            exit('Please Login');
        }

        /* change the filename. Without any extension. */
        $filename = '';
        if (! empty($filename)) {
            [$data_arrs, $key_arr] = $this->importservices_obj->WriteImportCsv('temp/'.$filename.'.csv');
        } else {
            return 'No file given.';
        }

        foreach ($data_arrs as $key => $insert_data_arr) {
            $insert_data_arr_with_key[$key] = array_combine($key_arr, $insert_data_arr);
        }

        $input_data = [];
        $input_data['text'] = 'Redirect';
        $input_data['status'] = '301';

        foreach ($insert_data_arr_with_key as $key => $value) {
            $is_empty = $this->importservices_obj->isEmptyValue($value['Source URL']);
            if (! $is_empty) {
                $input_data['source_url'] = parse_url($value['Source URL'])['path'];
            }

            $is_empty = $this->importservices_obj->isEmptyValue($value['Destination URL']);
            if (! $is_empty) {
                $input_data['destination_url'] = parse_url($value['Destination URL'])['path'];
            }

            $content[] = $input_data['text'].' '.$input_data['status'].' '.$input_data['source_url'].' '.$input_data['destination_url']."\n";
        }

        // file_put_contents('storage/temp/filename.txt', $content);

        Storage::put('temp/'.$filename.'.txt', $content);
        echo $filename.'.txt'.'&nbsp;'.'Done';
    }
}
