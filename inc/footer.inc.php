
    
        <footer class="footer text-center p-1">
            <div class="text-secondary">
                Copyright &copy; Glacier <?php echo date('Y'); ?>
                &nbsp|&nbsp All rights reserved.
            </div>
        </footer>
    </div>

    
    <!-- MODAL INCLUDES -->
    <?php include('./inc/modal/modalloader.php')?>
    <!-- JQUERY CDN -->
    <script src="assets/node_modules/jquery/dist/jquery.min.js" ></script>

    <!-- Bootstrap JS Scripts -->
    <script src="assets/node_modules/bootstrap-5.1.3/js/bootstrap.bundle.min.js" ></script>

    <!-- Toast PLugin -->
    <script src="assets/node_modules/toast/js/izitoast.min.js" type="text/javascript"></script>

    <!-- Tabulator JS Scripts -->
    <script src="assets/node_modules/tabulator-tables/dist/js/tabulator.min.js"></script>

    <!-- SELECT2 JS SCRIPTS -->
    <script src="assets/node_modules/select2-develop/dist/js/select2.min.js"></script>

    <!-- TOAST JS SCRIPTS -->
    <script src="assets/node_modules/toast/js/izitoast.min.js"></script>

    <!-- OWL JS SCRIPTS -->
    <script src="assets/node_modules/OwlCarousel/dist/owl.carousel.min.js"></script>

    <!-- SWAL JS SCRIPTS -->
    <script src="node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    
    <!-- Customized JS File -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/typed.js"></script>

</body>
</html>
<script>
    $(function(){
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>