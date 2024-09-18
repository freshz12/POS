<script src="{{ asset('library/flatpickr/flatpickr.js') }}"></script>
<script src="{{ asset('library/fullcalendar/dist/fullcalendar.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $("#appointments").fullCalendar({
            height: 'auto',
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay,listWeek'
            },
            editable: true,
            events: function(start, end, timezone, callback) {
                $.ajax({
                    url: 'appointments/index_data', // Replace with the path to your server-side script
                    dataType: 'json',
                    method: 'POST',
                    data: {
                        start: start.toISOString(),
                        end: end.toISOString(),
                        timezone: timezone
                    },
                    success: function(data) {
                        var formattedData = data.data.map(function(event) {
                            var backgroundColor = 'blue';
                            if (event.status === 'Completed') {
                                backgroundColor = 'green';
                            } else if (event.status === 'Pending') {
                                backgroundColor = 'orange';
                            } else if (event.status === 'Cancelled') {
                                backgroundColor = 'red';
                            }

                            return {
                                id: event.id,
                                title: event.customers.full_name,
                                start: event.start_date,
                                end: event.end_date,
                                backgroundColor: backgroundColor,
                                textColor: 'white',
                                ...event
                            };
                        });

                        callback(formattedData);
                    },
                    error: function() {
                        alert('There was an error fetching events.');
                    }
                });
            },
            eventRender: function(event, element) {
                var tooltipContent =
                    'Customer Name: ' + event.title + '<br>' +
                    'Start: ' + moment(event.start).format('YYYY-MM-DD HH:mm') + '<br>' +
                    'End: ' + (event.end ? moment(event.end).format('YYYY-MM-DD HH:mm') + '<br>' :
                        '') +
                    'Remarks: ' + event.remarks;


                element.attr('data-toggle', 'tooltip')
                    .attr('title', tooltipContent)
                    .attr('data-html', true)
                    .tooltip({
                        container: 'body',
                        trigger: 'hover',
                        placement: 'top'
                    });

                if (event.end && moment(event.end).isBefore(moment())) {
                    element.css({
                        'background-color': 'red',
                        'color': 'white'
                    });
                }
            },
            eventClick: function(event) {
                var $customerSelect = $('.select2.customers');
                var option = new Option(event.title, event.customer_id, true, true);
                $customerSelect.append(option).trigger('change');
                $customerSelect.val(event.customer_id).trigger('change');

                $('#status_edit').val(event.status);
                $('#start_edit').val(moment(event.start).format('YYYY-MM-DD HH:mm'));
                $('#end_edit').val(moment(event.end).format('YYYY-MM-DD HH:mm'));
                $('#remarks_edit').val(event.remarks || '');
                $('#eventId').val(event.id);

                $('#editappointment').modal('show');
            },
            timeFormat: 'H(:mm)'
        });

        flatpickr(".flatpickr", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            // minDate: new Date(),
            allowInput: true
        });

        $("#date_start").val(null);
        $("#date_end").val(null);

        
    });

    function openAddCustomerModal() {
        $('#customer_full_name').val(null);
        $('#customer_email').val(null);
        $('#customer_gender').val(null);
        $('#customer_phone').val(null);

        $('.select2.customers').select2('close');
        $('#addappointment').modal('hide');
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
                    $('#addappointment').modal('show');
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

    function clearValue() {
        $('#customer').val(null);
        $('#status').val(null);
        $('#date_start').val(null);
        $('#date_end').val(null);
        $('#remarks').val(null);
    }

    function handleDownload() {
        var baseUrl = "{{ url('/appointments/export') }}";
        var params = {
            // full_name: $('#full_name_filter').val(),
            // email: $('#email_filter').val(),
            // gender: $('#gender_filter').val(),
            // phone_number: $('#phone_number_filter').val(),
            // updated_at: $('#updated_at_filter').val()
        };
        var queryString = $.param(params);
        var downloadUrl = baseUrl + '?' + queryString;

        var tempLink = document.createElement('a');
        tempLink.href = downloadUrl;
        tempLink.download = 'appointments.xlsx';

        document.body.appendChild(tempLink);
        tempLink.click();

        document.body.removeChild(tempLink);
    }
</script>
