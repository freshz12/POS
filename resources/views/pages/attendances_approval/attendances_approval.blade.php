@extends('layouts.app')

@section('title', 'Attendances')

@push('style')
    {{-- @include('pages.attendances.css.attendances-css') --}}
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Attendances Approval</h1>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex mb-3" style="flex-grow: 1; align-items: flex-end;">
                                        <div style="margin-left: auto; display: flex; align-items: flex-end;">
                                            <button
                                                style="margin-right: 10px; max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-info" data-toggle="modal" data-target="#filterattendance">
                                                <i class="ion-funnel" data-pack="default" data-tags="sort"
                                                    style="font-size: 15px;"></i>
                                            </button>
                                            {{-- <button id="download_button" onclick="handleDownload()"
                                                style="margin-right: 10px; max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-success">
                                                <i class="ion-ios-download" data-pack="ios" data-tags="save, export"
                                                    style="font-size: 21px;"></i>
                                            </button> --}}
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
                                                <th scope="col">Employee Name</th>
                                                <th scope="col">Clock In</th>
                                                <th scope="col">Clock Out</th>
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

    {{-- Modal Edit --}}
    <div class="modal fade" tabindex="-1" role="dialog" id="editattendance">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approval Attendance <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ url('/attendances/approve_or_reject') }}" id="editForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="id" name="id" required>
                        <input type="hidden" id="approve_or_reject" name="approve_or_reject" required>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="employee_name" class="form-label">Employee Name</label>
                                    <input class="form-control" type="text" id="employee_name" name="employee_name"
                                        disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="clock_in" class="form-label">Clock In</label>
                                    <input class="form-control flatpickr" type="text" id="clock_in" name="clock_in"
                                    disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="clock_out" class="form-label">Clock Out</label>
                                    <input class="form-control flatpickr" type="text" id="clock_out" name="clock_out"
                                    disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="request_reason" class="form-label">Request Reason</label>
                                    <textarea class="form-control" type="text" id="request_reason" rows="3" name="request_reason" disabled></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="approved_or_rejected_reason" class="form-label">Approve or Reject Reason</label>
                                    <textarea class="form-control" type="text" rows="3" name="approved_or_rejected_reason" id="approved_or_rejected_reason" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button onclick="reject()" class="btn btn-danger btn-lg mx-2">Reject</button>
                        <button onclick="approve()" class="btn btn-success btn-lg mx-2">Approve</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Filter --}}
    <div class="modal fade" tabindex="-1" role="dialog" id="filterattendance">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Attendance Approval<br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <label for="full_name_filter" class="form-label">Employee Name</label>
                            <input class="form-control" type="text" id="full_name_filter" name="full_name">
                        </div>
                        <div class="col-6">
                            <label for="c" class="form-label">Request Reason</label>
                            <input class="form-control" type="text" id="request_reason_filter" name="full_name">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="clock_in_from_filter" class="form-label">Clock In From</label>
                                <input class="form-control" type="date" id="clock_in_from_filter"
                                    name="clock_in_from">
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="clock_in_to_filter" class="form-label">Clock In To</label>
                            <input class="form-control" type="date" id="clock_in_to_filter" name="clock_in_to">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="clock_out_from_filter" class="form-label">Clock Out From</label>
                                <input class="form-control" type="date" id="clock_out_from_filter"
                                    name="clock_out_from">
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="clock_out_to_filter" class="form-label">Clock Out To</label>
                            <input class="form-control" type="date" id="clock_out_to_filter" name="clock_out_to">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="approved_or_rejected_reason_filter" class="form-label">Approved Or Rejected
                                Reason</label>
                            <input class="form-control" type="text" id="approved_or_rejected_reason_filter">
                        </div>
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
    @include('pages.attendances_approval.js.attendances-approval-js')
@endpush
