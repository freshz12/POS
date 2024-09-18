@extends('layouts.app')

@section('title', 'Roles')

@push('style')
    {{-- <link rel="stylesheet" href="{{ asset('css/roles/roles.css') }}"> --}}
    <style>

        .bold-label {
            font-weight: bold;
        }
    </style>
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Roles</h1>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex mb-3" style="flex-grow: 1; align-items: flex-end;">
                                        <button style="max-height: 40px; display: flex; align-items: center;"
                                            class="btn btn-primary" data-toggle="modal" data-target="#addrole"
                                            onclick="getPermissions('create')">
                                            <i class="ion-plus-circled" data-pack="default" data-tags="sort"
                                                style="font-size: 17px; margin-left: -5px; margin-right: 7px;"></i>
                                            Add New Role
                                        </button>
                                        <div style="margin-left: auto; display: flex; align-items: flex-end;">
                                            <button
                                                style="margin-right: 10px; max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-info" data-toggle="modal" data-target="#filterrole">
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
                                                <th scope="col">Role Name</th>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="addrole">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Role <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="/roles/store" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <label for="name" class="form-label">Role Name <span style="color: red">*</span></label>
                                <input class="form-control" type="text" name="name" required>
                            </div>
                        </div>

                        <div class="main-content-inner">
                            <div class="row">
                                <div class="col-12 mt-3">
                                    <div class="card">

                                        <div class="form-group">
                                            <label for="name">Permissions</label>

                                            <div class="form-check">
                                                <input id="checkAll" onclick="toggleAllPermissions(this.checked)"
                                                    type="checkbox" class="form-check-input"> <label
                                                    class="form-check-label" for="checkPermissionAll">All</label>
                                            </div>
                                            <hr>
                                            <div id="permissions-container"></div>
                                        </div>
                                    </div>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="editrole">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Role <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="/roles/update" enctype="multipart/form-data" id="editForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <input class="form-control" type="hidden" id="edit_id" name="id" required>
                                <label for="name" class="form-label">Role Name <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="text" id="edit_name" name="name" required>
                            </div>
                        </div>

                        <div class="main-content-inner">
                            <div class="row">
                                <div class="col-12 mt-3">
                                    <div class="card">

                                        <div class="form-group">
                                            <label for="name">Permissions</label>

                                            <div class="form-check">
                                                <input id="checkEditAll" onclick="toggleEditAllPermissions(this.checked)"
                                                    type="checkbox" class="form-check-input"> <label
                                                    class="form-check-label" for="checkPermissionAll">All</label>
                                            </div>
                                            <hr>
                                            <div id="permissions-edit-container"></div>
                                        </div>
                                    </div>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="filterrole">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Role <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <label for="name" class="form-label">Role Name</label>
                            <input class="form-control" type="text" id="name_filter" name="name">
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
    @include('pages.settings.roles.js.roles-js')
@endpush
