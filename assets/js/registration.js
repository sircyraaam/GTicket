$(document).ready( () => {
    $('#logSBU, #logname').select2();
})

document.addEventListener("DOMContentLoaded", function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

$(document).keydown(function (event) {
    $('[data-toggle="tooltip"]').tooltip();
    if (event.keyCode == 123) {
        return false;
    }
    else if (event.ctrlKey && event.shiftKey && event.keyCode == 73) {
        return false;
    }
});

$('div').bind("contextmenu", function (e) {
    e.preventDefault();
});

function toasts_error(message) {
    iziToast.error({
        title: 'GTicket Alert: ',
        message: message,
        position: 'topRight',
        timeout: '2500'
    });
}

function protectToggleWithSwal(options = {}) {
  const {
    checkboxId = 'reg-log',
    labelSelector = 'label[for="reg-log"]',
    validateUrl = 'app/Controller/ajax_ticket.php',
    swalTitle = 'Admin Access Required',
    swalInputLabel = 'Enter admin password to switch:',
    swalErrorText = 'Access denied. Incorrect password.'
  } = options;

  const checkbox = document.getElementById(checkboxId);
  const label = document.querySelector(labelSelector);

  if (!checkbox || !label) return;

  label.addEventListener('click', function(e) {
    e.preventDefault();

    if (!checkbox.checked) {
      Swal.fire({
        title: swalTitle,
        input: 'password',
        inputLabel: swalInputLabel,
        inputPlaceholder: 'Password',
        inputAttributes: {
          autocapitalize: 'off',
          autocorrect: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Submit',
        preConfirm: (password) => {
          if (!password) {
            Swal.showValidationMessage('Password cannot be empty');
            return false;
          }

          return $.ajax({
            type: "POST",
            url: validateUrl,
            data: {
              function: 'validateAdminPassword',
              password: password
            },
            dataType: 'json'
          }).then(response => {
            if (!response.valid) {
              Swal.showValidationMessage(swalErrorText);
            }
            return response;
          }).catch(() => {
            Swal.showValidationMessage('Server error. Please try again.');
          });
        }
      }).then((result) => {
        if (result.isConfirmed && result.value?.valid) {
            checkbox.checked = true;
            signUpResetField();
        }
      });
    } else {
        checkbox.checked = false;
        signUpResetField();
    }
  });
}

function loadAllWarehousesforSignUP() {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: "POST",
            url: "app/Controller/ajax_ticket.php",
            data: {
                function: 'loadAllWarehouses'
            },
            dataType: 'json',
            success: function(result) {
                const { option } = result;
                $('#logSBU').html(`<option value="0">Select SBU</option>${option}`);
                resolve(result);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                reject(error); 
            }
        });
    });
}


function loadAllUsersforSignUP() {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: "POST",
            url: "app/Controller/ajax_ticket.php",
            data: {
                function: 'loadAllUsers'
            },
            dataType: 'json',
            success: function(result) {
                const { option } = result;
                $('#logname').html(`<option value="0">Select SDP User</option>${option}`);
                resolve(result);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                reject(error);
            }
        });
    });
}

