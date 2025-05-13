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

    const formDataArray = $(id).serializeArray();
    addRecordForm = convert_form_data_to_object(formDataArray);
    addRecordForm['ticket_type'] = type;

    // Add dropdown display text for specific fields
    addDropdownText(addRecordForm, ['userFullName', 'userSBU', 'userCategory']);

    console.log(addRecordForm);

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

    // Show confirmation modal
    activityModalFilter(
        "Do you want to add a new activity record for this Veteran?",
        `<button type="button" class="btn btn-danger btn-sm px-4" data-bs-dismiss="modal">Close</button>
         <button type="button" class="btn btn-primary btn-sm px-4" onclick="activityModalSubmit('addTicket')">Submit</button>`
    );
}

// Helper function to add dropdown display text
function addDropdownText(form, fields) {
    fields.forEach(field => {
        form[`${field}_text`] = $(`#${field} option:selected`).text().trim();
    });
}


function activityModalSubmit(type,id){
    switch(type){
        case 'addTicket':
            console.log(addRecordForm);
            $.ajax({
                type: "POST",
                url: "app/Controller/ajax_ticket.php",
                data: {
                    function: 'addRecord',
                    addRecordForm: addRecordForm
                },
                dataType: 'json',
                success: function (result) {
                    const thirdResult = result.result?.result;
                    if (thirdResult == '1'){
                        toasts_success("New Record Added Successfully.");
                    }else{toasts_error("Failed to add new Record.");}
                },
                error: function (xhr, status, error) {
                    console.log(xhr, status, error);
                    console.error("Error during AJAX request:", error);
                    toasts_error("An unexpected error occurred. Please try again.");
                }
            });
        break;
    }
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
