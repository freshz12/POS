<script src="{{ asset('library/bootstrap/dist/js/bootstrap4-toggle.min.js') }}"></script>
<script>
    $(document).ready(function() {
        const userPermissions = {
            canEdit: @json(auth()->user()->can('products_edit')),
            canDelete: @json(auth()->user()->can('products_delete')),
        };
        $('#datatable').DataTable({
            "dom": '<"row"<"col-12 d-flex justify-content-end"f>>' +
                '<"row"<"col-12"t>>' +
                '<"row"<"col-12"<"d-flex justify-content-start"l>>>' +
                '<"row"<"col-12"<i><"d-flex justify-content-end"p>>>',
            "pagingType": "full_numbers",
            "ajax": {
                "url": "{{ url('/products/index_data') }}",
                "dataSrc": "data",
                "data": function(d) {
                    d.product_name = $('#product_name_filter').val();
                    d.selling_price = $('#selling_price_filter').val();
                    d.unit_of_measurement = $('#unit_of_measurement_filter').val();
                    d.quantity = $('#quantity_filter').val();
                    d.is_included_in_receipt = $('#is_included_in_receipt_filter').val();
                    d.updated_at = $('#updated_at_filter').val();
                },
            },
            "columnDefs": [{
                    "targets": 0,
                    "render": function(data, type, row, meta) {
                        return meta.row + 1; // Incremental number starting from 1
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
                                <button onclick="ajaxEdit(${row.id})" class="btn btn-primary mr-2" data-toggle="modal" data-target="#editproduct">
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
                            <form action="/products/delete" method="POST" style="display:inline;" id="deleteProduct">
                                @csrf
                                <input class="form-control" type="hidden" name="id" value="${row.id}">
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
                }, {
                    "targets": 2,
                    "render": function(data, type, row, meta) {
                        var imageUrl = row.picture_path ? `/storage/${row.picture_path}` :
                            '/storage/files/default/product.png';
                        var height = row.picture_path ? 70 : 50;
                        var width = row.picture_path ? 100 : 50;
                        return `<img src="${imageUrl}" style="width:  ${width}px; height: ${height}px;">`;
                    },
                    "orderable": false,
                    "searchable": false
                },
                {
                    "targets": 4,
                    "render": function(data, type, row) {
                        return row.selling_price ? row.selling_price : '-';
                    }
                },
                {
                    "targets": 5,
                    "render": function(data, type, row) {
                        return row.unit_of_measurement ? row.unit_of_measurement : '-';
                    }
                },
                {
                    "targets": 7,
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
                    "data": "picture_path"
                },
                {
                    "data": "product_name"
                },
                {
                    "data": "selling_price"
                },
                {
                    "data": "unit_of_measurement"
                },
                {
                    "data": "quantity"
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

        $('#filterproduct').modal('hide');
    }

    function resetfilter() {
        $('#product_name_filter').val('');
        $('#selling_price_filter').val('');
        $('#unit_of_measurement_filter').val('');
        $('#quantity_filter').val('');
        $('#is_included_in_receipt_filter').val('');
        $('#updated_at_filter').val('');

        $('#datatable').DataTable().ajax.reload();

        $('#filterproduct').modal('hide');
    }

    function handleDownload() {
        var baseUrl = "{{ url('/products/export') }}";
        var params = {
            product_name: $('#product_name_filter').val(),
            selling_price: $('#selling_price_filter').val(),
            unit_of_measurement: $('#unit_of_measurement_filter').val(),
            quantity: $('#quantity_filter').val(),
            is_included_in_receipt: $('#is_included_in_receipt_filter').val(),
            updated_at: $('#updated_at_filter').val()
        };
        var queryString = $.param(params);
        var downloadUrl = baseUrl + '?' + queryString;

        var tempLink = document.createElement('a');
        tempLink.href = downloadUrl;
        tempLink.download = 'products.xlsx';

        document.body.appendChild(tempLink);
        tempLink.click();

        document.body.removeChild(tempLink);
    }

    $('#addproduct').on('show.bs.modal', function() {
        $(this).find('form')[0].reset();
    });

    function ajaxEdit(id) {
        $.ajax({
            url: '/products/show/' + id,
            type: 'GET',
            success: function(response) {
                $('#product_name').val(response[0]['product_name']);
                $('#sku').val(response[0]['sku']);
                $('#purchase_price').val(response[0]['purchase_price']);
                $('#selling_price').val(response[0]['selling_price']);
                $('#quantity').val(response[0]['quantity']);
                $('#unit_of_measurement').val(response[0]['unit_of_measurement']);
                $('#description').val(response[0]['description']);

                let isIncludedInReceipt = response[0]['is_included_in_receipt'];
                if (parseInt(isIncludedInReceipt) == 1) {
                    $('#is_included_in_receipt').bootstrapToggle('on');
                } else {
                    $('#is_included_in_receipt').bootstrapToggle('off');
                }

                $('#id').val(response[0]['id']);
                if (response[0]['picture_path']) {
                    $('#product_picture').attr('src', '/storage/' + response[0]['picture_path']).show();
                    $('#inputFile').hide();
                } else {
                    $('#product_picture').hide();
                }
            },
            error: function(xhr) {
                console.log('Error:', xhr.responseText);
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
                    $('#deleteProduct').submit();
                }
            });
    }
</script>
