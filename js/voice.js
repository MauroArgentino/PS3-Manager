	var accessToken = "8024b7f7c6534b5b8968aa58869e6408";
		var subscriptionKey = "35f6b81c-f11e-403f-8727-badef61c135b";
		var baseUrl = "https://api.api.ai/v1/";
		$(document).ready(function() {
			$("#keyword").keypress(function(event) {
				if (event.which == 13) {
					event.preventDefault();
					send();
				}
			});
			$("#rec").click(function(event) {
				switchRecognition();
			});
		});
		var recognition;
		function startRecognition() {
			recognition = new webkitSpeechRecognition();
			recognition.onstart = function(event) {
				updateRec();
			};
			recognition.onresult = function(event) {
				var text = "";
			    for (var i = event.resultIndex; i < event.results.length; ++i) {
			    	text += event.results[i][0].transcript;
			    }
			    
				
				var searchKeyword = text;
				if(text == "last played") { document.location.href = 'index.php?order=lastplayed'; }
				if(text == "most played") { document.location.href = 'index.php?order=numplayed'; }
				if(text == "release date") { document.location.href = 'index.php?order=rel_date'; }
				if(text == "score") { document.location.href = 'index.php?order=score'; }
				if(text == "random") { document.location.href = 'games.php?game=random'; }
				if(text == "reboot") { document.location.href = 'index.php?command=reboot'; }
				if(text == "restart") { document.location.href = 'index.php?command=reboot'; }
				
				setInput(text);
				stopRecognition();
				
				
			var numres = $('#numres').val();
			
			if (searchKeyword.length > 0) {
				
				$('#order').fadeTo(10, 0.2);
				$('#order').css("background-color","#A5A5A5");
				$('#order').prop('disabled', 'disabled');
				
				$('#numres').fadeTo(10, 0.2);
				$('#numres').css("background-color","#A5A5A5");
				$('#numres').prop('disabled', 'disabled'); 
				
				$.post('search.php', { keywords: searchKeyword }, function(data) {
					$('div#content_games').empty();
					$('div#res_number').empty();


					$.each(data, function() {
						$('div#res_number').text(this.rescount);

						if(this.name) {
							
							if(this.played == 0) {
								
								
								$('div#content_games').append('<div class="col-md-4 col-xs-4 gallery-grid " style="margin-bottom: 10px; font-size: 95%"><a class="example-image" href="games.php?game=' + this.id + '"><img class="example-image" id="example-image" src="covers/'+ this.covername +'" ><font color="black"><center>'+ this.name +'<center></font></a></div>');
								
								
							}
							else {
								$('div#content_games').append('<div class="col-md-4 col-xs-4 gallery-grid" style="margin-bottom: 10px; font-size: 95%"><a class="example-image" href="games.php?game=' + this.id + '"><img class="badgex example-image" id="example-image" src="covers/'+ this.covername +'"><span class="badgex">' + this.numplayed +'</span><font color="black"><center>'+ this.name +'<center></font></a></div>');	
							}
						}



					});
				}, "json");
			}


			else {
				
				$('#order').fadeTo(10, 1);
				$('#order').css("background-color","#FFF");
				$('#order').prop('disabled', false);
				
				 
				$('#numres').fadeTo(10, 1);
				$('#numres').css("background-color","#FFF");
				$('#numres').prop('disabled', false);
				
				$.post('search.php', { keywords: searchKeyword }, function(data) {
					$('div#content_games').empty()
					$('div#res_number').empty()


					$.each(data, function() {

						$('div#res_number').text(this.rescount);
						
						$('div#content_games').append('<div class="col-md-4 col-xs-4 gallery-grid" style="font-size: 95%"><a class="example-image-link" href="games.php?game=' + this.id + '"><img class="example-image" id="example-image" src="covers/'+ this.covername +'"><font color="black"><center>'+ this.name +'<center></font></a></div>');


					});
				}, "json");
		};

	
				
				
			};
			recognition.onend = function() {
				stopRecognition();
			};
			recognition.lang = "en-US";
			recognition.start();
		}
	
		function stopRecognition() {
			if (recognition) {
				recognition.stop();
				recognition = null;
			}
			updateRec();
		}
		function switchRecognition() {
			if (recognition) {
				stopRecognition();
			} else {
				startRecognition();
			}
		}
		
		function capitalise(string) {
			return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
		}
		
		function setInput(text) {
		
		
			$("#keyword").val(capitalise(text));
			send();
		}
		function updateRec() {
		
			$("#rec").html(recognition ? "<img style='height: 29px; width: auto;' src='images/mic-red.png'>" : "<img style='height: 29px; width: auto;' src='images/mic.png'>");
		}
		function send() {
			var text = $("#keyword").val();
			$.ajax({
				type: "POST",
				url: baseUrl + "query/",
				contentType: "application/json; charset=utf-8",
				dataType: "json",
				headers: {
					"Authorization": "Bearer " + accessToken,
					"ocp-apim-subscription-key": subscriptionKey
				},
				data: JSON.stringify({ q: text, lang: "en" }),
				success: function(data) {
					setResponse(JSON.stringify(data, undefined, 2));
				},
				error: function() {
					setResponse("Internal Server Error");
				}
			});
			setResponse("Loading...");
		}
		function setResponse(val) {
		
			$("#response").text(val);
		}
