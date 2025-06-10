    <!-- CORE PLUGINS-->
    <script src="{{ asset('/assets/app/vendors/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('/assets/app/vendors/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('/assets/app/js/plugin/jquery.notification.min.js') }}"></script>
    <script src="{{ asset('/assets/app/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/assets/app/vendors/toastr/toastr.min.js') }}"></script>
    <!-- PAGE LEVEL PLUGINS-->
    @yield('plugin-scripts')
    <!-- CORE SCRIPTS-->
    <!-- CORE SCRIPTS-->
    <script src="{{ asset('/assets/app/js/app.js') }}"></script>
    <!-- PAGE LEVEL SCRIPTS-->
    @yield('page-scripts')
    </body>
</html>