function fetchUserDetails(id){
     $.ajax({
            type: "POST",
            url: "app/Controller/ajax_ticket.php",
            data: {
                function: 'fetchUserDetails',
                id:id
            },
            dataType: 'json',
            success: function(result) {
                $('#signupemail').val(result.sdp_email);
                $('#logSBU').val(result.sdp_site).trigger('change');
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
}

function syncWarehousestoLocalDB(){

    $.ajax({
        type: "POST",
        url: "app/Controller/ajax_ticket.php",
        data: {
            function: 'syncWarehousestoLocalDB'
        },
        dataType: 'json',
        beforeSend: function() {
            showLoaderAlert();
        },
        success: function(result){
            const siteCount = result.site_sync?.count || 0;
            const userCount = result.user_sync?.count || 0;
            const siteStatus = result.site_sync?.result;
            const userStatus = result.user_sync?.result;

            const message = `<div class="text-align-center">
                                <strong>Site Sync:</strong> ${siteCount} site(s) updated <br>
                                <strong>User Sync:</strong> ${userCount} user(s) updated
                            </div>`;

            closeLoaderAlert();
            if (siteCount > 0 || userCount > 0) {
                showSyncSuccessAlert(message);
            } else if (siteStatus === 'NoUpdate' && userStatus === 'NoUpdate') {
                showSyncNoUpdateAlert(message);
            } else {
                const msg = (result.site_sync?.message || '') + ' ' + (result.user_sync?.message || '');
                showErrorSyncAlert('Sync Failed', msg.trim());
            }
        },
        error: function(xhr, status, error) {
            closeLoaderAlert();
            console.error('Sync failed:', status, error);
            showErrorSyncAlert('Sync Failed', 'Please try again to sync.');
        }
    });
}

function showSuccessAddAlert(message, email = null, plainPassword = null) {
    let timerInterval;
    let extraInfo = '';
    if (email && plainPassword) {
        extraInfo = `
            <br><br>
            <strong>Email:</strong> ${email}<br>
            <strong>Password:</strong> ${plainPassword}<br>
        `;
    }
    
    Swal.fire({
        title: 'Success!',
        html: `${message}${extraInfo}<br><br>Closing in <b></b> seconds...`,
        icon: 'success',
        showConfirmButton: true,
        confirmButtonText: 'OK',
        allowOutsideClick: false,
        timer: 5000,
        timerProgressBar: true,
        didOpen: () => {
            const timer = Swal.getPopup().querySelector("b");
            timerInterval = setInterval(() => {
                timer.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
            }, 100);
        },
        willClose: () => {
            clearInterval(timerInterval);
        }
    });
}


function showSyncSuccessAlert(message) {
    let timerInterval;
    Swal.fire({
        title: 'Sync Successful!',
        html: `${message}<br><br>Closing in <b></b> seconds...`,
        icon: 'success',
        showConfirmButton: true,
        confirmButtonText: 'OK',
        allowOutsideClick: false,
        timer: 5000,
        timerProgressBar: true,
        didOpen: () => {
            const timer = Swal.getPopup().querySelector("b");
            timerInterval = setInterval(() => {
                timer.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
            }, 100);
        },
        willClose: () => {
            clearInterval(timerInterval);
        }
    }).then(() => {
        loadAllWarehousesforSignUP();
        loadAllUsersforSignUP();
    });
}

function showSyncNoUpdateAlert(message) {
    let timerInterval;
    Swal.fire({
        title: 'No Data Updated',
        html: `${message}<br><br>Closing in <b></b> seconds...`,
        icon: 'info',
        showConfirmButton: true,
        confirmButtonText: 'OK',
        allowOutsideClick: false,
        timer: 5000,
        timerProgressBar: true,
        didOpen: () => {
            const timer = Swal.getPopup().querySelector("b");
            timerInterval = setInterval(() => {
                timer.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
            }, 100);
        },
        willClose: () => {
            clearInterval(timerInterval);
        }
    }).then(() => {
        loadAllWarehousesforSignUP();
        loadAllUsersforSignUP();
    });
}

function showLoaderAlert(){
    $('#activityconfirmationmodal').modal('hide');
    Swal.fire({
        title: 'Processing...',
        text: 'Please wait while we submit your data.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

function closeLoaderAlert(){
    Swal.close();
}

function showErrorSyncAlert(title = 'Error', message = 'An unexpected error occurred.') {
    Swal.fire({
        title: title,
        text: message,
        icon: 'error',
        confirmButtonText: 'OK',
        allowOutsideClick: false
    });
}

function SubmitSignUp(event, id) {
    if (event) event.preventDefault();

    let formElement;
    if (typeof id === 'string') {
        formElement = document.querySelector(id);
    } else if (id instanceof Element) {
        formElement = id;
    } else {
        throw new Error('Invalid form identifier passed');
    }

    const formData = new FormData(formElement);
    formData.set('function', 'signUp');

    const signUpForm = {};
    formData.forEach((value, key) => {
        signUpForm[key] = value;
    });

    const requiredFields = [
        { key: 'logsdpname', selector: '#logname', message: 'SDP User is required.', checkValue: "0", isSelect2: true },
        { key: 'logemail', selector: '#signupemail', message: 'Email is required.' },
        { key: 'lognumber', selector: '#contactnumber', message: 'Contact Number is required.' },
        { key: 'logsdpSBU', selector: '#logSBU', message: 'SBU is required.', checkValue: "0", isSelect2: true }
    ];

    for (let field of requiredFields) {
        const value = signUpForm[field.key];
        const isEmpty = field.hasOwnProperty('checkValue') ? value === field.checkValue : !value;
        const element = $(field.selector);

        if (isEmpty) {
            if (field.isSelect2) {
                element.next().find('.select2-selection').addClass('is-invalid').focus();
            } else {
                element.addClass('is-invalid').focus();
            }
            toasts_error(field.message);
            return false;
        } else {
            if (field.isSelect2) {
                element.next().find('.select2-selection').removeClass('is-invalid');
            } else {
                element.removeClass('is-invalid');
            }
        }
    }

    window.signUpFormData = formData;

    signUpModalFilter(
        "Do you want to create a new account?",
        `<button type="button" class="btn btn-danger btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
         <button type="button" class="btn btn-primary btn-sm px-4" onclick="submitActivity('createUser')">Submit</button>`
    );
}

function signUpModalFilter(message,footer){
    $('#loginconfirmationmodal').modal('show');
    $('#loginmodalmessage').text(message);
    $('#loginmodalfooter').html(footer);
}


function submitActivity(type) {
    switch(type) {
        case 'createUser':
            $('#loginconfirmationmodal').modal('hide');
            if (!window.signUpFormData) {
                toasts_error("Form data not found, please submit the form first.");
                return;
            }
            showLoaderAlert(window.signUpFormData);

            $.ajax({
                type: "POST",
                url: "app/Controller/ajax_ticket.php",
                data: window.signUpFormData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(result) {
                    closeLoaderAlert();
                    let parsedResult;
                    try {
                        parsedResult = JSON.parse(result.result);
                    } catch (e) {
                        showErrorAlert('Failed to parse server response');
                        return;
                    }

                    const resultCode = parsedResult.resultCode;
                    if (resultCode == 1) {
                        signUpResetField();
                        showSuccessAddAlert(parsedResult.resultMessage, result.email, result.plainPassword);
                    } else if(resultCode >= 2){
                        showErrorAlert(parsedResult.resultMessage);
                    } else {
                        showErrorAlert('Failed to Add New Record');
                    }
                },
                error: function(xhr, status, error) {
                    closeLoaderAlert();
                    console.error("Error during AJAX request:", error);
                    showErrorAlert('An unexpected error occurred. Please try again.');
                }
            });
            break;
    }
}

function signUpResetField(){
    $('#signupForm')[0].reset();
    $('#logname').val('0').trigger('change');
    $('#logSBU').val('0').trigger('change');
}

function showErrorAlert(message) {
    Swal.fire({
        title: 'Error',
        html: `${message}`,
        icon: 'error',
        confirmButtonText: 'OK',
        allowOutsideClick: false
    }).then(() => {
        $('#loginconfirmationmodal').modal('show');
    });
}


// LOG IN

$('#logusername, #logpassword').on('focus', function () {
    $(this).removeClass('is-invalid is-valid');
    $('#alert').slideUp();
}); 


function SubmitLogIn(event, id) {
    if (event) event.preventDefault();

    let formElement;
    if (typeof id === 'string') {
        formElement = document.querySelector(id);
    } else if (id instanceof Element) {
        formElement = id;
    } else {
        throw new Error('Invalid form identifier passed');
    }

    const formData = new FormData(formElement);
    formData.set('function', 'login');

  
    const requiredFields = [
        { key: 'loginusername', selector: '#logusername', message: 'Email is required.' },
        { key: 'loginpassword', selector: '#logpassword', message: 'Password is required.' }
    ];

    for (let field of requiredFields) {
        const value = formData.get(field.key);
        const isEmpty = !value;
        const element = $(field.selector);

        if (isEmpty) {
            if (field.isSelect2) {
                element.next().find('.select2-selection').addClass('is-invalid').focus();
            } else {
                element.addClass('is-invalid').focus();
            }
            toasts_error(field.message);
            return false; 
        } else {
            if (field.isSelect2) {
                element.next().find('.select2-selection').removeClass('is-invalid');
            } else {
                element.removeClass('is-invalid');
            }
        }
    }

    $.ajax({
        type: "POST",
        url: "app/Controller/ajax_ticket.php",
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'text',
        success: function(result){
            let data = JSON.parse(result);
            console.log(data);
            if(data.isUser == false){
                if(data.isLoggedActive == true){
                    login_ModalFilter(
                        `This will logout your account from other device. Do you still want to proceed?`,
                        `<button type="button" class="btn btn-danger btn-sm px-4" data-bs-dismiss="modal">Close</button>
                         <button type="button" class="btn btn-primary btn-sm px-4" onclick="login_ModalSubmit('logout_to_other_device',${data.user_id},'${data.token}', ${data.loginid})">Submit</button>`
                    );
                } else {
                    $('#logusername').addClass('is-invalid').focus();
                    $('#logpassword').addClass('is-invalid');
                    alert_error('div[id=alert]', data.validateLogin);
                }
            } else {
                location.href = '/GTicket/homepage.php';
            }
        },
        error: function(xhr, status, error){
            toasts_error('An error occurred during login. Please try again.');
            console.error('Login AJAX error:', error);
        }
    });
}

// ALERT START 
function alert_success(id,message){
    $(id).slideDown();
    $(id).text(message);
    $(id).removeClass("alert-danger");
    $(id).addClass("alert-success");
}


function alert_error(id,message){
    $(id).addClass("alert-danger");
    $(id).removeClass("alert-success");
    if($(id).css('display') == 'none'){
        $(id).slideDown();
        $(id).text(message);
    }
}
// ALERT END 

function login_ModalSubmit(functionName,user_id,token,loginid){

    $.ajax({
        type: "POST",
        url: "app/Controller/ajax_login.php",
        data: {
            function : functionName,
            user_id : user_id,
            token : token,
            loginid : loginid
        },
        datatype: 'html',
        success: function(result){ 
            // location.href = '/RMS/dashboard.php';
            console.log(result);
        }
    });

}