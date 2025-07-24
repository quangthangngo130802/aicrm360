@extends('backend.layouts.app')

@section('content')
    <x-breadcrumb />

    <x-page-header title="Cấu hình hạng mục">
        <button class="btn btn-outline-primary" id="add-row">
            <i class="fas fa-plus"></i> Thêm hàng mới
        </button>
    </x-page-header>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle" id="multiModelTable">
                <thead>
                    <tr>
                        <th>Nguồn khách hàng</th>
                        <th>Phân loại khách hàng</th>
                        <th>Kênh</th>
                        <th>Kết quả</th>
                    </tr>
                </thead>

                <tbody id="excel-body">
                    @for ($i = 0; $i < $maxRows; $i++)

                        <tr>
                            <td>
                                <input type="text" class="form-control" data-table="sources"
                                    value="{{ $sources[$i] ?? '' }}" data-original="{{ $sources[$i] ?? '' }}">
                            </td>

                            <td>
                                <input type="text" class="form-control" data-table="customer_categories"
                                    value="{{ $customerCategory[$i] ?? '' }}" data-original="{{ $customerCategory[$i] ?? '' }}">
                            </td>

                            <td>
                                <input type="text" class="form-control" data-table="channels"
                                    value="{{ $channel[$i] ?? '' }}" data-original="{{ $channel[$i] ?? '' }}">
                            </td>
                            <td>
                                <input type="text" class="form-control" data-table="results"
                                    value="{{ $result[$i] ?? '' }}" data-original="{{ $result[$i] ?? '' }}">
                            </td>

                        </tr>
                    @endfor
                </tbody>

            </table>
        </div>
    </div>
@endsection
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/backend/css/dataTables.min.css') }}">
@endpush
@push('scripts')
    <script>


        $(document).ready(function() {
            $('#add-row').click(function() {
                const row = $('#excel-body tr:first').clone();
                row.find('input').val('').removeAttr('data-original');
                $('#excel-body').append(row);
            });

            $(document).on('blur', '#excel-body input', function() {
                const $input = $(this);
                const value = $input.val().trim();
                const table = $input.data('table');

                const originalValue = $input.data('original') || '';

                // 👉 Nếu không thay đổi gì thì không làm gì cả
                if (value === originalValue) return;

                // 👉 Nếu xóa nội dung
                if (value === '') {
                    if (!originalValue) return;

                    if (!confirm(`Bạn có chắc muốn xóa "${originalValue}"?`)) {
                        $input.val(originalValue);
                        return;
                    }

                    $.ajax({
                        url: '/categorys/excel-delete',
                        method: 'DELETE',
                        data: {
                            table: table,
                            name: originalValue,
                        },
                        success: function(response) {
                            $input.val('');
                            $input.removeAttr('data-original');
                            aicrm.success(response.message)
                        },
                        error: function(xhr) {
                            aicrm.error(xhr.responseJSON.message)
                            $input.val(originalValue);
                        }
                    });
                } else {
                    $.ajax({
                        url: '/categorys/excel-save',
                        method: 'POST',
                        data: {
                            table: table,
                            name: value,
                            original_name: originalValue // nếu muốn xử lý cập nhật

                        },
                        success: function(response) {
                            $input.data('original', value); // cập nhật lại gốc
                            aicrm.success(response.message)
                        },
                        error: function(xhr) {
                            $input.val('')
                            aicrm.error(xhr.responseJSON.message)
                        }
                    });
                }
            });
        });
    </script>
@endpush
