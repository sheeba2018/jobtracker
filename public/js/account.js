


"use strict";

var ac = {}

/* THE INIT FUNCTION IS USED TO SET EVERYTHING UP.  USUALLY IT ADDS ALL EVENTLISTENERS BUT IT CAN CALL A FUNCTION AS WELL*/
ac.init = function(){
	if(Util.getEl('#addaccount').length != 0){
		Util.addLis(Util.getEl('#addaccount')[0], 'click',ac.addaccount);
		
	}
		
	if(Util.getEl('#updateaccount').length != 0){
		gen.getAccountList(function(){
			Util.addLis(Util.getEl('#acctlist')[0], 'change', ac.getAccountInfo);
		});
		
	}

	if(Util.getEl('#addaccountasset').length != 0){
		gen.getAccountList(function(){
			Util.addLis(Util.getEl('#acctlist')[0], 'change', ac.displayAddAssetForm);
		});
		
	}

	/* IF I AM ON THE PAGE WITH THE ID OF VIEWASSETACCOUNTLIST THEN ADD A CHANGE FUNCTION TO THE ACCTLIST AND A CLICK EVENT TO THE VIEWDELASSETSTABLEFUNCTIONS */
	if(Util.getEl('#viewassetaccountlist').length != 0){
		gen.getAccountList(function(){
			Util.addLis(Util.getEl('#acctlist')[0], 'change', ac.viewDeleteAccountAssets);
			Util.addLis(Util.getEl('#viewdelassetstable')[0], 'click', ac.viewDelAssetsTableFunctions)
		});
		
	}
}

ac.viewDeleteAccountAssets = function(){
	var data = {}
	data.flag = 'viewdeleaccoutassets';
	data.id = Util.getEl('#acctlist')[0].value;

	data = JSON.stringify(data);

	Util.sendRequest('../xhr/routes.php', function(res){
		Util.getEl('#viewdelassetstable')[0].innerHTML = res.responseText;
	},data);
}

ac.viewDelAssetsTableFunctions = function(e){
	var row = e.target.parentNode.parentNode.rowIndex;
	e.preventDefault();
	if(e.target.nodeName.toLowerCase() == 'a'){
		window.open(e.target.href,'_blank');
	}
	else if(e.target.value == 'Delete'){
		var data = {};
		data.flag = 'delaccountasset';
		data.id = e.target.id;

		data = JSON.stringify(data);

		Util.msgBox({
			heading: {text: 'WARNING', background: 'orange'},
			body: {text: 'You are about to delete this asset. It will not be recoverable. If this is what you want to do click "Ok" Otherwise click "Cancel"'},
			leftbtn: {text: 'Okay', background: 'green', display: 'block'},
			rightbtn: {text: 'Cancel', background: 'red', display: 'block'}
		})

		/* IF THE OK BUTTON IS CLICKED DELETE ACCOUNT FROM DATABASE AND ALL ASSETS*/
		Util.addLis(Util.getEl('#leftbtn')[0], 'click', function(){

			Util.closeMsgBox();
			Util.sendRequest('../xhr/routes.php', function(res){
				var response = JSON.parse(res.responseText);

				/* IF THE RESPONSE EQUALS SUCCESS THEN DELETE THE TABLE ROW OTHEWISE DO NOT */
				if(response.masterstatus === 'success'){
					Util.getEl('#accountAssetTable')[0].deleteRow(row);
				}

				/* DEPENDING ON WHAT WAS SENT BACK FROM THE SERVER DISPLAY APPROPRIATE MESSAGE.  I USE THE DISPLAYMESSAGEBOXES METHOD TO DISPLAY A COMMON WAY OF DISPLAYING MESSAGES, THE FIRST PARAMETER IS THE RESPONSE OBJECT THE SECOND IS AN ARRAY OF FORM ELEMENT IDS, IF THERE ARE NO FORM ELEMENT ID'S THEN THE ARRAY IS EMPTY */
				gen.displayMessageBoxes(response, []);
				
			},data);
		});

		Util.addLis(Util.getEl('#rightbtn')[0], 'click', function(){
			Util.closeMsgBox();
		});
	}
}

ac.displayAddAssetForm = function(e){
	if(gen.checkAccountSelection(e)){
		Util.getEl('#addassetform')[0].style.display = "block";
		Util.addLis(Util.getEl('#addaccountassetBtn')[0], 'click', ac.addAssetToAccount);
	}
	else {
		Util.getEl('#addassetform')[0].style.display = "none";
	}	

	
}

ac.addAssetToAccount = function(){
	var data = {};
	data.flag = 'addassettoaccount';
	data.masterstatus = "checking"
	data.elements = [
		{id: 'name', regex: 'name', msg: 'Name cannot be blank and must have a valid name', value: Util.getEl('#name')[0].value, status: "checking"},
		{id: 'file', msg: '', status: "checking"}
	]
	data.id = Util.getEl('#acctlist')[0].value;
	
	data = JSON.stringify(data);
	
	var formData = new FormData();
	
	/* APPEND THE FILE NAME AND FILE*/
	formData.append('file', Util.getEl('#file')[0].files[0]);
	formData.append('data', data);

	Util.sendRequest('../xhr/routes.php', function(res){
		
		//console.log(res.responseText);
		/* CLEAR ANY PREVIOUS ERRORS */
		gen.clearErrors();

		/* CREATE OBJECT FROM STRING SENT FROM SERVER */
		console.log(res.responseText);
		var response = JSON.parse(res.responseText);


		/* DEPENDING ON WHAT WAS SENT BACK FROM THE SERVER DISPLAY APPROPRIATE MESSAGE.  I USE THE DISPLAYMESSAGEBOXES METHOD TO DISPLAY A COMMON WAY OF DISPLAYING MESSAGES, THE FIRST PARAMETER IS THE RESPONSE OBJECT THE SECOND IS AN ARRAY OF FORM ELEMENT IDS, IF THERE ARE NO FORM ELEMENT ID'S THEN THE ARRAY IS EMPTY */
		gen.displayMessageBoxes(response,['name','file']);

	},formData, true);
}

