@if (session()->has('success'))
    <script>
        aicrm.success("{{ session('success') }}")
    </script>
@endif
