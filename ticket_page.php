<?php
include('./inc/header.inc.php');

$type = $_GET['type'] ?? 'support';
$ticketTitle = ($type === 'request') ? 'Submit Request Ticket' : 'Submit Support Ticket';
?>

<link rel="stylesheet" href="assets/css/ticket.css">
<link rel="stylesheet" href="assets/css/style.css">
    <section id="submit_ticket_section">
        <div class="container-fluid p-3">
            <div class="card container-shadow py-3 mb-2">
                <div class="d-flex justify-content-between align-items-center px-3">
                    <span class="fw-semibold fs-4 header-title" id="ticket_header">
                        <?= htmlspecialchars($ticketTitle) ?>
                    </span>
                    <a href="homepage.php" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1 shadow-sm rounded-3 px-3 py-2 text-decoration-none back-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Back">
                        <i class='bx bx-left-arrow-circle fs-5 icon'></i>
                    </a>
                </div>
            </div>

            <div class="tab-pane fade show active" id="item_details" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                <div class="p-2 border rounded shadow-sm mt-3">
                    <form id="submit_ticket" onsubmit="SubmitRecord(event,this,'<?= htmlspecialchars($type) ?>')">
                        <div class="row pt-4 g-3">
                            <input type="hidden" name="ticket_type" value="<?= htmlspecialchars($type) ?>">
                            <div class="col-lg-6">
                                <div class="form-floating">
                                    <select class="form-select" id="userFullName" name="name" style="width:100%" aria-label="Floating label select example">
                                        <option value="0" selected>Select User</option>
                                    </select>
                                    <label for="userFullName"><span class="text-danger">*</span>User Full Name:</label>
                                </div>
                            </div>
                            <div class="col-lg-6 d-flex">
                                <div class="form-floating flex-grow-1 me-2">
                                    <select class="form-select" id="userSBU" name="sbu" style="width:100%" aria-label="Floating label select example">
                                        <option value="0" selected>Select SBU</option>
                                    </select>
                                    <label for="userSBU"><span class="text-danger">*</span>SBU:</label>
                                </div>
                                <button id="syncBtn" class="btn btn-primary" type="button" onclick="syncCategoriestoLocalDB()" data-bs-toggle="tooltip" data-bs-placement="top" title="Sync if you can't find the Category"><i class='bx bx-sync'></i></button>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="userEmail" name="email" placeholder="name@example.com" autocomplete="off">
                                    <label for="userEmail"><span class="text-danger">*</span>User Email:</label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="userContact" name="contact" placeholder="name@example.com" autocomplete="off" maxlength="11" pattern="\d{11}" oninput="this.setCustomValidity(''); this.value = this.value.replace(/\D/g, '')" oninvalid="this.setCustomValidity('Contact number must be exactly 11 digits.')">
                                    <label for="userContact"><span class="text-danger">*</span>User Contact No.:</label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating">
                                    <select class="form-select" id="userCategory" name="category" style="width:100%" aria-label="Floating label select example">
                                        <option value="0" selected>Select Category</option>
                                    </select>
                                    <label for="userCategory"><span class="text-danger"></span>Category:</label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="ticketTitle" name="title" placeholder="name@example.com" autocomplete="off">
                                    <label for="ticketTitle"><span class="text-danger">*</span>Title / Subject:</label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="ticketDescription" name="description" placeholder="name@example.com" autocomplete="off">
                                    <label for="ticketDescription"><span class="text-danger">*</span>Description:</label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-floating">
                                    <input type="file" class="form-control" id="ticketAttachment" name="attachment" placeholder="name@example.com" autocomplete="off">
                                    <label for="ticketAttachment"><span class="text-danger">*</span>Attachment:</label>
                                </div>
                            </div>
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
    document.addEventListener("DOMContentLoaded", async () => {
        Swal.fire({
            title: 'Loading form...',
            text: 'Please wait while we load the ticket fields.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            await Promise.all([
                loadAllUsers(),
                loadAllWarehouses(),
                loadAllCategory()
            ]);
        } catch (err) {
            console.error("Error loading data:", err);
            Swal.fire({
                icon: 'error',
                title: 'Load Failed',
                text: 'Some form data could not be loaded. Please refresh the page.'
            });
        } finally {
            Swal.close();
            document.getElementById("submit_ticket_section").style.display = "block";
        }
    });
</script>
