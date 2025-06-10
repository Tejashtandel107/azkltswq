<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TestExport implements FromView
{
    protected $collection;

    public function __construct($obj)
    {
        $this->collection = $obj;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function view(): View
    {
        return view('admin.reports.inc.export', ['results' => $this->collection]);
    }
}
