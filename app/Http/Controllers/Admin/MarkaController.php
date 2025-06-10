<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MarkaRequest;
use App\Models\Item;
use App\Models\Marka;
use App\Services\MarkaService;
use App\Services\OrderItemService;
use Illuminate\Http\Request;

class MarkaController extends Controller
{
    protected $marka_obj;

    protected $orderitem_obj;

    public function __construct(MarkaService $marka, OrderItemService $orderitem)
    {
        $this->marka_obj = $marka;
        $this->orderitem_obj = $orderitem;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'pagetitle' => 'All Marka',
            'breadcrumbs' => ['Home' => route('admin.home'), 'Markas' => ''],
            'menuParent' => 'items',
            'menuChild' => 'item-markas',
        ];

        $show = $request->filled('show') ? $request->input('show') : 25;
        $keyword = $request->input('keyword');
        $items = Item::orderBy('name')->get();
        $queryInstance = Marka::select('marka.*', 'i.name as item_name')
            ->leftJoin('items as i', function ($join) {
                $join->on('marka.item_id', '=', 'i.item_id');
            });

        if ($request->filled('item_id')) {
            $item_id = $request->input('item_id');
            $queryInstance->where('marka.item_id', $item_id);
        }

        $search_fields = ['marka.name', 'i.name'];
        if ($keyword != '') {
            $this->marka_obj->filterByKeywords($search_fields, $queryInstance, $keyword);
        }
        $markas = $queryInstance->orderBy('marka.name')->paginate($show);

        return view('admin.item-marka.index', $data)->with(compact('markas', 'request', 'items', 'show'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'pagetitle' => 'Create Marka',
            'breadcrumbs' => ['Home' => route('admin.home'), 'Markas' => route('admin.item-marka.index'), 'Create' => ''],
            'menuParent' => 'items',
            'menuChild' => 'item-markas',
        ];

        [$items, $markas] = $this->marka_obj->pluckData(['item_id']);

        return view('admin.item-marka.create', $data)->with(compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MarkaRequest $request)
    {
        if ($this->marka_obj->store($request->all())) {
            return redirect()->route('admin.item-marka.index')->with(['type' => 'success', 'message' => 'Marka created successfully.']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ($request->filled('date')) {
            $price_date = Carbon::parse($request->date)->toDateTimeString();
        } else {
            $price_date = Carbon::now();
        }

        $item = ItemPrice::select('item_id', 'price')->where('item_id', $id)->where('price_date', $price_date)->first();
        if (isset($item)) {
            $item->type = 'success';

            return response()->json($item);
        } else {
            return response()->json(['type' => 'error']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = [
            'pagetitle' => 'Edit Marka',
            'breadcrumbs' => ['Home' => route('admin.home'), 'Markas' => route('admin.item-marka.index'), 'Edit' => ''],
            'menuParent' => 'items',
            'menuChild' => 'item-markas',
        ];

        $marka = Marka::findOrFail($id);
        [$items] = $this->marka_obj->pluckData(['item_id']);

        if ($marka) {
            return view('admin.item-marka.create', $data)->with(compact('marka', 'items'));
        } else {
            abort('403');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MarkaRequest $request, $id)
    {
        $type = 'error';
        $message = 'Sorry, failed to edit Marka. Please try again.';

        if ($this->marka_obj->update($id, $request->all())) {
            $type = 'success';
            $message = 'Marka updated successfully.';
        }

        return redirect()->route('admin.item-marka.index')->with(['type' => $type, 'message' => $message]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            $type = 'error';
            $message = 'Sorry, failed to delete Marka. Please try again.';
            $collection = $this->orderitem_obj->findWithTrashed('marka_id', $id)->get();

            if ($collection->count() > 0) {
                // $result = Marka::where('marka_id',$id)->delete();
                $type = 'error';
                $message = 'Marka is already used in the system so the system could not delete Marka.';
            } else {
                $result = Marka::where('marka_id', $id)->forceDelete();
                if ($result) {
                    $type = 'success';
                    $message = 'Marka deleted successfully.';
                }
            }

            return response()->json(['type' => $type, 'message' => $message]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    /*
    public function getAll(Request $request)
    {
        if($request->filled('date'))
            $price_date = Carbon::parse($request->date)->toDateTimeString();
        else
            $price_date = Carbon::now();

        $item = ItemPrice::select('item_id','price')->where("price_date",$price_date)->get();

        if(isset($item)){
            //$item->type="success";
            return response ()->json ($item);
        }
        else{
            return response ()->json (["type"=>"error"]);
        }
    }
    */

    /**
     * open modal to create new Marka
     *
     * @return \Illuminate\Http\Response
     */
    public function openModal(Request $request)
    {
        $modaltitle = 'Add New Marka';
        [$items] = $this->marka_obj->pluckData(['item_id']);

        return view('admin.modal.item-marka.create', compact('modaltitle', 'request', 'items'));
    }

    /**
     * Saves new Marka via modal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveModal(MarkaRequest $request)
    {
        if ($request->ajax()) {
            $type = 'error';
            $message = 'Sorry, failed to create marka. Please try again.';
            $result = $this->marka_obj->store($request->all());

            if ($result) {
                $type = 'success';
                $message = 'New marka created successfully.';
            }

            return response()->json(['type' => $type, 'message' => $message, 'markas' => $result]);
        }
    }
}
