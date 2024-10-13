@extends('layouts.app')

@section('title', 'Capsters')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/ionicons201/css/ionicons.min.css') }}">

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
                <h1>Capsters</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex mb-3" style="flex-grow: 1; align-items: flex-end;">
                                        @can('capsters_create')
                                            <button style="max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-primary" data-toggle="modal" data-target="#addcapster">
                                                <i class="ion-plus-circled" data-pack="default" data-tags="sort"
                                                    style="font-size: 17px; margin-left: -5px; margin-right: 7px;"></i>
                                                Add New Capster
                                            </button>
                                        @else
                                            <button style="max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-secondary" disabled>
                                                <i class="ion-plus-circled" data-pack="default" data-tags="sort"
                                                    style="font-size: 17px; margin-left: -5px; margin-right: 7px;"></i>
                                                Add New Capster
                                            </button>
                                        @endcan

                                        <div style="margin-left: auto; display: flex; align-items: flex-end;">
                                            <button
                                                style="margin-right: 10px; max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-info" data-toggle="modal" data-target="#filtercapster">
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
                                            <th scope="col">Capster Name</th>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="addcapster">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Capster <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ url('/capsters/store') }}" enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col">
                                <label for="capster_name" class="form-label">Capster Name <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="text" name="full_name" required>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="editcapster">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Capster <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ url('/capsters/update') }}" enctype="multipart/form-data" id="editForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input class="form-control" type="hidden" name="id" id="id" required>
                            <div class="col">
                                <label for="capster_name" class="form-label">Capster Name <span
                                        style="color: red">*</span></label>
                                <input class="form-control" type="text" name="full_name" id="capster_name" required>
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
    <div class="modal fade" tabindex="-1" role="dialog" id="filtercapster">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Capster <br>
                        <br>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <label for="capster_name" class="form-label">Capster Name</label>
                            <input class="form-control" type="text" id="capster_name_filter">
                        </div>
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
    @include('pages.capsters.js.capsters-js')
@endpush
