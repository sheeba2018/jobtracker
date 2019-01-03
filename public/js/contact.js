


"use strict";
var cont = {}

cont.init = function(){
	if(Util.getEl('#addcontact').length != 0){
		Util.addLis(Util.getEl('#addcontactBtn')[0], 'click', cont.addcontact);
	}
	if(Util.getEl('#updatecontact').length != 0){
		cont.getContactList('update');
	}
	if(Util.getEl('#managecontacts').length != 0){
		cont.getContactList('manage');
	}
	if(Util.getEl('#managecontacts').length != 0){
		Util.addLis(Util.getEl('#table')[0], 'click', cont.deleteAssoc);
	}
	if(Util.getEl('#deletecontacts').length != 0){
		Util.addLis(Util.getEl('#contacttable')[0], 'click', cont.deleteContact);
		cont.getContactTable();
	}
}
/* THIS FUNCTION GETS A LIST OF CONTACTS */
cont.getContact = function(e){
	if(e.target.value == 0){
		Util.msgBox({
			heading: {text: 'ERROR', background: 'red'},
			body: {text: 'You must select a contact'},
		});
		setTimeout(function(){
			Util.closeMsgBox();
		}, 2000);
	}
	else {
		var data = {}
		data.flag = 'getcontact';
		data.id = e.target.value;

		data = JSON.stringify(data);

		Util.sendRequest('../xhr/routes.php', function(res){
			Util.getEl('#updatecontactform')[0].innerHTML = res.responseText;
			Util.addLis(Util.getEl('#updatecontactBtn')[0], 'click', cont.updatecontact);
		}, data);
	}
}

/* THIS FUNCTION UPDATES A CONTACT */
cont.updatecontact = function(){
	var data = {}, i = 0;
	data.flag = 'updatecontact';
	data.contactId = Util.getEl('#contlst')[0].value;
	
	data.elements = [
		{regex:'name', id:'name', msg: 'Name cannot be empty and must be a valid name', status: 'checking'},
		{regex:'phone', id:'workphone', msg: 'Phone cannot be empty and must be in the format 999.999.9999', status: 'checking'},
		{regex:'phoneOpt', id: 'mobilephone', msg: 'Phone cannot be empty and must be in the format 999.999.9999', status: 'checking'},
		{regex:'email', id: 'email',msg: 'Cannot be empty and must be a valid email', status: 'checking', duplicate: true}
	];

	while(i < data.elements.length){
		data.elements[i].value = Util.getEl('#' + data.elements[i].id)[0].value;
		i++;
	}

	data = JSON.stringify(data);

	Util.sendRequest('../xhr/routes.php', function(res){
		var response = JSON.parse(res.responseText);

		/* CLEAR ANY PREVIOUS ERRORS */
		gen.clearErrors();

		/* DEPENDING ON WHAT WAS SENT BACK FROM THE SERVER DISPLAY APPROPRIATE MESSAGE.  I USE THE DISPLAYMESSAGEBOXES METHOD TO DISPLAY A COMMON WAY OF DISPLAYING MESSAGES, THE FIRST PARAMETER IS THE RESPONSE OBJECT THE SECOND IS AN ARRAY OF FORM ELEMENT IDS, IF THERE ARE NO FORM ELEMENT ID'S THEN THE ARRAY IS EMPTY */
		gen.displayMessageBoxes(response,['name','workphone','mobilephone','email'], function(){
			/* IF EVERYTHING IS OKAY THEN RELOAD PAGE.*/
			window.location.reload();
		});

		
		

	}, data);
}

/* THIS FUNCTION ADDS A NEW CONTACT */
cont.addcontact = function(){
	var data = {}, i = 0;
	data.flag = 'addcontact';
	
	data.elements = [
		{regex:'name', id:'name', msg: 'Name cannot be empty and must be a valid name', status: 'checking'},
		{regex:'phone', id:'workphone', msg: 'Phone cannot be empty and must be in the format 999.999.9999', status: 'checking'},
		{regex:'phoneOpt', id: 'mobilephone', msg: 'Phone cannot be empty and must be in the format 999.999.9999', status: 'checking'},
		{regex:'email', id: 'email',msg: 'Cannot be empty and must be a valid email', status: 'checking', duplicate: true}
	];

	while(i < data.elements.length){
		data.elements[i].value = Util.getEl('#' + data.elements[i].id)[0].value;
		i++;
	}

	data = JSON.stringify(data);

	Util.sendRequest('../xhr/routes.php',function(res){
		
		var response = JSON.parse(res.responseText);

		/* DEPENDING ON WHAT WAS SENT BACK FROM THE SERVER DISPLAY APPROPRIATE MESSAGE.  I USE THE DISPLAYMESSAGEBOXES METHOD TO DISPLAY A COMMON WAY OF DISPLAYING MESSAGES, THE FIRST PARAMETER IS THE RESPONSE OBJECT THE SECOND IS AN ARRAY OF FORM ELEMENT IDS, IF THERE ARE NO FORM ELEMENT ID'S THEN THE ARRAY IS EMPTY */
		gen.displayMessageBoxes(response,['name','workphone','mobilephone','email']);
		
	},data);


}

