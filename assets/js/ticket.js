$(document).ready( () => {
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
        "Do you want to submit this ticket?",
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
                    console.log(result);
                    const notice = result.notice;
                    closeLoaderAlert();
                    const thirdResult = result.result?.result;
                    if (thirdResult == '1' && !notice) {
                        showSuccessAlert(result.local_ticket_id);
                    } else if (thirdResult == '1' && notice) {
                        showSuccesswithNoticeAlert(result.local_ticket_id, notice);
                    } else {
                        closeLoaderAlert();
                        showErrorAlert('Failed to Add New Record' + `<br>` + result.error);
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
            function: 'syncWarehousestoLocalDB'
        },
        dataType: 'json',
        beforeSend: function() {
            showLoaderAlert();
        },
        success: function(result){
            console.log(result);
            const siteCount = result.site_sync?.count || 0;
            const userCount = result.user_sync?.count || 0;
            const categoryCount = result.category_sync?.count || 0;
            const siteStatus = result.site_sync?.result;
            const userStatus = result.user_sync?.result;
            const categoryStatus = result.category_sync?.result;

            const message = `<div class="text-align-center">
                                <strong>Site Sync:</strong> ${siteCount} site(s) updated <br>
                                <strong>User Sync:</strong> ${userCount} user(s) updated <br>
                                <strong>Category Sync:</strong> ${categoryCount} user(s) updated
                            </div>`;

            closeLoaderAlert();
            if (siteCount > 0 || userCount > 0 || categoryCount > 0) {
                showSyncSuccessAlert(message);
            } else if (siteStatus === 'NoUpdate' && userStatus === 'NoUpdate' && categoryStatus === 'NoUpdate') {
                showSyncNoUpdateAlert(message);
            } else {
                const msg = (result.site_sync?.message || '') + ' ' + (result.user_sync?.message || '') + ' ' + (result.category_sync?.message || '');
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


function loadAllWarehouses() {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: "POST",
            url: "app/Controller/ajax_ticket.php",
            data: {
                function: 'loadAllWarehouses'
            },
            dataType: 'json',
            success: function(result) {
                console.log(result);
                const { option } = result;
                $('#userSBU').html(`<option value="0">Select SBU</option>${option}`);
                resolve(result);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                reject(error); 
            }
        });
    });
}


function loadAllUsers() {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: "POST",
            url: "app/Controller/ajax_ticket.php",
            data: {
                function: 'loadAllUsers'
            },
            dataType: 'json',
            success: function(result) {
                console.log(result);
                const { option } = result;
                $('#userFullName').html(`<option value="0">Select User</option>${option}`);
                resolve(result);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                reject(error);
            }
        });
    });
}


function loadAllCategory() {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: "POST",
            url: "app/Controller/ajax_ticket.php",
            data: {
                function: 'loadAllCategory'
            },
            dataType: 'json',
            success: function(result) {
                console.log(result);
                const { option } = result;
                $('#userCategory').html(`<option value="0">Select Category</option>${option}`);
                resolve(result);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                reject(error);
            }
        });
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
        customLoader();
        $.ajax({
        type: "POST",
        url: "app/Controller/ajax_ticket.php",
        data: {
            function:'searchTicketByNumber',
            id: ticketnumber
        },
        dataType: 'json',
        success: function(result){
            console.log(result);
             if (result && result.serviceID) {
                closeLoaderAlert();
                sessionStorage.setItem('ticketData', JSON.stringify(result));
                window.location.href = 'ticket_status.php';
            } else {
                closeLoaderAlert();
                showErrorAlertinSearch(result.error);
            }
        },
        error: function(xhr, status, error) {
            closeLoaderAlert();
            console.log(xhr.responseJSON.error);
            showErrorAlertinSearch("("+  error + ") "+ xhr.responseJSON.error);        }
    });
    }
}

function getTicketDetailsFromSession() {
    const data = JSON.parse(sessionStorage.getItem('ticketData'));
    if (!data) return;

    $('#ticketCtrlNumberView').text((data.localTicketID) || '-');
    $('#ticketTypeView').text(toPascalCase(data.ticket_type) || '-');
    $('#ticketTitleView').text(data.title || '-');
    $('#ticketDescriptionView').text(data.description || '-');
    $('#userFullNameView').text(data.user_FullName || '-'); 
    $('#userEmailView').text(data.email || '-'); 
    $('#userContactView').text(data.contact || '-');
    $('#userSBUView').text(data.warehouseName || '-');
    $('#userCategoryView').text(data.category_name || '-');

    const techName = data.technician?.name || 'No assigned Technician';
    $('#ticketTechnicianView').text(techName);

    const resolutionContent = data.resolution?.content || 'No resolution yet';
    $('#ticketResolutionView').text(resolutionContent);

    const statusName = data.statusname || 'Unknown'; 
    const statusColorClass = getStatusColorClass(data.statuscolor); 
    $('#ticketStatusText')
        .text(statusName)
        .removeClass()
        .addClass(`bg-white text-white ${statusColorClass}`);

    const createdAtRaw = data.created_time || null;
    const updatedAtRaw = data.last_updated_time || null;
    $('#ticketCreatedAt').text(formatDate(createdAtRaw) || '-');
    $('#ticketUpdatedAt').text(formatDate(updatedAtRaw) || '-');

    const attachment = data.attachments;
    if (attachment) {
        $('#ticketAttachmentView').text(attachment);
    } else {
        $('#ticketAttachmentView').text('No attachment');
    }

    $('#ticketID').val(data.hashCode || null);


    sessionStorage.removeItem('ticketData');
}

function getStatusColorClass(hexColor) {
    switch ((hexColor || '').toLowerCase()) {
        case '#003366': return 'bg-dark text-white';        // Assigned
        case '#00ffcc': return 'bg-info text-dark';         // In Progress
        case '#ff0000': return 'bg-danger';                 // Onhold
        case '#0000ff': return 'bg-primary';                // Open
        case '#006400': return 'bg-success text-white';     // Closed
        case '#00ff66': return 'bg-success text-dark';      // Resolved
        default: return 'bg-secondary text-white';          // Fallback
    }
}

function formatDate(dateStr) {
    if (!dateStr) return null;
    const date = new Date(dateStr);
    if (isNaN(date)) return null;

    // Format example: May 23, 2025 14:35
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute:'2-digit' };
    return date.toLocaleString(undefined, options);
}

function printTicket(id){
    console.log(id);
    const targetUrl = "app/Controller/forms/service_ticket.php?ticket=" + encodeURI(id);
    window.open(targetUrl, "_blank");
}