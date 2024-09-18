<script>
    $(document).ready(function() {
        // Fetch products from the server
        function fetchProducts(query = '') {
            $.ajax({
                url: '/products/index_data',
                method: 'GET',
                data: { search: query },
                success: function(response) {
                    $('#productList').empty();
    
                    response.data.forEach(function(product) {
                        var imageUrl = product.picture_path ?
                            `/storage/${product.picture_path}` :
                            '/storage/files/default/product.png';
                        var height = product.picture_path ? 200 : 150;
                        var width = product.picture_path ? '100%' : '100%';
    
                        $('#productList').append(`
                            <div class="col-6 col-md-4 col-lg-3 mb-3">
                                <div class="card product-item" data-id="${product.id}" data-name="${product.product_name}" data-price="${product.selling_price}">
                                    <div class="card-body text-center">
                                        <img src="${imageUrl}" style="width: ${width}; height: ${height}px; object-fit: cover;" class="card-img-top" alt="${product.product_name}">
                                        <h5 class="card-title">${product.product_name}</h5>
                                        <p class="card-text">Quantity: ${product.quantity}</p>
                                        <button class="btn btn-primary btn-block add-to-cart-btn">Add to Cart</button>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching products:', error);
                }
            });
        }
    
        // Fetch products on page load
        fetchProducts();
    
        // Search products
        $('#searchProduct').on('input', function() {
            const query = $(this).val();
            fetchProducts(query);
        });
    
        // Show modal when "Add to Cart" button is clicked
        $('#productList').on('click', '.add-to-cart-btn', function() {
            var productCard = $(this).closest('.product-item');
            var productId = productCard.data('id');
            var productName = productCard.data('name');
            var productPrice = productCard.data('price');
    
            $('#productId').val(productId);
            $('#productName').val(productName);
            $('#productPrice').val(productPrice);
            $('#quantityModalLabel').text('Enter Quantity for ' + productName);
    
            // Clear quantity input
            $('#quantity').val('');
    
            // Show the modal
            $('#quantityModal').modal('show');
        });
    
        // Handle numpad button clicks
        $(document).on('click', '.numpad-button', function() {
            var currentValue = $('#quantity').val();
            var buttonValue = $(this).text();
    
            if ($(this).attr('id') === 'subtractButton') {
                // Remove the last digit
                $('#quantity').val(currentValue.slice(0, -1));
            } else if ($(this).attr('id') === 'okButton') {
                // Submit the form
                $('#quantityForm').submit();
            } else {
                // Add number to current value
                $('#quantity').val(currentValue + buttonValue);
            }
        });
    
        // Clear quantity input when modal is opened
        $('#quantityModal').on('show.bs.modal', function() {
            $('#quantity').val('');
        });
    
        // Handle form submission
        $('#quantityForm').submit(function(e) {
            e.preventDefault();
    
            var productId = $('#productId').val();
            var productName = $('#productName').val();
            var quantity = $('#quantity').val();
            var productPrice = $('#productPrice').val();
    
            var existingRow = $('table.table tbody tr').filter(function() {
                return $(this).data('id') == productId;
            });
    
            if (existingRow.length) {
                // Update existing row
                var currentQuantity = parseInt(existingRow.find('.quantity-input').val());
                var newQuantity = currentQuantity + parseInt(quantity);
                existingRow.find('.quantity-input').val(newQuantity);
                existingRow.find('.total-price-cell').text('Rp.' + (newQuantity * productPrice).toFixed(2));
            } else {
                // Add new row
                var newRow = `<tr data-id="${productId}">
                    <td>${productName}</td>
                    <td>
                        <div class="quantity-container">
                            <button class="quantity-button quantity-button-minus">-</button>
                            <input type="text" class="quantity-input" value="${quantity}" readonly>
                            <button class="quantity-button quantity-button-plus">+</button>
                        </div>
                    </td>
                    <td class="text-right price-cell">Rp.${productPrice}</td>
                    <td class="text-right total-price-cell">Rp.${(quantity * productPrice).toFixed(2)}</td>
                    <td class="text-right trash-container">
                        <button class="btn btn-danger btn-sm trash-button"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>`;
    
                $('table.table tbody').append(newRow);
            }
    
            updateTotalAmount();
            $('#quantityModal').modal('hide');
        });
    
        // Update the total amount in the cart
        function updateTotalAmount() {
            var totalAmount = 0;
            $('table.table tbody tr').each(function() {
                var totalPrice = $(this).find('.total-price-cell').text().replace('Rp.', '').replace(',', '');
                totalAmount += parseFloat(totalPrice);
            });
            $('#totalAmount').text(totalAmount.toFixed(2));
        }
    
        // Handle quantity button clicks
        $(document).on('click', '.quantity-button-plus', function() {
            var quantityInput = $(this).siblings('.quantity-input');
            var currentQuantity = parseInt(quantityInput.val());
            quantityInput.val(currentQuantity + 1);
            updateTotalAmount();
        });
    
        $(document).on('click', '.quantity-button-minus', function() {
            var quantityInput = $(this).siblings('.quantity-input');
            var currentQuantity = parseInt(quantityInput.val());
            if (currentQuantity > 1) {
                quantityInput.val(currentQuantity - 1);
                updateTotalAmount();
            }
        });
    
        // Handle trash button click
        $(document).on('click', '.trash-button', function() {
            $(this).closest('tr').remove();
            updateTotalAmount();
        });
    });
</script>