/* THIS FUNCTION GETS AND DISPLAYS A LIST OF ACCOUNTS*/
cont.getAccountList = function(){
	var data = {};
	data.flag = 'getaccountlist';
	data = JSON.stringify(data);
	Util.sendRequest('../xhr/routes.php', function(res){
		Util.getEl('#accountlist')[0].innerHTML = res.responseText;
		Util.addLis(Util.getEl('#acctlist')[0], 'change', cont.getJobList);
	}, data);	
}

/* THIS FUNCTION GETS AND DISPLAYS A LIST OF CONTACTS */
cont.getContactList = function(action){
	var data = {}, response;
	data.flag = 'getcontactlist';
	data = JSON.stringify(data);
	Util.sendRequest('../xhr/routes.php', function(res){
		response = JSON.parse(res.responseText);
		if(response.masterstatus === "success"){
			if(action === 'update'){
				Util.getEl('#contactlist')[0].innerHTML = response.contacts;
				Util.addLis(Util.getEl('#contlst')[0], 'change', cont.getContact);
			}
			else if(action === 'manage'){
				Util.getEl('#contactlist')[0].innerHTML = response.contacts;
				Util.addLis(Util.getEl('#contlst')[0], 'change', cont.manageContactInterface);
			}
		}
		else if(response.masterstatus === "error"){
			Util.getEl('#contactlist')[0].innerHTML = response.msg;
		}
	},data);
}

/* THIS FUNCTION SENDS AN AJAX REQUEST TO THE SERVER WHICH RETURNS AND DISPLAYS THE CONACT TABLE */
cont.getContactTable = function(){
	var data = {};
	data.flag = 'getcontacttable';
	data = JSON.stringify(data);
	Util.sendRequest('../xhr/routes.php', function(res){
		Util.getEl('#contacttable')[0].innerHTML = res.responseText;
	}, data);
}


/* THIS FUNCTION DELETES THE CONTACT FROM THE CONTACT TABLE AND DELETES THE CONTACT FROM THE JOB_CONTACT TABLE*/
cont.deleteContact = function(e){
	var row = e.target.parentNode.parentNode.rowIndex;
	if(e.target.type == 'button'){
		var data = {}
		data.flag = 'deletecontact';
		data.contId = e.target.id;
		data = JSON.stringify(data);

		/* DISPLAY WARNING MESSAGE*/
		Util.msgBox({
			heading: {text: 'WARNING', background: 'orange'},
			body: {text: 'You are about to delete this contact and all jobs related to this contact.  It will not be recoverable. If this is what you want to do click "Okay" Othewise click "Cancel"'},
			leftbtn: {text: 'Okay', background: 'green', display: 'block'},
			rightbtn: {text: 'Cancel', background: 'red', display: 'block'}
		})
			
		/* IF THE OK BUTTON IS CLICKED DELETE ACCOUNT FROM DATABASE AND ALL ASSETS*/
		Util.addLis(Util.getEl('#leftbtn')[0], 'click', function(){
			Util.closeMsgBox();
			Util.sendRequest('../xhr/routes.php', function(res){
				console.log(res.responseText);
				var response = JSON.parse(res.responseText);
				if(response.masterstatus === 'success'){
					/* DELETE THE ROW FROM THE CONTACT TABLE INSTEAD OF REALOADING IT */
					Util.getEl('#contTable')[0].deleteRow(row);
				}
				else if(response.masterstatus === 'error'){
					Util.msgBox({
						heading: {text: 'ERROR', background: 'red'},
						body: {text: response.msg},
						rightbtn: {text: 'Okay', background: 'green', display: 'block'}
					});

					Util.addLis(Util.getEl('#leftbtn')[0], 'click', function(){
						Util.closeMsgBox();
					});
				}

			},data);
		});

		Util.addLis(Util.getEl('#rightbtn')[0], 'click', function(){
			Util.closeMsgBox();
		});

	}
}

