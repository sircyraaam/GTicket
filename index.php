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

        <!-- <div class="row text-center">
            <div class="col-lg-12">
                <div class="form-floating mb-5 mt-5">
                    <input type="text" class="form-control" id="search" oninput="maintenancesearch('consultants',this.value)" placeholder="name@example.com">
                    <label for="search">Please provide your ticket number to view details</label>
                </div>
            </div>
        </div> -->

        <div class="row justify-content-center text-center mt-5 mb-5">
            <div class="col-lg-6">
                <div class="input-group form-floating shadow-sm">
                    <span class="input-group-text bg-light fw-semibold">GTIX</span>
                    <input type="text" class="form-control" id="searchID" placeholder="Enter your ticket number"aria-label="Ticket Number" autocomplete="off">
                    <button class="btn btn-primary" type="button" onclick="searchTicketByNumber()" data-bs-toggle="tooltip" data-bs-placement="top" title="Search">
                        <i class="bx bx-search-alt me-1"></i>
                    </button>
                </div>
                <div class="form-text mt-2">Please provide your ticket number to view details</div>
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