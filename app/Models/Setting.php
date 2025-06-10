<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['key', 'value'];

    public function uploadCompanyLogo($request)
    {
        $original_name = pathinfo($request->file('logo')->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = Helper::getUniqueFilename().'.'.$request->file('logo')->getClientOriginalExtension();
        $file_path = $request->file('logo')->storeAs(config('constant.COMPANY_LOGO_PATH'), $filename);
        if ($file_path) {
            return $file_path;
        } else {
            return false;
        }
    }
}
