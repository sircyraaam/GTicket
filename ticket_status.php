<?php include('./inc/header.inc.php'); ?>

<link rel="stylesheet" href="assets/css/ticket.css">

<section id="ticket_status_section">
    <h2 class="text-center mb-3">Service Ticket Copy</h2>

    <div class="text-center mb-3">
        <span id="ticketStatusText" class="bg-info">Loading...</span>
    </div>

    <div id="timestamps" class="text-center mb-4">
        <div>Created: <span id="ticketCreatedAt">-</span></div>
        <div>Last Updated: <span id="ticketUpdatedAt">-</span></div>
    </div>

    <div class="ticket-row">
        <div class="ticket-label">Ticket Control Number:</div>
        <div class="ticket-value" id="ticketCtrlNumberView">-</div>
    </div>
    <div class="ticket-row">
            <div class="ticket-label">Ticket Type:</div>
            <div class="ticket-value" id="ticketTypeView">-</div>
    </div>
    <div class="ticket-row">
        <div class="ticket-label">User Full Name:</div>
        <div class="ticket-value" id="userFullNameView">-</div>
    </div>
    <div class="ticket-row">
        <div class="ticket-label">SBU:</div>
        <div class="ticket-value" id="userSBUView">-</div>
    </div>
    <div class="ticket-row">
        <div class="ticket-label">Category:</div>
        <div class="ticket-value" id="userCategoryView">-</div>
    </div>
    <div class="ticket-row">
        <div class="ticket-label">User Email:</div>
        <div class="ticket-value" id="userEmailView">-</div>
    </div>
    <div class="ticket-row">
        <div class="ticket-label">User Contact No.:</div>
        <div class="ticket-value" id="userContactView">-</div>
    </div>
    <div class="ticket-row">
        <div class="ticket-label">Title / Subject:</div>
        <div class="ticket-value" id="ticketTitleView">-</div>
    </div>
    <div class="ticket-row">
        <div class="ticket-label">Description:</div>
        <div class="ticket-value" id="ticketDescriptionView">-</div>
    </div>
    <div class="ticket-row">
        <div class="ticket-label">Attachment:</div>
        <div class="ticket-value" id="ticketAttachmentView">-</div>
    </div>
    <div class="ticket-row">
        <div class="ticket-label">Remarks:</div>
        <div class="ticket-value" id="ticketRemarksView">-</div>
    </div>
    <div class="ticket-row">
        <div class="ticket-label">Technician:</div>
        <div class="ticket-value" id="ticketTechnicianView">No assigned Technician</div>
    </div>
    <div class="ticket-row">
        <div class="ticket-label">Resolution:</div>
        <div class="ticket-value" id="ticketResolutionView">No resolution yet</div>
    </div>
    <div class="print-btn text-center mt-4">
        <button id="ticketID" onclick="printTicket(this.value)" class="btn btn-outline-primary">Print Ticket</button>
        <a href="index.php" class="btn btn-outline-secondary">Back to Mainpage</a>
    </div>
</section>

<?php
include('./inc/footer.inc.php');
include('./inc/modal/modal_main.php');
?>

<script src="assets/js/ticket.js"></script>
<script>
    $(document).ready(function () {
        getTicketDetailsFromSession();

        $('a').on('click', function (e) {
        const href = $(this).attr('href');

        if (!href || href === '#' || href.startsWith('javascript:') || $(this).attr('target')) {
            return;
        }

        e.preventDefault();

            Swal.fire({
                title: 'Leave this page?',
                html: `
                        <p>This page is loaded based on <strong>temporary session data</strong>.
                        If you <strong>refresh</strong> or <strong>navigate away</strong>, the ticket information will be <span style="color: red;"><strong>lost</strong></span>.</p>
                        <p>Are you sure you want to leave?</p>
                    `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Leave Page',
                cancelButtonText: 'Stay Here',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });

        window.addEventListener('beforeunload', function (e) {
            e.preventDefault();
            e.returnValue = '';
        });
    });
</script>
