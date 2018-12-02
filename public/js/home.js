"use strict"
var hm = {}

hm.init = function(){
	if(Util.di('accountlist')){
		hm.getAccountList();	
	}
}

hm.getAccountList = function(){
	var data = {};
	data.flag = 'getaccountlist';
	data = JSON.stringify(data);
	Util.sendRequest('../../xhr/routes.php', function(res){
		Util.di('accountlist').innerHTML = res.responseText;
		Util.checkElementAddListener('acctlist', 'change', hm.accountInfo);
	}, data);	
}

hm.accountInfo = function(e){
	if(e.target.value == "0"){
		gen.displayMessageBoxTimed('red','Error','You must select an account','2000');
	}
	else {
		var data = {};
		data.flag = "getAccountInfo";
		data.accId = e.target.value;
		data = JSON.stringify(data);
		Util.sendRequest('../../xhr/routes.php',function(res){
			var dataArr = res.responseText.split('^^^');
			if(dataArr[0] == 'success'){
				console.log(dataArr[1]);
				var data = JSON.parse(dataArr[1]);
				var output = `<div id="accountinfo"><h2>${data[0].name}</h2>
				<address>${data[0].address}<br>${data[0].city} ${data[0].state} ${data[0].zip}</div>
				<div id="btns"><input type="button" value="Jobs" class="btn btn-primary" id="jobs">
				<input type="button" value="Contacts" class="btn btn-primary" id="contacts">
				<input type="button" value="Assets" class="btn btn-primary" id="assets"></div>`;
				Util.di('account').innerHTML = output;
			}
			else{

			}
		},data);
	}
}

hm.init();