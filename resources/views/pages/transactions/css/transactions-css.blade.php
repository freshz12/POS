<style>
    /* Adjust modal width and center alignment */
    .modal-dialog.modal-dialog-centered {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modal-content {
        width: 100%;
        max-width: 350px; /* Maintain previous width */
    }

    /* Numpad styling */
    .numpad {
        display: flex;
        flex-direction: column;
    }
    .numpad .row {
        margin-bottom: 5px;
    }
    .numpad-button {
        width: 100%;
        height: 60px; /* Adjust height */
        font-size: 1.5rem; /* Larger font size */
        border-radius: 5px;
        margin: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .numpad-button:focus {
        box-shadow: none; /* Remove focus outline */
    }
    #subtractButton {
        background-color: #f8d7da;
        color: #721c24;
        border: none;
    }
    #okButton {
        background-color: #28a745;
        border: none;
        color: white;
    }
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        border: none;
    }
    .btn-success:focus {
        box-shadow: none; /* Remove focus outline */
    }

    /* Adjust form-group for proper alignment */
    .form-group {
        margin-bottom: 15px;
    }
    .form-control {
        height: 60px; /* Match height with numpad buttons */
        font-size: 1.5rem; /* Adjust font size */
        text-align: right; /* Align text to right */
    }

    /* Style for the subtract button */
#subtractButton {
    background-color: red; /* Red background color for the subtract button */
    color: white; /* White text color for the subtract button */
    border: none; /* Remove default border */
    font-size: 1.5rem; /* Increase font size */
    display: flex; /* Use flexbox for alignment */
    align-items: center; /* Center align text and icon vertically */
    justify-content: center; /* Center align text and icon horizontally */
    cursor: pointer; /* Change cursor to pointer on hover */
}

#subtractButton:hover {
    content: 'No'; /* Show "No" on hover */
    background-color: darkred; /* Darker red background color on hover */
}

/* Style for the OK button */
#okButton {
    background-color: green; /* Green background color for the OK button */
    color: white; /* White text color for the OK button */
    border: none; /* Remove default border */
    font-size: 1.5rem; /* Increase font size */
    display: flex; /* Use flexbox for alignment */
    align-items: center; /* Center align text and icon vertically */
    justify-content: center; /* Center align text and icon horizontally */
    cursor: pointer; /* Change cursor to pointer on hover */
}

#okButton:hover {
    background-color: darkgreen; /* Darker green background color on hover */
}
/* Style for the input field */
#quantity {
    text-align: center; /* Center text horizontally */
    font-size: 2rem; /* Increase font size for better visibility */
    height: 60px; /* Adjust height to match numpad button height */
    line-height: 60px; /* Align text vertically in the middle */
    margin-bottom: 15px; /* Space below the input */
}

/* Style for the numpad */
.numpad {
    display: flex;
    flex-direction: column;
    height: auto;
    width: 100%; /* Full width to match modal content width */
}

/* Style for the numpad buttons */
.numpad-button {
    width: 100%; /* Full width for each button */
    height: 60px; /* Same height for consistency */
    font-size: 1.5rem; /* Larger font size for better readability */
}

/* Style for the subtract button */
#subtractButton {
    background-color: red; /* Red background color */
    color: white; /* White text color */
    border: none; /* Remove border */
    display: flex; /* Flexbox for alignment */
    align-items: center; /* Center icon vertically */
    justify-content: center; /* Center icon horizontally */
}

#subtractButton:hover {
    background-color: darkred; /* Darker red on hover */
    content: 'No'; /* Display "No" on hover */
}

/* Style for the OK button */
#okButton {
    background-color: green; /* Green background color */
    color: white; /* White text color */
    border: none; /* Remove border */
    display: flex; /* Flexbox for alignment */
    align-items: center; /* Center icon vertically */
    justify-content: center; /* Center icon horizontally */
}

#okButton:hover {
    background-color: darkgreen; /* Darker green on hover */
}

</style>
