<script>
    $(document).ready(function() {
        $('#created_from_filter').val(null);
        $('#created_to_filter').val(null);
        $('#created_type').val('today');
        $('#datatable').DataTable({
            "dom": '<"row"<"col-12 d-flex justify-content-end"f>>' +
                '<"row"<"col-12"t>>' +
                '<"row"<"col-12"<"d-flex justify-content-start"l>>>' +
                '<"row"<"col-12"<i><"d-flex justify-content-end"p>>>',
            "pagingType": "full_numbers",
            "ajax": {
                "url": "{{ url('/dashboards/summary_payment/index_data') }}",
                "type": "GET",
                "data": function(d) {
                    d.created_from_filter = $('#created_from_filter').val();
                    d.created_to_filter = $('#created_to_filter').val();
                    d.created_type = $('#created_type').val();
                },
            },
            "columnDefs": [{
                "targets": 0,
                "render": function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                "orderable": false,
                "searchable": false
            }],
            "columns": [{
                    "data": null
                },
                {
                    "data": "transaction_period",
                    "render": function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    "data": "total_customer",
                    "render": function(data, type, row) {
                        return data ? data : 'N/A';
                    }
                },
                {
                    "data": "total_amount",
                    "render": function(data, type, row) {
                        return data ? formatNumberWithCommas(data) : 'N/A';
                    }
                },
                {
                    "data": "total_cash",
                    "render": function(data, type, row) {
                        return data ? formatNumberWithCommas(data) : 'N/A';
                    }
                },
                {
                    "data": "total_edc",
                    "render": function(data, type, row) {
                        return data ? formatNumberWithCommas(data) : 'N/A';
                    }
                },
                {
                    "data": "total_qris",
                    "render": function(data, type, row) {
                        return data ? formatNumberWithCommas(data) : 'N/A';
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
            }
        });

    });

    function applyfilter() {
        $('#created_type').val('custom');
        $('#created_type_select').val('custom');
        $('#datatable').DataTable().ajax.reload();

        $('#filterproduct').modal('hide');
    }

    function resetfilter() {
        $('#created_from_filter').val(null);
        $('#created_to_filter').val(null);
        $('#created_type').val('today');

        $('#datatable').DataTable().ajax.reload();

        $('#filterproduct').modal('hide');
    }

    function handleDownload() {
        var baseUrl = "{{ url('/dashboards/summary_payment/export') }}";
        var params = {
            created_from_filter: $('#created_from_filter').val(),
            created_to_filter: $('#created_to_filter').val(),
            created_type: $('#created_type').val()
        };
        var queryString = $.param(params);
        var downloadUrl = baseUrl + '?' + queryString;

        var tempLink = document.createElement('a');
        tempLink.href = downloadUrl;
        tempLink.download = 'summary_payment.xlsx';

        document.body.appendChild(tempLink);
        tempLink.click();

        document.body.removeChild(tempLink);
    }

    function changeCreatedTypeFilter(created_type) {
        $('#created_from_filter').val(null);
        $('#created_to_filter').val(null);

        $('#created_type').val(created_type);

        $('#datatable').DataTable().ajax.reload();
    }

    function formatNumberWithCommas(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
</script>
