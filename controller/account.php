

<?php
/* ALL GET PAGE FUNCTIONS HERE */
function addAccountPage(){
	$pageData['base'] = "../";
	$pageData['title'] = "Add Account Page";
	$pageData['heading'] = "Job Tracker Add Account Page";
	$pageData['nav'] = true;
	$pageData['content'] = file_get_contents('views/admin/add_account.html');
	$pageData['js'] = "Util^general^account";
	$pageData['security'] = true;
	return $pageData;
}
function updateAccountPage(){
	$pageData['base'] = "../";
	$pageData['title'] = "Update Account Page";
	$pageData['heading'] = "Job Tracker Update Acount Page";
	$pageData['nav'] = true;
	$pageData['content'] = file_get_contents('views/admin/update_account.html');
	$pageData['js'] = "Util^general^account";
	$pageData['security'] = true;
	return $pageData;
}
function addAssetsAccountPage(){
	$pageData['base'] = "../";
	$pageData['title'] = "Add Assets Account Page";
	$pageData['heading'] = "Job Tracker Add Assets to Acount Page";
	$pageData['nav'] = true;
	$pageData['content'] = file_get_contents('views/admin/add_account_assets.html');
	$pageData['js'] = "Util^general^account";
	$pageData['security'] = true;
	return $pageData;
}
function viewDeleteAccountAsset(){
	$pageData['base'] = "../";
	$pageData['title'] = "View or Delete Account Asset";
	$pageData['heading'] = "Job Tracker View or Delete Account Asset Page";
	$pageData['nav'] = true;
	$pageData['content'] = file_get_contents('views/admin/view_delete_account_assets.html');
	$pageData['js'] = "Util^general^account";
	$pageData['security'] = true;
	return $pageData;
}
/* ALL XHR FUNCTIONS HERE */
function addAccount($dataObj){
	require_once '../classes/Validation.php';
	require_once '../classes/Pdo_methods.php';
	require_once '../classes/General.php';
	
	$validate = new Validation();
	
	$i = 0;
	$error = false;
	while($i < count($dataObj->elements)){
		if(!$validate->validate($dataObj->elements[$i]->regex, $dataObj->elements[$i]->value)){
			$error = true;
			$dataObj->elements[$i]->status = 'error';
		}
		$i++;
	}
	
	if($error){
		$dataObj->masterstatus = 'fielderrors';
		$data = json_encode($dataObj);
		echo $data;
	}
	else {
	
		$General = new General();
		$pdo = new PdoMethods();
		/* IF EVERYTHING IS VALID THEN CHECK FOR A DUPLICATE NAME 
		USE THE CHECKDUPLICATES METHOD FROM THE GENRERAL CLASS.  THE SECOND PARAMETER IS THE TABLE WE ARE GOING TO CHECK. IN THE METHOD BASED UPON THAT DECIDES WHAT QUERY WE USE. THE $PDO IS THE CONNECTION INSTANCE  SEE GENERAL METHOD FOR MORE INFO */
		
		$result = $General->checkDuplicates($dataObj, 'account', $pdo);
			
		/* BASED UPON THE RESULT CREATE A CUSTOM OBJECT CONTAINING THE MASTERSTATUS AND MESSAGE.  ON THE JAVASCRIPT END I CREATE A RESPONSE THAT BASED UPON WHAT IS SENT WILL DISPLAY A MESSAGE BOX SHOWING THE MESSAGE.  THIS IS A NICE WAY OF A CUSTOM MESSAGES BOX FOR THE USER BASED UPON WHAT HAD HAPPENED ON THE SERVER */
		if($result != 'error'){
			if(count($result) != 0){
				$response = (object) [
				    'masterstatus' => 'error',
				    'msg' => 'There is already an account by that name',
				  ];
				echo json_encode($response);
			}
			else {
				/* GET THE ACCOUNT NAME FROM THE DATAOBJ */
				$i = 0; $name = '';
				while($i < count($dataObj->elements)){
					if($dataObj->elements[$i]->id === 'name'){
						$name = $dataObj->elements[$i]->value;
						break;
					}
					$i++;
				}
				/* CREATE AND THE FOLDER BY ADDING THE NAME AND A TIMESTAMP TO KEEP IT UNIQUE */
				$foldername = $name.time();
				$foldername = str_replace(" ", "_", $foldername);
				$foldername = strtolower ($foldername);
				
				/* ADD THE FOLDER TO THE SERVER GIVE IT 777 PERMISSIONS */
				$path = '../public/account_folders/'.$foldername;
				$dir = mkdir($path, 0777);
				/* ADD ACCOUNT TO DATABASE 
				   IMPORTANT NOTE:  THE ORDER OF THE 	
				*/
				$sql = "INSERT INTO account (name, address, state, city, zip, folder) VALUES (:name, :address, :state, :city, :zip, :folder)";
				
				/* HERE I CREATE AN ARRAY THAT LISTS THE ELEMENT NAME, WHICH IS THE ID AND THE DATATYPE NEEDED BY PDO.  THEY ARE SEPERATED BY A ^^.  WHEN THIS IS RUN THROUGH THE CREATEBINDEDARRAY OF THE GENERAL CLASS, THAT MEHTHOD WILL CREATE A BINDED ARRAY*/
				$elementNames = array('name^^str','address^^str','state^^str','city^^str','zip^^str');
				
				/* CREATE BINDINGS NEEDED FOR PDO QUERY.  I CREATED A METHOD IN THE GENERAL CLASS THAT DOES THIS AUTOMATICALLY BY SENDING IN THE ELEMENTNAMES ARRAY AND THE DATAOBJ.  FOR THIS TO WORK YOU JUST HAVE THE CORRECT DATAOBJ STRUCTURE*/
				$bindings = $General->createBindedArray($elementNames, $dataObj);
				/* ADD THE FOLDER TO THE BINDINGS ARRAY*/
				array_push($bindings, array(':folder', $path, 'str'));
				
				/* IF THE DIRECTORY WAS CREATED THEN ADD TO THE DATABASE OTHERWISE SEND ERROR MESSAGE */
				if($dir){
					$result = $pdo->otherBinded($sql, $bindings);
										
					if($result == 'noerror'){
						$response = (object) [
					    	'masterstatus' => 'success',
					    	'msg' => 'The account has been added',
						];
						echo json_encode($response);
					}
                                        
					else {
						$response = (object) [
					    	'masterstatus' => 'error',
					    	'msg' => 'There was a problem adding the account',
						];
						echo json_encode($response);
					}
							
				}
				else {
					$response = (object) [
					    	'masterstatus' => 'error',
					    	'msg' => 'There was a problem making the directory',
						];
						echo json_encode($response);
				}
			}
		}
		else {
			$object = (object) [
				'masterstatus' => 'error',
				'msg' => 'There was an error with our sql statement',
			];
			echo json_encode($object);
		}
		
	}
}
function getAccountInfo($dataObj){
	require '../classes/Pdo_methods.php';
	$pdo = new PdoMethods();
	$sql = "SELECT * FROM account WHERE id=:id";
	$bindings = array(
		array(':id',$dataObj->id,'int'),
	);
	$records = $pdo->selectBinded($sql, $bindings);
	if($records == 'error'){
		$object = (object) [
			'masterstatus' => 'error',
			'msg' => 'There was an error with the sql statement',
		];
		echo json_encode($object);
	}
	else{
		if(count($records) != 0){
			
			$table = '<div class="row" style="margin-top: 20px">    
                <div class="col-md-6">
                <div class="form-group">
                  <label for="name">Name:</label>
                  <input type="text" class="form-control" id="name" name="name" value="'.$records[0]['name'].'">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="address">Address:</label>
                  <input type="text" class="form-control" name="address" id="address" value="'.$records[0]['address'].'">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label for="city">City:</label>
                  <input type="text" class="form-control" name="city" id="city" value="'.$records[0]['city'].'">
                </div>
              </div>
              <div class="row">
              <div class="col-md-1">
                <div class="form-group">
                  <label for="state">State:</label>
                  <input type="text" class="form-control" name="state" id="state" value="'.$records[0]['state'].'">
                </div>
              </div>
              <div class="row">
              <div class="col-md-2">
                <div class="form-group">
                  <label for="zip">Zip:</label>
                  <input type="text" class="form-control" name="zip" id="zip" value="'.$records[0]['zip'].'">
                  <input type="hidden" name="hiddenName" id="hiddenName" value="'.$records[0]['name'].'">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <input type="button" name="updateaccount" id="updateaccountBtn" class="btn btn-primary" value="Update Account" />
                </div>
              </div>
            </div>
            </div>';
            $object = (object) [
				'masterstatus' => 'success',
				'table' => $table,
			];
			echo json_encode($object);
            
		}
		else {
			$object = (object) [
				'masterstatus' => 'error',
				'msg' => 'No records found for that account',
			];
			echo json_encode($object);
		}
	}
}
function updateAccount($dataObj){
	/* THIS UPDATES THE DATABASE INFORMATION ONLY. THE FOLDER NAMES STAYS THE SAME. */
	require_once '../classes/Validation.php';
	require '../classes/Pdo_methods.php';
	require_once '../classes/General.php';
	
	$validate = new Validation();
	$pdo = new PdoMethods();
	$General = new General();
	$i = 0; $error = false;
	while($i < count($dataObj->elements)){
		if(!$validate->validate($dataObj->elements[$i]->regex, $dataObj->elements[$i]->value)){
			$error = true;
			$dataObj->elements[$i]->status = 'error';
		}
		$i++;
	}
	
	if($error){
		$dataObj->masterstatus = 'fielderrors';
		$data = json_encode($dataObj);
		echo $data;
	}
	else {
		
		/* IF EVERYTHING IS VALID THEN CHECK FOR A DUPLICATE NAME 
		HERE I LOOP THROUGH THE DATA ELEMENTS AND GET THE NAME THAT WAS ENTERED AND STORE THAT INTO A VARIABLE OF NEWNAME */
		foreach($dataObj->elements as $element){
			if($element->id === 'name'){
				$newName = $element->value;
				break;
			}
		}
		/* HERE I LOOP THROUGH DATA ELEMENTS AND GET THE ORIGINAL NAME THAT IS STORED IN A HIDDEN FIELD ON THE FORM. IT CONTAINS THE LAST NAME USED FOR THE ACCOUNT.*/
		foreach($dataObj->elements as $element){
			if($element->id === 'hiddenName'){
				$origName = $element->value;
				break;
			}
		}
		/* I COMPARE BOTH NAMES AND IF THEY ARE THE SAME THEN I MOVE ON SETTING THE RESULT TO AN EMPTY ARRAY WHICH IS WHAT THE CHECK DUPLICATES FUNCTION WILL RETURN IF THERE ARE NO DUPLICATE NAMES FOUND IN THE DATABASE.  I HAVE TO DO THIS BECAUSE TO ELIMINATE THE ERROR IF THE USER DID NOT CHANGE THE AND I RAN CHECK DUPCLIATES IT WOULD THINK THERE WAS A DUPLICATE NAME WHEN THERE WAS NOT.  OPTIONALLY I COULD HAVE SENT THE RECORD ID WITH THE NAME AND COMPARED IT IN THE DATABASE.*/
		if($origName === $newName){
			$result = [];
		}
		
		/* IF THE ORIGNAME AND NEWNAME DO NOT MATCH THEN A CHECK DUPLIATE WILL BE RUN ON THE NEW NAME TO INSURE THAT IT IS NOT BEING USED BY ANOTHER ACCOUNT. */
		else {
			$result = $General->checkDuplicates($dataObj, 'account', $pdo);
		}
		if($result != "error"){
			if(count($result) != 0){
				$response = (object) [
				    'masterstatus' => 'error',
				    'msg' => 'There is already an account by that name',
				  ];
				echo json_encode($response);
			}
			else {
				/* HERE I CREATE AN ARRAY THAT LISTS THE ELEMENT NAME, WHICH IS THE ID AND THE DATATYPE NEEDED BY PDO.  THEY ARE SEPERATED BY A ^^.  WHEN THIS IS RUN THROUGH THE CREATEBINDEDARRAY OF THE GENERAL CLASS, THAT METHOD WILL CREATE A BINDED ARRAY*/
				$elementNames = array('name^^str','address^^str','state^^str','city^^str','zip^^str');
						
				/* CREATE BINDINGS NEEDED FOR PDO QUERY.  I CREATED A METHOD IN THE GENERAL CLASS THAT DOES THIS AUTOMATICALLY BY SENDING IN THE ELEMENTNAMES ARRAY AND THE DATAOBJ.  FOR THIS TO WORK YOU JUST HAVE THE CORRECT DATAOBJ STRUCTURE*/
				$bindings = $General->createBindedArray($elementNames, $dataObj);
				/* ADD THE ACCOUNTID TO THE BINDINGS ARRAY*/
				array_push($bindings, array(':accountId', $dataObj->accountId, 'str'));
				/* CREATE SQL STATEMENT AND ADD BINDINGS */
				$sql = "UPDATE account SET name=:name, address=:address, state=:state, city=:city, zip=:zip WHERE id=:accountId";
			    $result = $pdo->otherBinded($sql, $bindings);
				if($result = 'noerror'){
					$object = (object) [
						'masterstatus' => 'success',
						'msg' => 'Account has been updated'
					];
					echo json_encode($object);
				}
				else {
					$object = (object) [
						'masterstatus' => 'error',
						'msg' => 'There was a problem updating the account'
					];
					echo json_encode($object);
				}
			}
		}
		else {
			$object = (object) [
				'masterstatus' => 'error',
				'msg' => 'There was a problem updating the account'
			];
			echo json_encode($object);
		}
		
	}
}


