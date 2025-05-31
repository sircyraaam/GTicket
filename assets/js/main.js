var internetstatus;


//Disable f12
$(document).keydown(function (event) {
    $('[data-toggle="tooltip"]').tooltip();
    if (event.keyCode == 123) {
        return false;
    }
    else if (event.ctrlKey && event.shiftKey && event.keyCode == 73) {
        return false;  //Disable from ctrl+shift+i
    }
});


//Disable right-click in div
$('div').bind("contextmenu", function (e) {
    e.preventDefault();
});//Disable f12
$(document).keydown(function (event) {
    if (event.keyCode == 123)return false;
    else if (event.ctrlKey && event.shiftKey && event.keyCode == 73)return false; 
});




//Disable right-click in div
$('div').bind("contextmenu", function (e) {
    e.preventDefault();
});




$(function(){

    if(window.location.href.includes('manage-users')) {
        $(".sidebar li[target]").removeClass('main-nav-active');
        $(".sidebar li[target=4]").addClass('main-nav-active');
    }else if(window.location.href.includes('receiving') || window.location.href.includes('putaway')){
        $(".sidebar li[target]").removeClass('main-nav-active');
        $(".sidebar li[target=1]").addClass('main-nav-active');
    }else if(window.location.href == "http://localhost/RMS/"){
        $(".sidebar li[target]").removeClass('main-nav-active');
        $(".sidebar li[target=0]").addClass('main-nav-active');
    }
    windowresize($(document).width());
    windowheight($(document).height());

    $('#RMS_ChangePassword').on('hidden.bs.modal', function (e) {
        $("#changepasswordoldpassword, #changepasswordnewpassword, #changepasswordconfirmpassword").removeClass('is-invalid');
    });
});


const body = document.querySelector('body'),
      sidebar = body.querySelector('nav'),
      toggle = body.querySelector(".toggle"),
      modeSwitch = body.querySelector(".toggle-switch"),
      modeText = body.querySelector(".mode-text");

// if(sessionStorage.getItem('mode') == 'dark'){
//     $('body').attr('class','dark');
//     modeText.innerText = "Light mode";
// }else{
//     $('body').removeAttr('class');
//     modeText.innerText = "Dark mode";
// }
// windowresize($(document).width());
// windowheight($(document).height());
$(window).resize(function(){
    windowresize($(document).width());
    windowheight($(document).height());
});



$('body').on('click','li[id=main-nav]',function(){
    $("i#chevArrow").css(
        'transform','rotate(0deg)'
    );
    $("li[id=main-nav]").removeClass("main-nav-active")
    for(var x=1; x<=6; x++){
        $('#sub-nav'+x).slideUp();
    }
    var target = $(this).attr('target');

    if($(this).attr('data-bool') == "1"){
        if($('#sub-nav'+target).css('display') == 'none'){
            $('#sub-nav'+target).slideDown();
    
            $(this).find("i.bx-chevron-right").css(
                'transform','rotate(90deg)'
            );
        }else{
            $('#sub-nav'+target).slideUp();
    
            $(this).find("i.bx-chevron-right").css(
                'transform','rotate(0deg)'
            );
        }
    }
    $(this).addClass('main-nav-active');
});



// SIDEBAR END
// NAV BAR START
$('#show-nav-details').click(()=>{
    $('.nav-settings').slideToggle(0);
});
// NAV BAR END




// LOG OUT USER FOR INACTIVITY
var previous = 0;
document.addEventListener("mousemove", function(evt) {
 if(previous > 0) return;
 previous++;
});
$('body').on('mousemove',()=>{
    previous++;
});
document.addEventListener("click", function(evt) {
  if(previous > 0) return;
  previous++;
});

window.addEventListener("scroll", function(evt) {
  if(previous > 0) return;
  previous++;
});

var timer = 0;
setInterval(function(){
    if(previous > 0 ){
        timer = 0;
    }else{
        if(timer == 600){
            main_ModalFilter(`It's been a long time since you don't see me, click 'Ok' if you want to REFRESH the page.`,`<button type="button" class="btn btn-outline-danger btn-sm px-4" data-bs-dismiss="modal" onclick="main_ModalSubmit(\'cancellogout\')" >Cancel</button><button type="button" class="btn btn-outline-primary btn-sm px-4" onclick="main_ModalSubmit(\'refresh\')" value="">Ok</button>`);
            timer = 0;
        }
    }
    timer++;
    previous = 0;
},1000); 