ac.addaccount = function(){
	var data = {}, i=0;
	
	/* CREATE AN ARRAY OF ALL THE ELEMENTS IDS, VALUES, MESSAGES, STATUS AND REGEX NAME*/
	data.elements = [
		{regex:'name', id:'name', msg: 'Name cannot be empty and must be a valid name', status: 'checking', duplicate: true},
		{regex:'address', id:'address', msg: 'Address cannot be empty and must be a valid address', status: 'checking'},
		{regex:'city', id: 'city', msg: 'City cannot be empty and must be a valid city', status: 'checking'},
		{regex:'state', id: 'state',msg: 'State must be a two letter uppercase abbreviation', status: 'checking'},
		{regex:'zip', id: 'zip',msg: 'Incorrect zip code', status: 'checking'}
	];

	data.masterstatus = 'checking';
	data.flag = 'addAccount';

	/* GET THE ELEMENT VALUES */
	while(i < data.elements.length){
		data.elements[i].value = Util.getEl('#' + data.elements[i].id)[0].value;
		i++;
	}
	data = JSON.stringify(data);
	

	Util.sendRequest('../xhr/routes.php', function(res){
		
		/* CLEAR ANY PREVIOUS ERRORS */
		gen.clearErrors();

		/* CREATE OBJECT FROM STRING SENT FROM SERVER */
		var response = JSON.parse(res.responseText);
		
		/* DEPENDING ON WHAT WAS SENT BACK FROM THE SERVER DISPLAY APPROPRIATE MESSAGE.  I USE THE DISPLAYMESSAGEBOXES METHOD TO DISPLAY A COMMON WAY OF DISPLAYING MESSAGES, THE FIRST PARAMETER IS THE RESPONSE OBJECT THE SECOND IS AN ARRAY OF FORM ELEMENT IDS, IF THERE ARE NO FORM ELEMENT ID'S THEN THE ARRAY IS EMPTY */
		gen.displayMessageBoxes(response,['name','address','city','state','zip']);

	}, data);
        
        
        
}


ac.getAccountInfo = function(e){
	var data = {};
	
	if(gen.checkAccountSelection(e)) {
		data.flag = 'getAccount'
		data.id = e.target.value;
		data = JSON.stringify(data);

		Util.sendRequest('../xhr/routes.php', function(res){
			
			/* CREATE OBJECT FROM STRING SENT FROM SERVER */
			var response = JSON.parse(res.responseText);

			/* DEPENDING ON WHAT WAS SENT BACK FROM THE SERVER DISPLAY APPROPRIATE MESSAGE */
			if(response.masterstatus === 'error'){
				Util.msgBox({
					heading: {text: 'ERROR', background: 'red'},
					body: {text: response.msg},
				})
			}

			else if(response.masterstatus === 'success'){
				Util.getEl('#updateform')[0].innerHTML = response.table;
				Util.addLis(Util.getEl('#updateaccountBtn')[0], 'click', ac.updateAccount);
			}
		}, data);
	}
}

ac.updateAccount = function(){
	var data = {}, i=0;

	
	/* CREATE AN ARRAY OF ALL THE ELEMENTS IDS, VALUES, MESSAGES, STATUS AND REGEX NAME*/
	data.elements = [
		{regex:'name', id:'name', msg: 'Name cannot be empty and must be a valid name', status: 'checking', duplicate: true},
		{regex:'address', id:'address', msg: 'Address cannot be empty and must be a valid address', status: 'checking'},
		{regex:'city', id: 'city', msg: 'City cannot be empty and must be a valid city', status: 'checking'},
		{regex:'state', id: 'state',msg: 'State must be a two letter uppercase abbreviation', status: 'checking'},
		{regex:'zip', id: 'zip',msg: 'Incorrect zip code', status: 'checking'},
		{regex:'skip', id: 'hiddenName',msg: '', status: 'checking'}
	];

	data.masterstatus = 'checking';
	data.flag = 'updateAccount';
	data.accountId = Util.getEl('#acctlist')[0].value;


	/* GET THE ELEMENT VALUES */
	while(i < data.elements.length){
		data.elements[i].value = Util.getEl('#' + data.elements[i].id)[0].value;
		i++;
	}

	data = JSON.stringify(data);

	Util.sendRequest('../xhr/routes.php', function(res){
		
		/* CLEAR ANY PREVIOUS ERRORS */
		gen.clearErrors();


		/* CREATE OBJECT FROM STRING SENT FROM SERVER */
		var response = JSON.parse(res.responseText);
		
		/* DEPENDING ON WHAT WAS SENT BACK FROM THE SERVER DISPLAY APPROPRIATE MESSAGE.  I USE THE DISPLAYMESSAGEBOXES METHOD TO DISPLAY A COMMON WAY OF DISPLAYING MESSAGES, THE FIRST PARAMETER IS THE RESPONSE OBJECT THE SECOND IS AN ARRAY OF FORM ELEMENT IDS, IF THERE ARE NO FORM ELEMENT ID'S THEN THE ARRAY IS EMPTY */
		gen.displayMessageBoxes(response,[]);

	}, data);

}

ac.getAccountTable = function(){
	var data = {};
		data.flag = 'getaccounttable';
		data = JSON.stringify(data);
		Util.sendRequest('../xhr/routes.php', function(res){
			Util.di('accounttable').innerHTML = res.responseText;
		}, data);
}

ac.init();
