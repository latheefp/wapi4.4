let limit = 25;
let page = 1;
let sessionId; // Declare sessionId in the broader scope
let socket; // Declare socket in the broader scope
let query;

$(document).ready(function () {

    toastr.options = {
        "positionClass": "toast-top-right",
        "containerId": "toast-container"
    };

    $.ajax({
        url: '/chats/createSession',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            console.log(response);
            sessionId = response.data.session_id;

            // socket = new WebSocket('ws://localhost:8080'); // Use the declared socket variable
            socket = new WebSocket(chat_url); // Use the declared socket variable
            // Handle WebSocket connection
            socket.onopen = () => {
                // console.log('WebSocket connected');
                // Register
                const message = {
                    session_id: sessionId, // Use the fetched session ID
                    type: "register",
                };
                socket.send(JSON.stringify(message));
                toastr.success('Client Registered');
            };

            // Handle WebSocket connection error
            socket.onerror = (error) => {
                toastr.error('Registration failed');
                console.log('WebSocket error:', error);
                console.log('WebSocket not connected');
            };

            // Handle incoming messages
            socket.onmessage = (event) => {
                const message = JSON.parse(event.data);
                switch (message.type) {
                    case "success":
                        toastr.success(message.message);
                        break;
                    case "contactlist":
                        // console.log("Contact list");
                        // console.log(message.message);
                        if (message.message.data) {
                            appendContacts(message.message.data);
                        }
                        break;
                    case "warning":
                        toastr.warning(message.message);
                        break;
                    case "failed":
                        toastr.error(message.message);
                        break;
                    case "ping":
                        //just ingnore ping.
                        break;
                    case "loadChathistory":
                        if (message['error']) {
                            toastr.warning(message.error);
                        } else {
                            $('#loading-icon').hide();
                            updateConversation(message);
                           attachScrollEventListeners();
                        }

                        break;
                    case "appendchat":
                        if (message['error']) {
                            toastr.warning(message.error);
                        } else {
                            $('#loading-icon').hide();
                            prependChat(message);
                           attachScrollEventListeners();
                        }
                        break;
                    default:
                        console.log("unknown message");
                        console.log(message)

                }
            };

            function prependChat(message){
                var contact_stream_id = message.contact_stream_id;
                var conversationId = 'conversation-' + contact_stream_id;
                var conversationDiv = $('#' + conversationId); // Corrected this line
                conversationDiv.prepend(message.html);
            }


            function updateConversation(message) {
                var contact_stream_id = message.contact_stream_id;
                var conversationId = 'conversation-' + contact_stream_id;
                // var conversationDiv = $(conversationId);
                var conversationDiv = $('#' + conversationId); // Corrected this line

                // Hide all other conversation divs
                $('#conversation .conversation-stream').hide();
                if (conversationDiv.length > 0) { //existing div. 
                    console.log('appending existing ' + conversationDiv.length);
                    conversationDiv.append(message.html).show();
                } else {
                    console.log('div conversation-' + contact_stream_id + " is zero and ")
                    // If the div does not exist, create it, append to the conversation container, and show it
                    $('#conversation').append('<div class="conversation-stream row message-stream" page=1 contact_stream_id="' + message.contact_stream_id + '"  id="conversation-' + contact_stream_id + '">' + message.html + '</div>');
                }

                // Scroll to the bottom of the conversation container
                var conversationContainer = $('#conversation');
                conversationContainer.scrollTop(conversationContainer[0].scrollHeight);




                // Scroll to the bottom of the conversation container
                var elem = $('#' + conversationId);
                elem.scrollTop(elem[0].scrollHeight);
            }

            // Function to display messages on the chat interface
            function appendMessage(userId, content, messageType) {
                const chatMessages = document.getElementById('chat-messages');
                const messageDiv = document.createElement('div');
                messageDiv.className = messageType;
                messageDiv.innerText = `${userId}: ${content}`;
                chatMessages.appendChild(messageDiv);
            }

            // Function to append contacts to the contact list
            function appendContacts(contacts) {
                const contactList = document.getElementById('sideBarContactList');

                contacts.forEach(contact => {
                    const contactStreamId = contact.contact_stream_id;
                    const name = contact.name || 'No Name';
                    const contactNumber = contact.contact_number;
                    const created = new Date(contact.created);
                    const timeAgo = timeAgoInWords(created);

                    const contactDiv = document.createElement('div');
                    contactDiv.className = 'row sideBar-body';
                    contactDiv.id = `sidebar-body-${contactStreamId}`;
                    contactDiv.innerHTML = `
                        <div class="col-sm-3 col-xs-3 sideBar-avatar">
                            <div class="avatar-icon">
                                <img src="https://bootdey.com/img/Content/avatar/avatar1.png" alt="${name}">
                            </div>
                        </div>
                        <div class="col-sm-9 col-xs-9 sideBar-main" onclick="loadchat(${contactStreamId},'${name}')">
                            <div class="row">
                                <div class="col-sm-6 col-xs-6 sideBar-name">
                                    <span class="name-meta" title="${contactNumber}">${name}</span>
                                </div>
                                <div class="col-sm-6 col-xs-6 pull-right sideBar-time">
                                    <span class="time-meta pull-right" title="${formatDate(created)}">${timeAgo}</span>
                                </div>
                            </div>
                        </div>
                    `;

                    contactList.appendChild(contactDiv);
                });
            }
        },
        error: function (xhr, status, error) {
            console.error('Error fetching session ID:', error);
        }
    });

    $('#sideBarContactList').on('scroll', function () {
        // alert("scrolling");
        console.log("Scrolling...");
        let div = $(this).get(0);
        if (div.scrollTop + div.clientHeight >= div.scrollHeight) {
            page += 5;
            console.log("PAging more" + limit + "' " + page + " " + query);
            loadcontact();
        }
    });

    // Function to format date
    function formatDate(date) {
        return date.toLocaleString();
    }

    // Function to calculate time ago in words
    function timeAgoInWords(date) {
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);
        let interval = Math.floor(seconds / 31536000);

        if (interval > 1) {
            return interval + ' years ago';
        }
        interval = Math.floor(seconds / 2592000);
        if (interval > 1) {
            return interval + ' months ago';
        }
        interval = Math.floor(seconds / 86400);
        if (interval > 1) {
            return interval + ' days ago';
        }
        interval = Math.floor(seconds / 3600);
        if (interval > 1) {
            return interval + ' hours ago';
        }
        interval = Math.floor(seconds / 60);
        if (interval > 1) {
            return interval + ' minutes ago';
        }
        return Math.floor(seconds) + ' seconds ago';
    }

    function loadcontact() {
        console.log("Loading more contacts by scrolling down");
        var query = $('#searchText').val();

        const message = {
            session_id: sessionId, // Use the fetched session ID
            type: "loadcontact",
            page: page,
            limit: limit,
            query: query
        };
        socket.send(JSON.stringify(message));
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
            url: "/uis/getrowhead/" + profile,  //this is the chatbox header. 
            method: "GET",
            beforeSend: function () {
                // Show the loading icon before making the AJAX request
                $('#loading-icon').show();
            },
            success: function (data) {
                $('#conv-row-head').html(data);

            }
        });

        //  $('.heading-name-meta').html(profile);
        $('#selectdNumber').val(contact);



        const message = {
            session_id: sessionId, // Use the fetched session ID
            type: "loadChathistory",
            page: lastID,
        };
        socket.send(JSON.stringify(message));


    }




});