// $(document).on('select2:open', () => {
//     document.querySelector('.select2-search__field').focus();
// });





// MAIN CONFIRMATION MODAL CONTENT FUNCTION START //
function main_ModalFilter(message,footer){
    $('#mainconfirmationmodal').modal('show');
    $('#mainmodalmessage').text(message);
    $('#mainmodalfooter').html(footer);
}
// MAIN CONFIRMATION MODAL CONTENT FUNCTION END //




// MAIN CONFIRMATION MODAL CONTENT FUNCTION START //
function main_ModalSubmit(type,value){
   switch(type){
    case 'logout':
        location.href = 'app/Controller/logout.php';
        break;
    default:
        location.reload();
        break;
   }
}
// CONFIRMATION MODAL CONTENT FUNCTION END //





// toggle.addEventListener("click" , () =>{
//     sidebaropenfunction();
//     if($('.sidebar').attr('class') == 'sidebar close'){
//         sessionStorage.setItem("status", "open");
//         $('.home').css('width','calc(100% - 78px)');
//         $('li[id=main-nav]').attr('data-bool','0');
//         for(var x=1; x<=6; x++){
//             $('#sub-nav'+x).slideUp();
//         }
//         $('i#chevArrow').hide();
//         $('#manageUser').css('padding-left', '10px');
//     }else{
//         $('i#chevArrow').css('transform', 'rotate(0deg)');
//         sessionStorage.setItem("status", "close");
//         $('.home').css('width','calc(100% - 250px)');
//         $('li[id=main-nav]').attr('data-bool','1');
//         $('i#chevArrow').show();
//         $('#manageUser').css('padding-left', '0');
//     }
// });




// modeSwitch.addEventListener("click" , () =>{
    
//     body.classList.toggle("dark");
//     if(body.classList.contains("dark")){
//         sessionStorage.setItem("mode", "dark");
//         modeText.innerText = "Light mode";

//         $(".select2-container--disabled .select2-selection, .select2-container--disabled .select2-selection__rendered, .select2-container--disabled .select2-selection__arrow").attr('style', '');
                
//         setTimeout(() => {
//             $(".select2-container--disabled .select2-selection, .select2-container--disabled .select2-selection__rendered, .select2-container--disabled .select2-selection__arrow").attr('style', 'background-color:#5b5c5d !important;');
//         }, 100);
//     }else{
//         sessionStorage.setItem("mode", "light");
//         modeText.innerText = "Dark mode";
        
//         $(".select2-container--disabled .select2-selection, .select2-container--disabled .select2-selection__rendered, .select2-container--disabled .select2-selection__arrow").attr('style', '');
//     }
// });




// function sidebaropenfunction(){
//     if(sessionStorage.getItem("status")=="open"){
//         $('.sidebar').removeClass('close');
//     }else{
//         $('.sidebar').addClass('close');
//     }
// }




function windowresize(width){
    sublink_hover();
    if((width >0 && width <= 600)){
        if($('.sidebar').attr('class') == 'sidebar close'){
            $('.sidebar').css('left','-100px');
            $('.home').css('width','calc(100% - 0px)');
            $('i#chevArrow').hide();
            $('li[id=main-nav]').attr('data-bool','0');
        }else{
            $('li[id=main-nav]').attr('data-bool','1');
            $('i#chevArrow').show();
        }
    }
    else if((width > 599 && width <= 800)){
        // for(var x=1; x<=6; x++){
        //     $('#sub-nav'+x).slideUp();
        // }
        if($('.sidebar').attr('class') == 'sidebar close'){
            $('.sidebar').css('left','0px');
            $('.home').css('width','calc(100% - 78px)');
            $('i#chevArrow').hide();
            $('li[id=main-nav]').attr('data-bool','0');
        }else{
            $('li[id=main-nav]').attr('data-bool','1');
            $('i#chevArrow').show();
        }
    }
    else{
        $('#nav-menu').removeClass('bx-x');
        $('#nav-menu').addClass('bx-menu');
        $('#nav-menu').css('left','0px');
        $("i#chevArrow").css(
            'transform','rotate(0deg)'
        );
        if($('.sidebar').attr('class') == 'sidebar close'){
            $('li[id=main-nav]').attr('data-bool','0');
            $('.home').css('width','calc(100% - 78px)');
            $('.sidebar').css('left','0px');
            $('i#chevArrow').hide();
        }else{
            // $('.home').css('width','calc(100% - 250px)');
            // $('.sidebar').css('left','0px');
            $('li[id=main-nav]').attr('data-bool','1');
            $('i#chevArrow').show();
        }
    }
    $('#nav-menu').removeClass('bx-x');
    $('#nav-menu').addClass('bx-menu');
    $('#nav-menu').css('left','0px');
    for(var x=0; x<=6; x++){$('#sub-nav-hover'+x).hide();}
}


