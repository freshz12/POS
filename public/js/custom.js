/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";

$('.select2.customers').select2({
    ajax: {
        url: '/customers/customers_data',
        type: 'POST',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                search: params.term,
                page: params.page || 1
            };
        },
        processResults: function (data, params) {


            params.page = params.page || 1;

            var results = [];
            $.each(data.data, function (index, item) {
                results.push({
                    id: item.id,
                    text: `Full Name : <strong>${item.full_name}</strong> (${item.gender})<br>
                            Phone Number : ${item.phone_number} <br>
                            Email : ${item.email}`
                });
            });

            if (data.data.length === 0) {
                results.push({
                    id: 'no-results',
                    text: `<a href="#" class="no-results-link" onclick="openAddCustomerModal(); return false;">No results found. Click here to add a customer.</a>`,
                    disabled: true
                });
            }

            return {
                results: results,
                pagination: {
                    more: (params.page * 30) < data.recordsTotal
                }
            };
        },
        cache: true
    },
    minimumInputLength: 3,
    escapeMarkup: function (markup) {
        return markup;
    },
    templateResult: function (data) {
        if (data.loading) {
            return data.text;
        }
        return $(`<div>${data.text}</div>`);
    },
    templateSelection: function (data) {
        return data.text ? data.text.split('<br>')[0].replace('Full Name : ', '') : data
            .text;
    }
});

$('.select2.capsters').select2({
    ajax: {
        url: '/capsters/index_data',
        type: 'GET',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                capster_name: params.term,
                page: params.page || 1
            };
        },
        processResults: function (data, params) {


            params.page = params.page || 1;

            var results = [];
            $.each(data.data, function (index, item) {
                results.push({
                    id: item.id,
                    text: item.full_name
                });
            });

            return {
                results: results,
                pagination: {
                    more: (params.page * 30) < data.recordsTotal
                }
            };
        },
        cache: true
    },
    minimumInputLength: 1,
});

function openAddCustomerModal() {
    $('#customer_full_name').val(null);
    $('#customer_email').val(null);
    $('#customer_gender').val(null);
    $('#customer_phone').val(null);

    $('.select2.customers').select2('close');
    $('#selectCustomerModal').modal('hide');
    $('#addcustomer').modal('show');
}

function submitCustomer() {
    $('#addcustomer').modal('hide');


    event.preventDefault();

    var formData = $('#customer_form').serialize();

    $.ajax({
        url: '/customers/store_ajax',
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success == true) {
                swal('Success', response.message, 'success');
                setTimeout(function() {
                    swal.close();
                }, 5000);
                $('#selectCustomerModal').modal('show');
            } else if (response.success == false) {
                swal('Error', response.message, 'error');
                setTimeout(function() {
                    swal.close();
                }, 5000);
            }
        },
        error: function(e) {
            alert(e);
        }
    });
}