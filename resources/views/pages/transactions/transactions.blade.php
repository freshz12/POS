<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Densetsu</title>

    <link rel="stylesheet" href="{{ asset('library/bootstrap/dist/css/bootstrap.min.css') }}">
    <script src="{{ asset('library/jquery/dist/jquery.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @include('pages.transactions.css.transactions-css')
    <link rel="stylesheet" href="{{ asset('library/font-awesome/css/all.min.css') }}">
</head>

<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>
    @if (session('success_message'))
        <script>
            $(document).ready(function() {
                swal('Success', '{{ session('success_message') }}', 'success');

                setTimeout(function() {
                    swal.close();
                }, 5000);
            });
        </script>
    @endif

    @if (session('change_message'))
        <script>
            $(document).ready(function() {
                swal('Success', '{{ session('change_message') }}', 'success');
            });
        </script>
    @endif

    @if ($errors->has('error_message'))
        <script>
            $(document).ready(function() {
                swal('Error', @json($errors->first('error_message')) + '!', 'error');
            });
        </script>
    @endif

    <div class="container-fluid">
        <!-- Top Redirect Button -->
        {{-- <div class="d-flex justify-content-start p-3">
            <a href="your_redirect_url" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div> --}}

        <section class="row no-gutters vh-100">
            <div class="col-md-6 col-lg-5 p-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="table-responsive flex-grow-1">
                            <table class="table table-striped">
                                <thead class="thead-fixed">
                                    <tr>
                                        <th class="text-center" style="width: 30%">Product</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Cart items will be added here dynamically -->
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer mt-auto">
                            <div class="row" style="font-size: 1.5em;">
                                <div class="col">Total Before Discount:</div>
                                <div class="col text-right">Rp.<span id="totalAmount">0</span></div>
                            </div>
                            <div class="row" style="font-size: 1.5em;">
                                <div class="col">Discount:</div>
                                <div class="col text-right" id="Totaldiscount"></div>
                            </div>
                            <div class="row" style="font-size: 1.5em;">
                                <div class="col">Sub Total:</div>
                                <div class="col text-right">Rp.<span id="finalTotalAmount">0</span></div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <button class="btn btn-danger btn-block" style="height: 50px"
                                        id="resetCartButton">Reset</button>
                                </div>
                                <div class="col">
                                    <button class="btn btn-info btn-block" style="height: 50px" id="addCoupon"
                                        data-toggle="modal" data-target="#addCouponModal">Coupon</button>
                                </div>
                                <div class="col">
                                    <button class="btn btn-success btn-block" style="height: 50px"
                                        id="payButton">Pay</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-7 p-3">
                <div class="card h-100">
                    <div class="card-header" style="position: relative;">
                        <a href="/dashboards/main"
                            style="display: block; background-color: #28a745; color: white; padding: 7px 20px; border-radius: 5px; text-align: center; text-decoration: none; cursor: pointer; position: relative; z-index: 2;">
                            <i class="fa fa-pie-chart" aria-hidden="true"></i>
                            Dashboard
                        </a>
                        <h4
                            style="position: absolute; width: 100%; text-align: center; top: 50%; transform: translateY(-50%); margin: 0; font-size: 2rem; z-index: 1;">
                            Products
                        </h4>
                    </div>


                    <div class="card-body d-flex flex-column">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="searchProduct"
                                placeholder="Search Product...">
                        </div>
                        <div class="row" id="productList">
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Quantity Modal -->
    <div class="modal fade" id="quantityModal" tabindex="-1" role="dialog" aria-labelledby="quantityModalLabel"
        aria-hidden="true">
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
                        <input type="hidden" id="productPrice" name="product_price">
                        <input type="hidden" id="availableQuantity" name="available_quantity">

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
                                            <button type="button" id="subtractButton"
                                                class="numpad-button btn-danger">
                                                -
                                            </button>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" class="numpad-button">0</button>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" id="okButton"
                                                class="numpad-button btn-success">OK</button>
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

    <div class="modal fade" id="addCouponModal" role="dialog" aria-labelledby="addCouponModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCouponModalLabel">Enter Coupon Unique Code</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            {{-- <label for="full_name" class="form-label">Enter Promo Unique Code</label> --}}
                            <select class="form-control select2 coupon" id="coupon" name="coupon"></select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="button" onclick="addCoupon()">Add</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Enter Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="paymentForm" method="POST" action="{{ url('/transactions/store') }}">
                        @csrf
                        <input type="hidden" id="customerIdInput" name="customer_id" value="{{ $customer_id }}">
                        <input type="hidden" id="capsterIdInput" name="capster_id" value="{{ $capster_id }}">
                        <input type="hidden" id="customerNameInput" name="customer_name"
                            value="{{ $customer_name }}">
                        <input type="hidden" id="capsterNameInput" name="capster_name"
                            value="{{ $capster_name }}">
                        <input type="hidden" id="totalAmountBeforeDiscount" name="amount_before_discount">
                        <input type="hidden" id="totalAmountInput" name="total_amount">
                        <input type="hidden" id="cartItemsInput" name="cart_items">
                        <input type="hidden" id="promoIdInput" name="promo_id">
                        <input type="hidden" id="paymentType" name="payment_method">
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="text" class="form-control" id="amount" name="amount" readonly>
                        </div>
                        <div class="numpad">
                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-4">
                                            <button type="button" class="numpad-button-payment">7</button>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" class="numpad-button-payment">8</button>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" class="numpad-button-payment">9</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <button type="button" class="numpad-button-payment">4</button>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" class="numpad-button-payment">5</button>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" class="numpad-button-payment">6</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <button type="button" class="numpad-button-payment">1</button>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" class="numpad-button-payment">2</button>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" class="numpad-button-payment">3</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <button type="button" id="subtractButtonPayment"
                                                class="numpad-button-payment btn-danger">
                                                -
                                            </button>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" class="numpad-button-payment">0</button>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" id="payButtonModal"
                                                class="numpad-button-payment-ok btn-success">OK</button>
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

    <!-- Custom Price Modal -->
    <div class="modal fade" id="customPriceModal" tabindex="-1" role="dialog"
        aria-labelledby="customPriceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customPriceModalLabel">Enter Custom Price</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="text" class="form-control" id="amountCustomPrice" name="amount" readonly>
                    </div>
                    <div class="numpad">
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-4">
                                        <button type="button" class="numpad-button-custom-price">7</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="numpad-button-custom-price">8</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="numpad-button-custom-price">9</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <button type="button" class="numpad-button-custom-price">4</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="numpad-button-custom-price">5</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="numpad-button-custom-price">6</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <button type="button" class="numpad-button-custom-price">1</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="numpad-button-custom-price">2</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="numpad-button-custom-price">3</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <button type="button" id="subtractButtonCustomPrice"
                                            class="numpad-button-custom-price btn-danger">
                                            -
                                        </button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="numpad-button-custom-price">0</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" id="payButtonModal"
                                            class="numpad-button-custom-price-ok btn-success">OK</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Customer Capster Modal --}}
    <div class="modal fade" id="selectCustomerModal" role="dialog" aria-labelledby="selectCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="selectCustomerModalLabel">Select Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="amount">Customer Name</label>
                        <select class="form-control select2 customers" id="customer" name="customer_id"
                            required></select>
                        <label for="amount">Capster Name</label>
                        <select class="form-control select2 capsters" id="capster" name="capster_id"
                            required></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="okCustomer">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paymentMethodModal" role="dialog" aria-labelledby="paymentMethodModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentMethodModalLabel">Select Payment Method</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="payment-buttons text-center">
                            <button type="button" class="btn btn-primary btn-lg m-2 payment-btn"
                                data-payment="Cash">
                                Cash
                            </button>
                            <button type="button" class="btn btn-success btn-lg m-2 payment-btn" data-payment="EDC">
                                EDC
                            </button>
                            <button type="button" class="btn btn-info btn-lg m-2 payment-btn" data-payment="QRIS">
                                QRIS
                            </button>
                        </div>
                    </div>
                </div>
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

    <!-- Additional Scripts -->
    <script src="{{ asset('library/popper.js/dist/umd/popper.js') }}"></script>
    <script src="{{ asset('library/tooltip.js/dist/umd/tooltip.js') }}"></script>
    <script src="{{ asset('library/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('library/jquery.nicescroll/dist/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('library/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('js/stisla.js') }}"></script>
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('library/datatables/media/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/page/bootstrap-modal.js') }}"></script>
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('library/sweetalert/dist/sweetalert.min.js') }}"></script>

    @stack('scripts')

    <!-- Template JS File -->
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>

    @include('pages.transactions.js.transactions-js')
</body>

</html>
