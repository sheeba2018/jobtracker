
"use strict";
var jb = {}


jb.init = function(){
	if(Util.getEl('#addjob').length != 0){
		jb.getAccountList('addjob');
	}
	if(Util.getEl('#viewjobcontacts').length != 0){
		jb.getAccountList('getjoblist');
	}
	if(Util.getEl('#addjobnotes').length != 0){
		jb.getAccountList('getjoblist');
	}
	if(Util.getEl('#viewupdatedeletejobnote').length != 0){
		Util.addLis(Util.getEl('#jobnotetable')[0], 'click', jb.updateDeleteJobNote);
		jb.getAccountList('getjoblist');
	}
	if(Util.getEl('#addjobasset').length != 0){
		jb.getAccountList('getjoblist');
	}
	if(Util.getEl('#viewdeletejobasset').length != 0){
		jb.getAccountList('getjoblist');
	}
	if(Util.getEl('#addjobhours').length != 0){
		jb.getAccountList('getjoblist');
	}
	if(Util.getEl('#updatedeletejobhours').length != 0){
		jb.getAccountList('getjoblist');
		Util.addLis(Util.getEl('#updateDeleteHours')[0], 'click', jb.updateDeleteJobHours);
	}

	if(Util.getEl('#printinvoice').length != 0){
		jb.getAccountList('getjoblist');
	}
	/*if(Util.di('accountlist')){
		jb.getAccountList();	
	}
	if(Util.di('assetaccountlist')){
		jb.getAssetAccountList();	
	}

	Util.checkElementAddListener('updateDeleteHours','click',jb.updateDeleteRecord);
	Util.checkElementAddListener('getinvoice','click',jb.getInvoice);
	Util.checkElementAddListener('addjobnote','click',jb.addJobNote);
	Util.checkElementAddListener('jobnotetable','click',jb.updateDeleteJobNote);*/
	
}


jb.getAssetAccountList = function(){
	var data = {};
	data.flag = 'getaccountlist';
	data = JSON.stringify(data);
	Util.sendRequest('../xhr/routes.php', function(res){
		Util.di('assetaccountlist').innerHTML = res.responseText;
		Util.checkElementAddListener('acctlist', 'change', jb.selectJob);
	}, data);	
}

jb.getAccountList = function(action){
	var data = {};
	data.flag = 'getaccountlist';
	data = JSON.stringify(data);
	gen.getAccountList(function(){
		if(action === 'addjob'){
			Util.addLis(Util.getEl('#acctlist')[0], 'change', jb.addJobForm);
		}
		else if(action === 'getjoblist'){
			Util.addLis(Util.getEl('#acctlist')[0], 'change', jb.jobList);
		}
	})
}


jb.jobList = function(){
	var data = {}
	data.flag = 'getJobList';
	data.accountId = Util.getEl('#acctlist')[0].value;

	if(data.accountId == 0){
		jb.checkAccountSelection();
		return;
	}

	data = JSON.stringify(data);

	Util.sendRequest('../xhr/routes.php', function(res){
		var response = JSON.parse(res.responseText);
		
		if(response.masterstatus === "success"){
			Util.getEl('#displayJobList')[0].innerHTML = response.jobs;
		}

		else if(response.masterstatus === "error"){
			
			/* REMOVE ANY PRE LOADED JOB NOTE TABLE. IF IT EXISTS */
			if(Util.getEl('#notesTable').length != 0){
				Util.getEl('#notesTable')[0].innerHTML = "";
			}
			Util.getEl('#displayJobList')[0].innerHTML = response.msg;
		}
		
		/* I DID THIS IF STATEMENT SO I COULD USE THE SAME FUNCTION BUT CALL DIFFERENT FUNCTIONS DEPENDING ON WHAT PAGE THE JOB LIST IS ON. */ 
		
		if(Util.getEl('#viewjobcontacts').length != 0){
			Util.addLis(Util.getEl('#displayJobList')[0], 'change', function(){
				jb.displayJobContactsTable();
			});
		}
		else if(Util.getEl('#addjobnotes').length != 0){
			Util.addLis(Util.getEl('#displayJobList')[0], 'change', function(){
				jb.displayAddJobNoteForm();
				Util.addLis(Util.getEl('#addjobnoteBtn')[0], 'click', jb.addJobNote);
			});
		}
		else if(Util.getEl('#viewupdatedeletejobnote').length != 0){
			Util.addLis(Util.getEl('#displayJobList')[0], 'change', function(){
				jb.displayViewJobNoteTable();
			});
		}
		else if(Util.getEl('#addjobasset').length != 0){
			Util.addLis(Util.getEl('#displayJobList')[0], 'change', function(){
				jb.displayJobAssetForm()
			});
		}

		else if(Util.getEl('#viewdeletejobasset').length != 0){
			Util.addLis(Util.getEl('#displayJobList')[0], 'change', function(){
				jb.viewJobAssetTable();
				Util.addLis(Util.getEl('#viewdelassetstable')[0], 'click', jb.viewDelAssetsTableFunctions)
			});
		}

		else if(Util.getEl('#addjobhours').length != 0){
			Util.addLis(Util.getEl('#displayJobList')[0], 'change', function() {
				jb.displayAddJobHoursForm()
			});
		}
		else if(Util.getEl('#updatedeletejobhours').length != 0){
			Util.addLis(Util.getEl('#displayJobList')[0], 'change', function() {
				jb.getJobHours();
			});
		}

		else if(Util.getEl('#printinvoice').length != 0){
			Util.addLis(Util.getEl('#displayJobList')[0], 'change', function() {
				jb.displayInvoiceForm();
			});
		}

			
		/*if(Util.di('viewdelassetstable')){
			Util.checkElementAddListener('jblst','change',jb.viewJobAssetTable);
		}
		else if(Util.di('addhoursform')){
			Util.checkElementAddListener('jblst','change',jb.displayAddJobHoursForm);
		}
		else if(Util.di('updateDeleteHours')){
			Util.checkElementAddListener('jblst','change',jb.getJobHours);
		}
		else if(Util.di('invoiceForm')){
			Util.checkElementAddListener('jblst','change',jb.displayInvoiceForm);
		}
		else if(Util.di('addjobnoteform')){
			Util.checkElementAddListener('jblst','change',jb.displayAddJobNoteForm);
		}
		else if(Util.di('viewjobnotetable')){
			Util.checkElementAddListener('jblst','change',jb.displayViewJobNoteTable);
		}
		else if(Util.di('jobcontactlist')){
			Util.checkElementAddListener('jblst','change',jb.displayJobContactsTable);
		}
		else{
			Util.checkElementAddListener('jblst','change',jb.displayJobAssetForm);
		}*/
		
	},data);
}

