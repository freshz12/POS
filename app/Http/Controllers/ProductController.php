<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use App\Exports\ProductsExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function index()
    {
        return view('pages.products.products', ['type_menu' => 'product']);
    }

    public function indexData(Request $request)
    {
        $products = Products::filterIndex($request)->orderBy('id', 'desc')->get();

        return response()->json(['data' => $products]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            // $duplicateSKU = Products::where('sku', $request->sku)->value('sku');
            // if ($duplicateSKU == $request->sku) {
            //     return back()->withErrors([
            //         'error_message' => 'The SKU has already been taken',
            //     ]);
            // }

            $filteredData = $request->except(['_token', 'file']);

            if ($request->hasFile('file')) {
                $file = $request->file('file');

                $filename = time() . '.' . $file->getClientOriginalExtension();

                $file->storeAs('public/files/products', $filename);

                $filePath = 'files/products/' . $filename;
            }

            $additionalData = [
                'picture_path' => $filePath ?? null,
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ];

            $productData = array_merge($filteredData, $additionalData);

            Products::create($productData);

            DB::commit();

            session()->flash('success_message', 'Product has been created successfully!');

            return redirect()->to('/products')->with('type_menu', 'products');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }

    public function show($id)
    {
        $product = Products::find($id);

        return response()->json([$product]);
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $product = Products::find($request->id);

            $filePath = null;

            if ($request->file) {
                Storage::delete('public/' . $product->picture_path);

                $file = $request->file('file');

                $filename = time() . '.' . $file->getClientOriginalExtension();

                $file->storeAs('public/files/products', $filename);

                $filePath = 'files/products/' . $filename;
            }

            $filteredData = $request->except(['_token', 'file']);

            $additionalData = [
                'updated_by' => auth()->user()->id,
            ];

            if ($filePath) {
                $additionalData['picture_path'] = $filePath;
            }

            $productData = array_merge($filteredData, $additionalData);

            $product->update($productData);

            DB::commit();

            session()->flash('success_message', 'Product has been updated successfully!');

            return redirect()->to('/products')->with('type_menu', 'products');

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

            $product = Products::find($request->id);

            if ($product->picture_path) {
                Storage::delete('public/' . $product->picture_path);
            }

            $product->deleted_by = auth()->user()->id;

            $product->save();

            $product->delete();

            DB::commit();

            session()->flash('success_message', 'Product has been deleted successfully!');

            return redirect()->to('/products')->with('type_menu', 'products');

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
            return Excel::download(new ProductsExport($request), 'products.xlsx');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }
}
