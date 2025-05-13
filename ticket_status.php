<?php
include('./inc/header.inc.php');
?>

<link rel="stylesheet" href="assets/css/ticket.css">
<section id="ticket_status_section">
    <div class="container-fluid p-3">
        <div class="card container-shadow py-3 mb-2">
            <div class="flex position-relative ps-3">
                <span class="fw-semibold fs-4 align-middle justify-content-start header-title" id="ticket_support_title">View Ticket Status</span>
            </div>
        </div>

        <div class="tab-pane fade show active" id="item_details" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
            <div class="p-2 border rounded shadow-sm mt-3">
                <form id="view_ticket_status_form" onsubmit="SubmitTicket(event,this)">
                    <div class="row pt-4 g-3">
                        <div class="col-lg-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="ticketRemarks" name="remarks" placeholder="name@example.com" autocomplete="off">
                                <label for="ticketRemarks">Remarks:</label>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-3">
                    <div class="d-block text-center mt-4 mb-2">
                        <button type="button" class="btn btn-outline-danger mx-1 btn-sm container-shadow px-3" data-bs-dismiss="modal" onclick="ticketResetField()">Reset</button>
                        <button type="submit" class="btn btn-outline-primary mx-1 btn-sm container-shadow px-3">Submit</button>
                    </div>
                </form>
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
    });
</script>