function sendMsg(message) {
    if (socket.readyState === WebSocket.OPEN) {
        socket.send(JSON.stringify(message));
        //  toastr.success('Message sent successfully.');
    } else {
        toastr.error('The connection is not active, try refreshing the page');
    }
}



function loadchatscrollup(contact_stream_id) {
    $('#selectdNumber').val(contact_stream_id);
    console.log("loadchatscroll, "+contact_stream_id);
    var conversationId = 'conversation-' + contact_stream_id;
    var conversationDiv = $('#' + conversationId); // Corrected this line
    currentpgae = parseInt(conversationDiv.attr('page')); // Convert to integer
    page=currentpgae + 1
    console.log("Current page is "+ currentpgae + " and new page is "+page)

    // lastID=10;

    if (conversationDiv.length > 0) {
        var chatContainer = (conversationDiv);
        disableScroll(conversationDiv);
        $('#loading-icon').show();

        //  page=page+1;
        const message = {
            session_id: sessionId, // Use the fetched session ID
            type: "appendchat",
            page: page,
            contact_stream_id: contact_stream_id
        };
        sendMsg(message);
    } else {
        console.log(conversationDiv.length + " is  zero initial history not loaded")
    }
}

function disableScroll(conversationDiv) {
    console.log("Disabling scroll");
    conversationDiv.off('scroll'); // Remove the scroll event listener
    conversationDiv.css('overflow', 'hidden'); // Prevent scrolling by setting overflow to hidden
}