/* THIS FUNCTION DELETES THE ASSOCIATION THAT A CONTACT HAS WITH A JOB */
cont.deleteAssoc = function(e){
	var row = e.target.parentNode.parentNode.rowIndex;
	if(e.target.value == 'Delete'){
		var idArr = e.target.id.split('&&&');
		var data = {};
		data.flag = 'delAssoc';
		data.acctId = idArr[0];
		data.jobId = idArr[1];
		data.contId = Util.getEl('#contlst')[0].value;

		data = JSON.stringify(data);
		Util.msgBox({
			heading: {text: 'WARNING', background: 'orange'},
			body: {text: 'You are about to delete this association from this contact. If this is what you want to do click "Ok" Othewise click "Cancel"'},
			leftbtn: {text: 'Okay', background: 'green', display: 'block'},
			rightbtn: {text: 'Cancel', background: 'red', display: 'block'}
		})

		Util.addLis(Util.getEl('#leftbtn')[0], 'click', function(){
			Util.closeMsgBox();
			Util.sendRequest('../xhr/routes.php',function(res){
				var response = JSON.parse(res.responseText);

				if(response.masterstatus === 'success'){
					Util.getEl('#table')[0].innerHTML = response.associations;
				}
				else if(response.masterstatus === 'error'){
					Util.msgBox({
						heading: {text: 'ERROR', background: 'red'},
						body: {text: response.msg},
						rightbtn: {text: 'Okay', background: 'green', display: 'block'}
					});
					Util.addLis(Util.getEl('#rightbtn')[0], 'click', function(){
						Util.closeMsgBox()
					});				
				}

			},data);
		});

		Util.addLis(Util.getEl('#rightbtn')[0], 'click', function(){
			Util.closeMsgBox();
		});

	}
}

/* THIS FUNCTION DISPLAYS THE INTERFACE NEED TO ASSIGN CONTACTS TO JOBS*/
cont.manageContactInterface = function(e){
	if(e.target.value == 0){
		Util.msgBox({
			heading: {text: 'ERROR', background: 'red'},
			body: {text: 'You must select a contact'},
			rightbtn: {text: 'Okay', background: 'green', display: 'block'}
		});
		Util.addLis(Util.getEl('#rightbtn')[0], 'click', function(){
			Util.closeMsgBox();
		});
	}
	else {
		var data = {};
		data.flag = 'managecontactinterface';
		data.contId = Util.getEl('#contlst')[0].value;
          
                data = JSON.stringify(data);

		Util.sendRequest('../xhr/routes.php',function(res){
			console.log(res.responseText);
			var response = JSON.parse(res.responseText);
                        
						
			Util.getEl('#name')[0].innerHTML = '<h2>'+response.name+'</h2>';
			Util.getEl('#table')[0].innerHTML = response.associations;
		        Util.getEl('#accountlist')[0].innerHTML = '<p>If you want to add an assocation to a job. Please select an account then select a job.</p>' + response.accounts;
			Util.addLis(Util.getEl('#acclst')[0], 'change', cont.getJobList);
			
			/* CLEAR JOB LIST IF WAS SHOWING FROM A PREVIOUS SELECTION */
			Util.getEl('#joblst')[0].innerHTML = "";
			
		},data);
	}
}

/*THIS FUNCTION GETS AND DISPLAYS A JOBS LI
 * ST*/
cont.getJobList = function(){
	var data = {};
	data.flag = 'getJobList';
	data.accountId = Util.getEl('#acclst')[0].value;

	data = JSON.stringify(data);
	//console.log(data);

	Util.sendRequest('../xhr/routes.php', function(res){
		var response = JSON.parse(res.responseText);

		if(response.masterstatus === 'success'){
			Util.getEl('#joblst')[0].innerHTML = response.jobs + '<div><input class="btn btn-success" type="button" id="addassocBtn" value="Add Assocation"></div>';
			Util.addLis(Util.getEl('#addassocBtn')[0], 'click', cont.addAssoc);
		}
		else if(response.masterstatus === 'error'){
			Util.getEl('#joblst')[0].innerHTML = "No jobs to display";
		}
		
	},data);
}

/* THIS FUNCTION ADDS CONTACTS TO A JOB */
cont.addAssoc = function(){

	var data = {}
	data.flag = 'addassoc';

	data.acctId = Util.getEl('#acclst')[0].value;
	data.jobId = Util.getEl('#joblist')[0].value;
	data.contId = Util.getEl('#contlst')[0].value;

	if(data.accId == 0 || data.jobId == 0 || data.contId == 0){
		Util.msgBox({
			heading: {text: 'ERROR', background: 'red'},
			body: {text: 'You must select an account and job'},
			rightbtn: {text: 'Okay', background: 'green', display: 'block'}
		});
		Util.addLis(Util.getEl('#rightbtn')[0], 'click', function(){
			Util.closeMsgBox()
		});
	}
	else {
		data = JSON.stringify(data);
                //console.log(data);

		Util.sendRequest('../xhr/routes.php', function(res){
			var response = JSON.parse(res.responseText);
                        //console.log(res.responseText);
                        //return;
			
			if(response.masterstatus === 'success'){
				Util.getEl('#table')[0].innerHTML = response.associations;
			}
			else if(response.masterstatus === 'error'){
				Util.msgBox({
					heading: {text: 'ERROR', background: 'red'},
					body: {text: response.msg},
					rightbtn: {text: 'Okay', background: 'green', display: 'block'}
				});
				Util.addLis(Util.getEl('#rightbtn')[0], 'click', function(){
					Util.closeMsgBox()
				});				
			}

		},data);
	}
}

cont.init();




























