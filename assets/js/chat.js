/* Created by: Kenrick Beckett

Name: Chat Engine
*/

var instanse = false;
var state;
var mes;
var file;
var msgs;
var user;
var chatArea;
var cuser;

function Chat () {
    this.update = updateChat;
    this.send = sendChat;
    this.getState = getStateOfChat;
}

$(function() {
                var name = $("#name-area").html();
                cuser = $("#name-area").text();
                user = cuser;
                chatArea =$("#file-name").text().replace(".txt","");
                console.log("welcome to chat "+cuser+" you are using "+chatArea);
                $("#name-area").text("You are: " + name );
                file=$("#file-name").html();
                chat.getState();
                $("#sendie").keydown(function(event) {
                 var key = event.which;
                 //all keys including return.
                 if (key >= 33) {

                     var maxLength = $(this).attr("maxlength");
                     var length = this.value.length;

                     // don't allow new content if length is maxed out
                     if (length >= maxLength) {
                         event.preventDefault();
                     }
                  }
                        });
                 // watch textarea for release of key press
                 $('#sendie').keyup(function(e) {

                          if (e.keyCode == 13) {

                    var text = $(this).val();
                                var maxLength = $(this).attr("maxlength");
                    var length = text.length;

                    // send
                    if (length <= maxLength + 1) {
                                                //console.log("file = "+file);
                                chat.send(text, name,file,msgs);
                                $(this).val("");

                    } else {

                                        $(this).val(text.substring(0, maxLength));

                                }


                          }
             });
$("#emo").change(function(){

    //alert('Selected value: ' + $(this).val());
    add = $(this).val();
    var box = $("#sendie");
    box.val(box.val() + add);
    $("#sendie").focus();
    $("#emo").val( "" );
});
});

//gets the state of the chat
function getStateOfChat(){
	if(!instanse){
		 instanse = true;
		 //console.log("File = "+file);
		 $.ajax({
		   type: "POST",
		   url: "process.php",
		   data: {  
   			'function': 'getState',
			'file': file
			//'msgs':
			},
                        dataType: "json",
		        success: function(data){
			   //console.log(data);
			   state = data.state;
			   instanse = false;
		   },
		});
	}	 
}

//Updates the chat
function updateChat(){
	//console.log("update chat start with "+file);
	matched = $(".chatbox .user");
	
	//msgs = matched.length;
	//console.log("msgs = "+msgs );
	 if(!instanse){
		 instanse = true;
	     $.ajax({
			   type: "POST",
			   url: "process.php",
			   data: {  
			   			'function': 'update',
						'state': state,
						'file': file,
						'msgs': msgs,
						'user':user,
						'chatarea': chatArea
						},
			   dataType: "json",
			   success: function(data){
				   //console.log(data);
				   //console.logdata.text.length
				   state = data.state;
				   //debug = data.chatArea; 	
				   //msgs = data.state;
				   //console.log("Text Length "+data.text.length);
				   //console.log(" state = "+state +" msgs = "+msgs);
				   if(data.text.length >0){
					chatarea = data.chatArea;
					cuser = data.user;
					for (var i = 0; i < data.text.length; i++) {
					//console.log($(data.text[i]));
                            	 $('#chat-area').append($(data.text[i] +"</br>"));
                       		 }
			//cuser = $(data).user;
			console.log("msg area = "+chatarea+" cuser = " +cuser+" page user "+user);
			console.log(data);
			//console.log(user+" "+);
			if (user !== cuser){
				console.log("this user needs notify");
				if (cuser !== ''  ){
			 	console.log("debug notify adding "+chatArea );
				rollSound = new Audio("sounds/new-message.mp3");
				rollSound.play();
				showNotification("new message from "+cuser);
				}
			else {
				console.log ("we need to add "+chatArea);
				showNotification(user+" has looged in not sure");
				//debug = chatArea;
			}
			}
                        matched = $(".chatbox .user");
                        //console.log( "after update msgs = "+ matched.length);
                         document.getElementById('chat-area').scrollTop = document.getElementById('chat-area').scrollHeight;
				   }
				  
				   instanse = false;
				   //state = data.state;
				   msgs = data.state;
				   //console.log("we have "+msgs+" now");	
			   },
			});
	 }
	 else {
		 setTimeout(updateChat, 1500);
	 }
}

//send the message
function sendChat(message, nickname,msgs)
{       
    updateChat();
    console.log("start send");
     $.ajax({
		   type: "POST",
		   url: "process.php",
		   data: {  
		   			'function': 'send',
					'message': message,
					'nickname': nickname,
					'msgs':msgs,
					'file': file,
					'user': user,
					'chatarea':chatArea
				 },
		   dataType: "json",
		   success: function(data){
			   console.log("send chat "+data);
			   //msgs = msgs+1;
			   updateChat();
		   },
		});
}

function showNotification(txt) {
	console.log("the message is "+txt);
	if (Notification.permission !== "denied") {
    // We need to ask the user for permission
    Notification.requestPermission().then((permission) => {
      // If the user accepts, let's create a notification
      if (permission === "granted") {
        //const notification = new Notification("Hi there!");
	console.log("granted");
        // …
      }
    });
  }
              if(window.Notification) {
                  Notification.requestPermission(function(status) {
                      console.log('Status: ', status); // show notification permission if permission granted then show otherwise message will not show
              var options = {
                  body: txt, // body part of the notification
                  dir: 'ltr', // use for direction of message
                  image:'images/logo.png', // use for show image
		  badge: 'images/logo.png'

                }
		console.log(options);
                        var n = new Notification('New Message', options);
                  });
              }
              else {
                  alert('Your browser doesn\'t support notifications.');
              }
          }

function getSearchParams(k){
 var p={};
 location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){p[k]=v})
 return k?p[k]:p;
}
