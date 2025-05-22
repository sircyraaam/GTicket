<?php
include('./inc/header.inc.php');
?>

<link rel="stylesheet" href="assets/css/ticket.css">
<section id="submit_ticket_section">
    <div class="container-fluid p-3">
        <div class="card container-shadow py-3 mb-2">
            <div class="d-flex justify-content-between align-items-center px-3">
                <span class="fw-semibold fs-4 header-title" id="ticket_header">
                    Frequently Asked Questions
                </span>
                <a href="index.php" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1 shadow-sm rounded-3 px-3 py-2 text-decoration-none back-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Back">
                    <i class='bx bx-left-arrow-circle fs-5 icon'></i>
                </a>
            </div>
        </div>

        <div class="tab-pane fade show active" id="faq_details" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
            <div class="p-2 border rounded shadow-sm mt-3">
            </div>
        </div>
    </div>

</section>

<?php
include('./inc/footer.inc.php');
include('./inc/modal/modal_main.php');
?>
<!-- <script src="assets/js/faq.js"></script> -->
<script>
    $(document).ready(function(){
    });
</script>