/* Uodate AccountTable ***********/

function accountTable(){
	require '../classes/Pdo_methods.php';
	$pdo = new PdoMethods();
	$sql = "SELECT id, name FROM account";
	$records = $pdo->selectNotBinded($sql);
	if($records == 'error'){
    	echo 'There has been and error processing your request';
    }
    /* IF EMAIL AND PASSWORD EXIST THEN ALLOW ACCESS */
    else {
    	if(count($records) != 0){
        	$accounts = '<table class="table table-bordered table-striped" id="acctTable">';
        	$accounts .= '<thead><tr><th>Account Name</th><th>Delete</th></tr></thead><tbody>';
        	foreach ($records as $row) {
        		$accounts .= "<tr><td style='width: 90%'>".$row['name']."</td>";
        		$accounts .= "<td style='width: 10%'><input type='button' class='btn btn-danger' value='Delete' id='".$row['id']."'></td></tr>";
        	}
        	$accounts .= '</tbody></table>';
        	echo $accounts;
	    }
	    else {
	    	echo 'No Accounts found';
	    }
    }
}
function addAsset($dataObj, $file){
	require '../classes/Validation.php';
	$Validation = new Validation();
	if(!$Validation->validate($dataObj->elements[0]->regex, $dataObj->elements[0]->value)){
		$dataObj->masterstatus = 'fielderrors';
		$dataObj->elements[0]->status = 'error';
	}
	if(empty($_FILES)) {
        $dataObj->masterstatus = 'fielderrors';
        $dataObj->elements[1]->msg = 'You must select a file';
        $dataObj->elements[1]->status = 'error';
        echo json_encode($dataObj);
        return;
    } 
	//return;
	/* I HAD TO CREATE THE VARIABLES BECAUSE PHP DID NOT LIKE ME PASSING IT BY REFERENCE */
	$filename = $_FILES['file']['name'];
	$filesize = $_FILES['file']['size'];
	$filetype = $_FILES['file']['type'];
	$filetempname = $_FILES['file']['tmp_name'];
	/* CHECK FILE SIZE AND TYPE */
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
    if (finfo_file($finfo, $filetempname) !== "application/pdf"){
    	finfo_close($finfo);
    	$dataObj->masterstatus = 'fielderrors';
    	$dataObj->elements[1]->msg = 'File is wrong type';
        $dataObj->elements[1]->status = 'error';
        echo json_encode($dataObj);
    }
    else if ($filesize > 1000000){
    	$dataObj->masterstatus = 'fielderrors';
    	$dataObj->elements[1]->msg = 'File size is too big';
        $dataObj->elements[1]->status = 'error';
        echo json_encode($dataObj);
    }
    /* IF ALL IS GOOD THEN ADD FILE AND UPDATE DATABASE */
    else{
    	require_once '../classes/Pdo_methods.php';
		$pdo = new PdoMethods();
		/* GET THE FOLDER PATH FROM THE ACCOUNT DATABASE */
		$sql = "SELECT folder FROM account WHERE id = :id";
		/* SINCE THERE IS ONLY ONE BINDING I DID NOT  NEED TO USE THE GENERAL CLASS*/
		$bindings = array(
			array(':id',$dataObj->id,'int'),
		);
		$records = $pdo->selectBinded($sql, $bindings);
		foreach($records as $row){
			$folder = $row['folder'];
		}
		/* REMOVE ALL SPACES FROM THE FILE NAME AND ADD UNDERSCORES */
		$filename = str_replace(" ","_",$filename);
		$path = $folder."/".$filename;
		
		if(!move_uploaded_file($filetempname, $path)){
			$dataObj->masterstatus = 'fielderrors';
	    	$dataObj->elements[1]->msg = 'There was an problem with the file';
	        $dataObj->elements[1]->status = 'error';
	        echo json_encode($dataObj);
			exit;
		}
		$sql = "INSERT INTO account_asset (account_id, name, file) VALUES (:id, :name, :file)";
		$bindings = array(
			array(':id',$dataObj->id,'int'),
			array(':name',$dataObj->elements[0]->value,'str'),
			array(':file',$path,'str')
		);
		$result = $pdo->otherBinded($sql, $bindings);
		if($result == 'noerror'){
			$object = (object) [
				'masterstatus' => 'success',
				'msg' => 'Asset has been added'
			];
			echo json_encode($object);
			
		}
		else {
			$object = (object) [
				'masterstatus' => 'error',
				'msg' => 'There was an error adding the asset'
			];
			echo json_encode($object);
		}
    }
}

