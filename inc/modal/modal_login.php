<!-- LOGIN CONFIRMATION MODAL FUNCTION START -->
<div class="modal fade pe-2 ps-2" id="loginconfirmationmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Confirmation</h1>
      </div>
      <div class="modal-body" id="loginmodalmessage">
      </div>
      <div class="modal-footer" id="loginmodalfooter">
        <!-- BUTTON CONTAINER -->
      </div>
    </div>
  </div>
</div>
<!-- LOGIN CONFIRMATION MODAL FUNCTION END -->

<!-- FORGOT PASSWORD MODAL START -->
<div class="modal fade" id="forgotPasswordModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="forgotPasswordModallabel" aria-hidden="true">
  <div class="modal-dialog ">
    <form id="forgotPasswordForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Forgot Password</h5>
        </div>
        <div class="modal-body">
          <div class="form-group mb-3">
            <label for="forgotEmail">Enter your registered email:</label>
            <input type="email" class="form-control" id="forgotEmail" name="forgotEmail" required placeholder="Your email">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Reset Password</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- FORGOT PASSWORD MODAL END -->