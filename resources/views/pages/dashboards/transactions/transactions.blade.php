@extends('layouts.app')

@section('title', 'Transactions')

@push('style')
    {{-- <link rel="stylesheet" href="{{ asset('css/transactions/transactions.css') }}"> --}}
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Transactions</h1>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex mb-3" style="flex-grow: 1; align-items: flex-end;">
                                        {{-- <button style="max-height: 40px; display: flex; align-items: center;"
                                            class="btn btn-primary" data-toggle="modal" data-target="#addtransaction">
                                            <i class="ion-plus-circled" data-pack="default" data-tags="sort"
                                                style="font-size: 17px; margin-left: -5px; margin-right: 7px;"></i>
                                            Add New Transaction
                                        </button> --}}
                                        <div style="margin-left: auto; display: flex; align-items: flex-end;">
                                            <button
                                                style="margin-right: 10px; max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-info" data-toggle="modal" data-target="#filtertransaction">
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
                                                <th scope="col">Transaction ID</th>
                                                <th scope="col">Customer Name</th>
                                                <th scope="col">Discount Type</th>
                                                <th scope="col">Total Amount Before Discount</th>
                                                <th scope="col">Discount Value</th>
                                                <th scope="col">Final Total Amount</th>
                                                <th scope="col">Capster Name</th>
                                                <th scope="col">Payment Method</th>
                                                <th scope="col">Transaction Date</th>
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

    {{-- Modal Edit --}}
    <div class="modal fade" tabindex="-1" role="dialog" id="edittransaction">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transaction_id"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ url('/transactions/update') }}" enctype="multipart/form-data" id="editForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input class="form-control" type="hidden" id="id" name="id" required>
                            <div class="col-6">
                                <label for="customer_name" class="form-label">Customer Name</label>
                                <input class="form-control" type="text" id="customer_name" name="customer_name" readonly>
                            </div>
                            <div class="col-6">
                                <label for="capster_name" class="form-label">Capster Name</label>
                                <input class="form-control" type="text" id="capster_name" name="capster_name"
                                    readonly>
                            </div>
                        </div>
                        <br>
                        <h5>Products</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Unit Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="productTableBody">
                                <!-- Product rows will be dynamically inserted here -->
                            </tbody>
                        </table>

                        <br>
                        <br>
                        <h5>Promo Products</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Unit Price</th>
                                    {{-- <th>Quantity</th>
                                    <th>Total</th> --}}
                                </tr>
                            </thead>
                            <tbody id="promoProductTableBody">
                                
                            </tbody>
                        </table>

                        <div class="row">
                            <input class="form-control" type="hidden" id="id" name="id" required>
                            <div class="col-6">
                            </div>
                            <div class="col-6">
                                <label for="total_amount" class="form-label">Total Amount</label>
                                <input class="form-control" type="text" id="total_amount" name="total_amount" readonly>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="filtertransaction">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Transaction <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <label for="transaction_id_filter" class="form-label">Transaction ID</label>
                            <input class="form-control" type="text" id="transaction_id_filter"
                                name="transaction_id_filter">
                        </div>
                        <div class="col-6">
                            <label for="customer_name_filter" class="form-label">Customer Name</label>
                            <input class="form-control" type="text" id="customer_name_filter"
                                name="customer_name_filter">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="total_amount_filter" class="form-label">Total Amount</label>
                            <input class="form-control" type="number" id="total_amount_filter"
                                name="total_amount_filter">
                        </div>
                        <div class="col-6">
                            <label for="capster_name_filter" class="form-label">Capster Name</label>
                            <input class="form-control" type="text" id="capster_name_filter"
                                name="capster_name_filter">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="created_at_filter_from" class="form-label">Transaction Date From</label>
                            <input class="form-control" type="date" id="created_at_filter_from">
                        </div>
                        <div class="col-6">
                            <label for="created_at_filter_to" class="form-label">Transaction Date To</label>
                            <input class="form-control" type="date" id="created_at_filter_to">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="payment_method_filter" class="form-label">Payment Method</label>
                            <select class="form-control" id="payment_method_filter">
                                <option>Cash</option>
                                <option>QRIS</option>
                                <option>EDC</option>
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
    @include('pages.dashboards.transactions.js.transactions-js')
@endpush