jb.viewJobAssetTable = function(){
	var data = {}
	data.flag = 'viewdelejobassets';
	data.jobId = Util.getEl('#joblist')[0].value;

	data = JSON.stringify(data);

	Util.sendRequest('../xhr/routes.php', function(res){
		var response = JSON.parse(res.responseText);
		if(response.masterstatus === 'error'){
			Util.getEl('#viewdelassetstable')[0].innerHTML = response.msg;
		}
		else if(response.masterstatus === 'success') {
			Util.getEl('#viewdelassetstable')[0].innerHTML = response.table;
		}

	},data);
}

/*THIS METHOD ALLOWS ONE TO VIEW AND DELETE JOB ASSETS*/
jb.viewDelAssetsTableFunctions = function(e){
	var row = e.target.parentNode.parentNode.rowIndex;
	/*MUST USE PREVENT DEFAULT TO PREVENT DEFAULT ANCHOR ELEMENT BEHAVIOR */
	e.preventDefault();

	if(e.target.nodeName.toLowerCase() == 'a'){
		window.open(e.target.href,'_blank');
	}

	else if(e.target.value == 'Delete'){
		var data = {};
		data.flag = 'deljobasset';
		data.assetId = e.target.id;

		data = JSON.stringify(data);

		Util.msgBox({
			heading: {text: 'WARNING', background: 'orange'},
			body: {text: 'You are about to delete this asset. It will not be recoverable. If this is what you want to do click "Okay" Othewise click "Cancel'},
			leftbtn: {text: 'Okay', background: 'green', display: 'block'},
			rightbtn: {text: 'Cancel', background: 'red', display: 'block'}
		});

		Util.addLis(Util.getEl('#leftbtn')[0], 'click', function(){
			Util.closeMsgBox();
			Util.sendRequest('../xhr/routes.php', function(res){
				var response = JSON.parse(res.responseText);
				if(response.masterstatus === 'error'){
					Util.msgBox({
						heading: {text: 'ERROR', background: 'red'},
						body: {text: response.msg}
					});
					setTimeout(function(){Util.closeMsgBox()}, 3000);
				}
				else if (response.masterstatus === 'success'){
					/* IF EVERYTHING IS OKAY RELOAD THE TABLE */
					jb.viewJobAssetTable();
				}
				
			},data);
		});

		Util.addLis(Util.getEl('#rightbtn')[0], 'click', function(){
			Util.closeMsgBox();
		});

	
		/* IF THE OK BUTTON IS CLICKED DELETE ACCOUNT FROM DATABASE AND ALL ASSETS
		Util.checkElementAddListener('ok','click', function(){
			gen.closeMessageBox();
			Util.sendRequest('../../xhr/routes.php', function(res){
				//console.log(res.responseText);
				var dataArr = res.responseText.split('^^^');
				if(dataArr[0]=='success'){
					gen.displayMessageBoxTimed('green','Success', dataArr[1],'2000');
					Util.di('accountAssetTable').deleteRow(row);
				}
				else{
					console.log(res.responseText);
					gen.displayMessageBoxTimed('red','Error', dataArr[1],'2000');
				}
			},data);
			
		});
		/* IF THE CANCEL BUTTON IS CLICKED DO NOTHING AND CLOSE THE MESSAGE BOX 
		Util.checkElementAddListener('cancel','click',gen.closeMessageBox);*/

	}
	
}
jb.displayJobAssetForm = function(){
	Util.getEl('#addjobassetform')[0].style.display = 'block';
	Util.addLis(Util.getEl('#addjobassetBtn')[0],'click',jb.addJobAsset);
}

