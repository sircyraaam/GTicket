function loadJOClosingReport(){
    $.ajax({
        type: 'POST',
        url: 'app/Controller/ajax_email.php',
        data: {
            function: 'loadJOClosingReport'
        },
        dataType: 'text',
        success: function(data){
            $('#joClosingList').show();                
            $('#joClosingList').empty();
            $('#joClosingList').append(data);
        }
    });
}