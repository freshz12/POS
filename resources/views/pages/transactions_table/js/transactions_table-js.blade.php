<script>
    $(document).ready(function() {
        const userPermissions = {
            canDelete: @json(auth()->user()->can('transactions_delete')),
        };
        $('#datatable').DataTable({
            "dom": '<"row"<"col-12 d-flex justify-content-end"f>>' +
                '<"row"<"col-12"t>>' +
                '<"row"<"col-12"<"d-flex justify-content-start"l>>>' +
                '<"row"<"col-12"<i><"d-flex justify-content-end"p>>>',
            "pagingType": "full_numbers",
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ url('/transactions_table/index_data') }}",
                "type": "POST",
                "data": function(d) {
                    d.customer_name = $('#customer_name_filter').val();
                    d.capster_name = $('#capster_name_filter').val();
                    d.transaction_id = $('#transaction_id_filter').val();
                    d.total_amount = $('#total_amount_filter').val();
                    d.created_at = $('#created_at_filter').val();
                },
            },
            "columnDefs": [{
                    "targets": 0,
                    "render": function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    "orderable": false,
                    "searchable": false
                },
                {
                    "targets": 1,
                    "render": function(data, type, row, meta) {
                        let deleteButton = '';
                        if (userPermissions.canDelete) {
                            deleteButton = `
                            <form action="{{ url('/transactions_table/delete') }}" method="POST" style="display:inline;" id="deletetransaction">
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
            <button onclick="ajaxEdit(${row.id})" class="btn btn-primary mr-2 position-relative" data-toggle="modal" data-target="#edittransaction">
                <i class="ion-eye" data-pack="default" data-tags="view, see, creeper"></i>
            </button>
            ${deleteButton}
        </div>
    `;
                    },

                    "orderable": false,
                    "searchable": false
                }, {
                    "targets": 4,
                    "render": function(data, type, row, meta) {
                        if (!row.promo) {
                            return '-';
                        }

                        return row.promo.type;
                    },
                    "orderable": false,
                    "searchable": false
                }, {
                    "targets": 6,
                    "render": function(data, type, row, meta) {
                        if (!row.promo) {
                            return '-';
                        }
                        type = row.promo.type;
                        if (type == 'Package' || type == 'Nominal') {
                            value = `${row.promo.value}`;
                        } else if (type == 'Percentage') {
                            value = `${row.promo.value}%`;
                        }
                        return value;
                    },
                    "orderable": false,
                    "searchable": false
                },
            ],
            "columns": [{
                    "data": null
                },
                {
                    "data": null
                },
                {
                    "data": "transaction_id"
                },
                {
                    "data": "customers.full_name"
                },
                {
                    "data": "promo.type"
                },
                {
                    "data": "amount_before_discount"
                },
                {
                    "data": "promo.value"
                },
                {
                    "data": "amount"
                },
                {
                    "data": "capster.full_name"
                },
                {
                    "data": "created_at"
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

        $('#filtertransaction').modal('hide');
    }

    function resetfilter() {
        $('#customer_name_filter').val('');
        $('#capster_name_filter').val('');
        $('#transaction_id_filter').val('');
        $('#total_amount_filter').val('');
        $('#created_at_filter').val('');

        $('#datatable').DataTable().ajax.reload();

        $('#filtertransaction').modal('hide');
    }

    function handleDownload() {
        var baseUrl = "{{ url('/transactions_table/export') }}";
        var params = {
            customer_name: $('#customer_name_filter').val(),
            capster_name: $('#capster_name_filter').val(),
            transaction_id: $('#transaction_id_filter').val(),
            total_amount: $('#total_amount_filter').val(),
            created_at: $('#created_at_filter').val()
        };
        var queryString = $.param(params);
        var downloadUrl = baseUrl + '?' + queryString;

        var tempLink = document.createElement('a');
        tempLink.href = downloadUrl;
        tempLink.download = 'transactions.xlsx';

        document.body.appendChild(tempLink);
        tempLink.click();

        document.body.removeChild(tempLink);
    }

    $('#addtransaction').on('show.bs.modal', function() {
        $(this).find('form')[0].reset();
    });

    function ajaxEdit(id) {
        $.ajax({
            url: '/transactions_table/show/' + id,
            type: 'GET',
            success: function(response) {
                populateTransactionModal(response[0]);
            },
            error: function(xhr) {
                alert('Error:', xhr.responseText);
            }
        });
    }

    function populateTransactionModal(data) {
        document.getElementById('id').value = data.id;
        document.querySelector('#transaction_id').innerText = `Transaction ID: ${data.transaction_id}`;
        document.getElementById('customer_name').value = data.customers.full_name;
        document.getElementById('capster_name').value = data.capster.full_name;
        document.getElementById('total_amount').value = 'Rp.' + formatNumberWithCommas(data.amount);

        const productTableBody = document.getElementById('productTableBody');
        const promoProductTableBody = document.getElementById('promoProductTableBody');
        productTableBody.innerHTML = '';
        promoProductTableBody.innerHTML = '';

        data.transaction_products.forEach(product => {
            const total = product.product_details.selling_price * product.quantity;
            const row = `
            <tr>
                <td>${product.product_details.product_name}</td>
                <td>Rp.${formatNumberWithCommas(product.product_details.selling_price)}</td>
                <td>${product.quantity}</td>
                <td>Rp.${formatNumberWithCommas(total)}</td>
            </tr>
        `;
            productTableBody.insertAdjacentHTML('beforeend', row);
        });

        if(data.promo){
            if(data.promo.type == 'Package'){
                data.promo_products.forEach(promo_product => {
                    const row = `
                    <tr>
                        <td>${promo_product.product_name}</td>
                        <td>Rp.${formatNumberWithCommas(promo_product.selling_price)}</td>
                    </tr>
                `;
                promoProductTableBody.insertAdjacentHTML('beforeend', row);
                });
            }
        }
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
                    $('#deletetransaction').submit();
                }
            });
    }

    function formatNumberWithCommas(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
</script>