jb.addJobAsset = function(){
	var data = {}
	data.flag = 'addjobasset';
	data.masterstatus = 'checking';
	
	/* NEED TO GET THE JOB ID */
	data.jobId = Util.getEl('#joblist')[0].value;
	data.elements = [
		{id: 'name', regex: 'name', msg: 'Name cannot be blank and must have a valid name (letters, numbers and spaces only)', value: Util.getEl('#name')[0].value, status: "checking"},
		{id: 'file', msg: '', status: "checking"}
	]

	data = JSON.stringify(data);

	var formData = new FormData();
	
	/* APPEND THE FILE NAME AND FILE*/
	formData.append('file', Util.getEl('#file')[0].files[0]);
	formData.append('data', data);
	
	Util.msgBox({
		heading: {text: 'UPLOADING', background: 'green'},
		body: {text: 'Uploading file please wait...'}
	})

	Util.sendRequest('../xhr/routes.php', function(res){
		Util.closeMsgBox();
		
		gen.clearErrors();

		/* CREATE OBJECT FROM STRING SENT FROM SERVER */
		var response = JSON.parse(res.responseText);


		/* DEPENDING ON WHAT WAS SENT BACK FROM THE SERVER DISPLAY APPROPRIATE MESSAGE.  I USE THE DISPLAYMESSAGEBOXES METHOD TO DISPLAY A COMMON WAY OF DISPLAYING MESSAGES, THE FIRST PARAMETER IS THE RESPONSE OBJECT THE SECOND IS AN ARRAY OF FORM ELEMENT IDS, IF THERE ARE NO FORM ELEMENT ID'S THEN THE ARRAY IS EMPTY */
		gen.displayMessageBoxes(response,['name','file'],function(){
			Util.getEl('#addjobassetform')[0].style.display = 'none';
			Util.getEl('#acctlist')[0].value = 0;
			Util.getEl('#displayJobList')[0].innerHTML = "";
			Util.getEl('#addjobassetform')[0].innerHTML = "";
		});


		/*gen.closeMessageBox();
		var dataArr = res.responseText.split('^^^');
		if(dataArr[0] == 'success'){
			gen.displayMessageBoxTimed('green','Success',dataArr[1],'2000');
			Util.di('file').value = '';
			Util.di('name').value = '';
			Util.di('addjobassetform').style.display = 'none';
			Util.di('acctlist').value = 0;
			Util.di('jblst').value = 0
		}
		else {
			console.log(res.responseText)
			gen.displayMessageBoxTimed('red','Error',dataArr[1],'2000');
		}*/
	},formData, true);
}

jb.addJobForm = function(e){
	if(e.target.value == 0){
		jb.checkAccountSelection();
	}
	else {
		Util.getEl('#addjobform')[0].style.display = 'block';
		Util.addLis(Util.getEl('#addjobBtn')[0],'click', jb.addjob);
	}
}

jb.addjob = function(){
	var data = {}
	data.flag = 'addjob';
	data.accountId = Util.getEl('#acctlist')[0].value;
	data.elements = [
		{regex:'name', id:'name', msg: 'Name cannot be empty and must be a valid name', status: 'checking', duplicate: true, value: Util.getEl('#name')[0].value},
	];

	data = JSON.stringify(data);

	Util.sendRequest('../xhr/routes.php', function(res){
		var response = JSON.parse(res.responseText);
		/* DEPENDING ON WHAT WAS SENT BACK FROM THE SERVER DISPLAY APPROPRIATE MESSAGE.  I USE THE DISPLAYMESSAGEBOXES METHOD TO DISPLAY A COMMON WAY OF DISPLAYING MESSAGES, THE FIRST PARAMETER IS THE RESPONSE OBJECT THE SECOND IS AN ARRAY OF FORM ELEMENT IDS, IF THERE ARE NO FORM ELEMENT ID'S THEN THE ARRAY IS EMPTY */
		
		gen.displayMessageBoxes(response,['name'], function(){
			/* IF EVERYTHING IS OKAY THEN RELOAD PAGE.*/
			window.location.reload();
		});

	}, data);
}

jb.displayAddJobHoursForm = function(){
	Util.getEl('#addhoursform')[0].style.display = 'block';
	Util.addLis(Util.getEl('#addjobhoursBtn')[0],'click', jb.addJobHours);
}

