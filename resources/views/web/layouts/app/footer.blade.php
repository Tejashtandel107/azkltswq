            <!-- END PAGE CONTENT-->
            <footer class="noprint page-footer">
                <div class="noprint to-top"><i class="fa fa-chevron-up"></i></div>
            </footer>
        </div>
    </div>
    <!-- END SEARCH PANEL-->
    <!-- BEGIN PAGA BACKDROPS-->
    <div class="sidenav-backdrop backdrop"></div>
    <div class="preloader-backdrop bg-white">
        <div class="page-preloader">Loading</div>
    </div>
    <!-- END PAGA BACKDROPS-->
    <!--BEGIN MODEL WRAPPER-->
    <div id="modalwrp"></div>
    <!--END MODEL WRAPPER-->
    <!-- CORE PLUGINS-->
    <script src="{{ asset('/assets/app/vendors/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('/assets/app/vendors/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('/assets/app/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/assets/app/vendors/metisMenu/dist/metisMenu.min.js') }}"></script>
    <script src="{{ asset('/assets/app/vendors/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('/assets/app/vendors/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('/assets/app/js/plugin/jquery.notification.js') }}"></script>
    <script src="{{ asset('/assets/app/js/plugin/jquery.form.min.js') }}"></script>
    <!-- PAGE LEVEL PLUGINS-->
    @yield('plugin-scripts')
    <!-- CORE SCRIPTS-->
    <script src="{{ asset('/assets/web/js/app.js') }}"></script>
    <!-- PAGE LEVEL SCRIPTS-->
    @yield('page-scripts')
    </body>
</html>