// Function to enable scrolling
// function enableScroll(conversationDiv) {
//     conversationDiv.on('scroll', function() {
//         // Your scroll event handling logic here
//     });
//     conversationDiv.css('overflow', 'auto'); // Allow scrolling by setting overflow to auto
// }



function loadchat(contact_stream_id, profile) {
    var sideBarBodies = document.querySelectorAll(".side-one .sideBar-body");

    sideBarBodies.forEach(function (element) {
        element.classList.remove("selected");
    });

    var clickedElement = document.getElementById("sidebar-body-" + contact_stream_id);
    if (clickedElement) {
        clickedElement.classList.add("selected");
        // $('#conv-row-head').html('');
        // $('#conversation').html('');

    }

    $.ajax({
        url: "/uis/getrowhead/" + profile,  //this is the chatbox header. 
        method: "GET",
        beforeSend: function () {
            // Show the loading icon before making the AJAX request
            $('#loading-icon').show();
        },
        success: function (data) {
            $('#conv-row-head').html(data);

        }
    });

    //  $('.heading-name-meta').html(profile);
    $('#selectdNumber').val(contact_stream_id);

    // lastID=10;
    var conversationId = 'conversation-' + contact_stream_id;
    var conversationDiv = $('#' + conversationId); // Corrected this line
    if (conversationDiv.length == 0) {
        //  page = parseInt(conversationDiv.attr('page')); // Convert to integer
        //  page=page+1;
        const message = {
            session_id: sessionId, // Use the fetched session ID
            type: "loadChathistory",
            page: 1,
            contact_stream_id: contact_stream_id
        };
        sendMsg(message);
    } else {
        console.log(conversationDiv.length + " is not zero Inintial loading is done, please scrollup for more loading.")
    }



}


function sendchat() {
    // console.log("newchat");
    var chatMessage = $('#comment').val().trim();

    // Check if the message is empty
    if (chatMessage.length === 0) {
        //   console.log("Empty message");
        return; // Exit the function
    }
    // API_KEY = $('#api_key').val();
    mobilenumberId = $('#selectdNumber').val();
    $('#comment').val('');
    const message = {
        session_id: sessionId, // Use the fetched session ID
        type: "sendchat",
        message: chatMessage,
        contact_stream_id: mobilenumberId,
        msgtype: "text"
    };

    sendMsg(message);


}

function attachScrollEventListeners() {
    $('.conversation-stream').css({
        
        'overflow-y': 'auto'
    });

    $('.conversation-stream').each(function () {
        var chatContainer = $(this);
        var lastScrollTop = chatContainer.scrollTop(); // Initialize with current scroll position

        chatContainer.off('scroll'); // Remove previous event listeners to avoid duplication
        chatContainer.on('scroll', function () {
            var currentScrollTop = chatContainer.scrollTop();
            console.log("Scrolling");

            // Check if scrolling up and at the top of the div
            if (currentScrollTop < lastScrollTop && currentScrollTop === 0) {
                console.log("scroll top.");
                // console.log($(this).attr('contact_stream_id')); // Log the attribute
                loadchatscrollup($(this).attr('contact_stream_id'));
            }

            // Update last scroll position
            lastScrollTop = currentScrollTop;
        });
    });
}


