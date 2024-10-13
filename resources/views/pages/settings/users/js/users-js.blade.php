<script>
    $(document).ready(function() {
        const userPermissions = {
            canEdit: @json(auth()->user()->can('users_edit')),
            canDelete: @json(auth()->user()->can('users_delete')),
        };

        $('#datatable').DataTable({
            "dom": '<"row"<"col-12 d-flex justify-content-end"f>>' +
                '<"row"<"col-12"t>>' +
                '<"row"<"col-12"<"d-flex justify-content-start"l>>>' +
                '<"row"<"col-12"<i><"d-flex justify-content-end"p>>>',
            "pagingType": "full_numbers",
            "ajax": {
                "url": "{{ url('/users/index_data') }}",
                "dataSrc": "data",
                "data": function(d) {
                    d.name = $('#name_filter').val();
                    d.username = $('#username_filter').val();
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
                                <button onclick="ajaxEdit(${row.id})" class="btn btn-primary mr-2" data-toggle="modal" data-target="#edituser">
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
                                <form action="{{ url('/users/delete') }}" method="POST" style="display:inline;" id="deleteuser">
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
                    "targets": 4,
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
                    "data": "name"
                },
                {
                    "data": "username"
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

    });

    function applyfilter() {
        $('#datatable').DataTable().ajax.reload();

        $('#filteruser').modal('hide');
    }

    function resetfilter() {
        $('#name_filter').val('');
        $('#username_filter').val('');
        $('#gender_filter').val('');
        $('#phone_number_filter').val('');
        $('#updated_at_filter').val('');

        $('#datatable').DataTable().ajax.reload();

        $('#filteruser').modal('hide');
    }

    function handleDownload() {
        var baseUrl = "{{ url('/users/export') }}";
        var params = {
            name: $('#name_filter').val(),
            username: $('#username_filter').val(),
            updated_at: $('#updated_at_filter').val()
        };
        var queryString = $.param(params);
        var downloadUrl = baseUrl + '?' + queryString;

        var tempLink = document.createElement('a');
        tempLink.href = downloadUrl;
        tempLink.download = 'users.xlsx';

        document.body.appendChild(tempLink);
        tempLink.click();

        document.body.removeChild(tempLink);
    }

    $('#adduser').on('show.bs.modal', function() {
        $(this).find('form')[0].reset();

        showAllRoles();
    });

    function showAllRoles(selectedValue = null, type = null) {
        $.ajax({
            url: '/users/roles_data',
            type: 'GET',
            success: function(response) {

                var $select = type == null ? $('#role') : $('#editrole');

                console.log($select);

                $select.empty();

                $select.append($('<option>', {
                    value: '',
                    hidden: true,
                    disabled: true,
                    selected: true
                }));
                $.each(response.data, function(index, item) {
                    var $option = $('<option>', {
                        value: item.id,
                        text: item.name
                    });

                    if (type !== null && selectedValue !== null) {
                        if (item.id == selectedValue) {
                            $option.attr('selected', 'selected');
                        }
                    }

                    $select.append($option);
                });
            },
            error: function(xhr) {
                alert('Error:', xhr.responseText);
            }
        });
    }

    function ajaxEdit(id) {
        $.ajax({
            url: '/users/show/' + id,
            type: 'GET',
            success: function(response) {
                $('#name').val(response[0]['name']);
                $('#username').val(response[0]['username']);
                $('#id').val(response[0]['id']);
                showAllRoles(response[0].roles[0].id, 'edit');
            },
            error: function(xhr) {
                alert('Error:', xhr.responseText);
            }
        });
    }

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
                    $('#deleteuser').submit();
                }
            });
    }
</script>
