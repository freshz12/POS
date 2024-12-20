@extends('layouts.app')

@section('title', 'Summary Payment')

@push('style')
    {{-- <link rel="stylesheet" href="{{ asset('css/products/products.css') }}"> --}}
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Summary Payment</h1>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex mb-3" style="flex-grow: 1; align-items: flex-end;">
                                        <div style="margin-left: auto; display: flex; align-items: flex-end;">
                                            <select class="form-control" style="margin-right: 10px; width: 100px;" onchange="changeCreatedTypeFilter(this.value)" id="created_type_select">
                                                <option selected value="today">Today</option>
                                                <option value="this_week">This Week</option>
                                                <option value="this_month">This Month</option>
                                                <option value="this_year">This Year</option>
                                            </select>
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

                                <div class="table-responsive">
                                    <table class="table-hover table" id="datatable" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 1%">#</th>
                                                <th scope="col">Transaction Period</th>
                                                <th scope="col">Total Customer</th>
                                                <th scope="col">Total Amount</th>
                                                <th scope="col">Total Cash</th>
                                                <th scope="col">Total EDC</th>
                                                <th scope="col">Total QRIS</th>
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

    {{-- Modal Filter --}}
    <div class="modal fade" tabindex="-1" role="dialog" id="filterproduct">
        <div class="modal-dialog modal-dialog-centered" role="document">
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
                    <input type="hidden" id="created_type" name="created_type">
                    <div class="row">
                        <div class="col">
                            <label for="created_from_filter" class="form-label">Transaction Date From</label>
                            <input class="form-control" type="date" id="created_from_filter"
                                name="created_from_filter">
                        </div>
                        <div class="col">
                            <label for="created_to_filter" class="form-label">Transaction Date To</label>
                            <input class="form-control" type="date" id="created_to_filter"
                                name="created_to_filter">
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
    @include('pages.dashboards.summary_payment.js.summary_payment-js')
@endpush
