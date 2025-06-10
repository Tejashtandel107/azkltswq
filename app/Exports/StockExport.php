<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StockExport implements FromView
{
    protected $collection;

    protected $request;

    public function __construct($obj, $req)
    {
        $this->collection = $obj;
        $this->request = $req;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function view(): View
    {
        return view('admin.reports.inc.export', ['results' => $this->collection, 'request' => $this->request]);
    }
}
