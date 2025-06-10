<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ItemRequest;
use App\Models\Item;
use App\Models\Marka;
use App\Services\ItemService;
use App\Services\OrderItemService;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    protected $item_obj;

    protected $orderitem_obj;

    public function __construct(ItemService $item, OrderItemService $orderitem)
    {
        $this->item_obj = $item;
        $this->orderitem_obj = $orderitem;
    }

    public function sampleDemo(Request $request)
    {
        return view('admin.modal.sample');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'pagetitle' => 'All Items',
            'breadcrumbs' => ['Home' => route('admin.home'), 'Items' => ''],
            'menuParent' => 'items',
            'menuChild' => 'items',
        ];

        $show = $request->filled('show') ? $request->input('show') : 25;
        $keyword = $request->input('keyword');
        $search_fields = ['name', 'description'];
        $queryInstance = Item::whereNull('deleted_at');

        if ($keyword != '') {
            // SEARCH KEYWORD
            $this->item_obj->filterByKeywords($search_fields, $queryInstance, $keyword);
        }
        $items = $queryInstance->orderBy('name', 'asc')->paginate($show);

        return view('admin.items.index', $data)->with(compact('items', 'request', 'show'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'pagetitle' => 'Create Item',
            'breadcrumbs' => ['Home' => route('admin.home'), 'Items' => route('admin.items.index'), 'Create' => ''],
            'menuParent' => 'items',
            'menuChild' => 'additem',
        ];

        return view('admin.items.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ItemRequest $request)
    {
        if ($this->item_obj->store($request->all())) {
            return redirect()->back()->with(['type' => 'success', 'message' => 'Item created successfully.']);
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
            'pagetitle' => 'Edit Item',
            'breadcrumbs' => ['Home' => route('admin.home'), 'Items' => route('admin.items.index'), 'Edit' => ''],
            'menuParent' => 'items',
            'menuChild' => 'items',
        ];

        $item = Item::findOrFail($id);
        if ($item) {
            return view('admin.items.create', $data)->with(compact('item'));
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
    public function update(ItemRequest $request, $id)
    {
        $type = 'error';
        $message = 'Sorry, failed to update item. Please try again.';

        if ($this->item_obj->update($id, $request->all())) {
            $type = 'success';
            $message = 'Item updated successfully.';
        }

        return redirect()->back()->with(['type' => $type, 'message' => $message]);
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
            $message = 'Sorry, failed to delete item. Please try again.';

            $collection = $this->orderitem_obj->findWithTrashed('item_id', $id)->get();

            if ($collection->count() > 0) {
                $type = 'error';
                $message = 'Item is already used in the system so the system could not delete Item.';
            } else {
                if (Item::find($id)->forceDelete()) {
                    $type = 'success';
                    $message = 'Item deleted successfully.';

                    if (Marka::where('item_id', $id)->forceDelete()) {
                        $type = 'success';
                        $message = 'Item with marka deleted successfully.';
                    }
                }
            }

            return response()->json(['type' => $type, 'message' => $message]);
        }
    }

    /**
     * open modal to create new Item
     *
     * @return \Illuminate\Http\Response
     */
    public function openModal(Request $request)
    {
        $modaltitle = 'Add New Item';

        return view('admin.modal.items.create', compact('modaltitle', 'request'));
    }

    /**
     * Saves new Item via modal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveModal(ItemRequest $request)
    {
        if ($request->ajax()) {
            $type = 'error';
            $message = 'Sorry, failed to create item. Please try again.';
            $result = $this->item_obj->store($request->all());
            if ($result) {
                $type = 'success';
                $message = 'New item created successfully.';
            }

            return response()->json(['type' => $type, 'message' => $message, 'items' => $result]);
        }
    }
}
