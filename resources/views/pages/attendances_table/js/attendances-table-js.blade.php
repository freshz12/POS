<script>
    $(document).ready(function() {
        const userPermissions = {
            canEdit: @json(auth()->user()->can('attendances_edit')),
            canDelete: @json(auth()->user()->can('attendances_delete')),
        };
        $('#datatable').DataTable({
            "dom": '<"row"<"col-12 d-flex justify-content-end"f>>' +
                '<"row"<"col-12"t>>' +
                '<"row"<"col-12"<"d-flex justify-content-start"l>>>' +
                '<"row"<"col-12"<i><"d-flex justify-content-end"p>>>',
            "pagingType": "full_numbers",
            "ajax": {
                "url": "{{ url('/attendances/index_data') }}",
                "dataSrc": "data",
                "data": function(d) {
                    d.full_name = $('#full_name_filter').val();
                    d.status = $('#status_filter').val();
                    d.clock_in_from = $('#clock_in_from_filter').val();
                    d.clock_in_to = $('#clock_in_to_filter').val();
                    d.clock_out_from = $('#clock_out_from_filter').val();
                    d.clock_out_to = $('#clock_out_to_filter').val();
                    d.approved_or_rejected_reason = $('#approved_or_rejected_reason_filter').val();
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
                                    <i class="ion-edit" data-pack="default" data-tags="change, update, write, type, pencil"></i>
                                </button>
                            `;
                        } else {
                            editButton = `
                                <button class="btn btn-secondary mr-2" disabled>
                                    <i class="ion-edit" data-pack="default" data-tags="change, update, write, type, pencil"></i>
                                </button>
                            `;
                        }

                        if (userPermissions.canDelete) {
                            deleteButton = `
                                <form action="{{ url('/attendances/delete') }}" method="POST" style="display:inline;" id="deleteattendance">
                                    @csrf
                                    <input type="hidden" name="id" value="${row.id}">
                                    <button onclick="confirmDelete(event)" class="btn btn-danger" id="buttondelete">
                                        <i class="ion-trash-a" data-pack="default" data-tags="delete, remove, dump"></i>
                                    </button>
                                </form>
                            `;
                        } else {
                            deleteButton = `
                                <button class="btn btn-danger" disabled>
                                    <i class="ion-trash-a" data-pack="default" data-tags="delete, remove, dump"></i>
                                </button>
                            `;
                        }

                        return `
                            <div class="btn-group" role="group" aria-label="Action buttons">
                                ${editButton}
                                ${deleteButton}
                            </div>
                        `;
                    },
                    "orderable": false,
                    "searchable": false
                },
                {
                    "targets": 3,
                    "render": function(data, type, row, meta) {
                        var imageUrl = row.photo_path_clock_in ? '/' + row.photo_path_clock_in :
                            '/storage/files/default/user.jpg';
                        var height = row.photo_path_clock_in ? 70 : 50;
                        var width = row.photo_path_clock_in ? 100 : 50;
                        return `<img src="${imageUrl}" style="width:  ${width}px; height: ${height}px;">`;
                    },
                    "orderable": false,
                    "searchable": false
                },
                {
                    "targets": 5,
                    "render": function(data, type, row, meta) {
                        var imageUrl = row.photo_path_clock_out ? '/' + row
                            .photo_path_clock_out :
                            '/storage/files/default/user.jpg';
                        var height = row.photo_path_clock_out ? 70 : 50;
                        var width = row.photo_path_clock_out ? 100 : 50;
                        return `<img src="${imageUrl}" style="width:  ${width}px; height: ${height}px;">`;
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
                    "data": "users.name"
                },
                {
                    "data": "photo_path_clock_in"
                },
                {
                    "data": "clock_in"
                },
                {
                    "data": "photo_path_clock_out"
                },
                {
                    "data": "clock_out"
                },
                {
                    "data": "status"
                },
                {
                    "data": "approved_or_rejected_reason"
                },
                {
                    "data": "updated_at"
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
        $('#status_filter').val('');
        $('#clock_in_from_filter').val('');
        $('#clock_in_to_filter').val('');
        $('#clock_out_from_filter').val('');
        $('#clock_out_to_filter').val('');
        $('#approved_or_rejected_reason_filter').val('');
        $('#updated_at_filter').val('');

        $('#datatable').DataTable().ajax.reload();

        $('#filterattendance').modal('hide');
    }

    function handleDownload() {
        var baseUrl = "{{ url('/attendances/export') }}";
        var params = {
            full_name: $('#full_name_filter').val(),
            status: $('#status_filter').val(),
            clock_in_from: $('#clock_in_from_filter').val(),
            clock_in_to: $('#clock_in_to_filter').val(),
            clock_out_from: $('#clock_out_from_filter').val(),
            clock_out_to: $('#clock_out_to_filter').val(),
            approved_or_rejected_reason: $('#approved_or_rejected_reason_filter').val(),
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
                $('#id').val(response[0]['id']);
            },
            error: function(xhr) {
                alert('Error:', xhr.responseText);
            }
        });
    }
    // let flatpickrInstance = flatpickr("#clock_out", {
    //     dateFormat: "Y-m-d H:i",
    //     enableTime: true,
    //     time_24hr: true
    // });

    // // Set value programmatically
    // $('#clock_out').val(moment(response[0]['clock_out']).format('YYYY-MM-DD HH:mm'));

    // // Update Flatpickr instance to reflect the set value
    // flatpickrInstance.setDate($('#clock_out').val());

    function confirmDelete(event) {
        event.preventDefault();
        swal({
                title: 'Are you sure?',
                // text: 'Once deleted, you will not be able to recover this imaginary file!',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $('#deleteattendance').submit();
                }
            });
    }
</script>
