@extends('layouts.app')

@section('title', 'Customers')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/customers/customers.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Customers</h1>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex mb-3" style="flex-grow: 1; align-items: flex-end;">
                                        <button style="max-height: 40px; display: flex; align-items: center;"
                                            class="btn btn-primary" data-toggle="modal" data-target="#addcustomer">
                                            <i class="ion-plus-circled" data-pack="default" data-tags="sort"
                                                style="font-size: 17px; margin-left: -5px; margin-right: 7px;"></i>
                                            Add New Customer
                                        </button>
                                        <div style="margin-left: auto; display: flex; align-items: flex-end;">
                                            <button
                                                style="margin-right: 10px; max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-info" data-toggle="modal" data-target="#filtercustomer">
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
                                                <th scope="col">Customer Name</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Gender</th>
                                                <th scope="col">Phone Number</th>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="addcustomer">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Customer <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="/customers/store" enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <label for="full_name" class="form-label">Customer Name <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="text" name="full_name" required>
                            </div>
                            <div class="col-6">
                                <label for="email" class="form-label">Email</label>
                                <input class="form-control" type="email" name="email">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="gender" class="form-label">Gender <span
                                            style="color: red">*</span></label>
                                    <select class="form-control" name="gender" required>
                                        <option value="F">Female</option>
                                        <option value="M">Male</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="phone_number" class="form-label">Phone Number <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="number" name="phone_number" required>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="editcustomer">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Customer <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="/customers/update" enctype="multipart/form-data" id="editForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                                <input class="form-control" type="hidden" id="id" name="id" required>
                            <div class="col-6">
                                <label for="full_name" class="form-label">Customer Name <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="text" id="full_name" name="full_name" required>
                            </div>
                            <div class="col-6">
                                <label for="email" class="form-label">Email</label>
                                <input class="form-control" type="email" id="email" name="email">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="gender" class="form-label">Gender <span
                                            style="color: red">*</span></label>
                                    <select class="form-control" name="gender" id="gender" required>
                                        <option value="F">Female</option>
                                        <option value="M">Male</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="phone_number" class="form-label">Phone Number <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="number" name="phone_number" id="phone_number"
                                    required>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="filtercustomer">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Customer <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <label for="full_name" class="form-label">Customer Name</label>
                            <input class="form-control" type="text" id="full_name_filter" name="full_name">
                        </div>
                        <div class="col-6">
                            <label for="email" class="form-label">Email</label>
                            <input class="form-control" type="text" id="email_filter" name="email">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-control" name="gender" id="gender_filter">
                                    <option value="B">Female and Male</option>
                                    <option value="F">Female</option>
                                    <option value="M">Male</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input class="form-control" type="number" id="phone_number_filter" name="phone_number">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="quantity" class="form-label">Last Updated Date</label>
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
    @include('pages.customers.js.customers-js')
@endpush
