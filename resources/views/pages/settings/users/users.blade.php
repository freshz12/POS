@extends('layouts.app')

@section('title', 'Users')

@push('style')
    {{-- <link rel="stylesheet" href="{{ asset('css/users/users.css') }}"> --}}
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Users</h1>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex mb-3" style="flex-grow: 1; align-items: flex-end;">
                                        @can('users_create')
                                            <button style="max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-primary" data-toggle="modal" data-target="#adduser">
                                                <i class="ion-plus-circled" data-pack="default" data-tags="sort"
                                                    style="font-size: 17px; margin-left: -5px; margin-right: 7px;"></i>
                                                Add New User
                                            </button>
                                        @else
                                            <button style="max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-secondary" disabled>
                                                <i class="ion-plus-circled" data-pack="default" data-tags="sort"
                                                    style="font-size: 17px; margin-left: -5px; margin-right: 7px;"></i>
                                                Add New User
                                            </button>
                                        @endcan

                                        <div style="margin-left: auto; display: flex; align-items: flex-end;">
                                            <button
                                                style="margin-right: 10px; max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-info" data-toggle="modal" data-target="#filteruser">
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
                                                <th scope="col">Name</th>
                                                <th scope="col">Username</th>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="adduser">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ url('/users/store') }}" enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col">
                                <label for="name" class="form-label">Name <span style="color: red">*</span></label>
                                <input class="form-control" type="text" name="name" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="username" class="form-label">Username <span style="color: red">*</span></label>
                                <input class="form-control" type="username" name="username">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="password" class="form-label">Password <span style="color: red">*</span></label>
                                <input class="form-control" type="password" name="password" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="role" class="form-label">Role <span style="color: red">*</span></label>
                                <select class="form-control" name="role" id="role" required>
                                </select>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="edituser">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ url('/users/update') }}" enctype="multipart/form-data" id="editForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input class="form-control" type="hidden" id="id" name="id" required>
                            <div class="col">
                                <label for="name" class="form-label">Name <span style="color: red">*</span></label>
                                <input class="form-control" type="text" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="username" class="form-label">Username</label>
                                <input class="form-control" type="username" id="username" name="username">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="role" class="form-label">Role <span style="color: red">*</span></label>
                                <select class="form-control" name="role" id="editrole" required>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="password" class="form-label">Change New Password</label>
                                <input class="form-control" type="text" id="editpassword" name="password">
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
    <div class="modal fade" tabindex="-1" role="dialog" id="filteruser">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter User <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <label for="name" class="form-label">Name</label>
                            <input class="form-control" type="text" id="name_filter" name="name">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="username" class="form-label">Username</label>
                            <input class="form-control" type="text" id="username_filter" name="username">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
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
    @include('pages.settings.users.js.users-js')
@endpush
