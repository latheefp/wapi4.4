<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <!--  This file has been downloaded from bootdey.com @bootdey on twitter -->
        <!--  All snippets are MIT license http://bootdey.com/license -->
        <!--  If you want to help us go here https://www.bootdey.com/help-us -->
        <title>Whatsapp web chat template - Bootdey.com</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
        <link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </head>
    <body>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <div class="container app">
            <div class="row app-one">
                <div class="col-sm-4 side">
                    <div class="side-one">
                        <div class="row heading">  
                            <div class="col-sm-3 col-xs-3 heading-avatar">
                                <div class="heading-avatar-icon">
                                    <img src="https://bootdey.com/img/Content/avatar/avatar1.png">
                                </div>
                            </div>
                            <?php $accountName = $this->request->getSession()->read('Account.name'); ?>
                            <span style="font-weight: bold; text-align: left;">
                                <h4><?php echo $accountName; ?></h4>
                            </span>
                        </div>

                        <div class="row searchBox">
                            <div class="col-sm-12 searchBox-inner">
                                <div class="form-group has-feedback">
                                    <input id="searchText" type="text" class="form-control" name="searchText" placeholder="Search">
                                    <span class="glyphicon glyphicon-search form-control-feedback"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row sideBar" id="sideBarContactList">

                        </div>
                    </div>

                    <div class="side-two">
                        <div class="row newMessage-heading">
                            <div class="row newMessage-main">
                                <div class="col-sm-2 col-xs-2 newMessage-back">
                                    <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                </div>
                                <div class="col-sm-10 col-xs-10 newMessage-title">
                                    New Chat
                                </div>
                            </div>
                        </div>

                        <div class="row composeBox">
                            <div class="col-sm-12 composeBox-inner">
                                <div class="form-group has-feedback">
                                    <input id="composeText" type="text" class="form-control" name="searchText" placeholder="Search People">
                                    <span class="glyphicon glyphicon-search form-control-feedback"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row compose-sideBar" id="compose-sideBar-body">

                        </div>
                    </div>
                </div>

                <div class="col-sm-8 conversation">
                    <div class="row heading" id="conv-row-head">

                    </div>


                    <div class="row message" id="conversation">
                        

                        <div class="row message-previous">
                            <div class="col-sm-12 previous">
                                <a onclick="previous(this)" id="ankitjain28" name="20">
                                    Show Previous Message!
                                </a>
                            </div>
                        </div>
                        
                    </div>
                    <div id="loading-icon" class="text-center" style="display:none ;">
                            <i class="fa fa-spinner fa-spin fa-3x"></i> Loading...
                        </div>

                    <div class="row reply resizable">
                        <div class="col-sm-1 col-xs-1 reply-emojis">
                            <i class="fa fa-smile-o fa-2x"></i>
                        </div>
                        <div class="col-sm-9 col-xs-9 reply-main">
                            <input type="hidden" id="selectdNumber">
                            <textarea class="form-control" rows="1" id="comment"></textarea>
                        </div>
                        <div class="col-sm-1 col-xs-1 reply-recording">
                            <i class="fa fa-microphone fa-2x" aria-hidden="true"></i>
                        </div>
                        <div class="col-sm-1 col-xs-1 reply-send">
                            <i class="fa fa-send fa-2x" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" id="api_key"  value="<?= $api_key->api_key ?>">



        <style type="text/css">
            html,
            body,
            div,
            span {
                height: 100%;
                width: 100%;
                overflow: hidden;
                padding: 0;
                margin: 0;
                box-sizing: border-box;
            }

            body {
                background: url("https://www.bootdey.com/img/bgy.png") no-repeat fixed center;
                background-size: cover;
            }

            .fa-2x {
                font-size: 1.5em;
            }

            .app {
                position: relative;
                overflow: hidden;
                top: 19px;
                height: calc(100% - 38px);
                margin: auto;
                padding: 0;
                box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .06), 0 2px 5px 0 rgba(0, 0, 0, .2);
            }

            .app-one {
                background-color: #f7f7f7;
                height: 100%;
                overflow: hidden;
                margin: 0;
                padding: 0;
                box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .06), 0 2px 5px 0 rgba(0, 0, 0, .2);
            }

            .side {
                padding: 0;
                margin: 0;
                height: 100%;
            }
            .side-one {
                padding: 0;
                margin: 0;
                height: 100%;
                width: 100%;
                z-index: 1;
                position: relative;
                display: block;
                top: 0;
            }

            .side-two {
                padding: 0;
                margin: 0;
                height: 100%;
                width: 100%;
                z-index: 2;
                position: relative;
                top: -100%;
                left: -100%;
                -webkit-transition: left 0.3s ease;
                transition: left 0.3s ease;

            }


            .heading {
                padding: 10px 16px 10px 15px;
                margin: 0;
                height: 60px;
                width: 100%;
                background-color: #eee;
                z-index: 1000;
            }

            .heading-avatar {
                padding: 0;
                cursor: pointer;

            }

            .heading-avatar-icon img {
                border-radius: 50%;
                height: 40px;
                width: 40px;
            }

            .heading-name {
                padding: 0 !important;
                cursor: pointer;
            }

            .heading-name-meta {
                font-weight: 700;
                font-size: 100%;
                padding: 5px;
                padding-bottom: 0;
                text-align: left;
                text-overflow: ellipsis;
                white-space: nowrap;
                color: #000;
                display: block;
            }
            .heading-online {
                display: none;
                padding: 0 5px;
                font-size: 12px;
                color: #93918f;
            }
            .heading-compose {
                padding: 0;
            }

            .heading-compose i {
                text-align: center;
                padding: 5px;
                color: #93918f;
                cursor: pointer;
            }

            .heading-dot {
                padding: 0;
                margin-left: 10px;
            }

            .heading-dot i {
                text-align: right;
                padding: 5px;
                color: #93918f;
                cursor: pointer;
            }

            .searchBox {
                padding: 0 !important;
                margin: 0 !important;
                height: 60px;
                width: 100%;
            }

            .searchBox-inner {
                height: 100%;
                width: 100%;
                padding: 10px !important;
                background-color: #fbfbfb;
            }


            /*#searchBox-inner input {
              box-shadow: none;
            }*/

            .searchBox-inner input:focus {
                outline: none;
                border: none;
                box-shadow: none;
            }

            .sideBar {
                padding: 0 !important;
                margin: 0 !important;
                background-color: #fff;
                overflow-y: auto;
                border: 1px solid #f7f7f7;
                height: calc(100% - 120px);
            }

            .sideBar-body {
                position: relative;
                padding: 10px !important;
                border-bottom: 1px solid #f7f7f7;
                height: 72px;
                margin: 0 !important;
                cursor: pointer;
            }

            .sideBar-body:hover {
                background-color: #f2f2f2;
            }

            .sideBar-avatar {
                text-align: center;
                padding: 0 !important;
            }

            .avatar-icon img {
                border-radius: 50%;
                height: 49px;
                width: 49px;
            }

            .sideBar-main {
                padding: 0 !important;
            }

            .sideBar-main .row {
                padding: 0 !important;
                margin: 0 !important;
            }

            .sideBar-name {
                padding: 10px !important;
            }

            .name-meta {
                font-size: 100%;
                padding: 1% !important;
                text-align: left;
                text-overflow: ellipsis;
                white-space: nowrap;
                color: #000;
            }

            .sideBar-time {
                padding: 10px !important;
            }

            .time-meta {
                text-align: right;
                font-size: 12px;
                padding: 1% !important;
                color: rgba(0, 0, 0, .4);
                vertical-align: baseline;
            }

            /*New Message*/

            .newMessage {
                padding: 0 !important;
                margin: 0 !important;
                height: 100%;
                position: relative;
                left: -100%;
            }
            .newMessage-heading {
                padding: 10px 16px 10px 15px !important;
                margin: 0 !important;
                height: 100px;
                width: 100%;
                background-color: #00bfa5;
                z-index: 1001;
            }

            .newMessage-main {
                padding: 10px 16px 0 15px !important;
                margin: 0 !important;
                height: 60px;
                margin-top: 30px !important;
                width: 100%;
                z-index: 1001;
                color: #fff;
            }

            .newMessage-title {
                font-size: 18px;
                font-weight: 700;
                padding: 10px 5px !important;
            }
            .newMessage-back {
                text-align: center;
                vertical-align: baseline;
                padding: 12px 5px !important;
                display: block;
                cursor: pointer;
            }
            .newMessage-back i {
                margin: auto !important;
            }

            .composeBox {
                padding: 0 !important;
                margin: 0 !important;
                height: 60px;
                width: 100%;
            }

            .composeBox-inner {
                height: 100%;
                width: 100%;
                padding: 10px !important;
                background-color: #fbfbfb;
            }

            .composeBox-inner input:focus {
                outline: none;
                border: none;
                box-shadow: none;
            }

            .compose-sideBar {
                padding: 0 !important;
                margin: 0 !important;
                background-color: #fff;
                overflow-y: auto;
                border: 1px solid #f7f7f7;
                height: calc(100% - 160px);
            }

            /*Conversation*/

            .conversation {
                padding: 0 !important;
                margin: 0 !important;
                height: 100%;
                /*width: 100%;*/
                border-left: 1px solid rgba(0, 0, 0, .08);
                /*overflow-y: auto;*/
            }

            .message {
                padding: 0 !important;
                margin: 0 !important;
                background: url("w.jpg") no-repeat fixed center;
                background-size: cover;
                overflow-y: auto;
                border: 1px solid #f7f7f7;
                height: calc(100% - 120px);
            }
            .message-previous {
                margin : 0 !important;
                padding: 0 !important;
                height: auto;
                width: 100%;
            }
            .previous {
                font-size: 15px;
                text-align: center;
                padding: 10px !important;
                cursor: pointer;
            }

            .previous a {
                text-decoration: none;
                font-weight: 700;
            }

            .message-body {
                margin: 0 !important;
                padding: 0 !important;
                width: auto;
                height: auto;
            }

            .message-main-receiver {
                /*padding: 10px 20px;*/
                max-width: 60%;
            }

            .message-main-sender {
                padding: 3px 20px !important;
                margin-left: 40% !important;
                max-width: 60%;
            }

            .message-text {
                margin: 0 !important;
                padding: 5px !important;
                word-wrap:break-word;
                font-weight: 200;
                font-size: 14px;
                padding-bottom: 0 !important;
            }

            .message-time {
                margin: 0 !important;
                margin-left: 50px !important;
                font-size: 12px;
                text-align: right;
                color: #9a9a9a;

            }

            .receiver {
                width: auto !important;
                padding: 4px 10px 7px !important;
                border-radius: 10px 10px 10px 0;
                background: #ffffff;
                font-size: 12px;
                text-shadow: 0 1px 1px rgba(0, 0, 0, .2);
                word-wrap: break-word;
                display: inline-block;
            }

            .sender {
                float: right;
                width: auto !important;
                background: #dcf8c6;
                border-radius: 10px 10px 0 10px;
                padding: 4px 10px 7px !important;
                font-size: 12px;
                text-shadow: 0 1px 1px rgba(0, 0, 0, .2);
                display: inline-block;
                word-wrap: break-word;
                height: auto;
            }


            /*Reply*/

            .reply {
                height: 60px;
                width: 100%;
                background-color: #f5f1ee;
                padding: 10px 5px 10px 5px !important;
                margin: 0 !important;
                z-index: 1000;
            }

            .reply-emojis {
                padding: 5px !important;
            }

            .reply-emojis i {
                text-align: center;
                padding: 5px 5px 5px 5px !important;
                color: #93918f;
                cursor: pointer;
            }

            .reply-recording {
                padding: 5px !important;
            }

            .reply-recording i {
                text-align: center;
                padding: 5px !important;
                color: #93918f;
                cursor: pointer;
            }

            .reply-send {
                padding: 5px !important;
            }

            .reply-send i {
                text-align: center;
                padding: 5px !important;
                color: #93918f;
                cursor: pointer;
            }

            .reply-main {
                padding: 2px 5px !important;
            }

            .reply-main textarea {
                width: 100%;
                resize: none;
                overflow: hidden;
                padding: 5px !important;
                outline: none;
                border: none;
                text-indent: 5px;
                box-shadow: none;
                height: 100%;
                font-size: 16px;
            }

            .reply-main textarea:focus {
                outline: none;
                border: none;
                text-indent: 5px;
                box-shadow: none;
            }
            .selected div{
                background: lightblue;
            }

            @media screen and (max-width: 700px) {
                .app {
                    top: 0;
                    height: 100%;
                }
                .heading {
                    height: 70px;
                    background-color: #009688;
                }
                .fa-2x {
                    font-size: 2.3em !important;
                }
                .heading-avatar {
                    padding: 0 !important;
                }
                .heading-avatar-icon img {
                    height: 50px;
                    width: 50px;
                }
                .heading-compose {
                    padding: 5px !important;
                }
                .heading-compose i {
                    color: #fff;
                    cursor: pointer;
                }
                .heading-dot {
                    padding: 5px !important;
                    margin-left: 10px !important;
                }
                .heading-dot i {
                    color: #fff;
                    cursor: pointer;
                }
                .sideBar {
                    height: calc(100% - 130px);
                }
                .sideBar-body {
                    height: 80px;
                }
                .sideBar-avatar {
                    text-align: left;
                    padding: 0 8px !important;
                }
                .avatar-icon img {
                    height: 55px;
                    width: 55px;
                }
                .sideBar-main {
                    padding: 0 !important;
                }
                .sideBar-main .row {
                    padding: 0 !important;
                    margin: 0 !important;
                }
                .sideBar-name {
                    padding: 10px 5px !important;
                }
                .name-meta {
                    font-size: 16px;
                    padding: 5% !important;
                }
                .sideBar-time {
                    padding: 10px !important;
                }
                .time-meta {
                    text-align: right;
                    font-size: 14px;
                    padding: 4% !important;
                    color: rgba(0, 0, 0, .4);
                    vertical-align: baseline;
                }
                /*Conversation*/
                .conversation {
                    padding: 0 !important;
                    margin: 0 !important;
                    height: 100%;
                    /*width: 100%;*/
                    border-left: 1px solid rgba(0, 0, 0, .08);
                    /*overflow-y: auto;*/
                }
                .message {
                    height: calc(100% - 140px);
                }
                .reply {
                    height: 70px;
                }
                .reply-emojis {
                    padding: 5px 0 !important;
                }
                .reply-emojis i {
                    padding: 5px 2px !important;
                    font-size: 1.8em !important;
                }
                .reply-main {
                    padding: 2px 8px !important;
                }
                .reply-main textarea {
                    padding: 8px !important;
                    font-size: 18px;
                }
                .reply-recording {
                    padding: 5px 0 !important;
                }
                .reply-recording i {
                    padding: 5px 0 !important;
                    font-size: 1.8em !important;
                }
                .reply-send {
                    padding: 5px 0 !important;
                }
                .reply-send i {
                    padding: 5px 2px 5px 0 !important;
                    font-size: 1.8em !important;
                }



            }

            .image-container {
                width: 1000px;
                height: 1000px; /* Adjust as needed */
                overflow: hidden; /* Hide overflowing parts of the image */
                }
        </style>


        <?=
        $this->Html->scriptBlock(sprintf(
                        'var csrfToken = %s;',
                        json_encode($this->request->getAttribute('csrfToken'))
        ));
        ?>

        <script type="text/javascript">




            $(function () {
                $(".heading-compose").click(function () {
                    $(".side-two").css({
                        "left": "0"
                    });
                });

                $(".newMessage-back").click(function () {
                    $(".side-two").css({
                        "left": "-100%"
                    });
                });





                $(document).on('keydown', '#comment', function (event) {
                    if (event.which === 13) {
                        event.preventDefault(); // Prevent the default Enter key behavior (e.g., line break)
                        console.log('Enter key pressed');
                        sendchat();
                    }
                });



                $(document).on('click', '.fa-send', function (event) {
                    message = $('#comment').val();
                    if (message.length == 0) {
                        sendchat();
                    }
                });




            })
        </script>


        <script>
            let limit = 25;
            let page = 1;
            $(document).ready(function () {

                loadcontact();
                limit = 5;
                page = 5;
                //   console.log(lengh);
                $('#sideBarContactList').on('scroll', function () {
                    let div = $(this).get(0);
                    if (div.scrollTop + div.clientHeight >= div.scrollHeight) {
                        page = page + 5;
                        loadcontact();
                    }
                });
            })

            $(".messages").animate({
                scrollTop: $(document).height()
            }, "fast");

            $("#profile-img").click(function () {
                $("#status-options").toggleClass("active");
            });

            $(".expand-button").click(function () {
                $("#profile").toggleClass("expanded");
                $("#contacts").toggleClass("expanded");
            });

            $("#status-options ul li").click(function () {
                $("#profile-img").removeClass();
                $("#status-online").removeClass("active");
                $("#status-away").removeClass("active");
                $("#status-busy").removeClass("active");
                $("#status-offline").removeClass("active");
                $(this).addClass("active");

                if ($("#status-online").hasClass("active")) {
                    $("#profile-img").addClass("online");
                } else if ($("#status-away").hasClass("active")) {
                    $("#profile-img").addClass("away");
                } else if ($("#status-busy").hasClass("active")) {
                    $("#profile-img").addClass("busy");
                } else if ($("#status-offline").hasClass("active")) {
                    $("#profile-img").addClass("offline");
                } else {
                    $("#profile-img").removeClass();
                }
                ;

                $("#status-options").removeClass("active");
            });


            function sendchat() {
                var message = $('#comment').val().trim();

                // Check if the message is empty
                if (message.length === 0) {
                    //   console.log("Empty message");
                    return; // Exit the function
                }
                API_KEY = $('#api_key').val();
                mobilenumberId = $('#selectdNumber').val();
                $('#comment').val('');

                $.ajax({
                    beforeSend: function (xhr) { // Add this line
                        xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                    },
                    url: "/apis/sendchat",
                    method: "POST",
                    data: {message: message, mobilenumberId: mobilenumberId},
                    headers: {
                        "Authorization": "Bearer " + API_KEY // Replace "YOUR_API_KEY" with your actual API key
                    },
                    success: function (data) {
                        console.log("updating data");
                        var conversationElement = document.getElementById("conversation");
                        var newElement = document.createElement("div");
                        newElement.classList.add("row", "message-body");
                        newElement.innerHTML = `<div class="col-sm-12 message-main-sender">
                            <div class="sender">
                            <div class="message-text">` + message + `</div>
                            <span class="message-time pull-right" title="Now">Now</span>
                            </div></div>`;

                        conversationElement.appendChild(newElement);
                        conversationElement.scrollTop = conversationElement.scrollHeight;
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        // Handle the error response
                    }

                });

            }



            function fetchMessages() { //new, need to replace getmsg. Not active yet.
                $.ajax({
                    beforeSend: function (xhr) { // Add this line
                        xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                    },
                    url: "/apis/getMessage",
                    method: "POST",
                    data: {message: message, mobilenumberId: mobilenumberId},
                    headers: {
                        "Authorization": "Bearer " + API_KEY // Replace "YOUR_API_KEY" with your actual API key
                    },
                    success: function (data)
                    {
                        $('#conversation').html(data);
                        var elem = document.getElementById('conversation');
                        elem.scrollTop = elem.scrollHeight;

                    }
                });
            }

            function newMessage() {
                message = $(".message-input input").val();
                if ($.trim(message) == '') {
                    return false;
                }
                $('<li class="sent"><img src="http://emilcarlsson.se/assets/mikeross.png" alt="" /><p>' + message + '</p></li>')
                        .appendTo($('.messages ul'));
                $('.message-input input').val(null);
                $('.contact.active .preview').html('<span>You: </span>' + message);
                $(".messages").animate({
                    scrollTop: $(document).height()
                }, "fast");
            }
            ;

            $('.submit').click(function () {
                newMessage();
            });






            $('#searchText').keydown(function (event) {
                let keyPressed = event.keyCode || event.which;
                if (keyPressed === 13) {
                    limit = 25;
                    page = 1;
                    $('#sideBarContactList').html('');
                    var query = $('#searchText').val();
                    loadcontact();
                }
            });






            function loadcontact() {
                var query = $('#searchText').val();
                $.ajax({
                    url: "/uis/getcontact?query=" + query + "&page=" + page + "&limit=" + limit,
                    method: "GET",
                    //   data:{customer:query},  
                    success: function (data)
                    {
//                        sideBarContactList = document.createElement("sideBarContactList");
//                             $('#sideBarContactList').html(data);
                        $('#sideBarContactList').append(data);
                        sideBarContactList.append = (data);
                        $('#sideBar-body').html(data);
                    }
                    //  this.count=this.count+10
                });
            }


            function loadchat(contact, profile) {
                var sideBarBodies = document.querySelectorAll(".side-one .sideBar-body");
                sideBarBodies.forEach(function (element) {
                    element.classList.remove("selected");
                });

                var clickedElement = document.getElementById("sidebar-body-" + contact);
                if (clickedElement) {
                    clickedElement.classList.add("selected");
                    $('#conv-row-head').html('');
                    $('#conversation').html('');

                }
                $.ajax({
                    url: "/uis/getrowhead/" + profile,
                    method: "GET",
                    beforeSend: function () {
                        // Show the loading icon before making the AJAX request
                        $('#loading-icon').show();
                    },
                    success: function (data)
                    {
                        $('#conv-row-head').html(data);

                    }
                });
                //  $('.heading-name-meta').html(profile);
                $('#selectdNumber').val(contact);
                $.ajax({
                    url: "/uis/getmsg/" + contact,
                    method: "GET",
                    success: function (data)
                    {
                        $('#conversation').html(data);
                        $('#loading-icon').hide();
                        var elem = document.getElementById('conversation');
                        elem.scrollTop = elem.scrollHeight;


                    },
                    error: function () {

                        $('#loading-icon').hide();
                        toastr.error('An error occurred while loading data.');

                    }

                });

            }


//            function fetchMessages() {
//                // Make an AJAX request to the server to fetch messages
//                // Update the chatConsole element with the received messages
//                // You can use XMLHttpRequest or any other AJAX library like jQuery.ajax()
//            }

// Function to send a message to the API


// Event handler for submitting the chat form
            document.getElementById("chatForm").addEventListener("submit", function (event) {
                event.preventDefault(); // Prevent the form from submitting

                var messageInput = document.getElementById("messageInput");
                var message = messageInput.value.trim();

                if (message !== "") {
                    sendMessage(message); // Send the message to the API
                    messageInput.value = ""; // Clear the input field
                }
            });

// Fetch messages from the server periodically
            setInterval(fetchMessages, 2000); // Adjust the interval as per your requirement
        </script>
    </body>
</html>