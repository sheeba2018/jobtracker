"use strict";
var gen = {}

/* RESPONSE IS THE RESPONSE OBJECT. FIELDSARR IS AN ARRAY OF FIELD ID VALUES THAT NEED TO BE CLEARED*/
gen.displayMessageBoxes = function(response, fieldsArr, cb){
	if(response.masterstatus === 'fielderrors'){
		gen.createErrorMsg(response.elements);
		$('[data-toggle="popover"]').popover();
	}
	else if(response.masterstatus === 'error'){
		Util.msgBox({
			heading: {text: 'ERROR', background: 'red'},
			body: {text: response.msg}
		})

		setTimeout(function(){
			Util.closeMsgBox();
		}, 2000);
	}
	
	else {
		
		Util.msgBox({
			heading: {text: 'SUCCESS', background: 'green'},
			body: {text: response.msg}
		})

		setTimeout(function(){
			Util.closeMsgBox();
			if(cb){
				cb();
			}
		}, 2000);
		
		/* IF EVERYTHING IS OKAY THEN CLEAR ALL THE FIELDS. I DO THIS BY CREATING AN ARRAY OF ALL THE FIELD ID NAMES AND THEN SENDING IT TO GEN.CLEARELEMENTFIELDS */
		gen.clearElementFields(fieldsArr);
	}
}

/*THIS CHECKS THE SELECTION OF THE ACCOUNT IF AN ACCOUNT IS NOT SELECTED THEN THE ERROR MESSSAGE IS DISPLAYED*/
gen.checkAccountSelection = function(e){
	if(e.target.value == 0){
		Util.msgBox({
			heading: {text: 'ERROR', background: 'red'},
			body: {text: 'You must select an account'},
			rightbtn: {text: 'Okay', background: 'green', display: 'block'}
		})
		Util.addLis(Util.getEl('#rightbtn')[0], 'click', function(){
			Util.closeMsgBox();
		});

		return false;
	}

	return true;
}

 
/* THIS FUNCTION GETS THE ACCOUNT LIST AS A DROPDOWN BOX AND ADDS INJECTS IT INTO THE DIV WITH THE ID OF ACCOUNTLIST */
gen.getAccountList = function(cb){
	var data = {};
	data.flag = 'getaccountlist';
	data = JSON.stringify(data);
	Util.sendRequest('../xhr/routes.php', function(res){
		var response = JSON.parse(res.responseText);
		if(response.masterstatus === "success"){
			Util.getEl('#accountlist')[0].innerHTML = response.accounts;
		}
		else if(response.masterstatus === "error"){
			Util.getEl('#accountlist')[0].innerHTML = response.msg;
		}
		
		/* I SEND A CALLBACK SO THAT I CAN ADD EVENT LISTENERS TO THE DROPDOWN BOX */
		cb();
	}, data);	
}

gen.getContactList = function(cb){
	var data = {};
	data.flag = 'getcontactlist';
	data = JSON.stringify(data);
	Util.sendRequest('../xhr/routes.php', function(res){
		var response = JSON.parse(res.responseText);
		if(response.masterstatus === "success"){
			Util.getEl('#contactlist')[0].innerHTML = response.accounts;
		}
		else if(response.masterstatus === "error"){
			Util.getEl('#contactlist')[0].innerHTML = response.msg;
		}
		
		/* I SEND A CALLBACK SO THAT I CAN ADD EVENT LISTENERS TO THE DROPDOWN BOX */
		cb();
	}, data);	
}

gen.clearElementFields = function(fieldsArr){
	var i = 0;
	while(i < fieldsArr.length){
		Util.getEl('#' + fieldsArr[i])[0].value = '';
		i++;
	}
}

gen.createErrorMsg = function(elements){
	var i = 0;
	while(i < elements.length){
		if(elements[i].status === 'error'){
			Util.getEl('[for=' + elements[i].id  + ']')[0].innerHTML += '<span class="glyphicon glyphicon-exclamation-sign error" aria-hidden="true" data-toggle="popover" title="Field Error" data-trigger="hover" data-content="' + elements[i].msg +'" >';
		}
		
		i++;
	}
}

gen.clearErrors = function(){
	var labels = Util.getEl('label'), i = 0;
	while(i < labels.length){
		if(labels[i].lastChild.nodeName.toLowerCase() === 'span'){
			labels[i].removeChild(labels[i].lastChild)
		}
		i++;
	}
}


/*gen.createErrorSpan = function(errorMsg){
	var span;
	var spanTxt;
	span = document.createElement('span');
	span.setAttribute('class','glyphicon glyphicon-exclamation-sign error');
	span.setAttribute('aria-hidden','true');
	span.setAttribute('data-toggle','popover');
	span.setAttribute('title','Field Error');
	span.setAttribute('data-trigger','hover');
	span.setAttribute('data-content',errorMsg);
	return span;
}*/

/* CLOSE MESSAGE BOX */
/*gen.closeMessageBox = function(){
	var bp = {};
	bp.boxElement = Util.di('ack');
	bp.titleElement = Util.di('ackheading');
	bp.bodyElement = Util.di('ackbody');
	Util.closeMessageBox(bp);
}*/