jb.addJobHours = function(){
	var data = {}, i = 0;
	data.flag = "addhours";
	data.jobId = Util.getEl('#joblist')[0].value;
	
	data.elements = [
		{id: 'jobDate' , regex: 'timestamp' , msg: 'You must select a date' ,status: 'checking'},
		{id: 'hours' , regex: 'hours' , msg: 'You must enter the hours.  Use a decimal for partial hours (ex 1 and 1/2 hours is 1.5)' ,status: 'checking'},
		{id: 'hourlyRate' , regex: 'hourlyrate' , msg: 'You must and hourly rate.  Just include the number no decimals' ,status: 'checking'},
		{id: 'description' , regex: 'text', msg: 'You must enter a description' ,status: 'checking'}
	]

	/* GET THE VALUES FOR ALL THE ELEMENTS */
	while(i < data.elements.length){
		data.elements[i].value = Util.getEl('#' + data.elements[i].id)[0].value;
		i++;
	}

	/* CONVERT SELECTED DATE TO TIME STAMP. NOTE: TIMESTAMP INCLUDES MILLISECONDS WHEN PHP DATE FORMAT DOES NOT.  IF YOU ARE TO CONVERT THIS TIMESTAMP USING PHP DATE OBJECT YOU MUST DIVIDE BY 1000 */
	data.elements[0].value = new Date(data.elements[0].value.replace(/-/g, ',')).getTime();

	data = JSON.stringify(data);

	Util.sendRequest('../xhr/routes.php', function(res){
		
		/* CLEAR AN PAST ERROR MESSAGES */
		gen.clearErrors()

		var response = JSON.parse(res.responseText);

		/* DEPENDING ON WHAT WAS SENT BACK FROM THE SERVER DISPLAY APPROPRIATE MESSAGE.  I USE THE DISPLAYMESSAGEBOXES METHOD TO DISPLAY A COMMON WAY OF DISPLAYING MESSAGES, THE FIRST PARAMETER IS THE RESPONSE OBJECT THE SECOND IS AN ARRAY OF FORM ELEMENT IDS, IF THERE ARE NO FORM ELEMENT ID'S THEN THE ARRAY IS EMPTY */
		
		gen.displayMessageBoxes(response,['jobDate','hours','hourlyRate','description'], function(){
			/* IF EVERYTHING IS OKAY THEN HIDE JOB LIST AND JOB HOURS FORM, ALSO RESET ACCOUNTS.*/
			Util.getEl('#acctlist')[0].value = 0;
			Util.getEl('#displayJobList')[0].innerHTML = "";
			Util.getEl('#addhoursform')[0].style.display = "none";
	
		});
	}, data);
}

jb.getJobHours = function(){
	var data = {};
	data.flag = "getjobhours";
	data.jobId = Util.getEl('#joblist')[0].value;

	data = JSON.stringify(data);
	
	Util.sendRequest('../xhr/routes.php', function(res){
		var response = JSON.parse(res.responseText);

		console.log(response);

		if(response.masterstatus === 'success'){
			Util.getEl('#updateDeleteHours')[0].innerHTML = response.table;
		}
		else if(response.masterstatus === 'error'){
			Util.getEl('#updateDeleteHours')[0].innerHTML = "response.msg";
		}
	}, data);

	
}

