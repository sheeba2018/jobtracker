"use strict";
var la = {}

la.init = function(){
	Util.addLis(Util.getEl('#login')[0], 'click',la.login);
	
}

/* CHECKS THAT THE USERNAME AND PASSWORD ARE NOT BLANK FIRST */
la.login = function(e){
	var data = {}
	data.email = Util.getEl('#email')[0].value;
	data.password = Util.getEl('#password')[0].value;
	if(data.email == "" || data.password == ""){
		Util.msgBox({
			heading: {text: 'ERROR', background: 'red'},
			body: {text: 'Email and Password cannot be blank'},
			rightbtn: {text: 'Okay', background: 'green', display: 'block'}
		})
		Util.addLis(Util.getEl('#rightbtn')[0], 'click', function(){
			Util.closeMsgBox();
		});

	}
	
	/* ADMIN LOGIN */
	else {
		Util.msgBox({
			heading: {text: 'PROCESSING LOGIN', background: 'green'},
			body: {text: 'We are processing your login please wait...'}
		});
		data.flag = 'login';
		data = JSON.stringify(data);
		Util.sendRequest('xhr/routes.php',function(res){
			Util.closeMsgBox();
			var response = JSON.parse(res.responseText);
			if(response.masterstatus === "error"){
				Util.msgBox({
					heading: {text: 'ERROR', background: 'red'},
					body: {text: response.msg}
				})

				setTimeout(function(){
					Util.closeMsgBox();
				}, 3000);
			}
			else {
				/* YOU WILL NEED TO CHANGE THIS URL TO YOUR URL FOR YOUR HOME PAGE OF YOUR JOB TRACKER APPLICATION DO NOT INCLUDE THE ANGLE BRACKETS */
				window.location = "http://198.211.114.100/job_tracker_student_version_php/home/";
			}
		}, data);
	}
}

la.init();
