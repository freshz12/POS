<script>
    $(document).ready(function() {

        $('#datatable').DataTable({
            "dom": '<"row"<"col-12 d-flex justify-content-end"f>>' +
                '<"row"<"col-12"t>>' +
                '<"row"<"col-12"<"d-flex justify-content-start"l>>>' +
                '<"row"<"col-12"<i><"d-flex justify-content-end"p>>>',
            "pagingType": "full_numbers",
            "ajax": {
                "url": "{{ url('/dashboards/customers/index_data') }}",
                "type": "GET",
                "data": function(d) {
                    d.customer_name = $('#customer_name_filter').val();
                    d.phone_number = $('#phone_number_filter').val();
                    d.total_spent = $('#total_spent_filter').val();
                    d.total_transactions = $('#total_transactions_filter').val();
                },
            },
            "columnDefs": [{
                "targets": 0,
                "render": function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                "orderable": false,
                "searchable": false
            }, {
                "targets": 2,
                "render": function(data, type, row) {
                    return formatNumberWithCommas(row.total_spent);
                }
            }, ],
            "columns": [{
                    "data": null
                },
                {
                    "data": "customer_name",
                    "render": function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    "data": "phone_number",
                    "render": function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    "data": "total_spent"
                },
                {
                    "data": "total_transactions",
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

    });

    function applyfilter() {
        $('#datatable').DataTable().ajax.reload();

        $('#filtercustomer').modal('hide');
    }

    function resetfilter() {
        $('#customer_name_filter').val('');
        $('#phone_number_filter').val('');
        $('#total_transactions_filter').val('');
        $('#total_spent_filter').val(null);

        $('#datatable').DataTable().ajax.reload();

        $('#filtercustomer').modal('hide');
    }

    function handleDownload() {
        var baseUrl = "{{ url('/dashboards/customers/export') }}";
        var params = {
            customer_name: $('#customer_name_filter').val(),
            phone_number: $('#phone_number_filter').val(),
            total_transactions: $('#total_transactions_filter').val(),
            total_spent: $('#total_spent_filter').val()
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

    function formatNumberWithCommas(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
</script>