function viewDeleteAsset($dataObj){
	require_once '../classes/Pdo_methods.php';
	$pdo = new PdoMethods();
	$sql = "SELECT id, name, file FROM account_asset WHERE account_id = :id";
	$bindings = array(
		array(':id',$dataObj->id,'int'),
	);
	$records = $pdo->selectBinded($sql, $bindings);
	if(count($records) == 0){
		echo 'There are now assets for this account';
	}
	else{
		
            $table = '<table class="table table-bordered table-striped" id="accountAssetTable"><thead><tr><th>Name</th><th>Delete</th></tr></thead><tbody>';
	
            foreach($records as $row){
			$table .= '<tr><td style="width: 80%"><a href="../docs/'.$row['file'].'">'.$row['name'].'</a></td>';
			$table .= '<td style="width: 20%"><input type="button" class="btn btn-danger" id="'.$row['job_id'].'" value="Delete"></td></tr>';
		}
		$table .= '</table>';
		echo $table;
	}
}
function delAsset($dataObj){
    
	require_once '../classes/Pdo_methods.php';
	$pdo = new PdoMethods();
	$sql = "SELECT file FROM account_asset WHERE id = :id";
	$bindings = array(
		array(':id',$dataObj->id,'int'),
	);
	$records = $pdo->selectBinded($sql, $bindings);
	foreach($records as $row){
		$filepath = $row['file'];
	}
	if(!unlink($filepath)){
		$object = (object) [
			'masterstatus' => 'error',
			'msg' => 'Could not delete file'
		];
		echo json_encode($object);
		exit;
	}
	$sql = "DELETE FROM account_asset WHERE id=:id";
	$result = $pdo->otherBinded($sql, $bindings);
	if($result = 'noerror'){
		$object = (object) [
			'masterstatus' => 'success',
			'msg' => 'Record Deleted'
		];
		echo json_encode($object);
	}
	else {
		$object = (object) [
			'masterstatus' => 'error',
			'msg' => 'Could not delete record'
		];
		echo json_encode($object);
	}
}
?>