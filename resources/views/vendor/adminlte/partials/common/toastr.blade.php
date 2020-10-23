
    <script type="text/javascript">
        @if($errors->any())
            @foreach($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif
        @if(session('info'))
        toastr.info("{{ session('info') }}");
        @endif
        @if(session('warning'))
        toastr.warning("{{ session('warning') }}");
        @endif
        @if(session('success'))
        toastr.success("{{ session('success') }}");
        @endif
    </script>
