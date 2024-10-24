@extends('layouts.app')

@section('title', 'Appointments')

@push('style')
    {{-- <link rel="stylesheet" href="{{ asset('css/appointments/appointments.css') }}"> --}}

    <link rel="stylesheet" href="{{ asset('library/fullcalendar/dist/fullcalendar.min.css') }}">

    <style>
        .form-control.flatpickr {
            background-color: #fdfdff;
            color: #495057;
            border: 2px solid #f2f3fe;
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Appointments</h1>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex mb-3" style="flex-grow: 1; align-items: flex-end;">
                                        @can('appointments_create')
                                            <button style="max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-primary" data-toggle="modal" data-target="#addappointment"
                                                onclick="clearValue()">
                                                <i class="ion-plus-circled" data-pack="default" data-tags="sort"
                                                    style="font-size: 17px; margin-left: -5px; margin-right: 7px;"></i>
                                                Add New Appointment
                                            </button>
                                        @else
                                            <button style="max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-secondary" disabled>
                                                <i class="ion-plus-circled" data-pack="default" data-tags="sort"
                                                    style="font-size: 17px; margin-left: -5px; margin-right: 7px;"></i>
                                                Add New Appointment
                                            </button>
                                        @endcan

                                        <div style="margin-left: auto; display: flex; align-items: flex-end;">
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

                                <div class="card-body">
                                    <div class="fc-overflow">
                                        <div id="appointments"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>


    {{-- Modal Create --}}
    <div class="modal fade" role="dialog" id="addappointment">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Appointment <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="create_appointment_form" action="{{ url('/appointments/store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                                <label for="customer_name" class="form-label">Customer Name <span
                                        style="color: red">*</span></label>
                                <select class="form-control select2 customers" id="customer" name="customer_id"
                                    required></select>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="status" class="form-label">Status <span
                                            style="color: red">*</span></label>
                                    <select class="form-control" name="status" id="status" required>
                                        <option value="Pending">Pending</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label for="date_start" class="form-label">Date Start <span
                                        style="color: red">*</span></label>
                                <input class="form-control flatpickr" type="text" name="date_start" id="date_start"
                                    required>
                            </div>
                            <div class="col-6">
                                <label for="date_end" class="form-label">Date End <span style="color: red">*</span></label>
                                <input class="form-control flatpickr" type="text" name="date_end" id="date_end"
                                    required>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-6">
                                <label for="amount">Capster Name</label>
                                <select class="form-control select2 capsters" id="capster" name="capster_id"
                                    required></select>
                            </div>
                            <div class="col-6">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" name="remarks" id="remarks" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="submit_appointment_button" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Create Customers --}}
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
                <form method="post" action="{{ url('/customers/store_ajax') }}" id="customer_form">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <label for="full_name" class="form-label">Customer Name <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="text" id="customer_full_name" name="full_name"
                                    required>
                            </div>
                            <div class="col-6">
                                <label for="email" class="form-label">Email</label>
                                <input class="form-control" type="email" id="customer_email" name="email">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="gender" class="form-label">Gender <span
                                            style="color: red">*</span></label>
                                    <select class="form-control" name="gender" id="customer_gender" required>
                                        <option value="F">Female</option>
                                        <option value="M">Male</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="phone_number" class="form-label">Phone Number <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="number" name="phone_number" id="customer_phone"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button class="btn btn-primary" type="button" onclick="submitCustomer()">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="editappointment" role="dialog" aria-labelledby="editappointment" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editappointments">Edit Event</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="appointmentForm" action="{{ url('/appointments/update') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="customer_full_name_edit">Customer Name <span
                                            style="color: red">*</span></label>
                                    <select class="form-control select2 customers" id="customer_full_name_edit"
                                        name="customer_id" required></select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="status" class="form-label">Status <span
                                            style="color: red">*</span></label>
                                    <select class="form-control" name="status" id="status_edit" required>
                                        <option value="Pending">Pending</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="eventStart">Start Date</label>
                                    <input type="text" class="form-control flatpickr" id="start_edit" name="start"
                                        required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="eventEnd">End Date</label>
                                    <input type="text" class="form-control flatpickr" id="end_edit" name="end"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="amount">Capster Name</label>
                                    <select class="form-control select2 capsters" id="capster_edit" name="capster_id"
                                        required></select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="eventRemarks">Remarks</label>
                                    <textarea class="form-control" id="remarks_edit" name="remarks"></textarea>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="eventId" name="id">
                        <input type="hidden" id="customer_name" name="customer_name">
                        <input type="hidden" id="capster_name" name="capster_name">
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-danger mx-2" data-dismiss="modal">Close</button>
                        <button class="btn btn-success mx-2" id="create_transaction" type="button">Create Transaction</button>
                        <button class="btn btn-primary mx-2" type="submit">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Filter --}}
    {{-- <div class="modal fade" tabindex="-1" role="dialog" id="filterappointment">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Appointment <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <label for="full_name" class="form-label">Appointment Name</label>
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
    </div> --}}
@endsection

@push('scripts')
    @include('pages.appointments.js.appointments-js')
@endpush
