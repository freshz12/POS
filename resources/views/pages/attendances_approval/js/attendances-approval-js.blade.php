<script>
    $(document).ready(function() {
        const userPermissions = {
            canEdit: @json(auth()->user()->can('attendances_approve_or_reject')),
        };
        $('#datatable').DataTable({
            "dom": '<"row"<"col-12 d-flex justify-content-end"f>>' +
                '<"row"<"col-12"t>>' +
                '<"row"<"col-12"<"d-flex justify-content-start"l>>>' +
                '<"row"<"col-12"<i><"d-flex justify-content-end"p>>>',
            "pagingType": "full_numbers",
            "ajax": {
                "url": "{{ url('/attendances/approval_index_data') }}",
                "dataSrc": "data",
                "data": function(d) {
                    d.full_name = $('#full_name_filter').val();
                    d.clock_in_from = $('#clock_in_from_filter').val();
                    d.clock_in_to = $('#clock_in_to_filter').val();
                    d.clock_out_from = $('#clock_out_from_filter').val();
                    d.clock_out_to = $('#clock_out_to_filter').val();
                    d.request_reason = $('#request_reason_filter').val();
                    d.updated_at = $('#updated_at_filter').val();
                },
            },
            "columnDefs": [{
                    "targets": 0,
                    "render": function(data, type, row, meta) {
                        return meta.row + 1;
                    },
                    "orderable": false,
                    "searchable": false
                },
                {
                    "targets": 1,
                    "render": function(data, type, row, meta) {
                        let editButton = '';
                        let deleteButton = '';

                        if (userPermissions.canEdit) {
                            editButton = `
                                <button onclick="ajaxEdit(${row.id})" class="btn btn-primary mr-2" data-toggle="modal" data-target="#editattendance">
                                    <i class="ion-checkmark" data-pack="default" data-tags="change, update, write, type, pencil"></i>
                                </button>
                            `;
                        } else {
                            editButton = `
                                <button class="btn btn-secondary mr-2" disabled>
                                    <i class="ion-checkmark" data-pack="default" data-tags="change, update, write, type, pencil"></i>
                                </button>
                            `;
                        }

                        return `
                            <div class="btn-group" role="group" aria-label="Action buttons">
                                ${editButton}
                            </div>
                        `;
                    },
                    "orderable": false,
                    "searchable": false
                }
            ],
            "columns": [{
                    "data": null
                },
                {
                    "data": null
                },
                {
                    "data": "users.name",
                    "render": function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    "data": "clock_in",
                    "render": function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    "data": "clock_out",
                    "render": function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    "data": "updated_at",
                    "render": function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                }
            ],
            "language": {
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                },
                "lengthMenu": "Show _MENU_ entries"
            },
            "drawCallback": function(settings) {
                var api = this.api();
                var pagination = $(this).closest('.dataTables_wrapper').find(
                    '.dataTables_paginate');
                var pageInfo = api.page.info();
                var pageList = '';

                if (pageInfo.page === 0) {
                    pageList +=
                        '<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">Previous</a></li>';
                } else {
                    pageList += '<li class="page-item"><a class="page-link" href="#" data-page="' +
                        (pageInfo.page - 1) + '">Previous</a></li>';
                }

                for (var i = 0; i < pageInfo.pages; i++) {
                    if (i === pageInfo.page) {
                        pageList +=
                            '<li class="page-item active"><a class="page-link" href="#" data-page="' +
                            i + '">' + (i + 1) + ' <span class="sr-only">(current)</span></a></li>';
                    } else {
                        pageList +=
                            '<li class="page-item"><a class="page-link" href="#" data-page="' + i +
                            '">' + (i + 1) + '</a></li>';
                    }
                }

                if (pageInfo.page === pageInfo.pages - 1) {
                    pageList +=
                        '<li class="page-item disabled"><a class="page-link" href="#">Next</a></li>';
                } else {
                    pageList += '<li class="page-item"><a class="page-link" href="#" data-page="' +
                        (pageInfo.page + 1) + '">Next</a></li>';
                }

                pagination.html(
                    '<div class=""><nav aria-label="..."><ul class="pagination">' +
                    pageList + '</ul></nav></div>');

                pagination.find('a.page-link').on('click', function(e) {
                    e.preventDefault();
                    var newPage = $(this).data('page');
                    if (newPage !== undefined) {
                        api.page(newPage).draw('page');
                    }
                });
            },
            "initComplete": function() {
                $('.dataTables_filter').appendTo('#filter-container');
                $('.dataTables_filter input').addClass('form-control');
                $('.dataTables_length select').addClass('form-select');
                resetfilter();
            }
        });

        $("#clock_in").val(null);
        $("#clock_out").val(null);

    });

    function applyfilter() {
        $('#datatable').DataTable().ajax.reload();

        $('#filterattendance').modal('hide');
    }

    function resetfilter() {
        $('#full_name_filter').val('');
        $('#clock_in_from_filter').val('');
        $('#clock_in_to_filter').val('');
        $('#clock_out_from_filter').val('');
        $('#clock_out_to_filter').val('');
        $('#request_reason_filter').val('');
        $('#updated_at_filter').val('');

        $('#datatable').DataTable().ajax.reload();

        $('#filterattendance').modal('hide');
    }

    function handleDownload() {
        var baseUrl = "{{ url('/attendances/export') }}";
        var params = {
            full_name: $('#full_name_filter').val(),
            clock_in_from: $('#clock_in_from_filter').val(),
            clock_in_to: $('#clock_in_to_filter').val(),
            clock_out_from: $('#clock_out_from_filter').val(),
            clock_out_to: $('#clock_out_to_filter').val(),
            request_reason: $('#request_reason_filter').val(),
            updated_at: $('#updated_at_filter').val()
        };
        var queryString = $.param(params);
        var downloadUrl = baseUrl + '?' + queryString;

        var tempLink = document.createElement('a');
        tempLink.href = downloadUrl;
        tempLink.download = 'attendances.xlsx';

        document.body.appendChild(tempLink);
        tempLink.click();

        document.body.removeChild(tempLink);
    }

    $('#addattendance').on('show.bs.modal', function() {
        $(this).find('form')[0].reset();
    });

    function ajaxEdit(id) {
        $.ajax({
            url: '/attendances/show/' + id,
            type: 'GET',
            success: function(response) {
                $('#clock_in').val(moment(response[0]['clock_in']).format('YYYY-MM-DD HH:mm'));
                $('#clock_out').val(moment(response[0]['clock_out']).format('YYYY-MM-DD HH:mm'));
                $('#request_reason').val(response[0]['request_reason']);
                $('#employee_name').val(response[0]['users']['name']);
                $('#approved_or_rejected_reason').val('');
                $('#id').val(response[0]['id']);
            },
            error: function(xhr) {
                alert('Error:', xhr.responseText);
            }
        });
    }

    function reject() {
        if ($('#approved_or_rejected_reason').val() === '') {
            return;
        }
        $('#approve_or_reject').val('Rejected');
        $('#editForm').submit();
    }

    function approve() {
        if ($('#approved_or_rejected_reason').val() === '') {
            return;
        }
        $('#approve_or_reject').val('Approved');
        $('#editForm').submit();
    }

</script>
