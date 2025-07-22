<div class="sidebar-header">
    <img src="{{ fileExists($setting->logo) }}" class="logo-icon" alt="logo icon" />
</div>
<!--navigation-->
@php
    $menus = json_decode(file_get_contents(resource_path('views/backend/data/menu.json')), true);
    $currentUrl = request()->path(); // VD: 'employees'
    $isAdmin = Auth::user()->is_admin;
@endphp

<ul class="metismenu" id="menu">
    @foreach ($menus as $item)
        @php
            $itemIsAdmin = $item['is_admin'] ?? null;

            // Ẩn menu nếu is_admin = 1 mà user không phải admin
            if ($itemIsAdmin === 1 && $isAdmin != 1) {
                continue;
            }

            $open = isMenuActive($item, $currentUrl) ? 'mm-active' : '';
        @endphp

        <li class="{{ $open }}">
            <a href="{{ $item['url'] }}" class="{{ isset($item['children']) ? 'has-arrow' : '' }}">
                <div class="parent-icon">
                    <i class="{{ $item['icon'] }}"></i>
                </div>
                <div class="menu-title">{{ $item['title'] }}</div>
            </a>

            @if (isset($item['children']))
                <ul>
                    @foreach ($item['children'] as $child)
                        @php
                            $childIsAdmin = $child['is_admin'] ?? null;
                            if ($childIsAdmin === 1 && $isAdmin != 1) {
                                continue;
                            }
                        @endphp

                        <li class="{{ isChildActive($child, $currentUrl) ? 'mm-active' : '' }}">
                            <a href="{{ $child['url'] }}">
                                <i class="far fa-dot-circle me-2"></i> {{ $child['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
</ul>

<!--end navigation-->
