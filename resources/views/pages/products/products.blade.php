@extends('layouts.app')

@section('title', 'Products')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/ionicons201/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/bootstrap/dist/css/bootstrap4-toggle.min.css') }}">
    <style>
        .table td,
        .table th {
            vertical-align: middle;
            text-align: center;
        }

        .table-container {
            display: flex;
            justify-content: center;
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Products</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex mb-3" style="flex-grow: 1; align-items: flex-end;">
                                        @can('products_create')
                                            <button style="max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-primary" data-toggle="modal" data-target="#addproduct">
                                                <i class="ion-plus-circled" data-pack="default" data-tags="sort"
                                                    style="font-size: 17px; margin-left: -5px; margin-right: 7px;"></i>
                                                Add New Product
                                            </button>
                                        @else
                                            <button style="max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-secondary" disabled>
                                                <i class="ion-plus-circled" data-pack="default" data-tags="sort"
                                                    style="font-size: 17px; margin-left: -5px; margin-right: 7px;"></i>
                                                Add New Product
                                            </button>
                                        @endcan

                                        <div style="margin-left: auto; display: flex; align-items: flex-end;">
                                            <button
                                                style="margin-right: 10px; max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-info" data-toggle="modal" data-target="#filterproduct">
                                                <i class="ion-funnel" data-pack="default" data-tags="sort"
                                                    style="font-size: 15px;"></i>
                                            </button>
                                            <button id="download_button" onclick="handleDownload()"
                                                style="margin-right: 10px; max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-success">
                                                <i class="ion-ios-download" data-pack="ios" data-tags="save, export"
                                                    style="font-size: 21px;"></i>
                                            </button>
                                            <div id="filter-container"
                                                style="display: flex; align-items: center; max-height: 55px;"></div>
                                        </div>
                                    </div>
                                </div>
                                <table class="table-hover table" id="datatable" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th style="width: 1%">#</th>
                                            <th style="width: 1%"></th>
                                            <th style="width: 1%"></th>
                                            <th scope="col">Product Name</th>
                                            <th scope="col">Selling Price</th>
                                            <th scope="col">Unit Of Measurement</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Last Updated Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </section>
    </div>

    {{-- Modal Create --}}
    <div class="modal fade" tabindex="-1" role="dialog" id="addproduct">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ url('/products/store') }}" enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <label for="product_name" class="form-label">Product Name <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="text" name="product_name" required>
                            </div>
                            <div class="col-6">
                                <label for="sku" class="form-label">SKU</label>
                                <input class="form-control" type="text" name="sku">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label for="purchase_price" class="form-label">Purhase Price</label>
                                <input class="form-control" type="number" name="purchase_price">
                            </div>
                            <div class="col-6">
                                <label for="selling_price" class="form-label">Selling Price <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="number" name="selling_price" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label for="quantity" class="form-label">Quantity <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="number" name="quantity" required>
                            </div>
                            <div class="col-6">
                                <label for="unit_of_measurement" class="form-label">Unit of Measurement</label>
                                <input class="form-control" type="text" name="unit_of_measurement">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="message-text" name="description" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label for="file" class="form-label">Picture of Product</label>
                                <input class="form-control" type="file" name="file">
                            </div>
                            <div class="col-6">
                                <label for="product_picture" class="form-label">Is Included in Receipt</label><br>
                                <input type="checkbox" name="is_included_in_receipt" data-toggle="toggle">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" tabindex="-1" role="dialog" id="editproduct">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ url('/products/update') }}" enctype="multipart/form-data" id="editForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input class="form-control" type="hidden" name="id" id="id" required>
                            <div class="col-6">
                                <label for="product_name" class="form-label">Product Name <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="text" name="product_name" id="product_name"
                                    required>
                            </div>
                            <div class="col-6">
                                <label for="sku" class="form-label">SKU</label>
                                <input class="form-control" type="text" id="sku" name="sku">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label for="purchase_price" class="form-label">Purhase Price</label>
                                <input class="form-control" type="number" id="purchase_price" name="purchase_price">
                            </div>
                            <div class="col-6">
                                <label for="selling_price" class="form-label">Selling Price <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="number" id="selling_price" name="selling_price"
                                    required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label for="quantity" class="form-label">Quantity <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="number" id="quantity" name="quantity" required>
                            </div>
                            <div class="col-6">
                                <label for="unit_of_measurement" class="form-label">Unit of Measurement</label>
                                <input class="form-control" type="text" id="unit_of_measurement"
                                    name="unit_of_measurement">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label for="product_picture" class="form-label">Picture of Product</label><br>
                                <img src="" id="product_picture" style="width:170px; height:170px;">
                                <input class="form-control" id="file" type="file" name="file">
                            </div>
                            <div class="col-6">
                                <label for="product_picture" class="form-label">Is Included in Receipt</label><br>
                                <input type="checkbox" id="is_included_in_receipt" name="is_included_in_receipt"
                                    data-toggle="toggle">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Filter --}}
    <div class="modal fade" tabindex="-1" role="dialog" id="filterproduct">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Product <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <label for="product_name" class="form-label">Product Name</label>
                            <input class="form-control" type="text" id="product_name_filter">
                        </div>
                        <div class="col-6">
                            <label for="selling_price" class="form-label">Selling Price</label>
                            <input class="form-control" type="number" id="selling_price_filter">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="unit_of_measurement" class="form-label">Unit of Measurement</label>
                            <input class="form-control" type="text" id="unit_of_measurement_filter">
                        </div>
                        <div class="col-6">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input class="form-control" type="number" id="quantity_filter">
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="col-6">
                            <label for="quantity" class="form-label">Last Updated Date</label>
                            <input class="form-control" type="date" id="updated_at_filter">
                        </div>
                        <div class="col-6">
                            <label for="is_included_in_receipt_filter" class="form-label">Is Included in
                                Receipt</label><br>
                            <select class="form-control" name="is_included_in_receipt" id="is_included_in_receipt_filter"
                                required>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" onclick="resetfilter()" class="btn btn-danger">Reset</button>
                    <button type="button" onclick="applyfilter()" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('pages.products.js.products-js')
@endpush
