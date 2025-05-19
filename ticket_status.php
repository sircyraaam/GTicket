<?php
include('./inc/header.inc.php');
?>

<link rel="stylesheet" href="assets/css/ticket.css">
<section id="ticket_status_section">
    <div class="container-fluid p-3">
        <div class="card container-shadow py-3 mb-2">
            <div class="d-flex justify-content-between align-items-center px-3">
                <span class="fw-semibold fs-4 header-title" id="ticket_status_header">
                    View Ticket Status
                </span>
                <a href="index.php" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1 shadow-sm rounded-3 px-3 py-2 text-decoration-none back-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Back">
                    <i class='bx bx-left-arrow-circle fs-5 icon'></i>
                </a>
            </div>
        </div>

        <div class="tab-pane fade show active" id="item_details" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
            <div class="p-2 border rounded shadow-sm mt-3">
                    <div class="row pt-4 g-3">
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="ticketTypeView" name="type" placeholder="name@example.com" autocomplete="off" disabled>
                                <label for="ticketTypeView">Ticket Type:</label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="ticketStatusView" name="status" placeholder="name@example.com" autocomplete="off" disabled>
                                <label for="ticketStatusView">Status:</label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <select class="form-select" id="userFullNameView" name="name" style="width:100%" aria-label="Floating label select example" disabled>
                                    <option value="0" selected>Select User</option>
                                </select>
                                <label for="userFullNameView"><span class="text-danger">*</span>User Full Name:</label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <select class="form-select" id="userSBUView" name="sbu" style="width:100%" aria-label="Floating label select example" disabled>
                                    <option value="0" selected>Select SBU</option>
                                </select>
                                <label for="userSBUView"><span class="text-danger">*</span>SBU:</label>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="userEmailView" name="email" placeholder="name@example.com" autocomplete="off" disabled>
                                <label for="userEmailView"><span class="text-danger">*</span>User Email:</label>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="userContactView" name="contact" placeholder="name@example.com" autocomplete="off" maxlength="11" pattern="\d{11}" oninput="this.setCustomValidity(''); this.value = this.value.replace(/\D/g, '')" oninvalid="this.setCustomValidity('Contact number must be exactly 11 digits.')" disabled>
                                <label for="userContactView"><span class="text-danger">*</span>User Contact No.:</label>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating">
                                <select class="form-select" id="userCategoryView" name="category" style="width:100%" aria-label="Floating label select example" disabled>
                                    <option value="0" selected>Select Category</option>
                                </select>
                                <label for="userCategoryView"><span class="text-danger"></span>Category:</label>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="ticketTitleView" name="title" placeholder="name@example.com" autocomplete="off" disabled>
                                <label for="ticketTitleView"><span class="text-danger">*</span>Title / Subject:</label>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="ticketDescriptionView" name="description" placeholder="name@example.com" autocomplete="off" disabled>
                                <label for="ticketDescriptionView"><span class="text-danger">*</span>Description:</label>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="ticketAttachmentView" name="attachment" placeholder="name@example.com" autocomplete="off" disabled>
                                <label for="ticketAttachmentView"><span class="text-danger">*</span>Attachment:</label>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="ticketRemarksView" name="remarks" placeholder="name@example.com" autocomplete="off" disabled>
                                <label for="ticketRemarksView">Remarks:</label>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-3">
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