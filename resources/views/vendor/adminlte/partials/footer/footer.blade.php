<footer class="main-footer py-1 px-2">
    @yield('footer')

    <div class="row">
        <div class="col">
            <div class="text-muted" title="Версия"><i class="fa fa-code-branch"></i> {{ version() }}</div>
        </div>
        <div class="col text-right">
            <a class="text-muted" title="Помощь" href="tel:+77763442424"><i class="fa fa-life-ring"></i> Помощь</a>
        </div>
    </div>
</footer>
