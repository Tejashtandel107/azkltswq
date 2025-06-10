<?php

namespace App\Helpers;

use Carbon\Carbon;
use Storage;

class Helper
{
    public static function DateFormat($date = '', $format = '')
    {
        if ($date) {
            $format = (empty($format)) ? config('constant.DATE_FORMAT') : $format;
            $dt = Carbon::parse($date);

            return $dt->format($format);
        } else {
            return false;
        }
    }

    /**
     * convert the D-M-Y format to D/M/Y
     */
    public static function convertDateFormat($value = '', $format = '')
    {
        if ($value) {
            $format = (empty($format)) ? config('constant.DATE_FORMAT_SHORT') : $format;

            return Carbon::createFromFormat('!'.$format, $value);
        } else {
            return false;
        }
    }

    public static function trimInputs($input = [])
    {
        $trim_if_string = function ($var) {
            if (! is_array($var)) {
                return trim($var);
            }
        };

        return array_map($trim_if_string, $input);

        return $input;
    }

    public static function getUniqueFilename($prefix = '', $more_entropy = true)
    {
        $filename = uniqid($prefix, $more_entropy);
        $filename = str_replace('.', '', $filename);

        return $filename;
    }

    public static function getPlaceHolderImg($value)
    {
        if (Storage::exists($value)) {
            return Storage::url($value);
        } else {
            return config('constant.DEFAULT_IMG_HOLDER');
        }
    }

    public static function getProfileImg($value)
    {
        if (! empty($value) && Storage::exists($value)) {
            return Storage::url($value);
        } else {
            return config('constant.DEFAULT_IMG_PROFILE');
        }
    }

    public static function getStoragePath($value)
    {
        if (Storage::exists($value)) {
            return Storage::url($value);
        } else {
            return '';
        }
    }

    public static function getStorageRealPath($file = '')
    {
        return Storage::disk(config('filesystems.local'))->path($file);
    }

    public static function getPaymentMethod()
    {
        $payment_methods = config('constant.PAYMENT_METHODS');

        return $payment_methods;
    }

    public static function getPaymentMethodValue($key)
    {
        if (! empty($key)) {
            return config("constant.PAYMENT_METHODS.$key");
        } else {
            return null;
        }
    }

    public static function formatAmount($amount = 0.00)
    {
        return number_format((float) ($amount ?? 0.00), 2, '.', ',');
    }

    public static function formatWeight($weight = 0.00)
    {
        return number_format((float) ($weight ?? 0.00), 2, '.', '');
    }

    public static function getHumanDate($value)
    {
        if (! empty($value)) {
            return Carbon::parse($value)->toFormattedDateString();
        } else {
            return null;
        }
    }

    public static function getLocationCode($chamber_number, $floor_number, $grid_number)
    {
        return 'CH'.$chamber_number.'F'.$floor_number.(self::getGrigNumber($grid_number));
    }

    public static function getGrigNumber($value)
    {
        return (is_numeric($value)) ? (sprintf('%02d', $value)) : $value;
    }

    public static function uploadImg($request_file, $filepath)
    {

        $original_name = pathinfo($request_file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = Helper::getUniqueFilename().'.'.$request_file->getClientOriginalExtension();
        $file_path = $request_file->storeAs($filepath, $filename);
        if ($file_path) {
            return $file_path;
        } else {
            return false;
        }
    }
}
