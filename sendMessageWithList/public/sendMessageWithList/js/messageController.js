
FormController = {
 // keeps track how much chars ar filled in the sms texterea
    countChar: function(val){
        var len = val.value.length;
        $('#charNum').html( 160 - len + '/160');
    },
    checkFormat:function(val){
        var numbers = val.split(';');   
        window.validation = false;
        for(a in numbers) {
           window.validation = /00(9[976]\d|8[987530]\d|6[987]\d|5[90]\d|42\d|3[875]\d|2[98654321]\d|9[8543210]|8[6421]|6[6543210]|5[87654321]|4[987654310]|3[9643210]|2[70]|7|1)\d{1,14}$/.test(numbers[a]);
        }
    },
    succes:function(nr) {
        $('#succes').toggleClass('comein');
        $('.check').toggleClass('scaledown');
        $('#go').fadeToggle(nr);
    },
    error:function(nr) {
        $('#error').toggleClass('comein');
        $('.cross').toggleClass('scaledown');
        $('#go').fadeToggle(nr);
    }, 
};

function validate(type){
    var fieldsObj = {
        mobile_number: {
            validators: {
                empty: {
                    message: 'Please insert at least one mobile number'
                },
                regexp: {
                        regexp: /00(9[976]\d|8[987530]\d|6[987]\d|5[90]\d|42\d|3[875]\d|2[98654321]\d|9[8543210]|8[6421]|6[6543210]|5[87654321]|4[987654310]|3[9643210]|2[70]|7|1)\d{1,14}$/,
                        message: 'Not a valid mobile number, please enter as follows: 00{country code}{mobile number}<br>Multiple mobile numbers must be separated bij a semicolon ( ; ) '
                }
            }          
        },
        message: {
            validators: {
                notEmpty: {
                    message: 'Please enter message to be sent'
                }
            }
        }
    };
    
    $('#messageForm').formValidation({
        framework: 'bootstrap',
        icon: {
			valid: 'glyphicon glyphicon-ok',
			invalid: 'glyphicon glyphicon-remove',
			validating: 'glyphicon glyphicon-refresh'
		},
        fields: fieldsObj
    });
}

$(document).ready(function () {
    validate();
});

$("#sendSms").click(function(){

    window.smsTo = document.getElementById('mobile_number').value;  
    window.smsText = document.getElementById('message').value; 

    if( !window.smsTo || !window.smsText || !window.validation ) {

        setTimeout(function(){FormController.error(50)},100);
        setTimeout(function(){FormController.error(500)},3000); 
    } else {
        var data = {smsTo: window.smsTo, smsText: window.smsText};
        var path = 'functions/sendSms.php';    
        $.ajax({
            url: path,
            type: "POST",
            data: data,
            success: function () {
               
                setTimeout(function(){FormController.succes(50)},100);
                setTimeout(function(){FormController.succes(500)},5000);
                document.getElementById("mobile_number").value  = "";
                document.getElementById("message").value  = "";
            },
            error: function () {
                 setTimeout(function(){FormController.error(50)},100);
                setTimeout(function(){FormController.error(500)},6500); 
            }
        });
    }
});

$("#numberList").click(function(){

    var path = 'list/NumberList.php';    
    $.ajax({
        dataType: "json",
        url:path,
        type: "POST",
        success: function (data) {
            document.getElementById('mobile_number').value = data ;
            //document.getElementById("mobile_number").disabled = true;
            FormController.checkFormat(data);
        }
    }); 
})
             