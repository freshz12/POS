<script>
    let allProducts = [];

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(async function() {
        $.ajax({
            url: '/promos/index_data',
            type: 'GET',
            data: {
                is_active: true,
                is_without_package: true
            },
            dataType: 'json',
            success: function(data) {
                var results = [];
                $.each(data.data, function(index, item) {
                    results.push({
                        id: item.id,
                        text: `Promo Name : <strong>${item.name}</strong> <br>
                       Type : ${item.type} <br> Unique Code : ${item.unique_code}`,
                        unique_code: item.unique_code,
                        value: item.value,
                        type: item.type,
                        product_id: item.product_id
                    });
                });

                $(".select2#productCoupon").select2({
                    data: results,
                    escapeMarkup: function(markup) {
                        return markup; // Let Select2 escape markup
                    },
                    templateResult: function(data) {
                        if (data.loading) {
                            return data.text;
                        }
                        return $(`<div>${data.text}</div>`);
                    },
                    templateSelection: function(data) {
                        return data.text.split('<br>')[0].replace('Promo Name : ',
                            '');
                    }
                });

                $(".select2#productCoupon").val(null).trigger('change');
            },
            error: function(xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });

        resetTransaction();

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
            var isCustomPrice = productCard.data('is-custom-price');

            if (productQuantity <= 0) {
                swal('Out of Stock', 'This product is currently out of stock and cannot be added!',
                    'error');
                return;
            }


            $('#productId').val(productId);
            $('#productName').val(productName);
            $('#productPrice').val(productPrice);
            $('#availableQuantity').val(productQuantity);
            $('#quantityModalLabel').text('Enter Quantity for ' + productName);
            $('#quantity').val('');

            if (isCustomPrice && @json(auth()->user()->can('custom_price'))) {
                $('#customPriceModal').modal('show');
                return;
            }

            $('#quantityModal').modal('show');
        });

        $(document).on('click', '.numpad-button-custom-price-ok', function() {
            $('#productPrice').val($('#amountCustomPrice').val().replace(/\./g, ''));
            $('#customPriceModal').modal('hide');
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

        $(document).on('click', '.numpad-button-custom-price', function() {
            var currentValue = $('#amountCustomPrice').val().replace(/\./g, '');
            var buttonValue = $(this).text();
            var newValue;

            if ($(this).attr('id') === 'subtractButtonCustomPrice') {
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
            $('#amountCustomPrice').val(formattedAmount);
        });



        $('#quantityModal').on('show.bs.modal', function() {
            $('#quantity').val('');
        });

        $('#customPriceModal').on('show.bs.modal', function() {
            $('#amountCustomPrice').val('');
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
                let total = formatNumberWithCommas(productPrice * quantity);
                var newRow = `<tr data-id="${productId}" data-coupon-id="">
                                <td class="text-center">${productName}</td>
                                <td class="text-right price-cell">${formattedPrice}</td>
                                <td>
                                    <div class="quantity-container d-flex align-items-center">
                                        <button class="btn btn-primary btn-lg quantity-button quantity-button-minus" type="button">-</button>
                                        <input type="text" class="form-control text-center quantity-input" style="width: 60px; height: 45px;" value="${quantity}" readonly>
                                        <button class="btn btn-primary btn-lg quantity-button quantity-button-plus" type="button">+</button>
                                    </div>
                                </td>
                                <td class="text-center discount-cell">-</td>
                                <td class="text-center total-cell">${total}</td>
                                <td class="text-right trash-container" style="white-space: nowrap;">
                                    <button class="btn btn-danger btn-lg trash-button"><i class="fas fa-trash"></i></button>
                                    <button class="btn btn-success btn-lg discount-product-button"><i style="font-size: 13px;" class="fa-solid fa-tag"></i></button>
                                </td>
                            </tr>`;
                $('table.table tbody').append(newRow);
            }

            updateTotalAmount();
            $('#quantityModal').modal('hide');
        });

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

        $(document).on('click', '.discount-product-button', function() {
            let productId = $(this).closest('tr').data('id');
            let couponId = $(this).closest('tr').data('coupon-id');
            $('.select2#productCoupon').val(couponId).trigger('change');
            $('#currentDiscountProductId').val(productId);
            $('#addCouponProductModal').modal('show');
        });

        $('#resetCartButton').on('click', function() {
            resetTransaction();
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
                $('#amountCustomPrice').val('');
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
            let capsterName = $('#capster option:selected').text();
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

            let transactionID;

            $.ajax({
                url: '/transactions/store',
                method: 'POST',
                data: {
                    customer_id: customerId,
                    capster_id: capsterId,
                    customer_name: $('#customerNameInput').val(),
                    capster_name: $('#capsterNameInput').val(),
                    amount_before_discount: totalAmountInput,
                    total_amount: finalTotalAmount,
                    cart_items: cartItems,
                    promo_id: promoIdInput,
                    payment_method: $('#paymentType').val(),
                    amount: cashPaidText
                },
                success: function(response) {
                    transactionID = response.running_number;
                    hideLoading();
                    printReceipt(response.cart_items, finalTotalAmount, cashPaidText,
                        customerId,
                        capsterName, transactionID, response.discount, response.transaction);
                    $('#paymentMethodModal').modal('hide');
                    resetTransaction();
                    swal('Success', `${response.success_message}`, 'success');
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching products:', error);
                }
            });

            return;

            // $('#paymentForm').submit();
        }

        async function printReceipt(cartItems, totalAmount, cashPaidText, customerId, capsterName,
            transactionID,
            discount, transaction) {
            let paymentType = $('#paymentType').val();

            let receiptContent = `
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <title>Thermal Printer Receipt</title>
            </head>
            <body>
            <div id="receipt" style="font-family: 'Courier New', monospace; font-size: 10pt; text-align: center; width:57mm">
                
                <div style="text-align: center; margin-bottom: 10px;">
                    <b>Densetsu</b><br>
                    Ruko Jl Grand Wisata Bekasi No. 16 Blok AA-12 Lambangsari Kec. Tambun Selatan Kab. Bekasi Jawa Barat 17510
                </div>
                <br>
                <div style="text-align: left; margin-bottom: 5px;">
                    Transaction ID: ${transactionID}<br>
                    Operator: ${capsterName}<br>
                    Payment Method: ${paymentType}
                </div>

                <div style="text-align: left;">-------------------------</div>`;

            cartItems.forEach(item => {
                if (item.is_included_in_receipt == 1) {
                    receiptContent += `
                        <div style="text-align: left; margin: 5px 0;">
                            <div>
                                <span style="float: left;">${item.name}</span><br>
                                <span style="float: left;">${item.price} x ${item.qty}</span>
                                <span style="float: right;">${formatNumberWithCommas(item.price_per_product)}</span>
                    `;

                    if (item.promo) {
                        let value;
                        if (item.promo.type == 'Nominal') {
                            value = formatNumberWithCommas(item.promo.value);
                        } else if (item.promo.type == 'Percentage') {
                            value = item.promo.value + '%';
                        }
                        if (value) {
                            receiptContent += `<br>`;
                            receiptContent += `
                                <span style="float: left;">Discount</span>
                                <span style="float: right;">${formatNumberWithCommas(value)}</span>
                            `;
                        }
                    }

                    receiptContent += `
                            </div><br>
                        </div>
                        <br>
                    `;
                }
            });


            receiptContent += `
                <div style="text-align: left;">-------------------------</div>

                <div style="text-align: left; margin: 5px 0;">`;

            if (parseFloat(transaction.amount_before_discount - transaction.amount) !== 0) {
                receiptContent += `<div>
                            <span style="float: left;">Subtotal</span>
                            <span style="float: right;">${formatNumberWithCommas(transaction.amount_before_discount)}</span>
                        </div><br>
                        <div>
                            <span style="float: left;">Discount</span>
                            <span style="float: right;">${formatNumberWithCommas(transaction.amount_before_discount - transaction.amount)}</span>
                        </div><br>`;
            }

            receiptContent += `
                    <div>
                        <span style="float: left;">Total&nbsp;</span>
                        <span style="float: right;">${formatNumberWithCommas(transaction.amount)}</span>
                    </div>
                </div>

                <div style="text-align: left;">-------------------------</div>

                <div style="margin-top: 10px; text-align: center; margin-bottom: 20px;">
                    Thank you for coming!<br>
                    ${new Date().toLocaleString()}<br><br><br><br><br>
                    .
                </div>
            </div>
            </body>
            </html>
            `;

            var iframe = document.createElement('iframe');
            iframe.style.position = 'absolute';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = 'none';
            document.body.appendChild(iframe);

            var doc = iframe.contentWindow.document;
            doc.open();
            doc.write(receiptContent);
            doc.close();

            iframe.contentWindow.focus();
            iframe.contentWindow.print();

            // Remove the iframe after printing
            iframe.remove();

            await delay(1000);

            iframe = document.createElement('iframe');
            iframe.style.position = 'absolute';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = 'none';
            document.body.appendChild(iframe);

            doc = iframe.contentWindow.document;
            doc.open();
            doc.write(receiptContent);
            doc.close();

            iframe.contentWindow.focus();
            iframe.contentWindow.print();

            iframe.remove();
        }

        function delay(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
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
                coupon: $(this).data('coupon-id'),
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
                <div class="card product-item ${cardClass}" data-id="${product.id}" data-quantity="${product.quantity}" data-name="${product.product_name}" data-price="${product.selling_price}" data-is-custom-price="${product.is_custom_price}" data-discount-product="0">
                    <img style="height: auto; width: auto;" src="/storage/${product.picture_path || 'files/default/product.png'}" class="card-img-top ${overlayClass}" alt="${product.product_name}">
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
            let dicountAmount = 0;
            var priceText = $(this).find('.price-cell').text();
            var quantityText = $(this).find('.quantity-input').val();
            var discountText = $(this).find('.discount-cell').text();

            if (discountText !== '-') {
                dicountAmount = parseFloat(discountText.replace(/\./g, '').replace(/,/g, '.'));
            }

            var price = parseFloat(priceText.replace(/\./g, '').replace(/,/g, '.'));
            var quantity = parseInt(quantityText, 10);

            $(this).find('.total-cell').text(formatNumberWithCommas((price * quantity) - dicountAmount))

            if (!isNaN(price) && !isNaN(quantity)) {
                originalTotalAmount += (price * quantity) - dicountAmount;
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
            totalAmount = Math.floor(originalTotalAmount - discountValue);
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

    function addProductCoupon() {
        let couponAmount;
        let selectedCoupon = $('.select2#productCoupon').select2('data')[0];
        let currProductId = $('#currentDiscountProductId').val();
        let row = $(`tr[data-id="${currProductId}"]`);
        let priceText = row.find('.price-cell').text().trim();
        let price = parseFloat(priceText.replace(/\./g, ''), 10);
        let quantityText = row.find('.quantity-input').val().trim();
        let quantity = parseInt(quantityText.replace(/\./g, ''), 10);


        if (selectedCoupon.type.toLowerCase() == 'percentage') {
            couponAmount = Math.floor(((price * quantity) * selectedCoupon.value) / 100);
        } else if (selectedCoupon.type.toLowerCase() == 'nominal') {
            couponAmount = selectedCoupon.value;
        }

        row.data('coupon-id', selectedCoupon.id);
        row.find('.discount-cell').text(formatNumberWithCommas(couponAmount));
        updateTotalAmount();
        $('#addCouponProductModal').modal('hide');
    }

    function addCoupon() {
        var selectedCoupon = $('.select2.coupon#coupon').select2('data')[0];
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

    function resetTransaction() {
        $('.select2.coupon').val(null).trigger('change');
        $('.select2.coupon').removeData('unique-number').removeData('value').removeData('type');
        $('#customer').val(null).trigger('change');
        $('#capster').val(null).trigger('change');
        $('#customer').val(null).trigger('select2:clear');
        $('#capster').val(null).trigger('select2:clear');
        $('table.table tbody').empty();
        updateTotalAmount();
    }

    function showLoading() {
        $("#loadingOverlay").fadeIn();
    }

    function hideLoading() {
        $("#loadingOverlay").fadeOut();
    }
</script>
