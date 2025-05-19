$(document).ready( () => {
    $('#veteranActivityOnProcessTableModal').on('shown.bs.modal', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    $('#userSBU, #userFullName, #userCategory').select2();

})

// ACTIVITY MODAL FILTER START //
function activityModalFilter(message,footer){
    $('#activityconfirmationmodal').modal('show');
    $('#activitymodalmessage').text(message);
    $('#activitymodalfooter').html(footer);
}
// ACTIVITY MODAL FILTER END //


function SubmitRecord(event, id, type) {
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

    formData.set('ticket_type', type);
    formData.set('function', 'addRecord');
    addDropdownTextToFormData(formData, ['userFullName', 'userSBU', 'userCategory']);

    const addRecordForm = {};
    formData.forEach((value, key) => {
        addRecordForm[key] = value;
        console.log(`Key: ${key}, Value: ${value}`);
    });


    console.log(formData);

    const requiredFields = [
        { key: 'name', selector: '#userFullName', message: 'User Full Name is required.', checkValue: "0", isSelect2: true },
        { key: 'sbu', selector: '#userSBU', message: 'SBU is required.', checkValue: "0", isSelect2: true },
        { key: 'email', selector: '#userEmail', message: 'Email is required.' },
        { key: 'contact', selector: '#userContact', message: 'Contact number is required.' },
        { key: 'title', selector: '#ticketTitle', message: 'Title / Subject is required.' },
        { key: 'description', selector: '#ticketDescription', message: 'Description is required.' },
    ];

    for (let field of requiredFields) {
        const value = addRecordForm[field.key];
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

    window.ticketFormData = formData;

    activityModalFilter(
        "Do you want to add a new activity record for this Veteran?",
        `<button type="button" class="btn btn-danger btn-sm px-4" data-bs-dismiss="modal">Close</button>
         <button type="button" class="btn btn-primary btn-sm px-4" onclick="activityModalSubmit('addTicket')">Submit</button>`
    );
}

function addDropdownTextToFormData(formData, fields) {
    fields.forEach(field => {
        const text = $(`#${field} option:selected`).text().trim();
        formData.set(`${field}_text`, text);
    });
}

function activityModalSubmit(type) {
    switch(type) {
        case 'addTicket':
            if (!window.ticketFormData) {
                toasts_error("Form data not found, please submit the form first.");
                return;
            }

            showLoaderAlert();

            $.ajax({
                type: "POST",
                url: "app/Controller/ajax_ticket.php",
                data: window.ticketFormData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(result) {
                    closeLoaderAlert();
                    const thirdResult = result.result?.result;
                    if (thirdResult == '1') {
                        showSuccessAlert(result.local_ticket_id);
                    } else {
                        closeLoaderAlert();
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

function syncWarehousestoLocalDB(){
    $.ajax({
        type: "POST",
        url: "app/Controller/ajax_ticket.php",
        data: {
            function:'syncWarehousestoLocalDB'
        },
        dataType: 'json',
        success: function(result){
            console.log(result);
            if (result.result === 'Success') {
                showSyncSuccessAlert(result.count);
            } else if(result.result === 'NoUpdate'){
                showSyncNoUpdateAlert(result.count);
            } else {
                showErrorSyncAlert('Sync Failed', result.message + '. Could not complete the sync.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Sync failed:', status, error);
            showErrorSyncAlert('Sync Failed', 'Please try again to sync.');
        }
    });
}

function loadAllWarehouses(){
    $.ajax({
        type: "POST",
        url: "app/Controller/ajax_ticket.php",
        data: {
            function:'loadAllWarehouses'
        },
        dataType: 'json',
        success: function(result){
            console.log(result);
            const {option} = result;
            $('#userSBU').html(`<option value="0">Select SBU</option>${option}`);
        }
    });
}

function loadAllUsers(){
    $.ajax({
        type: "POST",
        url: "app/Controller/ajax_ticket.php",
        data: {
            function:'loadAllUsers'
        },
        dataType: 'json',
        success: function(result){
            if (result.success && Array.isArray(result.users)) {
                let options = '<option value="0">Select User</option>';
                result.users.forEach(function(user) {
                    options += `<option value="${user.id}">${user.name}</option>`;
                });
                $('#userFullName').html(options);
            } else {
                console.error("Unexpected response:", result);
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", error);
        }
    });
}

function loadAllCategory() {
    $.ajax({
        type: "POST",
        url: "app/Controller/ajax_ticket.php",
        data: {
            function: 'loadAllCategory'
        },
        dataType: 'json',
        success: function(result) {
            console.log(result);
            if (result.success && Array.isArray(result.categories)) {
                let options = '<option value="0">Select Category</option>';
                result.categories.forEach(function(category) {
                    options += `<option value="${category.id}">${category.name}</option>`;
                });
                $('#userCategory').html(options);
            } else {
                console.error("Unexpected response format:", result);
                $('#userCategory').html('<option value="0">No categories available</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", error);
            $('#userCategory').html('<option value="0">Error loading categories</option>');
        }
    });
}

function ticketResetField(){
    $('#submit_ticket')[0].reset();
    $('#userFullName').val('0').trigger('change');
    $('#userSBU').val('0').trigger('change');
}

function searchTicketByNumber(){
    var ticketnumber = $('#searchID').val();

    if (ticketnumber == '' || ticketnumber == null){
        $("#searchID").addClass('is-invalid').focus();
        return false;
    }else{
        console.log(ticketnumber);
        $.ajax({
        type: "POST",
        url: "app/Controller/ajax_ticket.php",
        data: {
            function:'sync_sdp_sites',
            id: ticketnumber
        },
        dataType: 'json',
        success: function(result){
            console.log(result);
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", error);
        }
    });
    }

    $.ajax({
        type: "POST",
        url: "app/Controller/ajax_ticket.php",
        data: {
            function:'searchTicketByNumber',
            id: ticketnumber
        },
        dataType: 'json',
        success: function(result){
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", error);
        }
    });
}
