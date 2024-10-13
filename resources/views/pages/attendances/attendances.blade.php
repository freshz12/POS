@extends('layouts.app')

@section('title', 'Attendances')

@push('style')
    @include('pages.attendances.css.attendances-css')
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="body-header">
                                <h1>Attendances</h1>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex mb-3" style="flex-grow: 1; align-items: flex-end;">
                                        @can('attendances_view')
                                            <a href="{{ url('/attendances/index_history') }}"
                                                style="max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-primary">
                                                View Attendance History
                                            </a>
                                        @else
                                            <button style="max-height: 40px; display: flex; align-items: center;"
                                                class="btn btn-secondary" disabled>
                                                View Attendance History
                                            </button>
                                        @endcan
                                    </div>
                                </div>

                                @if ($attendance !== null)
                                    <div class="last-checkin text-center">
                                        <p style="font-size: 1.5em;">Last Clock In:</p>
                                        <strong style="font-size: 1.8em;">{{ $attendance }}</strong>
                                    </div>
                                @endif

                                <div class="checkin-container d-flex justify-content-center align-items-center">
                                    <button type="button" class="circle-button" id="open-modal">
                                        <i class="fas {{ $attendance == null ? 'fa-sign-in-alt' : 'fa-sign-out-alt' }}"></i>
                                        <span style="display: block; font-size: 1em;">
                                            {{ $attendance == null ? 'Clock In' : 'Clock Out' }}
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-labelledby="photoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="photoModalLabel">Capture Your Photo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <video id="video" width="100%" height="400" autoplay></video>
                    <canvas id="canvas" width="800" height="600" style="display: none;"></canvas>
                    <input type="hidden" name="photo" id="photo-data" required>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-danger btn-lg" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-lg" id="capture">Capture Photo</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('pages.attendances.js.attendances-js')
@endpush