jb.updateDeleteJobHours = function(e){
	if(e.target.value == 'Update'){
		var i = 0, data = {}
		data.flag = 'gethoursupdateform';
		data.hourId = e.target.id;

		data = JSON.stringify(data);

		
		/* IF UPDATE BUTTON IS CLICKED SHOW THE UPDATED FORM POPULATED WITH DATA FROM DATABASE */
		Util.sendRequest('../xhr/routes.php', function(res){
			var response = JSON.parse(res.responseText);
			if(response.masterstatus === 'error'){
				Util.msgBox({
					heading: {text: 'ERROR', background: 'red'},
					body: {text: response.msg}
				});
			}
			else if (response.masterstatus === 'success'){
				
				/* IF THE RESPONSE IS SUCCESSFUL THEN DISPLAY THE FORM POPULATED WITH THE DATA*/
				Util.getEl('#updateDeleteHours')[0].innerHTML = response.form;

				/* SET THE FORM TO DISPLAY BLOCK SO IT WILL BE SEEN*/
				Util.getEl('#updateHoursForm')[0].style.display = "block";

				/* ADD EVENT LISTENER TO THE UPDATE BUTTON */
				Util.addLis(Util.getEl('#updatejobhoursBtn')[0], 'click', function(){
					
					var i = 0;


					/*BECAUSE THE DATA WAS CONVERTED TO A STRING FOR THE AJAX REQUEST I HAVE TO CHANGE IT TO AN OBJECT AGAIN BEFORE I CAN ADD THE PROPERTIES */
					data = JSON.parse(data);
					data.flag = "updatehours";
										
					data.elements = [
						{id: 'jobDate' , regex: 'timestamp' , msg: 'You must select a date' ,status: 'checking'},
						{id: 'hours' , regex: 'hours' , msg: 'You must enter the hours.  Use a decimal for partial hours (ex 1 and 1/2 hours is 1.5)' ,status: 'checking'},
						{id: 'hourlyRate' , regex: 'hourlyrate' , msg: 'You must and hourly rate.  Just include the number no decimals' ,status: 'checking'},
						{id: 'description' , regex: 'text', msg: 'You must enter a description' ,status: 'checking'}
					]

					/* GET THE VALUES FOR ALL THE ELEMENTS */
					while(i < data.elements.length){
						data.elements[i].value = Util.getEl('#' + data.elements[i].id)[0].value;
						i++;
					}

					/* CONVERT SELECTED DATE TO TIME STAMP. NOTE: TIMESTAMP INCLUDES MILLISECONDS WHEN PHP DATE FORMAT DOES NOT.  IF YOU ARE TO CONVERT THIS TIMESTAMP USING PHP DATE OBJECT YOU MUST DIVIDE BY 1000 */
					data.elements[0].value = new Date(data.elements[0].value.replace(/-/g, ',')).getTime();

					data = JSON.stringify(data);

					Util.sendRequest('../xhr/routes.php', function(res){
												
						/* CLEAR AN PAST ERROR MESSAGES */
						gen.clearErrors()

						var response = JSON.parse(res.responseText);

						/* IF ALL IS GOOD HIDE THE FORM.  I ADDED THIS HERE BECAUSE THE DISPLAYMESSAGEBOXES FUNCTION WOULD SHOW UP UNDERNEATH THE FORM AS THE FORM WOULD NOT CLOSE UNTIL THE CALLBACK WAS FIRED.*/
						if(response.masterstatus === 'success'){
							Util.getEl('#updateHoursForm')[0].style.display = "none";

							/* GET THE JOB ID AND SEND AN AJAX REQUEST TO RELOAD THE TABLE*/
							
							/*BECAUSE THE DATA WAS CONVERTED TO A STRING FOR THE AJAX REQUEST I HAVE TO CHANGE IT TO AN OBJECT AGAIN BEFORE I CAN ADD THE PROPERTIES */
							data = JSON.parse(data);
							data.flag = "getjobhours";
							data.jobId = Util.getEl('#joblist')[0].value;

							data = JSON.stringify(data);

							/* AJAX REQUEST TO UPDATE THE TABLE TO SHOW THE CHANGES. */
							Util.sendRequest('../xhr/routes.php', function(res){
								var response = JSON.parse(res.responseText);
								if(response.masterstatus === 'success'){
									Util.getEl('#updateDeleteHours')[0].innerHTML = response.table;
								}
								else {
									Util.getEl('#updateDeleteHours')[0].innerHTML = response.msg;
								}
							}, data);
						}

						/* DEPENDING ON WHAT WAS SENT BACK FROM THE SERVER DISPLAY APPROPRIATE MESSAGE.  I USE THE DISPLAYMESSAGEBOXES METHOD TO DISPLAY A COMMON WAY OF DISPLAYING MESSAGES, THE FIRST PARAMETER IS THE RESPONSE OBJECT THE SECOND IS AN ARRAY OF FORM ELEMENT IDS, IF THERE ARE NO FORM ELEMENT ID'S THEN THE ARRAY IS EMPTY */
						gen.displayMessageBoxes(response,['jobDate','hours','hourlyRate','description']);
					}, data);


				});//end add listener
			}//end else if

		}, data);
	}

	else if(e.target.value == 'Delete'){
		//var row = e.target.parentNode.parentNode.rowIndex;
		var data = {};
		data.flag = "deletehours";
		data.hourId = e.target.id;
		
		data = JSON.stringify(data);

		Util.msgBox({
			heading: {text: 'WARNING', background: 'orange'},
			body: {text: 'You are about to delete this hour. It will not be recoverable. If this is what you want to do click "Ok" Otherwise click "Cancel"'},
			leftbtn: {text: 'Okay', background: 'green', display: 'block'},
			rightbtn: {text: 'Cancel', background: 'red', display: 'block'}
		});

		/* IF THE OKAY BUTTON IS CLICK THEN DELETE THE JOB HOUR SELECTED FROM THE DATABASE*/
		Util.addLis(Util.getEl('#leftbtn')[0], 'click', function(){
			Util.closeMsgBox();		
			
			Util.sendRequest('../xhr/routes.php', function(res){
				
				var response = JSON.parse(res.responseText);

				if(response.masterstatus === 'success'){
					
					/*BECAUSE THE DATA WAS CONVERTED TO A STRING FOR THE AJAX REQUEST I HAVE TO CHANGE IT TO AN OBJECT AGAIN BEFORE I CAN ADD THE PROPERTIES */
					data = JSON.parse(data);
					data.flag = "getjobhours";
					data.jobId = Util.getEl('#joblist')[0].value;

					data = JSON.stringify(data);

					/* IF ALL IS GOOD THEN SEND AJAX REQUEST TO UPDATE THE TABLE TO SHOW THE CHANGES.  OR IF THERE ARE NO MORE JOB HOURS ENTERED SHOW THAT THEIR ARE NO MORE JOB HOURS FOR THE JOB */
					Util.sendRequest('../xhr/routes.php', function(res){
						var response = JSON.parse(res.responseText);
						if(response.masterstatus === 'success'){
							Util.getEl('#updateDeleteHours')[0].innerHTML = response.table;
						}
						else {
							Util.getEl('#updateDeleteHours')[0].innerHTML = response.msg;
						}
					}, data);
				}
				else if(response.masterstatus === 'error'){
					Util.msgBox({
						heading: {text: 'ERROR', background: 'red'},
						body: {text: response.msg}
					})
				}			
			}, data);
		});

		
		/* IF THE CANCEL BUTTON IS CLICKED THEN CLOSE THE MESSAGE BOX */
		Util.addLis(Util.getEl('#rightbtn')[0], 'click', function(){
			Util.closeMsgBox();
		});
	
	}
}