$('#nav-menu').click(function(){
    for(var x=0; x<=6; x++){$('#sub-nav-hover'+x).hide();}
    if($('.sidebar').css('left') == '0px'){
        $(this).css('left','0px');
        $('.sidebar').css('left','-100px');
        $('.home').css('width','calc(100% - 0px)');
        $(this).removeClass('bx-x');
        $(this).addClass('bx-menu');
    }else{
        $(this).css('left','80px');
        $('.sidebar').css('left','0px');
        $('.home').css('width','calc(100% - 0px)');
        $(this).removeClass('bx-menu');
        $(this).addClass('bx-x');
    }
});




function windowheight(height){
    if(height <= 800){
        $('#sub-nav-hover4').css('top','40px');
        $('#sub-nav-hover5').css('top','100px');
    }else{
        $('#sub-nav-hover4').css('top','340px');
        $('#sub-nav-hover5').css('top','400px');
    }
}




function sublink_hover(){
    $('body').on('mouseenter','li[id=main-nav]',function(){
        for(var x=0; x<=6; x++){$('#sub-nav-hover'+x).hide();}
        
        var target = $(this).attr('target');
        if($(this).attr('data-bool') == "0"){
            if($('#sub-nav-hover'+target).css('display') == 'none'){
                $('#sub-nav-hover'+target).show();
                $('#sub-nav-hover'+target).css('top',parseInt($(this).position().top)+108);
            }else $('#sub-nav-hover'+target).hide();
        }
    }).on('mouseleave','ul[class=sublink-hover]',function(){
        for(var x=0; x<=6; x++){$('#sub-nav-hover'+x).hide();}
    });
}




// ==== RETURNS NUMBERS ONLY START ==== //
function numberonly(evnt){ 
    var ASCIICode = (evnt.which) ? evnt.which : evnt.keyCode
    if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))return false;
}
// ==== RETURNS NUMBERS ONLY END ==== //




// CONVERT FORM START 
function convert_form_data_to_object(form){
    var arr={};
    for(var x=0; x<form.length; x++)arr[form[x].name] = form[x].value.trim();
    return arr;
}
// CONVERT FORM END 




// GET CURRENT DATE START
function getCurrentDate(){
    var d = new Date();
    var month = d.getMonth()+1;
    var day = d.getDate();

    var output = d.getFullYear() + '-' +
        (month<10 ? '0' : '') + month + '-' +
        (day<10 ? '0' : '') + day;
    return Date.parse(output);
}
//GET CURRENT DATE END




//toasters
//normal toasters
function toasts_info(message) {
    iziToast.info({
        title: 'GTicket Alert: ',
        message: message,
        position: 'topRight',
        timeout: '2500'
    });
}




function toasts_warning(message) {
    iziToast.warning({
        title: 'GTicket Alert: ',
        message: message,
        position: 'topRight',
        timeout: '2500'
    });
}




function toasts_success(message) {
    iziToast.success({
        title: 'GTicket Alert: ',
        message: message,
        position: 'topRight',
        timeout: '2500'
    });
}




function toasts_error(message) {
    iziToast.error({
        title: 'GTicket Alert: ',
        message: message,
        position: 'topRight',
        timeout: '2500'
    });
}


//normal toasters




//location reload toasters
function toasts_info_reload(message) {
    iziToast.info({
        title: 'GTicket Alert: ',
        message: message,
        position: 'topRight',
        timeout: '1500',
        onClosed: function(instance, toast, closedBy) {
            location.reload();
        }

    });
}




function toasts_warning_reload(message) {
    iziToast.warning({
        title: 'GTicket Alert: ',
        message: message,
        position: 'topRight',
        timeout: '1500',
        onClosed: function(instance, toast, closedBy) {
            location.reload();
        }
    });
}




