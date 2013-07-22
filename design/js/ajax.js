/*
 *
 * Credits to : Curtis Crewe <curtis@budgetwebsitesolutions.co.uk>
 * Freelancer : Paid4Upload <http://www.freelancer.com/u/Paid4Upload.html>
 *
 */

	$(document).ready(function() {
		
		$("#submit").click(function(event) {
			event.preventDefault();
			var Username = $('#username').val();
			var Password = $('#password').val();
			var Submit = $('#submit');
			
			Submit.attr("disabled", "disabled");
			Submit.val("Logging in...");
			
			$.ajax({
				type: "GET",
				data: "act=login&username=" + Username + "&password=" + Password,
				url: "/secure/ajax.php",
				success: function(data){
					
					console.log(data);
					
					Parsed = $.parseJSON(data);
					
					switch(Parsed['result']) {
							
						case "200":
							$('#form').remove();
							parseInfo = $.parseJSON(Parsed['primaryLogin']);
							
							console.log(parseInfo);
							
							window.username = parseInfo['username'];
							window.oauth_token = parseInfo['oauth_token'];
							window.oauth_secret = parseInfo['oauth_access'];
							window.sessionId = parseInfo['sessionId'];
							window.secret = parseInfo['secret'];
							window.consumer = parseInfo['consumer'];
							
							$('#ajaxResponse').html('<div class="success">Successfully logged in as '+window.username+'</div>');
							$("#contacts-ajax").html("");
							
							Status = "";
							$.each(parseInfo['contactList'], function(i, obj) {
								$.get('/secure/ajax.php?act=status&user='+ obj['contact']['id'] +'&', function(data) {
									$("#contacts-ajax").append("<tr><td>" + obj['contact']['id'] + "</td><td>" + data + "</td><td><a href='#' id='startChat' data-user='" + obj['contact']['id'] + "'>Start conversation</a></td></tr>");
								});
							});
							
							break;
						case "41":
							alert("Failed to login!");
							break;
						case "42":
							alert("Failed to get Access Token");
							break;
						case "43":
							alert("Failed to request Access Token");
							break;
							
					}
				},
				error: function() {
					alert("Error");
				}
			});
		});
		
		$("#contacts-ajax").on("click", "#startChat", function(event) {
			var thisUser = $(this).data("user");
			$('#user').val(thisUser);
			$('#ajaxResponse').html('<div class="success">Chat initiated with '+thisUser+'</div>');
		});
		
		$("#submitChat").click(function(event) {
			
			thisUser = $('#user').val();
			thisMessage = $('#msg').val();
			
			$.ajax({
				type: "GET",
				data: "act=chat&user="+ thisUser +"&msg="+thisMessage,
				url: "/secure/ajax.php",
				success: function(data) {
					setTimeout(waitForNotification,1000);
				},
				error: function() {
					alert("Chat could not be submitted");
				}
			});
		});
		
		window.LastId = 0;
		
		function waitForNotification() {
			$.ajax({
            			type: "GET",
            			data: "act=longpoll",
				url: "/secure/ajax.php",
            			async: true,
            			cache: false,
            			timeout: 10000,
            			success: function(data){
            				Parsed = $.parseJSON(data);
            				$.each(Parsed, function(arrayID,group) {
            					var box = $("#robot");
	    					if(window.LastId < group.sequence) {
	    						box.val(box.val() + group.sender + " : " + group.msg + "\n");
	    						window.LastId = group.sequence;
	    						$("#robot").scrollTop($("#robot")[0].scrollHeight);
	    					}
            				});
            				setTimeout(waitForNotification,1000);
	     			},
     				error: function(x, t, m) {
        				setTimeout(waitForNotification,1000);
    				}
    			});
		}
		
	});