@extends('backend.layouts.app')

@section('content')
    <div class="py-4">

        <x-breadcrumb :breadcrumbs="[['label' => 'Nhật ký chăm sóc']]" />

        <x-page-header title="Nhật ký chăm sóc">
            {{-- @if (Auth::user()->is_admin == 1) --}}
            <a href="/customer_care/save" class="btn btn-primary" >
                <i class="fas fa-plus me-1"></i> Thêm nhật ký chăm sóc
            </a>
            {{-- @endif --}}
        </x-page-header>

        <!-- Filter Section -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="search-customer"
                            placeholder="Tìm theo tên khách hàng...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="search-status">
                            <option value="">Kết quả</option>
                            @foreach ($results as $item )
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="search-date">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="select-user">
                            <option value="">Nhân viên</option>
                            @foreach ($users as $item )
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>STT</th>
                        <th>Khách hàng</th>
                        <th>Nhân viên</th>
                        <th>Kênh</th>
                        <th>Ngày chăm sóc gần nhất</th>
                        <th>Chi tiết</th>
                        <th>Kết quả</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="appointment-table">
                    @include('backend.customer_care.partials.table')
                </tbody>
            </table>
        </div>

        <div id="pagination-links">
            @include('backend.customer_care.partials.pagination')
        </div>


    </div>


@endsection

@push('styles')
@endpush

@push('scripts')
    <script>
        function fetchAppointments(url = "/customer_care") {
            let customer = $('#search-customer').val();
            let status = $('#search-status').val();
            let user = $('#select-user').val();
            let date = $('#search-date').val();
            let keyword = $('#search-general').val();

            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    customer: customer,
                    status: status,
                    date: date,
                    keyword: keyword,
                    user: user
                },
                dataType: 'json',
                success: function(response) {
                    $('#appointment-table').html(response.table);
                    $('#pagination-links').html(response.pagination);
                    history.pushState(null, '', url);
                },
                error: function() {
                    alert('Lỗi khi tải dữ liệu.');
                }
            });
        }


        $('#search-customer, #search-status, #search-date, #search-general, #select-user').on('change', function() {
            fetchAppointments();
        });

        // Gọi lại khi phân trang
        $(document).on('click', '#pagination-links .pagination a', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');
            if (url) {
                fetchAppointments(url);
            }
        });

    </script>
    <script>
        $(document).on('click', '.btn-delete-appointment', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Xoá lịch hẹn?',
                text: "Bạn có chắc chắn muốn xoá lịch hẹn này?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Xoá',
                cancelButtonText: 'Không',
                customClass: {
                    popup: 'small-swal'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/customer_care/delete`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: function(res) {
                            Swal.fire('Đã xoá!', res.message || 'Lịch hẹn đã được xoá.',
                                    'success')
                                .then(() => location.reload());
                        },
                        error: function(xhr) {
                            Swal.fire('Lỗi!', xhr.responseJSON?.message ||
                                'Không thể xoá lịch hẹn.', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endpush