function toasts_success_reload(message) {
    iziToast.success({
        title: 'GTicket Alert: ',
        message: message,
        position: 'topRight',
        timeout: '1500',
        onClosed: function(instance, toast, closedBy) {
            location.reload();
        }
    });
}




function toasts_error_reload(message) {
    iziToast.error({
        title: 'GTicket Alert: ',
        message: message,
        position: 'topRight',
        timeout: '1500',
        onClosed: function(instance, toast, closedBy) {
            location.reload();
        }
    });
}




function toasts_session_error_reload(message) {
    iziToast.error({
        title: 'Session Expired',
        message: message,
        position: 'topRight',
        timeout: '3500',
        onClosed: function(instance, toast, closedBy) {
            location.reload();
        }
    });
}





function toasts_print_issuance(message) 
{
    iziToast.success({
        title: 'GTicket Alert: ',
        message: message,
        position: 'topRight',
        timeout: '1500',
        onClosing: function(instance, toast, closedBy) 
        {
            
        },
        onClosed: function(instance, toast, closedBy) 
        {
            // var OBD = $('#obd_obd').val();
            // window.open("tcpdf/Gpaq/printswstcpdf.php?obd=" + OBD, "_blank");
        }
    });
}




function ReplaceNumberWithCommas(number) {
    //Seperates the components of the number
    var n = number.toString().split(".");
    if(n.length == 1){
        n.push('000');
    }

    //Comma-fies the first part
    n[0] = n[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    //Combines the two sections
    return n.join(".");
}




// CUSTOMIZE MODAL START //
var text = "Loading....";
var textstored = "";
var delaytime=100,count=0,x=0;
// function modal
function show_customize_modal(element){
    count=0,delaytime=100;
    var tag = $(element);
    tag.show();
    if(count == 10){
        $('#loading-text').text("Loading....");
    }else{
        while(count < 10){
            textstored="";
            if(x == 0){
                for(x; x<text.length; x++){
                    delaytext(delaytime,x);
                }
            }
            count++;
        }
    }
}




function show_page_loader(){
    $("#pageLoader").show();
}




function hide_page_loader(){
    $("#pageLoader").hide();
}




function hide_customize_modal(element){
    x = 0;
    var tag = $(element);
    tag.hide();
    text = "Loading....";
}




function delaytext(time,indexx){
    setTimeout(()=>{
        if(textstored.length == text.length){
            $('#loading-text').text("");
            textstored = "";
        }else{
            $('#loading-text').text(textstored);
        }
        textstored+=text[indexx];
    },time);
    delaytime +=100;
}
// CUSTOMIZE MODAL END //




// === RETURN FILE EXTENSION START === //
function getFileExtension(filename){
    const extension = filename.split('.').pop();
    return extension;
}
// === RETURN FILE EXTENSION END === //




// === SHOW ALL PASSWORD FIELD FROM CHANGE PASSWORD FORM START === //
function showallpasswordfield(id){
    if ($('input#'+id).is(':checked')) {
        $('#changepasswordnewpassword').attr('type','text');
        $('#changepasswordconfirmpassword').attr('type','text');
    }else{
        $('#changepasswordnewpassword').attr('type','password');
        $('#changepasswordconfirmpassword').attr('type','password');
    }
}
// === SHOW ALL PASSWORD FIELD FROM CHANGE PASSWORD FORM END === //



function ChangePasswordForm(e,ChangePasswordForm){
    e.preventDefault();
    let form = convert_form_data_to_object($(ChangePasswordForm).serializeArray());
    var lowerCaseLetters = /[a-z]/g,upperCaseLetters = /[A-Z]/g,numbers = /[0-9]/g;
    if(form.old == "" ){
        $("#changepasswordoldpassword").addClass('is-invalid').focus();
        toasts_error("Old Password is required.");
        return false;
    }else{
        $("#changepasswordoldpassword").removeClass('is-invalid');
    }

    if(form.new == ""){
        $("#changepasswordnewpassword").addClass('is-invalid').focus();
        toasts_error("New Password is required.");
        return false;
    }else{
        $("#changepasswordnewpassword").removeClass('is-invalid');
    }

    if(!form.new.match(lowerCaseLetters)){
        $("#changepasswordnewpassword").addClass('is-invalid').focus();
        toasts_error("New Password needs atleast one lowercase letter.");
        return false;
    }else{
        $("#changepasswordnewpassword").removeClass('is-invalid');
    }

    if(!form.new.match(upperCaseLetters)){
        $("#changepasswordnewpassword").addClass('is-invalid').focus();
        toasts_error("New Password needs atleast one capital(uppercase) letter.");
        return false;
    }else{
        $("#changepasswordnewpassword").removeClass('is-invalid');
    }

    if(!form.new.match(numbers)){
        $("#changepasswordnewpassword").addClass('is-invalid').focus();
        toasts_error("New Password needs atleast one number.");
        return false;
    }else{
        $("#changepasswordnewpassword").removeClass('is-invalid');
    }

    if(form.new <=9){
        $("#changepasswordnewpassword").addClass('is-invalid').focus();
        toasts_error("New Password should be 8 characters.");
        return false;
    }else{
        $("#changepasswordnewpassword").removeClass('is-invalid');
    }

    if(form.confirm == ""){
        $("#changepasswordconfirmpassword").addClass('is-invalid').focus();
        toasts_error("Confirm Password is required.");
        return false;
    }else{
        $("#changepasswordconfirmpassword").removeClass('is-invalid');
    }

    if(form.confirm != form.new){
        $("#changepasswordconfirmpassword").addClass('is-invalid').focus();
        toasts_error("Confirm password doesn't match to new password.");
        return false;
    }else{
        $("#changepasswordconfirmpassword").removeClass('is-invalid');

        $.post('app/Controller/ajax_maintenance.php',{function:"ChangePassword",arrform:form},
        function(result){
            var data = (JSON.parse(result));
            if(data.result == 'SUCCESS'){
                $('#RMS_ChangePassword').modal('hide');
                $("#changepasswordoldpassword").removeClass('is-invalid');
                toasts_success_reload("Successfully update password.");
            }else if(data.result == 'XEQUAL'){
                $("#changepasswordoldpassword").addClass('is-invalid').focus();
                toasts_error("Incorrect Old password.");
            }else if(data.result == 'CANNOTBEEQUAL'){
                $("#changepasswordoldpassword").addClass('is-invalid').focus();
                $("#changepasswordnewpassword").addClass('is-invalid').focus();
                toasts_error("New Password cannot be equal to Old Password.")
            }else{
                toasts_error("Failed to update your password.");
            }
        });
    }
}





function show_tableloader(targettable){
    $('#'+targettable).find('.tabulator-tableholder').append('<div id="tableloader"><div id="tableloader-box" class="text-center"><div class="position-relative" id="tableloader-loaderbox"><div id="edgeloader"></div><img src="assets/img/tableloader.gif" alt=""></div><h1 class="fs-6 pt-2" id="loader-text">Loading Data...</h1></div</div>');
}


function hide_tableloader(){
    $('div[id=tableloader]').remove();
}



function randomcolor(num){
    let arrcolor = ['#FFE4C4','#FBE7A1','#AAF0D1','#EDDA74','#C0C0C0','#98AFC7','#A0CFEC','#B4CFEC','#ADDFFF','#C2DFFF','#C6DEFF','#B0CFDE','#D5D6EA','#E3E4FA','#7FFFD4','#36F57F','#00FA9A','#ADFF2F','#E2F516','#CCFB5D','#64E986','#6AFB92','#C2E5D3','#FFDAB9'];
    return arrcolor[num];
}



function filteredNumber(e)
{
    if ( e.which == 101 ) {
        return false;
    }else{
        $("#rirCompartmentTemp,#sr_storage_temp,#rirProdTemp").removeClass('is-invalid'); 
        return true;
    }
}




// HEX TO RGB FUNCTION START //
function hex2rgb(hex, opacity) {
    var h=hex.replace('#', '');
    h =  h.match(new RegExp('(.{'+h.length/3+'})', 'g'));

    for(var i=0; i<h.length; i++)
        h[i] = parseInt(h[i].length==1? h[i]+h[i]:h[i], 16);

    if (typeof opacity != 'undefined')  h.push(opacity);

    return 'rgba('+h.join(',')+')';
}
// HEX TO RGB FUNCTION END //




// CUSTOM TOOLTIP FUNCTION START //
function customTooltip(target,type){
    if(type == "show"){
        $(target).slideDown(200);
    }else{
        $(target).slideUp(0);
    }
    
}
// CUSTOM TOOLTIP FUNCTION END //



function gettime(h,m,s){
    if(h >= 0 && h<12){
        if(m <10)return h+":0"+m+":"+s+" am";
        else return h+":"+m+":"+s+" am";
    }
    else{
        if(m<10)return h%12+":0"+m+":"+s+" pm";
        else return h%12+":"+m+":"+s+" pm";
    } 
}


function gettimenosec(h,m){
    if(h >= 0 && h<12){
        if(m <10)return h+":0"+m+" AM";
        else return h+":"+m+" AM";
    }
    else{
        if(m<10)return h%12+":0"+m+" PM";
        else return h%12+":"+m+" PM";
    } 
}




// TABULATOR HEADER MENU TABLE START //
function tabulatorHeaderMenu(){
    var menu = [];
    let content = this;
    var columns = content.getColumns();
    columns.splice(0,1);
    let icon = document.createElement("i");
    icon.classList.add("bx")
    icon.classList.add("bxs-show");
    let label = document.createElement("span");
    let title = document.createElement("span");
    title.textContent = " View All";
    label.appendChild(icon);
    label.appendChild(title);
        menu.push({
            label: label,
            action:function(e){
                for(let key in columns){
                    content.showColumn(columns[key]._column.field);
                }
            }
        });
    for(let column of columns)
    {
        let icon = document.createElement("i");
        icon.classList.add("bx");
        icon.classList.add(column.isVisible() ? "bxs-check-square" : "bxs-checkbox");
        let label = document.createElement("span");
        let title = document.createElement("span");
        title.textContent = " " + column.getDefinition().title;
        label.appendChild(icon);
        label.appendChild(title);
        menu.push({
            label:label,
            action:function(e){
                e.stopPropagation();
                column.toggle();
                if(column.isVisible()){
                    icon.classList.remove("bxs-checkbox");
                    icon.classList.add("bxs-check-square");
                }else{
                    icon.classList.remove("bxs-check-square");
                    icon.classList.add("bxs-checkbox");
                }
            }
        });
    }
    return menu;
}


function tabulatorScroll(){
    setTimeout(()=>{$
        ('button[data-bs-toggle="tooltip"]').tooltip(); 
        $('[data-bs-toggle="tooltip"]').tooltip();
    },100);
}


function RestrictSpaceSpecial(e) {  
    return ((e.which == 42) || (e.which == 40) || (e.which == 41) || (e.which == 43) || (e.which == 91) || (e.which == 92) || (e.which == 124) || (e.which == 60) || (e.which == 62) || (e.which == 63) ? false : true);
}


function numberAndletterOnly(e) {
    var k;
    document.all ? k = e.keyCode : k = e.which;
    return ((k == 46) || (k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
}


function retrictDot(e){
    return ((e.which == 46) || (e.which == 101) ? false : true);
}


function dashOnly(e){
    let specialchar = [33,64,35,36,37,94,38,42,40,41,95,43,61,123,91,125,93,124,92,34,39,63,47,62,46,60,44,58,59];
    if(specialchar.indexOf(e.which) != -1) return false;
    else return true;
}

function dotInTypeNumber(e){
    let specialchar = [33,64,35,36,37,94,38,42,40,41,95,43,61,123,91,125,93,124,92,34,39,63,47,62,45,60,44,58,59];
    if(specialchar.indexOf(e.which) != -1) return false;
    else return true;
}

function DashAndNumberOnly(e){
    var charCode = (e.which) ? e.which : e.keyCode;
    if (charCode != 45 && charCode > 31 && (charCode < 48 || charCode > 57))return false;
}

function RestrictWhiteSpace(e){
    return (e.which != 32);
}

$('section, footer, .sidebar').click(()=>{
    $('.nav-settings').slideUp(0);
});
$('section, footer, #stickyHeadeNav, .home').click(()=>{
    for(var x=0; x<=6; x++){$('#sub-nav-hover'+x).hide();}
});

function showOldPassword(icon,target){
    if($(target).attr('type') == 'text'){
        $(target).attr('type','password');
        $(icon).removeClass('bxs-show');
        $(icon).addClass('bxs-hide');
    }else{
        $(target).attr('type','text');
        $(icon).removeClass('bxs-hide');
        $(icon).addClass('bxs-show');
        $(icon).attr('data-bs-title','Old Password Visible');
    }
}

function keyPress(id){
    $("#"+id).removeClass('is-invalid');
}

function onChange(id,name,tag, type){ 
    
    if(tag === 'INPUT'){ 
        if(type == 'radio' || type == 'checkbox'){
            $("input[name="+name+"], #sr_storage_temp").removeClass('is-invalid');
        }else{
            $("#"+id).removeClass('is-invalid');
        }
        
    }else if(tag === 'SELECT'){
        $("#"+id).next().find('.select2-selection').removeClass('is-invalid');
    }
    
}

function show_modal_loader(){
    $('#modal_loader').modal('show');
}

function hide_modal_loader(){
    $('#modal_loader').modal('hide');
}

// NEW MODAL LOADER

// DATETIME FORMATTER //
function formatDateForDatetimeLocal(inputDate) {
    let date = new Date(inputDate); 
    let formattedDate = date.toLocaleString("sv-SE").slice(0, 16);
    return formattedDate;
}
// DATETIME FORMATTER //

// ENCRYPT //
function encrypt(text) {
    return CryptoJS.AES.encrypt(text, secretKey).toString();
}
// ENCRYPT //

// DECRYPT //
function decrypt(encryptedText) {
    const bytes = CryptoJS.AES.decrypt(encryptedText, secretKey);
    return bytes.toString(CryptoJS.enc.Utf8);
}
// DECRYPT //

// EMPTY //
function empty(value){
    return value === null || value === undefined || value === '' || (Array.isArray(value) && value.length === 0) || (typeof value === 'object' && Object.keys(value).length === 0);
}
// EMPTY //

// SWAL

function showAlert(title, message, icon) {
    Swal.fire({
      title: title,
      text: message,
      icon: icon,
      confirmButtonText: 'OK'
    });
  }

function confirmAction(title, text, confirmCallback) {
Swal.fire({
    title: title,
    text: text,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: 'Cancel'
}).then((result) => {
    if (result.isConfirmed) {
    confirmCallback();
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
        loadAllCategory();
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
        loadAllCategory();
    });
}

function showSuccessAlert(controlNumber) {
    let timerInterval;
    Swal.fire({
        title: 'Success!',
        html: `Your ticket has been submitted.<br><strong>Control Number:</strong> ${controlNumber}<br><br>Closing in <b></b> seconds...`,
        icon: 'success',
        showConfirmButton: true,
        confirmButtonText: 'OK',
        allowOutsideClick: false,
        timer: 10000,
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
        location.reload();
    });
}

function showSuccesswithNoticeAlert(controlNumber, notice) {
    let timerInterval;
    Swal.fire({
        title: 'Success!',
        html: `Your ticket has been submitted but with a notice.<br><strong>Control Number:</strong> ${controlNumber} <br> ${notice} <br><br> Closing in <b></b> seconds...`,
        icon: 'info',
        showConfirmButton: true,
        confirmButtonText: 'OK',
        allowOutsideClick: false,
        timer: 10000,
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
        location.reload();
    });
}

function showErrorAlert(message) {
    Swal.fire({
        title: 'Error',
        html: `${message}`,
        icon: 'error',
        confirmButtonText: 'OK',
        allowOutsideClick: false
    }).then(() => {
        $('#activityconfirmationmodal').modal('show');
    });
}

function showErrorAlertinSearch(message) {
    Swal.fire({
        title: 'Error',
        html: `${message}`,
        icon: 'error',
        confirmButtonText: 'OK',
        allowOutsideClick: false
    })
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

function customLoader() {
    Swal.fire({
        title: 'Processing...',
         html: `
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <img src="assets/img/loader.gif" alt="Loading..." style="width: 350px; height: 100px;" />
                    <p>Please wait while we process your request.</p>
                </div>
            `,
        showConfirmButton: false,
        allowOutsideClick: false
    });
}

function toPascalCase(str) {
    if (!str) return '';
    return str
        .replace(/[_\- ]+/g, ' ')
        .toLowerCase()
        .replace(/\w\S*/g, w => w.charAt(0).toUpperCase() + w.slice(1))
        .replace(/\s+/g, '');
}