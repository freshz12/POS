@extends('layouts.app')

@section('title', 'Transactions')

@push('style')
@include('pages.transactions.css.transactions-css')
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Transactions</h1>
            </div>
            <div class="section-body">
                <div class="mt-5">
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Qty</th>
                                                <th class="text-right">Price</th>
                                                <th class="text-right">Price</th>
                                                <th class="text-right">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Cart items will be added here dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col">Total:</div>
                                        <div class="col text-right">Rp.<span id="totalAmount">0.00</span></div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col">
                                            <button class="btn btn-danger btn-block">Cancel</button>
                                        </div>
                                        <div class="col">
                                            <button class="btn btn-primary btn-block">Checkout</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-6">
                            <div class="card">
                                <div class="card-header" style="margin-bottom: 0;">
                                    <h4>Products</h4>
                                </div>
                                <div class="card-body product-list-container" style="padding-top: 0;">
                                    <div class="mb-3">
                                        <input type="text" class="form-control" id="searchProduct"
                                            placeholder="Search Product...">
                                    </div>
                                    <div class="row" id="productList">
                                        <!-- Products will be loaded here via AJAX -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

<!-- Quantity Modal -->
<div class="modal fade" id="quantityModal" tabindex="-1" role="dialog" aria-labelledby="quantityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quantityModalLabel">Enter Quantity</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="quantityForm">
                    <input type="hidden" id="productId" name="product_id">
                    <input type="hidden" id="productName" name="product_name">
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="text" class="form-control" id="quantity" name="quantity" readonly>
                    </div>
                    <div class="numpad">
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-4">
                                        <button type="button" class="numpad-button">7</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="numpad-button">8</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="numpad-button">9</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <button type="button" class="numpad-button">4</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="numpad-button">5</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="numpad-button">6</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <button type="button" class="numpad-button">1</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="numpad-button">2</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="numpad-button">3</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <button type="button" id="subtractButton" class="numpad-button btn-danger">
                                            -
                                        </button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="numpad-button">0</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" id="okButton" class="numpad-button btn-success">OK</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>











@endsection

@push('scripts')
    @include('pages.transactions.js.transactions-js')
@endpush