jb.displayInvoiceForm = function(){
	Util.getEl('#invoiceForm')[0].style.display = "block";
	Util.addLis(Util.getEl('#getinvoiceBtn')[0], 'click', jb.getInvoice);
}

jb.getInvoice = function(){
	/* HERE I DID TWO JAVASCRIPT CHECKS.  THE REASON I DID NOT CHECK ON THE SERVER IS BECAUSE I AM SENDING GET STATEMENTS WHICH CAN BE EASILY MANIPULATED SO I HAVE THE CHECKS ALSO ON THE BACKEND PAGE INVOICE.PHP */
	
	/* THIS CHECKS TO MAKE SURE THE BEGINNING AND ENDING DATES ARE NOT BLANK */
	if(Util.getEl('#begdate')[0].value == '' || Util.getEl('#enddate')[0].value == ''){
		Util.msgBox({
			heading: {text: 'ERROR', background: 'red'},
			body: {text: 'You must select a beginning date and ending date'}
		})

		setTimeout(function(){Util.closeMsgBox();}, 2000);
	}

	/* THIS CHECKS TO MAKE SURE THE ENDING DATE IS GREATER THEN THE BEGINNING DATE */
	else if(Util.getEl('#enddate')[0].value < Util.getEl('#begdate')[0].value){
		Util.msgBox({
			heading: {text: 'ERROR', background: 'red'},
			body: {text: 'Ending date must be greater then beginning date'}
		})

		setTimeout(function(){Util.closeMsgBox();}, 2000);
	}
	
	/* IF ALL GOES WELL THEN SEND THE ID, AND DATES (IN TIMESTAMP FORMAT) TO THE INVOICE.PHP PAGE. */
	else {
		
		var data = {};
		data.id = Util.getEl('#joblist')[0].value;
		data.begdate = new Date(Util.getEl('#begdate')[0].value.replace(/-/g,',')).getTime();
		data.enddate = new Date(Util.getEl('#enddate')[0].value.replace(/-/g,',')).getTime();

		window.open("http://198.211.114.100/job_tracker_student_version_php/invoice.php?id="+data.id+"&begdate="+data.begdate+"&enddate="+data.enddate, '_blank');

	}
}

jb.displayAddJobNoteForm = function(){
	if(Util.getEl('#joblist')[0].value == 0){
		jb.checkJobSelection();
		Util.getEl('#addjobnoteform')[0].style.display = "none";
	}
	else {
		Util.getEl('#addjobnoteform')[0].style.display = "block";
	}
	
}

jb.addJobNote = function(){
	var data = {}, i = 0;
	data.flag = 'addjobnote';
	data.jobid = Util.getEl('#joblist')[0].value;

	data.elements = [
		{id: 'jobDate', regex: 'timestamp', msg: 'You must select a date'},
		{id: 'notename', regex: 'name', msg: 'You must enter a name and is should be letters, spaces or number only.'},
		{id: 'note', regex: 'text', msg: 'You must enter job note and it cannot contain special characters.'}
	]

	while(i < data.elements.length){
		data.elements[i].value = Util.getEl('#' + data.elements[i].id)[0].value;
		i++;
	}

	/* CONVERT SELECTED DATE TO TIME STAMP. NOTE: TIMESTAMP INCLUDES MILLISECONDS WHEN PHP DATE FORMAT DOES NOT.  IF YOU ARE TO CONVERT THIS TIMESTAMP USING PHP DATE OBJECT YOU MUST DIVIDE BY 1000 */
	data.elements[0].value = new Date(Util.getEl('#jobDate')[0].value.replace(/-/g,',')).getTime();
	
	data = JSON.stringify(data);
	Util.sendRequest('../xhr/routes.php',function(res){
		
		/* CLEAR ANY PREVIOUS FORM FIELD ERRORS */
		gen.clearErrors();

		/* CREATE OBJECT FROM STRING SENT FROM SERVER */
		var response = JSON.parse(res.responseText);
		
		/* DEPENDING ON WHAT WAS SENT BACK FROM THE SERVER DISPLAY APPROPRIATE MESSAGE.  I USE THE DISPLAYMESSAGEBOXES METHOD TO DISPLAY A COMMON WAY OF DISPLAYING MESSAGES, THE FIRST PARAMETER IS THE RESPONSE OBJECT THE SECOND IS AN ARRAY OF FORM ELEMENT IDS, IF THERE ARE NO FORM ELEMENT ID'S THEN THE ARRAY IS EMPTY */
		gen.displayMessageBoxes(response,['jobDate','notename','note']);
	},data);
}

