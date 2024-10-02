<script>

    let allProducts = [];

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

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

        // Sort products so that those with quantity 0 or below are at the bottom
        products.sort(function(a, b) {
            return (a.quantity > 0 ? -1 : 1) - (b.quantity > 0 ? -1 : 1);
        });

        products.forEach(function(product) {
            // Apply darker styling if quantity is 0 or less
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

    $(document).ready(function() {
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

        $('#paymentModal').on('show.bs.modal', function() {
            $('#amount').val('');
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


        function updateTotalAmount() {
            var totalAmount = 0;
            $('table.table tbody tr').each(function() {
                var priceText = $(this).find('.price-cell').text();
                var quantityText = $(this).find('.quantity-input').val();

                var price = parseFloat(priceText.replace(/\./g, '').replace(/,/g, '.'));
                var quantity = parseInt(quantityText, 10);

                if (!isNaN(price) && !isNaN(quantity)) {
                    totalAmount += price * quantity;
                }
            });

            let formattedAmount = formatNumberWithCommas(totalAmount);

            $('#totalAmount').text(formattedAmount);
        }



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
            $('table.table tbody').empty();
            updateTotalAmount();
        });

        $('#payButton').on('click', function() {
            if ($('table.table tbody tr').length === 0) {
                swal('Your cart is empty', 'Please add products to the cart before proceeding!',
                    'error');
            } else {
                $('#customer').val(null).trigger('change');
                $('#capster').val(null).trigger('change');
                $('#selectCustomerModal').modal('show');
            }
        });

        $('#okCustomer').on('click', function() {
            if ($('#customer').val() == null || $('#capster').val() == null) {
                swal('Error',
                    'Please select both customer and capster!', 'error');
                return;
            } {
                $('#selectCustomerModal').modal('hide');
                $('#paymentModal').modal('show');
            }
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

                if (product.id && product.name && product.qty && product.price) {
                    cartItems.push(product);
                }
            });


            return JSON.stringify(cartItems);
        }

        $('#payButtonModal').on('click', function() {
            let totalAmountText = $('#totalAmount').text().trim();
            let totalAmount = parseFloat(totalAmountText.replace(/\./g, '').replace(/,/g, '.'));

            let cashPaidText = $('#amount').val().trim();
            let customerId = $('#customer').val();
            let capsterId = $('#capster').val();
            let cashPaid = parseFloat(cashPaidText.replace(/\./g, '').replace(/,/g, '.'));

            if (cashPaid < totalAmount) {
                swal('Insufficient Payment',
                    'The amount you have entered is less than the total amount.', 'error');
                return;
            }

            let cartItems = getCartItems();

            $('#totalAmountInput').val(
                totalAmountText);
            $('#cashPaidInput').val(cashPaidText);
            $('#cartItemsInput').val(cartItems);
            $('#customerIdInput').val(customerId);
            $('#capsterIdInput').val(capsterId);

            $('#paymentForm').submit();
        });


        function formatNumberWithCommas(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    });
</script>
