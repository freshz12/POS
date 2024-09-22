<script>
    $(document).ready(function() {
        const userPermissions = {
            canEdit: @json(auth()->user()->can('roles_edit')),
            canDelete: @json(auth()->user()->can('roles_delete')),
        };

        $('#datatable').DataTable({
            "dom": '<"row"<"col-12 d-flex justify-content-end"f>>' +
                '<"row"<"col-12"t>>' +
                '<"row"<"col-12"<"d-flex justify-content-start"l>>>' +
                '<"row"<"col-12"<i><"d-flex justify-content-end"p>>>',
            "pagingType": "numbers",
            "ajax": {
                "url": "{{ url('/roles/index_data') }}",
                "dataSrc": "data",
                "data": function(d) {
                    d.name = $('#name_filter').val();
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
                                <button onclick="ajaxEdit(${row.id})" class="btn btn-primary mr-2" data-toggle="modal" data-target="#editrole">
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
                                <form action="/roles/delete" method="POST" style="display:inline;" id="deleterole">
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

        $('#filterrole').modal('hide');
    }

    function resetfilter() {
        $('#name_filter').val('');
        $('#updated_at_filter').val('');

        $('#datatable').DataTable().ajax.reload();

        $('#filterrole').modal('hide');
    }

    function handleDownload() {
        var baseUrl = "{{ url('/roles/export') }}";
        var params = {
            name: $('#name_filter').val(),
            updated_at: $('#updated_at_filter').val()
        };
        var queryString = $.param(params);
        var downloadUrl = baseUrl + '?' + queryString;

        var tempLink = document.createElement('a');
        tempLink.href = downloadUrl;
        tempLink.download = 'roles.xlsx';

        document.body.appendChild(tempLink);
        tempLink.click();

        document.body.removeChild(tempLink);
    }

    $('#addrole').on('show.bs.modal', function() {
        $(this).find('form')[0].reset();
    });

    function ajaxEdit(id) {
        $.ajax({
            url: '/roles/show/' + id,
            type: 'GET',
            success: function(response) {
                $('#edit_id').val(response[0].id);
                $('#edit_name').val(response[0].name);
                getPermissions("edit", response)
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
                    $('#deleterole').submit();
                }
            });
    }

    function getPermissions(modulType, data = null) {
        $.ajax({
            url: "{{ url('/roles/permissions_data') }}",
            type: 'GET',
            success: function(response) {
                if (modulType == 'create') {
                    assignPermissions(response.data)
                } else if (modulType == 'edit') {
                    $('#editrole').modal('show');
                    assignEditPermissions(response.data, data[0].permissions)
                }
            },
            error: function(xhr) {
                alert('Error:', xhr.responseText);
            }
        });
    }


    // Global variable to keep track of permission states
    let permissionStates = {};

    function assignPermissions(data) {
        const container = $('#permissions-container');
        container.empty();

        // Initialize permissionStates
        permissionStates = {};

        $.each(data.permissionTypes, function(index, type) {
            permissionStates[type.type] = {
                checked: false,
                permissions: []
            };
        });

        $.each(data.permissionTypes, function(index, type) {
            const typeId = type.type.replace(/\s+/g, ''); // Remove spaces for id

            const typeDiv = $('<div>', {
                class: 'row'
            });
            const typeCol = $('<div>', {
                class: 'col-6'
            });
            const formCheck = $('<div>', {
                class: 'form-check'
            });
            const checkbox = $('<input>', {
                type: 'checkbox',
                class: 'form-check-input',
                id: `checkPermissionType${typeId}`,
            });
            const label = $('<label>', {
                class: 'form-check-label bold-label',
                for: checkbox.attr('id'),
                text: type.type
            });

            checkbox.change(function() {
                const isChecked = $(this).is(':checked');
                typeDiv.find('.permission-checkbox').prop('checked', isChecked);
                permissionStates[type.type].checked = isChecked;
                updateMainCheckbox(); // Ensure main checkbox reflects the correct state
            });

            formCheck.append(checkbox).append(label);
            typeCol.append(formCheck);
            typeDiv.append(typeCol);

            const permissionsCol = $('<div>', {
                class: 'col-6 role-management-checkbox'
            });
            typeDiv.append(permissionsCol);

            const permissions = data.permissions[type.type] || [];
            permissionStates[type.type].permissions = permissions.map(p => p.id);

            $.each(permissions, function(index, permission) {
                const permissionId = permission.id;
                const permissionCheck = $('<div>', {
                    class: 'form-check'
                });
                const permissionInput = $('<input>', {
                    type: 'checkbox',
                    class: 'form-check-input permission-checkbox',
                    id: `checkPermission${permissionId}`,
                    value: permission.id,
                    name: `permissions[]`
                });
                const permissionLabel = $('<label>', {
                    class: 'form-check-label',
                    for: permissionInput.attr('id'),
                    text: permission.name
                });

                permissionInput.change(function() {
                    updateTypeCheckbox(type.type);
                });

                permissionCheck.append(permissionInput).append(permissionLabel);
                permissionsCol.append(permissionCheck);
            });

            container.append(typeDiv);

            if (index < data.permissionTypes.length - 1) {
                container.append('<hr>');
            }
        });

        function updateTypeCheckbox(typeId) {
            const typeCheckbox = $(`#checkPermissionType${typeId.replace(/\s+/g, '')}`);
            const typeDiv = typeCheckbox.closest('.row');
            const allPermissions = typeDiv.find('.permission-checkbox');
            const checkedPermissions = allPermissions.filter(':checked');

            const allChecked = allPermissions.length > 0 && allPermissions.length === checkedPermissions.length;
            typeCheckbox.prop('checked', allChecked);

            permissionStates[typeId].checked = allChecked;
            updateMainCheckbox(); // Ensure main checkbox reflects the correct state
        }

        function updateMainCheckbox() {
            const allTypeCheckboxes = $('#permissions-container').find('.form-check-input').not('.permission-checkbox');
            const allTypeChecked = allTypeCheckboxes.length > 0 && allTypeCheckboxes.length === allTypeCheckboxes
                .filter(':checked').length;
            $('#checkAll').prop('checked', allTypeChecked);
        }
    }

    function toggleAllPermissions(isChecked) {
        const checkStatus = isChecked ? true : false;

        // Toggle all permission checkboxes
        $('#permissions-container').find('.permission-checkbox').each(function() {
            $(this).prop('checked', checkStatus);
        });

        // Toggle all type checkboxes
        $('#permissions-container').find('.form-check-input').not('.permission-checkbox').each(function() {
            $(this).prop('checked', checkStatus);
        });

        // Update the permissionStates object
        $.each(permissionStates, function(typeName, state) {
            state.checked = checkStatus;
        });

        // Ensure main checkbox reflects the correct state
        $('#checkAll').prop('checked', checkStatus);
    }

    let permissionEditStates = {};

    function assignEditPermissions(data, rolesPermissions) {
        const container = $('#permissions-edit-container');
        container.empty();

        permissionEditStates = {};

        // Initialize permission states
        $.each(data.permissionTypes, function(index, type) {
            permissionEditStates[type.type] = {
                checked: false,
                permissions: []
            };
        });

        // Iterate over permission types and create the UI
        $.each(data.permissionTypes, function(index, type) {
            const typeId = type.type.replace(/\s+/g, ''); // Remove spaces for ID

            const typeDiv = $('<div>', {
                class: 'row'
            });
            const typeCol = $('<div>', {
                class: 'col-6'
            });
            const formCheck = $('<div>', {
                class: 'form-check'
            });

            const typeCheckbox = $('<input>', {
                type: 'checkbox',
                class: 'form-check-input',
                id: `checkEditPermissionType${typeId}`,
            });
            const typeLabel = $('<label>', {
                class: 'form-check-label bold-label',
                for: typeCheckbox.attr('id'),
                text: type.type
            });

            // Toggle all permissions for this type when type checkbox is changed
            typeCheckbox.change(function() {
                const isChecked = $(this).is(':checked');
                typeDiv.find('.permission-edit-checkbox').prop('checked',
                    isChecked); // Check/uncheck all permissions under this type
                permissionEditStates[type.type].checked = isChecked; // Update permissionEditStates
                updateMainCheckbox(); // Update main "check all" checkbox state
            });

            formCheck.append(typeCheckbox).append(typeLabel);
            typeCol.append(formCheck);
            typeDiv.append(typeCol);

            const permissionsCol = $('<div>', {
                class: 'col-6 role-management-checkbox'
            });
            typeDiv.append(permissionsCol);

            const permissions = data.permissions[type.type] || [];
            permissionEditStates[type.type].permissions = permissions.map(p => p.id);

            // Iterate over permissions and create checkboxes
            $.each(permissions, function(index, permission) {
                const permissionId = permission.id;

                const permissionCheck = $('<div>', {
                    class: 'form-check'
                });
                const permissionInput = $('<input>', {
                    type: 'checkbox',
                    class: 'form-check-input permission-edit-checkbox',
                    id: `checkEditPermission${permissionId}`,
                    value: permission.id,
                    name: `permissions[]`,
                    checked: rolesPermissions.includes(permission.id.toString()) ? true :
                        false // Check if permission is assigned
                });
                const permissionLabel = $('<label>', {
                    class: 'form-check-label',
                    for: permissionInput.attr('id'),
                    text: permission.name
                });

                permissionCheck.append(permissionInput).append(permissionLabel);
                permissionsCol.append(permissionCheck);



                // Handle the change event for individual permissions
                permissionInput.change(function() {
                    updateTypeCheckbox(type
                        .type); // Update type checkbox based on individual permissions
                    updateMainCheckbox(); // Ensure the "check all" checkbox is updated
                });
            });

            container.append(typeDiv);

            if (index < data.permissionTypes.length - 1) {
                container.append('<hr>');
            }

            // After rendering all permissions, check if the type checkbox should be checked based on permissions
            updateTypeCheckbox(type.type);
        });

        // Ensure the main checkbox is unchecked initially
        $('#checkEditAll').prop('checked', false);

        // Check if all permissions are checked after rendering and update the "check all" checkbox
        updateMainCheckbox();

        // Update the type checkbox based on the permissions
        function updateTypeCheckbox(typeId) {
            const typeCheckbox = $(`#checkEditPermissionType${typeId.replace(/\s+/g, '')}`);
            const typeDiv = typeCheckbox.closest('.row');
            const allPermissions = typeDiv.find('.permission-edit-checkbox');
            const checkedPermissions = allPermissions.filter(':checked');

            const allChecked = allPermissions.length > 0 && allPermissions.length === checkedPermissions.length;
            typeCheckbox.prop('checked', allChecked); // Check/uncheck the type checkbox

            permissionEditStates[typeId].checked = allChecked;
        }

        // Update the main "check all" checkbox based on ALL permissions checkboxes
        function updateMainCheckbox() {
            const allPermissions = $('#permissions-edit-container').find('.permission-edit-checkbox');
            const allCheckedPermissions = allPermissions.filter(':checked');

            const allChecked = allPermissions.length > 0 && allPermissions.length === allCheckedPermissions.length;

            // Check or uncheck the main "check all" checkbox based on permission states
            $('#checkEditAll').prop('checked', allChecked);
        }
    }

    // Toggle all permissions checkbox
    function toggleEditAllPermissions(isChecked) {
        const checkStatus = isChecked ? true : false;

        // Toggle all permission checkboxes
        $('#permissions-edit-container').find('.permission-edit-checkbox').each(function() {
            $(this).prop('checked', checkStatus);
        });

        // Toggle all type checkboxes
        $('#permissions-edit-container').find('.form-check-input').not('.permission-edit-checkbox').each(function() {
            $(this).prop('checked', checkStatus);
        });

        // Update the permissionEditStates object
        $.each(permissionEditStates, function(typeName, state) {
            state.checked = checkStatus;
        });

        // Ensure the main checkbox reflects the correct state
        $('#checkEditAll').prop('checked', checkStatus);
    }
</script>