jb.displayViewJobNoteTable = function(){
	var data = {};
	data.flag = 'getjobnotetable';
	data.jobid = Util.getEl('#joblist')[0].value;
	data = JSON.stringify(data);

	Util.sendRequest('../xhr/routes.php', function(res){
		var response = JSON.parse(res.responseText);
		if(response.masterstatus === 'success'){
			Util.getEl('#jobnotetable')[0].innerHTML = response.table;
		}	
		else if(response.masterstatus === 'error'){
			Util.getEl('#jobnotetable')[0].innerHTML = response.msg;
		}	

	},data);

}

jb.updateDeleteJobNote = function(e){
	if(e.target.value == 'Delete'){
		var data = {}
		data.flag = 'deletenote';

		Util.msgBox({
			heading: {text: 'WARNING', background: 'orange'},
			body: {text: 'You are about to delete this job note. It will not be recoverable. If this is what you want to do click "Okay" Otherwise click "Cancel"'},
			leftbtn: {text: 'Okay', background: 'green', display: 'block'},
			rightbtn: {text: 'Cancel', background: 'red', display: 'block'}
		})

				
		Util.addLis(Util.getEl('#leftbtn')[0], 'click', function(){
			Util.closeMsgBox();

			data.id = e.target.id;
			data = JSON.stringify(data);

			Util.sendRequest('../xhr/routes.php', function(res){
				console.log(res.responseText);
				var response = JSON.parse(res.responseText);

				if(response.masterstatus === 'success'){
					/* IF EVERTHING IS OKAY THEN RELOAD THE TABLE */
					jb.displayViewJobNoteTable();
				}
				else if(response.masterstatus === 'Error'){
					Util.msgBox({
						heading: {text: 'ERROR', background: 'red'},
						body: {text: response.msg},
					});
					setTimeout(function(){Util.closeMsgBox()}, 2000);
				}
				
			}, data);
		});

		Util.addLis(Util.getEl('#rightbtn')[0], 'click', function(){
			Util.closeMsgBox()
		});

	}
	/* THIS FUNCTION JUST LOADS THE FORM WITH THE NOTE DATA PRE-POPULATED */
	else if(e.target.value == 'Update'){
		var data = {};
		data.flag = "updatenoteform"
		data.noteId = e.target.id;

		data = JSON.stringify(data);

		Util.sendRequest('../xhr/routes.php',function(res){
			var response = JSON.parse(res.responseText);
			if(response.masterstatus === 'success'){
				Util.getEl('#updateNoteForm')[0].innerHTML = response.form;
				
				/* SHOW THE UPDATE NOTE FORM */
				Util.getEl('#updateNoteForm')[0].style.display = 'block';

				/* ADD EVENT LISTENER TO THE BUTTON ON THE POPUP FORM */
				Util.addLis(Util.getEl('#updatejobnoteBtn')[0], 'click', jb.updateJobNoteProc);
				
				/* DISABLE THE TABLE BUTTONS. THIS IS DONE SO THE USER CANNOT PRESS A BUTTON WHILE THE FORM IS DISPLAYED*/
				jb.disableJobNoteTableButtons();
			}
			else if(response.masterstatus === 'error'){
				Util.msgBox({
					heading: {text: 'ERROR', background: 'red'},
					body: {text: response.msg},
					rightbtn: {text: 'Okay', background: 'green', display: 'block'}
				});
				Util.addLis(Util.getEl('#rightbtn')[0], 'click', function(){
					Util.closeMsgBox();
				});
			}

		},data)
	}
}


