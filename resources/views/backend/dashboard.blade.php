@extends('backend.layouts.app')

@section('content')
    <div class="p-6 mx-auto">
        <h1 class="text-3xl font-bold mb-6">Dashboard Quản trị ZNS</h1>

        <!-- Bộ lọc thời gian -->
        <!-- Bộ lọc -->
        <!-- Nút lọc -->

        <div class="flex flex-wrap gap-2 mb-4">
            <button class="filter-btn px-4 py-2 bg-blue-600  border rounded-lg text-white" data-filter="today">Hôm
                nay</button>
            <button class="filter-btn px-4 py-2 bg-white border rounded-lg text-gray-700" data-filter="yesterday">Hôm
                qua</button>
            <button class="filter-btn px-4 py-2 bg-white border rounded-lg text-gray-700" data-filter="last_7_days">7 ngày
                qua</button>

            <!-- Nút hiện bộ lọc -->
            <button id="show-custom-date" class="px-4 py-2 bg-white border rounded-lg text-gray-700">Tùy chỉnh</button>

            <!-- Date range input đẹp -->
            <div id="custom-date-range" class="hidden flex-wrap gap-2 items-center">
                <input id="date-range-picker" type="text"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-60 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Chọn khoảng ngày" />
                <button id="custom-filter-btn"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Lọc</button>
            </div>
        </div>






        <!-- Số liệu thống kê -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 shadow">
                <p class="text-sm text-gray-500">Khách hàng</p>
                <p class="text-2xl font-bold text-green-600" id="total-customer">{{ $customerSummary['customer'] }}</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow">
                <p class="text-sm text-gray-500">Dơn hàng</p>
                <p class="text-2xl font-bold text-red-600" id="count-order">{{ $orderCount }}</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow">
                <p class="text-sm text-gray-500">Tiền đơn hàng</p>
                <p class="text-2xl font-bold" id="total-amount">{{ $orderSum }}</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow">
                <p class="text-sm text-gray-500">Nhân viên</p>
                <p class="text-2xl font-bold text-yellow-600" id="total-user">{{ $userCount }}</p>
            </div>

        </div>

    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Flatpickr CSS -->
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Giao diện Tailwind đẹp hơn -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Ngôn ngữ Tiếng Việt -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
    <script>
        function updateDashboard(data) {
            $('#total-customer').text(number_format(data.customerSummary.customer));
            $('#count-order').text(number_format(data.orderCount));
            $('#total-amount').text(number_format(data.orderSum) + ' đ');
            $('#total-user').text(number_format(data.userCount));
        }

        function number_format(num) {
            return new Intl.NumberFormat('vi-VN').format(num);
        }

        $('#show-custom-date').on('click', function() {
            $('#custom-date-range').removeClass('hidden').addClass('flex');


            $('.filter-btn').removeClass('bg-blue-600 text-white').addClass('bg-white border text-gray-700');
            $(this).removeClass('bg-white border text-gray-700').addClass('bg-blue-600 text-white');


            let dateRange = $('#date-range-picker').val();
            let [from, to] = dateRange.split(' đến ').map(str => str.trim());

            if (from && to) {
                $.get("{{ route('dashboard.filter') }}", {
                    filter: 'custom',
                    from: from,
                    to: to
                }, function(response) {
                    updateDashboard(response);
                }).fail(() => alert('Lỗi khi tải dữ liệu'));
            }
        });

        flatpickr("#date-range-picker", {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "vn",
            maxDate: "today"
        });



        $('.filter-btn').on('click', function(e) {
            e.preventDefault();

            let filter = $(this).data('filter');


            $('#custom-date-range').addClass('hidden');


            $('.filter-btn').removeClass('bg-blue-600 text-white').addClass('bg-white border text-gray-700');
            $(this).removeClass('bg-white border text-gray-700').addClass('bg-blue-600 text-white');
            $('#show-custom-date').removeClass('bg-blue-600 text-white').addClass('bg-white border text-gray-700');

            $.get("{{ route('dashboard.filter') }}", {
                filter: filter
            }, function(response) {
                updateDashboard(response);
            }).fail(() => alert('Lỗi khi tải dữ liệu'));
        });


        $('#custom-filter-btn').on('click', function() {
            let dateRange = $('#date-range-picker').val();
            let [from, to] = dateRange.split(' đến ').map(str => str.trim());

            if (!from || !to) {
                alert('Vui lòng chọn đầy đủ ngày bắt đầu và kết thúc');
                return;
            }


            $('.filter-btn').removeClass('bg-blue-600 text-white').addClass('bg-white border text-gray-700');
            $(this).removeClass('bg-white border text-gray-700').addClass('bg-blue-600 text-white');



            $.get("{{ route('dashboard.filter') }}", {
                filter: 'custom',
                from: from,
                to: to
            }, function(response) {
                updateDashboard(response);
            }).fail(() => alert('Lỗi khi tải dữ liệu'));
        });
    </script>
@endpush
