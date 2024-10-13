<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Promos;
use App\Models\Products;
use Illuminate\Http\Request;
use App\Exports\PromosExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class PromosController extends Controller
{

    public function index()
    {
        return view('pages.promos.promos', ['type_menu' => 'promos']);
    }

    public function indexData(Request $request)
    {
        $promos = Promos::filterIndex($request)
            ->orderBy('id', 'desc')->get();

        return response()->json(['data' => $promos]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $duplicatePromo = Promos::whereNotNull('unique_code')->where('unique_code', '<>', '')->where('unique_code', $request->unique_code)->value('unique_code');
            if ($duplicatePromo == $request->unique_code && !empty($duplicatePromo)) {
                return back()->withErrors([
                    'error_message' => "The unique_code \"$request->unique_code\" has already been taken",
                ]);
            }

            $except_data = ['_token'];
            $additionalData = [
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ];

            if ($request->type == 'Package') {
                $product_id = json_encode(explode(',', $request->input('selected_products')));

                $additionalExceptData = ['selected_products', 'value_package'];
                $except_data = array_merge($except_data, $additionalExceptData);

                $additionalData2 = ['product_id' => $product_id,];
                $additionalData = array_merge($additionalData, $additionalData2);
            }

            $filteredData = $request->except($except_data);

            $promoData = array_merge($filteredData, $additionalData);

            Promos::create($promoData);

            DB::commit();

            session()->flash('success_message', 'Promo has been created successfully!');

            return redirect()->to('/promos')->with('type_menu', 'promos');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }

    public function show($id)
    {
        $products = [];

        $promo = Promos::find($id);

        $promo->start_date = Carbon::parse($promo->start_date)->format('Y-m-d');
        $promo->end_date = Carbon::parse($promo->end_date)->format('Y-m-d');

        if ($promo->product_id) {
            $products_id = json_decode($promo->product_id);

            foreach ($products_id as $product_id) {
                $product = Products::find($product_id);
                if ($product) {
                    $products[] = $product;
                }
            }

            $promo->products = $products;
        }

        return response()->json([$promo]);
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $duplicatePromo = Promos::whereNotNull('unique_code')->where('unique_code', '<>', '')->where('id', '<>', $request->id)->where('unique_code', $request->unique_code)->value('unique_code');
            if ($duplicatePromo == $request->unique_code && !empty($duplicatePromo)) {
                return back()->withErrors([
                    'error_message' => "The unique_code \"$request->unique_code\" has already been taken",
                ]);
            }

            $promo = Promos::find($request->id);

            $promo->name = $request->name;
            $promo->unique_code = $request->unique_code;
            $promo->type = $request->type;
            $promo->start_date = $request->start_date;
            $promo->end_date = $request->end_date;
            $promo->package_quantity = $request->package_quantity;

            if ($request->type === 'Nominal' || $request->type === 'Percentage') {
                $promo->product_id = null;
                $promo->value = $request->value;
            } else if ($request->type === 'Package') {
                $promo->product_id = json_encode(explode(',', $request->input('selected_products')));
                $promo->value = null;
            }

            $promo->save();

            DB::commit();

            session()->flash('success_message', 'Promo has been updated successfully!');

            return redirect()->to('/promos')->with('type_menu', 'promos');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            DB::beginTransaction();

            $promo = Promos::find($request->id);

            $promo->deleted_by = auth()->user()->id;

            $promo->save();

            $promo->delete();

            DB::commit();

            session()->flash('success_message', 'Promo has been deleted successfully!');

            return redirect()->to('/promos')->with('type_menu', 'promos');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }

    public function export(Request $request)
    {
        try {
            return Excel::download(new PromosExport($request), 'promos.xlsx');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }
}
