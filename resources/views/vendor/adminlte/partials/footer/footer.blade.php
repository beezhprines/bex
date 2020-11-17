<footer class="main-footer py-1 px-2">
    @yield('footer')

    <div class="row">
        <div class="col">
            <div class="text-muted" title="Версия"><i class="fa fa-code-branch"></i> {{ version() }}</div>
        </div>
    </div>
</footer>
