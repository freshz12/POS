<script>
    $(document).ready(function() {
        const userPermissions = {
            canEdit: @json(auth()->user()->can('promos_edit')),
            canDelete: @json(auth()->user()->can('promos_delete')),
        };
        $('#datatable').DataTable({
            "dom": '<"row"<"col-12 d-flex justify-content-end"f>>' +
                '<"row"<"col-12"t>>' +
                '<"row"<"col-12"<"d-flex justify-content-start"l>>>' +
                '<"row"<"col-12"<i><"d-flex justify-content-end"p>>>',
            "pagingType": "full_numbers",
            "ajax": {
                "url": "{{ url('/promos/index_data') }}",
                "dataSrc": "data",
                "data": function(d) {
                    d.name = $('#name_filter').val();
                    d.unique_code = $('#unique_code_filter').val();
                    d.type = $('#type_filter').val();
                    d.value = $('#value_filter').val();
                    d.start_date = $('#start_date_filter').val();
                    d.end_date = $('#end_date_filter').val();
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
                                <button onclick="ajaxEdit(${row.id})" class="btn btn-primary mr-2" data-toggle="modal" data-target="#editpromo">
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
                                <button onclick="confirmDeleteAjax(event, ${row.id})" class="btn btn-danger">
                                    <i class="ion-trash-a" data-pack="default" data-tags="delete, remove, dump"></i>
                                </button>
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
                    "targets": 5,
                    "render": function(data, type, row) {
                        if (row.type === 'Percentage') {
                            return row.value + '%';
                        } else if (row.type === 'Nominal') {
                            return 'Rp. ' + row.value;
                        } else {
                            return '';
                        }
                    }
                }
            ],
            "columns": [{
                    "data": null
                },
                {
                    "data": null
                },
                {
                    "data": "name",
                    "render": function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    "data": "unique_code",
                    "render": function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    "data": "type",
                    "render": function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    "data": "value"
                },
                {
                    "data": "start_date",
                    "render": function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    "data": "end_date",
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

        $('#div_percentage').hide();
        $('#div_package').hide();
        $('#div_percentage_edit').hide();
        $('#div_package_edit').hide();

    });

    $('#type').change(function() {
        let type = $(this).val();
        let divNominal = $('#div_nominal');
        let divPercentage = $('#div_percentage');
        let divPackage = $('#div_package');
        let valueNominal = $('#value_nominal');
        let valuePercentage = $('#value_percentage');
        let valuePackage = $('#value_package');
        let packageQuantity = $('#package_quantity');

        if (type == 'Nominal') {
            typeNominal(divNominal, divPercentage, divPackage, valueNominal, valuePercentage, valuePackage,
                packageQuantity)
        } else if (type == 'Percentage') {
            typePercentage(divNominal, divPercentage, divPackage, valueNominal, valuePercentage, valuePackage,
                packageQuantity);
        } else if (type == 'Package') {
            typePackage(divPackage, valueNominal, valuePercentage, valuePackage, packageQuantity);
        }
    })



    $('#type_edit').change(function() {
        typeEdit($(this));
    })

    function typeEdit(typeElement) {
        let type = typeElement.val();
        let divNominal = $('#div_nominal_edit');
        let divPercentage = $('#div_percentage_edit');
        let divPackage = $('#div_package_edit');
        let valueNominal = $('#value_nominal_edit');
        let valuePercentage = $('#value_percentage_edit');
        let valuePackage = $('#value_package_edit');
        let packageQuantity = $('#package_quantity_edit');

        if (type == 'Nominal') {
            typeNominal(divNominal, divPercentage, divPackage, valueNominal, valuePercentage, valuePackage,
                packageQuantity)
        } else if (type == 'Percentage') {
            typePercentage(divNominal, divPercentage, divPackage, valueNominal, valuePercentage, valuePackage,
                packageQuantity);
        } else if (type == 'Package') {
            typePackage(divPackage, valueNominal, valuePercentage, valuePackage, packageQuantity);
        }
    }

    $('.select2.products').on('select2:select', function(e) {
        var selectedProduct = e.params.data;

        var productText = selectedProduct.text;
        var productName = productText.match(/Product Name : <strong>(.*?)<\/strong>/)[1];
        var sku = productText.match(/SKU : (.*)/)[1] || 'N/A';

        var productExists = $('#productList tr').filter(function() {
            return $(this).data('productId') === selectedProduct.id;
        }).length > 0;

        if (!productExists) {
            $('#noProductRow').remove();

            $('#productList').append(`
            <tr data-product-id="${selectedProduct.id}">
                <td>${productName}</td>
                <td>${sku}</td>
                <td><button type="button" class="btn btn-danger btn-sm removeProductBtn"><i class="fas fa-trash"></i></button></td>
            </tr>
        `);
            updateSelectedProducts();
        }

        $(this).val(null).trigger('change');
    });

    $('#value_package_edit').on('select2:select', function(e) {
        var selectedProduct = e.params.data;

        var productText = selectedProduct.text;
        var productName = productText.match(/Product Name : <strong>(.*?)<\/strong>/)[1];
        var sku = productText.match(/SKU : (.*)/)[1] || 'N/A';

        var productExists = $('#productListEdit tr').filter(function() {
            return $(this).data('productId') === selectedProduct.id;
        }).length > 0;

        if (!productExists) {
            $('#noProductRowEdit').remove();

            $('#productListEdit').append(`
            <tr data-product-id="${selectedProduct.id}">
                <td>${productName}</td>
                <td>${sku}</td>
                <td><button type="button" class="btn btn-danger btn-sm removeProductBtn"><i class="fas fa-trash"></i></button></td>
            </tr>
        `);
            updateSelectedProductsEdit();
        }

        $(this).val(null).trigger('change');
    });

    function updateSelectedProducts() {
        var productIds = [];

        $('#productList tr').each(function() {
            var productId = $(this).data('productId');
            if (productId) {
                productIds.push(productId);
            }
        });

        $('#selected_products').val(productIds.join(','));
    }

    function updateSelectedProductsEdit() {
        var productIds = [];

        $('#productListEdit tr').each(function() {
            var productId = $(this).data('productId');
            if (productId) {
                productIds.push(productId);
            }
        });

        $('#selected_products_edit').val(productIds.join(','));
    }


    $(document).on('click', '.removeProductBtn', function() {
        $(this).closest('tr').remove();
        updateSelectedProducts();

        if ($('#productList tr').length === 0) {
            $('#productList').append(`
                <tr id="noProductRow">
                    <td colspan="3" class="text-center">No products added</td>
                </tr>
            `);
        }
    });

    $(document).on('click', '.removeProductBtnEdit', function() {
        $(this).closest('tr').remove();
        updateSelectedProductsEdit();

        if ($('#productListEdit tr').length === 0) {
            $('#productListEdit').append(`
                <tr id="noProductRow">
                    <td colspan="3" class="text-center">No products added</td>
                </tr>
            `);
        }
    });

    function applyfilter() {
        $('#datatable').DataTable().ajax.reload();

        $('#filterpromo').modal('hide');
    }

    function resetfilter() {
        $('#name_filter').val('');
        $('#unique_code_filter').val('');
        $('#type_filter').val('');
        $('#value_filter').val('');
        $('#start_date_filter').val('');
        $('#end_date_filter').val('');
        $('#updated_at_filter').val('');

        $('#datatable').DataTable().ajax.reload();

        $('#filterpromo').modal('hide');
    }

    function handleDownload() {
        var baseUrl = "{{ url('/promos/export') }}";
        var params = {
            name: $('#name_filter').val(),
            unique_code: $('#unique_code_filter').val(),
            type: $('#type_filter').val(),
            value: $('#value_filter').val(),
            start_date: $('#start_date_filter').val(),
            end_date: $('#end_date_filter').val(),
            updated_at: $('#updated_at_filter').val()
        };
        var queryString = $.param(params);
        var downloadUrl = baseUrl + '?' + queryString;

        var tempLink = document.createElement('a');
        tempLink.href = downloadUrl;
        tempLink.download = 'promos.xlsx';

        document.body.appendChild(tempLink);
        tempLink.click();

        document.body.removeChild(tempLink);
    }

    $('#addpromo').on('show.bs.modal', function() {
        $(this).find('form')[0].reset();
    });

    function ajaxEdit(id) {
        $.ajax({
            url: '/promos/show/' + id,
            type: 'GET',
            success: function(response) {
                typeEditElement = $('#type_edit');
                $('#name_edit').val(response[0]['name']);
                $('#unique_code_edit').val(response[0]['unique_code']);
                typeEditElement.val(response[0]['type']);
                $('#start_date_edit').val(response[0]['start_date']);
                $('#end_date_edit').val(response[0]['end_date']);
                $('#id').val(response[0]['id']);
                typeEdit(typeEditElement);

                if (typeEditElement.val() == 'Nominal') {
                    $('#value_nominal_edit').val(response[0]['value']);
                } else if (typeEditElement.val() == 'Percentage') {
                    $('#value_percentage_edit').val(response[0]['value']);
                } else if (typeEditElement.val() == 'Package') {
                    $('#package_quantity_edit').val(response[0]['package_quantity']);
                    $('#selected_products_edit').val(response[0]['product_id']);

                    $('#noProductRowEdit').remove();
                    $('#productListEdit').empty();

                    response[0]['products'].forEach(function(product) {
                        $('#productListEdit').append(`
                            <tr data-product-id="${product.id}">
                                <td>${product.product_name}</td>
                                <td>${product.sku}</td>
                                <td><button type="button" class="btn btn-danger btn-sm removeProductBtnEdit"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        `);
                    });
                    updateSelectedProductsEdit();
                }
            },
            error: function(xhr) {
                alert('Error:', xhr.responseText);
            }
        });
    }

    function confirmDeleteAjax(event, id) {
        event.preventDefault();

        swal({
                title: 'Are you sure?',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ url('/promos/delete') }}",
                        type: 'POST',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            swal('Deleted!', 'Record has been deleted.', 'success');
                            $('#datatable').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            swal('Error!', 'Failed to delete the Promo.', 'error');
                        }
                    });
                }
            });
    }

    function typeNominal(divNominal, divPercentage, divPackage, valueNominal, valuePercentage, valuePackage,
        packageQuantity) {
        divNominal.show();
        divPercentage.hide();
        divPackage.hide();
        valueNominal.removeAttr('disabled');
        valuePercentage.attr('disabled', 'disabled').val('');
        valuePackage.attr('disabled', 'disabled').val('');
        packageQuantity.attr('disabled', 'disabled').val('');
    }

    function typePercentage(divNominal, divPercentage, divPackage, valueNominal, valuePercentage, valuePackage,
        packageQuantity) {
        divNominal.hide();
        divPercentage.show();
        divPackage.hide();
        valueNominal.attr('disabled', 'disabled').val('');
        valuePercentage.removeAttr('disabled');
        valuePackage.attr('disabled', 'disabled').val('');
        packageQuantity.attr('disabled', 'disabled').val('');
    }

    function typePackage(divPackage, valueNominal, valuePercentage, valuePackage, packageQuantity) {
        divPackage.show();
        valueNominal.attr('disabled', 'disabled').val('');
        valuePercentage.attr('disabled', 'disabled').val('');
        valuePackage.removeAttr('disabled');
        packageQuantity.removeAttr('disabled');
    }
</script>
