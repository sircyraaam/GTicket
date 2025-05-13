<?php
include('./inc/header.inc.php');
?>

<link rel="stylesheet" href="assets/css/index.css">
<section id="home-section">
    <div class="container-fluid p-3">

        <div class="row">
            <div class="col text-center typing-header">
                <h2 class="fw-bold typing-text"><span class="typed"></span></h2>
            </div>
        </div>

        <div class="row text-center">
            <!-- Support Ticket -->
            <div class="col-md-4 mb-2">
                <a href="ticket_page.php?type=support" class="text-decoration-none text-dark">
                    <div class="card container-shadow py-3 h-100" id="support_ticket_id">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold fs-4 header-title">Support Ticket</h5>
                            <p class="card-text">For specific technical issue that was encountered by the user, prompting the need for a thorough investigation to ensure timely remediation</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Request Ticket -->
            <div class="col-md-4 mb-2">
                <a href="ticket_page.php?type=request" class="text-decoration-none text-dark">
                    <div class="card container-shadow py-3 h-100" id="request_ticket_id">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold fs-4 header-title">Request Ticket</h5>
                            <p class="card-text">For users requesting something new</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- FAQ -->
            <div class="col-md-4 mb-2">
                <a href="faq.html" class="text-decoration-none text-dark">
                    <div class="card container-shadow py-3 h-100" id="faq_ticket_id">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold fs-4 header-title">FAQ</h5>
                            <p class="card-text">Common Issues with defined solutions</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>




<?php
include('./inc/footer.inc.php');
include('./inc/modal/modal_main.php');
?>
<script src="assets/js/ticket.js"></script>
<script>
    $(document).ready(function(){
        var typed = new Typed('.typed', {
        strings: ["How can we help you?", "Do you need any help?"],
        loop: true,
        typeSpeed: 100,
        backSpeed: 50,
        backDelay: 2000
        });
    });
</script>