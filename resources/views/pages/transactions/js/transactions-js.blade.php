<script>
    let allProducts = [];

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        $('#customer').val(null).trigger('change');
        $('#capster').val(null).trigger('change');

        var selectedCustomerId = $('#customerIdInput').val();
        var selectedCustomerName = $('#customerNameInput').val();
        if (selectedCustomerId) {
            var CustomerSelect = $('.select2#customer');
            var optionCustomer = new Option(selectedCustomerName, selectedCustomerId, true, true);
            CustomerSelect.append(optionCustomer).trigger('change');
            CustomerSelect.val(selectedCustomerId).trigger('change');
        }

        var selectedCapsterId = $('#capsterIdInput').val();
        var selectedCapsterName = $('#capsterNameInput').val();

        if (selectedCapsterId) {
            var capsterSelect = $('.select2#capster');
            var optionCapster = new Option(selectedCapsterName, selectedCapsterId, true, true);
            capsterSelect.append(optionCapster).trigger('change');
            capsterSelect.val(selectedCapsterId).trigger('change');
        }

        fetchProducts();
        $('#searchProduct').on('input', function() {
            const query = $(this).val().toLowerCase();
            const filteredProducts = allProducts.filter(product =>
                product.product_name.toLowerCase().includes(query)
            );
            displayProducts(filteredProducts);
        });

        $('#productList').on('click', '.product-item', function() {
            var productCard = $(this);
            var productId = productCard.data('id');
            var productName = productCard.data('name');
            var productPrice = productCard.data('price');
            var productQuantity = productCard.data('quantity');

            if (productQuantity <= 0) {
                swal('Out of Stock', 'This product is currently out of stock and cannot be added!',
                    'error');
                return; // Prevent the modal from opening
            }

            $('#productId').val(productId);
            $('#productName').val(productName);
            $('#productPrice').val(productPrice);
            $('#availableQuantity').val(productQuantity);
            $('#quantityModalLabel').text('Enter Quantity for ' + productName);
            $('#quantity').val('');
            $('#quantityModal').modal('show');
        });


        $(document).on('click', '.numpad-button', function() {
            var currentValue = $('#quantity').val();
            var buttonValue = $(this).text();
            var newValue;

            if ($(this).attr('id') === 'subtractButton') {
                $('#quantity').val(currentValue.slice(0, -1));
            } else if ($(this).attr('id') === 'okButton') {
                var quantityValue = $('#quantity').val();
                var availableQuantity = $('#availableQuantity').val(); // Get available quantity

                // Validate entered quantity
                if (quantityValue === '' || parseInt(quantityValue, 10) < 1) {
                    swal('Error', 'Please enter a quantity of 1 or greater!', 'error');
                } else if (parseInt(quantityValue, 10) > parseInt(availableQuantity, 10)) {
                    swal('Error', 'Entered quantity exceeds available stock!', 'error');
                } else {
                    $('#quantityForm').submit();
                }
            } else {
                if (currentValue === '0' && buttonValue !== '0') {
                    newValue = buttonValue;
                } else {
                    newValue = currentValue + buttonValue;
                }

                newValue = Math.max(parseInt(newValue), 1).toString();
                $('#quantity').val(newValue);
            }
        });

        $(document).on('click', '.numpad-button-payment', function() {
            var currentValue = $('#amount').val().replace(/\./g, '');
            var buttonValue = $(this).text();
            var newValue;

            if ($(this).attr('id') === 'subtractButtonPayment') {
                currentValue = currentValue.slice(0, -1);
                if (currentValue === '') {
                    currentValue = '0';
                }
            } else {

                if (currentValue === '0' && buttonValue !== '0') {
                    newValue = buttonValue;
                } else {
                    newValue = currentValue + buttonValue;
                }
                currentValue = newValue;
            }


            let formattedAmount = formatNumberWithCommas(parseInt(currentValue,
                10));
            $('#amount').val(formattedAmount);
        });



        $('#quantityModal').on('show.bs.modal', function() {
            $('#quantity').val('');
        });


        $('#quantityForm').submit(function(e) {
            e.preventDefault();

            var productId = $('#productId').val();
            var productName = $('#productName').val();
            var quantity = $('#quantity').val();
            var productPrice = $('#productPrice').val();
            var formattedPrice = formatNumberWithCommas(productPrice);

            var existingRow = $('table.table tbody tr').filter(function() {
                return $(this).data('id') == productId;
            });

            if (existingRow.length) {

                var currentQuantity = existingRow.find('.quantity-input').val();
                var newQuantity = (parseInt(currentQuantity) + parseInt(quantity))
                    .toString();
                existingRow.find('.quantity-input').val(newQuantity);
            } else {
                var newRow = `<tr data-id="${productId}">
                                <td class="text-center">${productName}</td>
                                <td>
                                    <div class="quantity-container d-flex align-items-center">
                                        <button class="btn btn-primary btn-lg quantity-button quantity-button-minus" type="button">-</button>
                                        <input type="text" class="form-control text-center quantity-input" style="width: 60px; height: 45px;" value="${quantity}" readonly>
                                        <button class="btn btn-primary btn-lg quantity-button quantity-button-plus" type="button">+</button>
                                    </div>
                                </td>
                                <td class="text-right price-cell">${formattedPrice}</td>
                                <td class="text-right trash-container">
                                    <button class="btn btn-danger btn-lg trash-button"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>`;
                $('table.table tbody').append(newRow);
            }

            updateTotalAmount();
            $('#quantityModal').modal('hide');
        });


        // function updateTotalAmount() {
        //     var totalAmount = 0;
        //     $('table.table tbody tr').each(function() {
        //         var priceText = $(this).find('.price-cell').text();
        //         var quantityText = $(this).find('.quantity-input').val();

        //         var price = parseFloat(priceText.replace(/\./g, '').replace(/,/g, '.'));
        //         var quantity = parseInt(quantityText, 10);

        //         if (!isNaN(price) && !isNaN(quantity)) {
        //             totalAmount += price * quantity;
        //         }
        //     });

        //     let formattedAmount = formatNumberWithCommas(totalAmount);

        //     $('#totalAmount').text(formattedAmount);
        // }

        $(document).on('click', '.quantity-button-plus', function() {
            var quantityInput = $(this).siblings('.quantity-input');
            var currentQuantity = quantityInput.val();
            quantityInput.val((parseInt(currentQuantity) + 1).toString());
            updateTotalAmount();
        });

        $(document).on('click', '.quantity-button-minus', function() {
            var quantityInput = $(this).siblings('.quantity-input');
            var currentQuantity = quantityInput.val();
            var newQuantity = Math.max(parseInt(currentQuantity) - 1,
                1);
            quantityInput.val(newQuantity.toString());
            updateTotalAmount();
        });


        $(document).on('click', '.trash-button', function() {
            $(this).closest('tr').remove();
            updateTotalAmount();
        });

        $('#resetCartButton').on('click', function() {
            $('.select2.coupon').val(null).trigger('change');
            $('.select2.coupon').removeData('unique-number').removeData('value').removeData('type');
            $('table.table tbody').empty();
            updateTotalAmount();
        });

        $('#payButton').on('click', function() {
            if ($('table.table tbody tr').length === 0) {
                swal('Your cart is empty', 'Please add products to the cart before proceeding!',
                    'error');
            } else {
                $('#selectCustomerModal').modal('show');
            }
        });

        $('#okCustomer').on('click', function() {
            if ($('#customer').val() == null || $('#capster').val() == null) {
                swal('Error',
                    'Please select both customer and capster!', 'error');
                return;
            }

            $('#selectCustomerModal').modal('hide');
            $('#paymentMethodModal').modal('show');
        });

        $('.payment-btn').on('click', function() {
            let paymentType = $(this).data('payment');
            $('#paymentType').val(paymentType);

            if (paymentType !== 'Cash') {
                submitTransaction(false);
            } else {
                $('#paymentMethodModal').modal('hide');
                $('#amount').val('');
                $('#amount').val($('#finalTotalAmount').text());

                $('#paymentModal').modal('show');
            }
        });

        function submitTransaction(isCash = false) {
            showLoading();
            let finalTotalAmount = $('#finalTotalAmount').text().trim();
            let totalAmountInput = $('#totalAmount').text().trim();
            let totalAmount = parseFloat(finalTotalAmount.replace(/\./g, '').replace(/,/g, '.'));

            cashPaidisCash = isCash ? $('#amount').val() : $('#finalTotalAmount').text();

            let cashPaidText = cashPaidisCash.trim();
            let customerId = $('#customer').val();
            let capsterId = $('#capster').val();
            let promoIdInput = $('.select2.coupon').val();
            let cashPaid = parseFloat(cashPaidText.replace(/\./g, '').replace(/,/g, '.'));

            if (cashPaid < totalAmount) {
                swal('Insufficient Payment',
                    'The amount you have entered is less than the total amount.', 'error');
                return;
            }

            let cartItems = getCartItems();

            $('#totalAmountInput').val(finalTotalAmount);
            $('#totalAmountBeforeDiscount').val(totalAmountInput);
            $('#amount').val(cashPaidText);
            $('#cartItemsInput').val(cartItems);
            $('#customerIdInput').val(customerId);
            $('#capsterIdInput').val(capsterId);
            $('#promoIdInput').val(promoIdInput);

            $('#paymentForm').submit();
        }


        $('#payButtonModal').on('click', function() {
            submitTransaction(true);
        });

    });

    function getCartItems() {
        let cartItems = [];

        $('tbody tr').each(function(index) {

            let product = {
                id: $(this).data('id'),
                name: $(this).find('td.text-center').first().text()
                    .trim(),
                qty: $(this).find('.quantity-input').val().trim(),
                price: $(this).find('.price-cell').text().trim()
            };

            if (product.id && product.name && product.qty && product.price && product.price !== "0") {
                cartItems.push(product);
            }
        });


        return JSON.stringify(cartItems);
    }

    function fetchProducts() {
        $.ajax({
            url: '/products/index_data',
            method: 'GET',
            success: function(response) {
                allProducts = response.data;
                displayProducts(allProducts);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching products:', error);
            }
        });
    }

    function displayProducts(products) {
        $('#productList').empty();

        products.sort(function(a, b) {
            return (a.quantity > 0 ? -1 : 1) - (b.quantity > 0 ? -1 : 1);
        });

        products.forEach(function(product) {
            const isOutOfStock = product.quantity <= 0;
            const cardClass = isOutOfStock ? 'out-of-stock' : '';
            const overlayClass = isOutOfStock ? 'out-of-stock-overlay' : '';

            $('#productList').append(`
            <div class="col-6 col-md-4 col-lg-3 mb-3">
                <div class="card product-item ${cardClass}" data-id="${product.id}" data-quantity="${product.quantity}" data-name="${product.product_name}" data-price="${product.selling_price}">
                    <img src="/storage/${product.picture_path || 'files/default/product.png'}" class="card-img-top ${overlayClass}" alt="${product.product_name}">
                    <div class="product-overlay ${overlayClass}">${product.product_name} (${product.quantity})</div>
                </div>
            </div>
        `);
        });
    }

    function formatNumberWithCommas(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }


    function updateTotalAmount(packageNominal = null) {
        var originalTotalAmount = 0;

        $('table.table tbody tr').each(function() {
            var priceText = $(this).find('.price-cell').text();
            var quantityText = $(this).find('.quantity-input').val();

            var price = parseFloat(priceText.replace(/\./g, '').replace(/,/g, '.'));
            var quantity = parseInt(quantityText, 10);

            if (!isNaN(price) && !isNaN(quantity)) {
                originalTotalAmount += price * quantity;
            }
        });

        var totalAmount = originalTotalAmount; // Set initial total amount

        var couponType = $('.select2.coupon').data('type');
        var couponValue = $('.select2.coupon').data('value');

        var discountValue = 0;

        if (couponType === 'Nominal' && couponValue) {
            discountValue = parseFloat(couponValue);
            totalAmount = Math.max(originalTotalAmount - discountValue, 0);
            $('#Totaldiscount').text('Rp.' + formatNumberWithCommas(discountValue));
        } else if (couponType === 'Percentage' && couponValue) {
            discountValue = originalTotalAmount * (couponValue / 100);
            totalAmount = Math.max(originalTotalAmount - discountValue, 0);
            $('#Totaldiscount').text(couponValue + '%');
        } else if (couponType === 'Package') {
            let totalDiscountText = $('#Totaldiscount').text();
            let numericValue = parseInt(totalDiscountText.replace(/[^0-9]/g, ''), 10);
            originalTotalAmount += numericValue;
        } else {
            $('#Totaldiscount').text('No discount');
        }

        let formattedOriginalAmount = formatNumberWithCommas(originalTotalAmount);
        let formattedAmount = formatNumberWithCommas(totalAmount);

        $('#totalAmount').text(formattedOriginalAmount);

        $('#finalTotalAmount').text(formattedAmount);
    }

    function addCoupon() {
        $('#coupon').val();
        var selectedCoupon = $('.select2.coupon').select2('data')[0];
        $('.select2.coupon').data('unique-code', selectedCoupon.unique_code);
        $('.select2.coupon').data('value', selectedCoupon.value);
        $('.select2.coupon').data('type', selectedCoupon.type);

        if (selectedCoupon.type === 'Package') {
            prods = $.ajax({
                url: '/products/get-products',
                method: 'POST',
                data: {
                    product_id: selectedCoupon.product_id
                },
                success: function(response) {
                    let products = response.data;
                    response.data.forEach(function(productId) {
                        const productName = `${productId.product_name}`;
                        const quantity = 1;
                        const formattedPrice = '0';

                        // Create the new row with the specified details
                        var newRow = `<tr data-id="${productId.id}">
                        <td class="text-center">${productName}</td>
                        <td>
                            <div class="quantity-container d-flex align-items-center">
                                <button class="btn btn-primary btn-lg quantity-button quantity-button-minus" type="button" disabled>-</button>
                                <input type="text" class="form-control text-center quantity-input" style="width: 60px; height: 45px;" value="${quantity}" readonly>
                                <button class="btn btn-primary btn-lg quantity-button quantity-button-plus" type="button" disabled>+</button>
                            </div>
                        </td>
                        <td class="text-right price-cell">${formattedPrice}</td>
                        <td class="text-right trash-container">
                            <button class="btn btn-danger btn-lg trash-button" disabled><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>`;

                        // Append the new row to the table body
                        $('table.table tbody').append(newRow);
                    });

                    let totalSellingPrice = 0;
                    products.forEach(function(product) {
                        totalSellingPrice += parseInt(product.selling_price);
                    });
                    $('#Totaldiscount').text('Rp.' + formatNumberWithCommas(totalSellingPrice));
                    updateTotalAmount(totalSellingPrice);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching products:', error);
                }
            });
        } else {
            updateTotalAmount();
        }

        $('#addCouponModal').modal('hide');
    }

    function showLoading() {
        $("#loadingOverlay").fadeIn();
    }

    function hideLoading() {
        $("#loadingOverlay").fadeOut();
    }
</script>
