<?php

namespace App\Http\Controllers\Web;

use App\Exports\StockExport;
use App\Helpers\Zip;
use App\Http\Controllers\Controller;
use App\Models\Chamber;
use App\Models\Customer;
use App\Models\Floor;
use App\Models\Grid;
use App\Models\Item;
use App\Models\Marka;
use App\Services\OrderItemService;
use Auth;
use Carbon\Carbon;
use Excel;
use File;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Storage;

class FullLedgerReportController extends Controller
{
    protected $orderitem_obj;

    public function __construct(OrderItemService $orderitem)
    {
        $this->orderitem_obj = $orderitem;
    }

    public function show(Request $request)
    {
        $data = [
            'pagetitle' => 'Stocks Report',
            'breadcrumbs' => ['Home' => route('user.home'), 'Ledger Report' => ''],
            'menuParent' => 'reports',
            'menuChild' => 'full-ledger',
        ];

        $user = Auth::user();
        $customer_id = $user->customer_id;

        $request['c'] = $customer_id;

        $chambers = Chamber::all()->keyBy('chamber_id');
        $floors = Floor::all()->keyBy('floor_id');
        $grids = Grid::all()->keyBy('grid_id');

        if ($request->filled('c')) {

            $items = $this->orderitem_obj->fetchCustomerItems($customer_id);

            // $items = Item::withTrashed()->orderBy('name')->get();
        }

        if ($request->filled('i') && $request->filled('c')) {
            $markas = $this->orderitem_obj->fetchCustomerMarka($request->input('i'), $customer_id);

            // $markas = Marka::withTrashed()->where('item_id',$request->input('i'))->orderBy('name')->get();
        } else {
            $markas = collect();
        }
        $company_info = $this->orderitem_obj->getCompanyDetails(config('constant.SETTINGS_KEY'));
        // $customers = Customer::where('customer_id',$customer_id)->withTrashed()->orderBy('companyname')->get();
        $results = null;
        if ($request->filled('c') || $request->filled('i') || $request->filled('m') || $request->filled('q') || $request->filled('from') || $request->filled('to')) {
            $results = $this->orderitem_obj->manageLedger($request);
        }

        return view('web.reports.full-ledger', $data)->with(compact('request', 'results', 'items', 'markas', 'user', 'chambers', 'floors', 'grids', 'company_info', 'customer_id'));
    }

    public function export(Request $request)
    {
        error_reporting(0);
        if ($request->ajax()) {

            // with collection
            $results = null;

            if ($request->filled('c') || $request->filled('i') || $request->filled('m') || $request->filled('q') || $request->filled('from') || $request->filled('to')) {
                // Create .cvs files in temp folder
                $results = $this->orderitem_obj->manageLedger($request);
                if (isset($results) && ($results->count() > 0)) {
                    $full_ledgers = $results->groupBy('customer_id');
                    Storage::delete(Storage::files('temp'));

                    foreach ($full_ledgers as $key => $ledger_results) {
                        $customer_info = $ledger_results->first();
                        $filename = Str::slug($customer_info->fullname).'-'.$customer_info->customer_id.'.csv';
                        $export = new StockExport($ledger_results, $request);
                        Excel::store($export, $filename);
                        Storage::move($filename, 'temp/'.$filename);
                    }

                    // Zip Folder
                    $time = Carbon::now()->toDateString();
                    $zip_file = 'stock-reports-'.$time.'.zip';
                    $zip_obj = new Zip;
                    $destination = Helper::getStorageRealPath($zip_file);
                    $source = Helper::getStorageRealPath('temp');
                    $response = $zip_obj->create($source, $destination);
                    $zip_url = Storage::url($zip_file);
                    if ($response) {
                        return response()->json(['type' => 'success', 'message' => 'Export created successfully.', 'url' => $zip_url]);
                        // return Storage::download($zip_file);
                    } else {
                        return response()->json(['type' => 'error', 'message' => 'Sorry, we have encountered an error during CSV file generation.']);
                    }
                } else {
                    return response()->json(['type' => 'error', 'message' => 'No data found to generate report.']);
                }
            } else {
                return response()->json(['type' => 'error', 'message' => 'No data found to generate report.']);
            }
        }
    }
}
