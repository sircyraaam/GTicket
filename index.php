<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Glacier - Ticket Management System</title>

    <!-- JQUERY CDN -->
    <script src="assets/node_modules/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/node_modules/bootstrap-5.1.3/css/bootstrap.css">

    <!-- Boxicons -->
    <link rel="stylesheet" href="assets/node_modules/boxicons-2.1.4/css/boxicons.min.css">

    <!-- Toast Plugin -->
    <link rel="stylesheet" href="assets/node_modules/toast/css/izitoast.min.css"/>

    <!-- Tabulator -->
    <link rel="stylesheet" href="assets/node_modules/tabulator-tables/dist/css/tabulator_bootstrap5.min.css">

    <!-- Select2 -->
    <link rel="stylesheet" href="assets/node_modules/select2-develop/dist/css/select2.min.css">

    <!-- Icon -->
    <link rel="icon" href="assets/img/GILILOGO3.png" type="image/gif" sizes="16x16">

    <!-- Owl Carousel -->
    <link rel="stylesheet" href="assets/node_modules/OwlCarousel/dist/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/node_modules/OwlCarousel/dist/assets/owl.theme.default.min.css">

    <!-- SweetAlert -->
    <link rel="stylesheet" href="node_modules/sweetalert2/dist/sweetalert2.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/register.css">
    <link rel="stylesheet" href="assets/css/modal.css">
</head>
<body>

<section id="registration_section">
    <div class="section">
        <div class="container">
            <div class="row full-height justify-content-center">
                <div class="col-12 text-center align-self-center py-5">
                    <div class="section pb-5 pt-5 pt-sm-2 text-center">
                        <h6 class="mb-2 pb-2"><span>Log In</span><span>Sign Up</span></h6>
                        <input class="checkbox" type="checkbox" id="reg-log" name="reg-log">
                        <label for="reg-log" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Click to switch"></label>

                        <div class="card-3d-wrap mx-auto">
                            <div class="card-3d-wrapper">

                                <!-- Log In -->
                                <div class="card-front">
                                    <div class="center-wrap">
                                        <div class="section text-center">
                                            <h4 class="mb-4 pb-3">Log In</h4>
                                            <div class="alert alert-success" role="alert" id="alert">
                                                A simple secondary alert—check it out!
                                            </div>
                                            <form id="loginForm" onsubmit="SubmitLogIn(event,this)">
                                                <div class="form-group">
                                                    <input type="text" name="loginusername" class="form-style" placeholder="Your Email" id="logusername" autocomplete="off">
                                                    <i class="input-icon bx bxs-user-circle"></i>
                                                </div>	
                                                <div class="form-group mt-2 position-relative">
                                                    <input type="password" name="loginpassword" class="form-style" placeholder="Your Password" id="logpassword" autocomplete="off">
                                                    <i class="input-icon bx bx-key"></i>
                                                    <i class="toggle-password bx bx-show" id="togglePassword" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Show Password"></i>
                                                </div>
                                                <div class="d-block text-center mt-3 mb-1">
                                                    <button type="submit" class="btn btn-outline-primary mx-1 btn-sm container-shadow px-3">Submit</button>
                                                </div>
                                            </form>
                                            <p class="mb-0 mt-4 text-center">
                                                <a href="#" class="link" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot your password?</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sign Up -->
                                <div class="card-back">
                                    <div class="center-wrap">
                                        <div class="section text-center">
                                            <h4 class="mb-1 pb-1">Sign Up</h4>

                                            <div class="mb-2">
                                                <button id="syncButton" class="btn btn-outline-primary mx-1 btn-sm container-shadow px-3" onclick="syncWarehousestoLocalDB()" data-bs-toggle="tooltip" data-bs-placement="left" title="Sync if you can't find the User or SBU">
                                                    <i class="bx bx-sync"></i> Sync
                                                </button>
                                            </div>

                                            <form id="signupForm" onsubmit="SubmitSignUp(event,this)">
                                                <div class="form-group">
                                                    <select class="form-style" id="logname" name="logsdpname" required onchange="fetchUserDetails(this.value)">
                                                        <option value="0" disabled selected>Select SDP User</option>
                                                    </select>
                                                    <i class="input-icon bx bxs-user-circle"></i>
                                                </div>

                                                <div class="form-group mt-2">
                                                    <input type="email" name="logemail" class="form-style" placeholder="Your Email" id="signupemail" autocomplete="off">
                                                    <i class="input-icon bx bxs-envelope"></i>
                                                </div>

                                                <div class="form-group mt-2">
                                                    <input type="text" name="lognumber" class="form-style" placeholder="Your Contact Number" id="contactnumber" autocomplete="off" maxlength="11" pattern="\d{11}" oninput="this.setCustomValidity(''); this.value = this.value.replace(/\D/g, '')" oninvalid="this.setCustomValidity('Contact number must be exactly 11 digits.')">
                                                    <i class="input-icon bx bxs-envelope"></i>
                                                </div>

                                                <div class="form-group mt-2">
                                                    <select class="form-style" id="logSBU" name="logsdpSBU">
                                                        <option value="" disabled selected>Select SBU</option>
                                                    </select>
                                                    <i class="input-icon bx bxs-business"></i>
                                                </div>
                                                <div class="d-block text-center mt-2 mb-1">
                                                    <button type="submit" class="btn btn-outline-primary mx-1 btn-sm container-shadow px-3">Submit</button>
                                                </div>
                                            </form>
                                        </div> <!-- section text-center -->
                                    </div>
                                </div>

                            </div> <!-- card-3d-wrapper -->
                        </div> <!-- card-3d-wrap -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('./inc/modal/modal_login.php'); ?>

<!-- Bootstrap Bundle -->
<script src="assets/node_modules/bootstrap-5.1.3/js/bootstrap.bundle.min.js"></script>

<!-- Select2 -->
<script src="assets/node_modules/select2-develop/dist/js/select2.min.js"></script>

<!-- SweetAlert2 -->
<script src="node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

<!-- Toast -->
<script src="assets/node_modules/toast/js/izitoast.min.js"></script>

<!-- App JS -->
<script src="assets/js/registration.js"></script>

<script>
  $(document).ready(function () {
    var toggleIcon = $('#togglePassword');
    var passwordInput = $('#logpassword');
    var tooltip = new bootstrap.Tooltip(toggleIcon[0]);

    toggleIcon.on('click', function () {
      const isPassword = passwordInput.attr('type') === 'password';
      passwordInput.attr('type', isPassword ? 'text' : 'password');

      toggleIcon.toggleClass('bx-show bx-hide');

      const newTitle = isPassword ? 'Hide Password' : 'Show Password';
      toggleIcon.attr('title', newTitle).tooltip('dispose').tooltip(); // reinit tooltip
    });
  });

    loadAllWarehousesforSignUP();
    loadAllUsersforSignUP();
    // protectToggleWithSwal();
</script>

</body>
</html>
