@if ($label)
    <label for="{{ $id }}" class="form-label fw-medium {{ $required ? 'required' : '' }}">
        {{ $label }}
    </label>
@endif
<select name="{{ $multiple ? $name . '[]' : $name }}" id="{{ $id }}" class="form-control"
    {{ $multiple ? 'multiple' : '' }}>
    @unless ($multiple)
        <option value="">-- Chọn {{ strtolower($placeholder ?? $label) }} --</option>
    @endunless
    @foreach ($options as $key => $text)
        @php
            $shouldSelect = false;

            if ($multiple && is_array($value) && in_array($key, $value)) {
                $shouldSelect = true;
            } elseif (!$multiple && $value == $key) {
                $shouldSelect = true;
            } elseif (count($options) === 1 && empty($value) && Auth::check() && Auth::user()->is_admin == 0) {
                $shouldSelect = true;
            }
        @endphp

        <option value="{{ $key }}" {{ $shouldSelect ? 'selected' : '' }}>
            {{ $text }}
        </option>
    @endforeach


</select>

<small class="text-danger error-message {{ $name }}"></small>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('{{ $id }}');
            if (element) {
                new Choices(element, {
                    removeItemButton: {{ $multiple ? 'true' : 'false' }},
                    placeholder: true,
                    placeholderValue: 'Chọn {{ strtolower($placeholder ?? $label) }}',
                    shouldSort: false,
                    searchEnabled: true
                });
            }
        });
    </script>
@endpush
