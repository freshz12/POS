@extends('layouts.app')

@section('title', 'Promos')

@push('style')
    @include('pages.promos.css.promos-css')
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Promos</h1>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex mb-3" style="flex-grow: 1; align-items: flex-end;">
                                        @can('promos_create')
                                            <button style="max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-primary" data-toggle="modal" data-target="#addpromo">
                                                <i class="ion-plus-circled" data-pack="default" data-tags="sort"
                                                    style="font-size: 17px; margin-left: -5px; margin-right: 7px;"></i>
                                                Add New Promo
                                            </button>
                                        @else
                                            <button style="max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-secondary" disabled>
                                                <i class="ion-plus-circled" data-pack="default" data-tags="sort"
                                                    style="font-size: 17px; margin-left: -5px; margin-right: 7px;"></i>
                                                Add New Promo
                                            </button>
                                        @endcan

                                        <div style="margin-left: auto; display: flex; align-items: flex-end;">
                                            <button
                                                style="margin-right: 10px; max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-info" data-toggle="modal" data-target="#filterpromo">
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

                                <div class="table-responsive">
                                    <table class="table-hover table" id="datatable" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 1%">#</th>
                                                <th style="width: 1%"></th>
                                                <th scope="col">Promo Name</th>
                                                <th scope="col">Unique Code</th>
                                                <th scope="col">Type</th>
                                                <th scope="col">value</th>
                                                <th scope="col">Start Date</th>
                                                <th scope="col">End Date</th>
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
            </div>


        </section>
    </div>

    {{-- Modal Create --}}
    <div class="modal fade" role="dialog" id="addpromo">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Promo <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ url('/promos/store') }}" enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="selected_products" id="selected_products">
                        <div class="row">
                            <div class="col-6">
                                <label for="name" class="form-label">Promo Name <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="text" name="name" required>
                            </div>
                            <div class="col-6">
                                <label for="unique_code" class="form-label">Unique Code <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="text" name="unique_code" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label for="type" class="form-label">Type <span style="color: red">*</span></label>
                                <select class="form-control" name="type" id="type" required>
                                    <option value="Nominal">Nominal</option>
                                    <option value="Percentage">Percentage</option>
                                    <option value="Package">Package</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <div id="div_nominal">
                                    <label for="value" class="form-label">Value</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="groupNominal">Rp</span>
                                        </div>
                                        <input class="form-control" type="number" id="value_nominal" name="value"
                                            required>
                                    </div>
                                </div>
                                <div id="div_percentage">
                                    <label for="value" class="form-label">Value</label>
                                    <div class="input-group">
                                        <input class="form-control" type="number" id="value_percentage" name="value"
                                            disabled required>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="groupPercentage">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label for="start_date" class="form-label">Start Date <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="date" name="start_date" required>
                            </div>
                            <div class="col-6">
                                <label for="end_date" class="form-label">End Date <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="date" name="end_date" required>
                            </div>
                        </div>
                        <br><br>
                        <div id="div_package">
                            <div class="row">
                                <div class="col-6">
                                    <div class="input-group">
                                        <label for="value_package" class="form-label">Add Product</label>
                                        <input class="form-control products select2" type="text" id="value_package"
                                            name="value_package">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="package_quantity" class="form-label">Package Quantity</label>
                                    <input class="form-control" type="number" id="package_quantity" name="package_quantity" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <table class="table table-bordered" id="selectedProductsTable">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>SKU</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="productList">
                                            <tr id="noProductRow">
                                                <td colspan="3" class="text-center">No products added</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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
    <div class="modal fade" role="dialog" id="editpromo">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Promo <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ url('/promos/update') }}" id="editForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="selected_products" id="selected_products_edit">
                        <input type="hidden" name="id" id="id">
                        <div class="row">
                            <div class="col-6">
                                <label for="name" class="form-label">Promo Name <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="text" id="name_edit" name="name" required>
                            </div>
                            <div class="col-6">
                                <label for="unique_code" class="form-label">Unique Code <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="text" id="unique_code_edit" name="unique_code" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label for="type" class="form-label">Type <span style="color: red">*</span></label>
                                <select class="form-control" name="type" id="type_edit" required>
                                    <option value="Nominal">Nominal</option>
                                    <option value="Percentage">Percentage</option>
                                    <option value="Package">Package</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <div id="div_nominal_edit">
                                    <label for="value" class="form-label">Value</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="groupNominal">Rp</span>
                                        </div>
                                        <input class="form-control" type="number" id="value_nominal_edit" name="value"
                                            required>
                                    </div>
                                </div>
                                <div id="div_percentage_edit">
                                    <label for="value" class="form-label">Value</label>
                                    <div class="input-group">
                                        <input class="form-control" type="number" id="value_percentage_edit" name="value"
                                            disabled required>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="groupPercentage">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label for="start_date" class="form-label">Start Date <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="date" id="start_date_edit" name="start_date" required>
                            </div>
                            <div class="col-6">
                                <label for="end_date" class="form-label">End Date <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="date" id="end_date_edit" name="end_date" required>
                            </div>
                        </div>
                        <br><br>
                        <div id="div_package_edit">
                            <div class="row">
                                <div class="col-6">
                                    <div class="input-group">
                                        <label for="value_package" class="form-label">Add Product</label>
                                        <input class="form-control products select2" type="text" id="value_package_edit"
                                            name="value_package">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="package_quantity" class="form-label">Package Quantity</label>
                                    <input class="form-control" type="number" id="package_quantity_edit" name="package_quantity" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <table class="table table-bordered" id="selectedProductsTableEdit">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>SKU</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="productListEdit">
                                            <tr id="noProductRowEdit">
                                                <td colspan="3" class="text-center">No products added</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="filterpromo">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Promo <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <label for="name_filter" class="form-label">Promo Name</label>
                            <input class="form-control" type="text" id="name_filter">
                        </div>
                        <div class="col-6">
                            <label for="unique_code_filter" class="form-label">Unique Code</label>
                            <input class="form-control" type="text" id="unique_code_filter">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="type_filter" class="form-label">Type</label>
                            <select class="form-control" id="type_filter">
                                <option value="Nominal">Nominal</option>
                                <option value="Percentage">Percentage</option>
                                <option value="Package">Package</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="value_filter" class="form-label">Value</label>
                            <input class="form-control" type="number" id="value_filter">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="start_date_filter" class="form-label">Start Date</label>
                            <input class="form-control" type="date" id="start_date_filter">
                        </div>
                        <div class="col-6">
                            <label for="end_date_filter" class="form-label">End Date</label>
                            <input class="form-control" type="date" id="end_date_filter">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="updated_at_filter" class="form-label">Last Updated Date</label>
                            <input class="form-control" type="date" id="updated_at_filter">
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
    @include('pages.promos.js.promos-js')
@endpush
