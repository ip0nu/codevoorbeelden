<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Send Message</title>

        <!-- Bootstrap -->
        <link href="//static.atpi.com/bootstrap/css/kendo.common-bootstrap.min.css" rel="stylesheet" />
        <link href="//static.atpi.com/bootstrap/css/kendo.bootstrap.min.css" rel="stylesheet" />
        <link href="//static.atpi.com/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="//static.atpi.com/bootstrap/formvalidation/dist/css/formValidation.min.css" rel="stylesheet" />
        <link href="//static.atpi.com/bootstrap/stylesheets/style_atpi.css" rel="stylesheet">
        <link href="//static.atpi.com/bootstrap/stylesheets/style_atpi.css" rel="stylesheet">
        <link href="stylesheets/style.css" rel="stylesheet" type="text/css"/>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
	</head>
    <body>
        <nav class="headerbar">
            <div class="col-sm-6 brand"><img src="//static.atpi.com/bootstrap/img/logo_atpi.png" width="62" height="55" border="0" /></div>
        </nav>
        <div class="col-sm-12 no-pad-left no-pad-right">
            <div id="custom-bootstrap-topmenu" class="navbar"></div>
        </div>
        <div class="container grid">
            <div class="gridWrapper">
                <div class="row">
                    <div class="gridheader left">
                        <div class="col-md-12 no-pad-left no-pad-right icon-mail">
                            Send Message
                        </div>
                    </div>
                </div>
                <form method="post" id="messageForm">
                    <div class="row">
                        <div class="form-group col-md-12 col-sm-12">
                            <label for="mobile_number">Mobile number(s)</label>
                            <textarea class="input-big form-control" id="mobile_number" name="mobile_number" oninput="FormController.checkFormat(this.value)" placeholder="Mobile number(s)" ></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <p class="bg-info margin-bottom-20">please enter as follows: 00{country code}{mobile number}. Multiple mobile numbers must be separated by a semicolumn ( ; )</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12 col-sm-12">
                            <label for="message">Message</label>
                            <textarea class="input-big form-control" maxlength="160" id="message" name="message" placeholder="Message" oninput="FormController.countChar(this)"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <p class="bg-info margin-bottom-20 txtRight">Characters:<span id="charNum">160/160</span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <button class="btnAction icon-mail pull-right" id="sendSms">Send</button>
                             <button class="btnAction icon-list pull-right" id="numberList">Use list</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>     
        <script src="//static.atpi.com/bootstrap/js/jquery-1.9.1.min.js"></script>
        <script src="//static.atpi.com/bootstrap/js/kendo.all.min.js"></script>
        <script src="//static.atpi.com/bootstrap/js/bootstrap.min.js"></script>
        <script src="//static.atpi.com/bootstrap/formvalidation/dist/js/formValidation.min.js"></script>
        <script src="//static.atpi.com/bootstrap/formvalidation/dist/js/framework/bootstrap.min.js"></script>
        <script src="js/messageController.js"></script>         
        <div class='message' id="succes">
            <div class='check'>
              &#10004;
            </div>
            <p>
                Success
            </p>
            <p>
                Your SMS has been sent.
            </p>
        </div>        
        <div class='message' id="error">
            <div class='cross'>
                &#10006;
            </div>
            <p>
                Something went wrong
            </p>
            <p>
                Your SMS has not been sent.
            </p>
        </div>
    </body>
</html>
