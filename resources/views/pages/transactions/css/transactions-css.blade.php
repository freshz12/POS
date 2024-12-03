<style>
    /* Adjust modal width and center alignment */
    .out-of-stock {
        opacity: 0.5;
        /* Makes the product card a bit faded */
    }

    .out-of-stock-overlay {
        color: #888;
        /* Darkens the text and overlay */
        background-color: rgba(0, 0, 0, 0.3);
        /* Darkens the background slightly */
    }

    .modal-dialog.modal-dialog-centered {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-dialog.modal-fullscreen {
        max-width: 100vw;
        max-height: 100vh;
    }

    .modal-content {
        width: 100%;
        max-width: 100vw;
        max-height: 100vh;
        height: 100%;
        overflow: hidden;
        /* Prevent scrollbars */
    }

    /* Numpad styling */
    .numpad {
        display: flex;
        flex-direction: column;
        height: 100%;
        width: 100%;
    }

    .numpad .row {
        margin-bottom: 5px;
    }

    .numpad-button {
        width: 100%;
        height: 60px;
        /* Adjust height */
        font-size: 1.5rem;
        /* Larger font size */
        border-radius: 5px;
        margin: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .numpad-button:focus {
        box-shadow: none;
        /* Remove focus outline */
    }

    .numpad-button-payment, .numpad-button-custom-price {
        width: 100%;
        height: 60px;
        /* Adjust height */
        font-size: 1.5rem;
        /* Larger font size */
        border-radius: 5px;
        margin: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .numpad-button-payment:focus, .numpad-button-custom-price:focus {
        box-shadow: none;
        /* Remove focus outline */
    }

    .numpad-button-payment-ok, .numpad-button-custom-price-ok {
        width: 100%;
        height: 60px;
        /* Adjust height */
        font-size: 1.5rem;
        /* Larger font size */
        border-radius: 5px;
        margin: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .numpad-button-payment-ok:focus, .numpad-button-custom-price-ok:focus {
        box-shadow: none;
        /* Remove focus outline */
    }



    #subtractButton {
        background-color: red;
        /* Red background color */
        color: white;
        /* White text color */
        border: none;
        /* Remove border */
        font-size: 1.5rem;
        /* Increase font size */
        display: flex;
        /* Use flexbox for alignment */
        align-items: center;
        /* Center align text and icon vertically */
        justify-content: center;
        /* Center align text and icon horizontally */
        cursor: pointer;
        /* Change cursor to pointer on hover */
    }

    #subtractButton:hover {
        background-color: darkred;
        /* Darker red background color on hover */
    }

    #okButton {
        background-color: green;
        /* Green background color */
        color: white;
        /* White text color */
        border: none;
        /* Remove border */
        font-size: 1.5rem;
        /* Increase font size */
        display: flex;
        /* Use flexbox for alignment */
        align-items: center;
        /* Center align text and icon vertically */
        justify-content: center;
        /* Center align text and icon horizontally */
        cursor: pointer;
        /* Change cursor to pointer on hover */
    }

    #okButton:hover {
        background-color: darkgreen;
        /* Darker green background color on hover */
    }

    /* Style for the input field */
    #quantity {
        text-align: center;
        /* Center text horizontally */
        font-size: 2rem;
        /* Increase font size */
        height: 60px;
        /* Match height with numpad button height */
        line-height: 60px;
        /* Align text vertically in the middle */
        margin-bottom: 15px;
        /* Space below the input */
    }

    #amount, #amountCustomPrice {
        text-align: center;
        /* Center text horizontally */
        font-size: 2rem;
        /* Increase font size */
        height: 60px;
        /* Match height with numpad button height */
        line-height: 60px;
        /* Align text vertically in the middle */
        margin-bottom: 15px;
        /* Space below the input */
    }

    /* Style for the numpad */
    .numpad {
        display: flex;
        flex-direction: column;
        height: auto;
        width: 100%;
        /* Full width to match modal content width */
    }

    /* Style for the numpad buttons */


    /* Optional: Adjust the width of the main content area */
    .main-content {
        overflow: hidden;
        /* Prevent scrollbars on main content */
        height: 100vh;
        /* Full viewport height */
    }

    /* Container for the product list */
    #productList {
        max-height: 600px;
        /* Adjust height as needed */
        overflow-y: auto;
        /* Enable vertical scrolling */
    }

    /* Ensure the product items are displayed properly */
    .product-item {
        margin-bottom: 15px;
        /* Space between product items */
    }


    .product-item img {
        width: 100%;
        height: 200px;
        /* Set height to ensure square aspect ratio */
        object-fit: cover;
        /* Maintain aspect ratio while covering the container */
    }

    .product-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 33%;
        /* Overlay height, adjust as needed */
        background: rgba(255, 255, 255, 0.8);
        /* White with 80% opacity */
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .product-name {
        font-size: 1.2rem;
        color: black;
        /* Adjust text color as needed */
    }

    /* Adjust button styling and hide the button */
    .add-to-cart-btn {
        display: none;
        /* Hide the button */
    }

    .quantity-button {
        width: 50px;
        height: 45px;
        font-size: 1.5rem;
    }

    .table-responsive {
        max-height: 523px;
        overflow-y: auto;
    }

    .thead-fixed {
        position: sticky;
        /* Make the header sticky */
        top: 0;
        /* Stick to the top */
        background-color: #f8f9fa;
        /* Background color to differentiate */
        z-index: 1;
        /* Ensure it stays on top */
        /* Optional: Add box-shadow for better visibility */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .payment-buttons .btn {
        border-radius: 10px;
        padding: 15px 30px;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .payment-buttons .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    }

    .payment-buttons .btn:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.5);
    }
</style>