/* THIS ACTUALLY DOES THE PROCESS OF UPDATING THE JOB NOTE*/
jb.updateJobNoteProc = function(e){
	var data = {}, i = 0;
	data.flag = 'updatenote';
	data.noteId = e.target.name;
	data.elements = [
		{id: 'jobDate', regex: 'timestamp', msg: 'You must select a date'},
		{id: 'notename', regex: 'name', msg: 'You must enter a name and is should be letters, spaces or number only.'},
		{id: 'note', regex: 'text', msg: 'You must enter job note and it cannot contain special characters.'}
	]
	while(i < data.elements.length){
		data.elements[i].value = Util.getEl('#' + data.elements[i].id)[0].value;
		i++;
	}

	/* CONVERT SELECTED DATE TO TIME STAMP */
	data.elements[0].value = new Date(Util.getEl('#jobDate')[0].value.replace(/-/g,',')).getTime();

	data = JSON.stringify(data);

	Util.sendRequest('../xhr/routes.php', function(res){
		
		/* CLEAR AN PRE EXISTING ELEMENT FIELD ERRORS */
		gen.clearErrors();

		var response = JSON.parse(res.responseText);

		/* IF THERE ARE FIELDERRORS THEN DO NOT MAKE THE POP UP FORM DISAPPAER*/
		if(response.masterstatus === 'fielderrors'){
			/* DEPENDING ON WHAT WAS SENT BACK FROM THE SERVER DISPLAY APPROPRIATE MESSAGE.  I USE THE DISPLAYMESSAGEBOXES METHOD TO DISPLAY A COMMON WAY OF DISPLAYING MESSAGES, THE FIRST PARAMETER IS THE RESPONSE OBJECT THE SECOND IS AN ARRAY OF FORM ELEMENT IDS, IF THERE ARE NO FORM ELEMENT ID'S THEN THE ARRAY IS EMPTY */
			gen.displayMessageBoxes(response,['jobDate','notename','note']);
		}
		/* IF THERE ARE NO FIELD ERRORS THEN MAKE THE POP UP FORM DISAPPEAR */
		else {
			/* HIDE THE UPDATE NOTE FORM */
			Util.getEl('#updateNoteForm')[0].style.display = 'none';

			/* ENABLE THE TABLE BUTTONS */
			jb.enableJobNoteTableButtons();

			gen.displayMessageBoxes(response,['jobDate','notename','note'], function(){
				jb.displayViewJobNoteTable();
			});
		}
	}, data);

	

}

/* THIS DISABLES ALL THE JOB NOTE TABLE BUTTONS */
jb.disableJobNoteTableButtons = function(){
	var tablebtns = Util.getEl('#notesTable input[type="button"]'), i = 0;
	while(i < tablebtns.length){
		tablebtns[i].disabled = true;
		i++;
	}
}

/*THIS ENABLES THE JOB NOTE TABLE BUTTONS */
jb.enableJobNoteTableButtons = function(){
	var tablebtns = Util.getEl('#notesTable input[type="button"]'), i = 0;
	while(i < tablebtns.length){
		tablebtns[i].disabled = false;
		i++;
	}
}


/* THIS DISABLES ALL THE JOB HOURS TABLE BUTTONS */
jb.disableJobHourTableButtons = function(){
	var tablebtns = Util.getEl('#hoursTable input[type="button"]'), i = 0;
	while(i < tablebtns.length){
		tablebtns[i].disabled = true;
		i++;
	}
}

/*THIS ENABLES THE JOB HOURS TABLE BUTTONS */
jb.enableJobHourTableButtons = function(){
	var tablebtns = Util.getEl('#hoursTable input[type="button"]'), i = 0;
	while(i < tablebtns.length){
		tablebtns[i].disabled = false;
		i++;
	}
}


/*THIS FUNCTION WILL DISPLAY THE CONTACTS TABLE FOR THE JOB SELECTED, IF THERE ARE NOT CONTACTS FOR THE JOB THEN A MESSAGE WILL APPEAR STATING THAT. */
jb.displayJobContactsTable = function(){
	var data = {};
	data.flag = "getjobcontacts";
	data.jobId = Util.getEl('#joblist')[0].value;

	/* IF THE USER SELECTED THE SELECT JOB OPTION AN ERROR MESSAGE WILL APPEAR AND ANY TABLE THAT WAS DISPLAYED WILL BE REMOVED */
	if(data.jobId == 0){
		jb.checkJobSelection();
		Util.getEl('#contactTable')[0].innerHTML = "";
		return;
	}
	
	data = JSON.stringify(data);
	
	Util.sendRequest('../xhr/routes.php', function(res){
		var response = JSON.parse(res.responseText);
		if(response.masterstatus === 'success'){
			Util.getEl('#contactTable')[0].innerHTML = response.table;
		}
		else if(response.masterstatus === 'error'){
			Util.getEl('#contactTable')[0].innerHTML = response.msg;	
		}

	},data);
}

/* THIS FUCTION WILL MAKE SURE AN ACCOUNT IS SELECTED */
jb.checkAccountSelection = function(){
	Util.msgBox({
		heading: {text: 'ERROR', background: 'red'},
		body: {text: 'You must select an account'}
	})
	setTimeout(function(){
		Util.closeMsgBox();
	}, 2000);
}

/* THIS FUNCTION WILL MAKE SURE A JOB IS SELECTED */
jb.checkJobSelection = function(){
	Util.msgBox({
		heading: {text: 'ERROR', background: 'red'},
		body: {text: 'You must select a job'}
	})
	setTimeout(function(){
		Util.closeMsgBox();
	}, 2000);
}

jb.init();


