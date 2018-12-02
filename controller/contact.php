
<?php

/* ALL GET PAGE FUNCTIONS HERE */

function addContactPage() {
    $pageData['base'] = "../";
    $pageData['title'] = "Add Contact Page";
    $pageData['heading'] = "Job Tracker Add Contact Page";
    $pageData['nav'] = true;
    $pageData['content'] = file_get_contents('views/admin/add_contact.html');
    $pageData['js'] = "Util^general^contact";
    $pageData['security'] = true;

    return $pageData;
}

function updateContactPage() {
    $pageData['base'] = "../";
    $pageData['title'] = "Update Contact Page";
    $pageData['heading'] = "Job Tracker Update Contact Page";
    $pageData['nav'] = true;
    $pageData['content'] = file_get_contents('views/admin/update_contact.html');
    $pageData['js'] = "Util^general^contact";
    $pageData['security'] = true;

    return $pageData;
}

function manageContactPage() {
    $pageData['base'] = "../";
    $pageData['title'] = "Add Assets Contact Page";
    $pageData['heading'] = "Job Tracker Add Assets to Contact Page";
    $pageData['nav'] = true;
    $pageData['content'] = file_get_contents('views/admin/manage_contacts.html');
    $pageData['js'] = "Util^general^contact";
    $pageData['security'] = true;

    return $pageData;
}

function deleteContactPage() {
    $pageData['base'] = "../";
    $pageData['title'] = "View or Delete Contact Asset";
    $pageData['heading'] = "Job Tracker View or Delete Contact Asset Page";
    $pageData['nav'] = true;
    $pageData['content'] = file_get_contents('views/admin/delete_contacts.html');
    $pageData['js'] = "Util^general^contact";
    $pageData['security'] = true;

    return $pageData;
}

/* ALL XHR FUNCTIONS HERE */

function addContact($dataObj) {

    require_once '../classes/Validation.php';
    require_once '../classes/Pdo_methods.php';
    require_once '../classes/General.php';

    $validate = new Validation();

    $i = 0;
    $error = false;
    while ($i < count($dataObj->elements)) {
        if (!$validate->validate($dataObj->elements[$i]->regex, $dataObj->elements[$i]->value)) {
            $error = true;
            $dataObj->elements[$i]->status = 'error';
        }
        $i++;
    }

    if ($error) {
        $dataObj->masterstatus = 'fielderrors';
        $data = json_encode($dataObj);
        echo $data;
    } else {

        $General = new General();
        $pdo = new PdoMethods();
        /* IF EVERYTHING IS VALID THEN CHECK FOR A DUPLICATE NAME 
          USE THE CHECKDUPLICATES METHOD FROM THE GENRERAL CLASS.  THE SECOND PARAMETER IS THE TABLE WE ARE GOING TO CHECK. IN THE METHOD BASED UPON THAT DECIDES WHAT QUERY WE USE. THE $PDO IS THE CONNECTION INSTANCE  SEE GENERAL METHOD FOR MORE INFO */

        $result = $General->checkDuplicates($dataObj, 'contact', $pdo);

        /* BASED UPON THE RESULT CREATE A CUSTOM OBJECT CONTAINING THE MASTERSTATUS AND MESSAGE.  ON THE JAVASCRIPT END I CREATE A RESPONSE THAT BASED UPON WHAT IS SENT WILL DISPLAY A MESSAGE BOX SHOWING THE MESSAGE.  THIS IS A NICE WAY OF A CUSTOM MESSAGES BOX FOR THE USER BASED UPON WHAT HAD HAPPENED ON THE SERVER */

        if ($result != 'error') {
            if (count($result) != 0) {
                $response = (object) [
                            'masterstatus' => 'error',
                            'msg' => 'There is already an contact by that name',
                ];
                echo json_encode($response);
            } else {
                /* GET THE ACCOUNT NAME FROM THE DATAOBJ */
                $i = 0;
                $email = '';
                while ($i < count($dataObj->elements)) {
                    if ($dataObj->elements[$i]->id === 'email') {
                        $email = $dataObj->elements[$i]->value;
                        break;
                    }
                    $i++;
                }

                /* ADD ACCOUNT TO DATABASE   IMPORTANT NOTE:  THE ORDER OF THE 	 */
                $sql = "INSERT INTO contact (name, work_phone, mobile_phone, email) VALUES (:name, :workphone, :mobilephone, :email)";

                /* HERE I CREATE AN ARRAY THAT LISTS THE ELEMENT NAME, WHICH IS THE ID AND THE DATATYPE NEEDED BY PDO.  THEY ARE SEPERATED BY A ^^.  WHEN THIS IS RUN THROUGH THE CREATEBINDEDARRAY OF THE GENERAL CLASS, THAT MEHTHOD WILL CREATE A BINDED ARRAY */
                $elementNames = array('name^^str', 'workphone^^str', 'mobilephone^^str', 'email^^str');

                /* CREATE BINDINGS NEEDED FOR PDO QUERY.  I CREATED A METHOD IN THE GENERAL CLASS THAT DOES THIS AUTOMATICALLY BY SENDING IN THE ELEMENTNAMES ARRAY AND THE DATAOBJ.  FOR THIS TO WORK YOU JUST HAVE THE CORRECT DATAOBJ STRUCTURE */
                $bindings = $General->createBindedArray($elementNames, $dataObj);
                /* ADD THE FOLDER TO THE BINDINGS ARRAY */
                array_push($bindings, array('str'));

                /* IF THE DIRECTORY WAS CREATED THEN ADD TO THE DATABASE OTHERWISE SEND ERROR MESSAGE */

                $result = $pdo->otherBinded($sql, $bindings);

                if ($result == 'noerror') {
                    $response = (object) [
                                'masterstatus' => 'success',
                                'msg' => 'Thecontact has been added',
                    ];
                    echo json_encode($response);
                } else {
                    $response = (object) [
                                'masterstatus' => 'error',
                                'msg' => 'There was a problem adding the contact',
                    ];
                    echo json_encode($response);
                }
            }
        } else {
            $object = (object) [
                        'masterstatus' => 'error',
                        'msg' => 'There was an error with our sql statement',
            ];
            echo json_encode($object);
        }
    }
}

function updateContact($dataObj) {
    /* THIS UPDATES THE DATABASE INFORMATION ONLY. THE FOLDER NAMES STAYS THE SAME. */
    require_once '../classes/Validation.php';
    require '../classes/Pdo_methods.php';
    require_once '../classes/General.php';

    $validate = new Validation();
    $pdo = new PdoMethods();
    $General = new General();
    $i = 0;
    $error = false;
    while ($i < count($dataObj->elements)) {
        if (!$validate->validate($dataObj->elements[$i]->regex, $dataObj->elements[$i]->value)) {
            $error = true;
            $dataObj->elements[$i]->status = 'error';
        }
        $i++;
    }

    if ($error) {
        $dataObj->masterstatus = 'fielderrors';
        $data = json_encode($dataObj);
        echo $data;
    } else {

        /* IF EVERYTHING IS VALID THEN CHECK FOR A DUPLICATE NAME 
          HERE I LOOP THROUGH THE DATA ELEMENTS AND GET THE NAME THAT WAS ENTERED AND STORE THAT INTO A VARIABLE OF NEWNAME */
        foreach ($dataObj->elements as $element) {
            if ($element->id === 'name') {
                $newName = $element->value;
                break;
            }
        }
        /* HERE I LOOP THROUGH DATA ELEMENTS AND GET THE ORIGINAL NAME THAT IS STORED IN A HIDDEN FIELD ON THE FORM. IT CONTAINS THE LAST NAME USED FOR THE ACCOUNT. */
        foreach ($dataObj->elements as $element) {
            if ($element->id === 'hiddenName') {
                $origName = $element->value;
                break;
            }
        }
        /* I COMPARE BOTH NAMES AND IF THEY ARE THE SAME THEN I MOVE ON SETTING THE RESULT TO AN EMPTY ARRAY WHICH IS WHAT THE CHECK DUPLICATES FUNCTION WILL RETURN IF THERE ARE NO DUPLICATE NAMES FOUND IN THE DATABASE.  I HAVE TO DO THIS BECAUSE TO ELIMINATE THE ERROR IF THE USER DID NOT CHANGE THE AND I RAN CHECK DUPCLIATES IT WOULD THINK THERE WAS A DUPLICATE NAME WHEN THERE WAS NOT.  OPTIONALLY I COULD HAVE SENT THE RECORD ID WITH THE NAME AND COMPARED IT IN THE DATABASE. */
        if ($origName === $newName) {
            $result = [];
        }

        /* IF THE ORIGNAME AND NEWNAME DO NOT MATCH THEN A CHECK DUPLIATE WILL BE RUN ON THE NEW NAME TO INSURE THAT IT IS NOT BEING USED BY ANOTHER ACCOUNT. */ else {
            $result = $General->checkDuplicates($dataObj, 'contact', $pdo);
        }
        if ($result != "error") {
            if (count($result) != 0) {
                $response = (object) [
                            'masterstatus' => 'error',
                            'msg' => 'There is already an conatct by that name',
                ];
                echo json_encode($response);
            } else {
                /* HERE I CREATE AN ARRAY THAT LISTS THE ELEMENT NAME, WHICH IS THE ID AND THE DATATYPE NEEDED BY PDO.  THEY ARE SEPERATED BY A ^^.  WHEN THIS IS RUN THROUGH THE CREATEBINDEDARRAY OF THE GENERAL CLASS, THAT METHOD WILL CREATE A BINDED ARRAY */
                $elementNames = array('name^^str', 'workphone^^str', 'mobilephone^^str', 'email^^str');

                /* CREATE BINDINGS NEEDED FOR PDO QUERY.  I CREATED A METHOD IN THE GENERAL CLASS THAT DOES THIS AUTOMATICALLY BY SENDING IN THE ELEMENTNAMES ARRAY AND THE DATAOBJ.  FOR THIS TO WORK YOU JUST HAVE THE CORRECT DATAOBJ STRUCTURE */
                $bindings = $General->createBindedArray($elementNames, $dataObj);
                /* ADD THE ACCOUNTID TO THE BINDINGS ARRAY */
                array_push($bindings, array(':contactId', $dataObj->contactId, 'int'));
                /* CREATE SQL STATEMENT AND ADD BINDINGS */
                $sql = "UPDATE contact SET name=:name, work_phone=:workphone, mobile_phone=:mobilephone, email=:email WHERE id=:contactId";
                $result = $pdo->otherBinded($sql, $bindings);
                if ($result = 'noerror') {
                    $object = (object) [
                                'masterstatus' => 'success',
                                'msg' => 'Contact  has been updated'
                    ];
                    echo json_encode($object);
                } else {
                    $object = (object) [
                                'masterstatus' => 'error',
                                'msg' => 'There was a problem updating the contact'
                    ];
                    echo json_encode($object);
                }
            }
        } else {
            $object = (object) [
                        'masterstatus' => 'error',
                        'msg' => 'There was a problem updating the contact'
            ];
            echo json_encode($object);
        }
    }
}

function getContact($dataObj) {
    require '../classes/Pdo_methods.php';
    $pdo = new PdoMethods();
    $sql = "SELECT * FROM contact WHERE id=:id";
    $bindings = array(
        array(':id', $dataObj->id, 'int'),
    );
    $records = $pdo->selectBinded($sql, $bindings);
    if ($records == 'error') {
        $object = (object) [
                    'masterstatus' => 'error',
                    'msg' => 'There was an error with the sql statement',
        ];
        echo json_encode($object);
    } else {
        if (count($records) != 0) {


            $table = '<div class="row" style="margin-top: 20px">    
                <div class="col-md-6">
                <div class="form-group">
                  <label for="name">Name:</label>
                  <input type="text" class="form-control" id="name" name="name" value="' . $records[0]['name'] . '">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="workphone">Work Phone:</label>
                  <input type="text" class="form-control" name="workphone" id="workphone" value="' . $records[0]['work_phone'] . '">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="mobilephone">Mobile Phone:</label>
                  <input type="text" class="form-control" name="mobilephone" id="mobilephone" value="' . $records[0]['mobile_phone'] . '">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="email">Email:</label>
                  <input type="text" class="form-control" name="email" id="email" value="' . $records[0]['email'] . '">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <input type="button" name="updatecontact" id="updatecontactBtn" class="btn btn-primary" value="Update Contact" />
                </div>
              </div>
            </div>
            </div>';

            $object = (object) [
                        'masterstatus' => 'success',
                        'table' => $table,
            ];
            //echo json_encode($object);
            echo($object->table);
        } else {
            if (count($records) != 0) {
                $object = (object) [
                            'masterstatus' => 'error',
                            'msg' => 'No records found for that account',
                ];
                echo json_encode($object);
            }
        }
    }
}


function mcInterface($dataObj){
  require '../classes/Pdo_methods.php';

    $pdo = new PdoMethods();
    $name="";
    $associations="";
    $accounts="";
    $sql="select name from contact where id=:id";
     $bindings = array(
        array(':id', $dataObj->contId, 'int'),
    );
    $records = $pdo->selectBinded($sql, $bindings);
    //$name1="bob";
    $name=$records[0]['name'];
    
   $associations = getAssocTable($dataObj);
     
    
    
    $accounts=getAllAccounts();
      
    
            $object = (object) [
                        'masterstatus' => 'success',
                       'name' => $name,
                       'associations' => $associations,
                        'accounts' => $accounts
            ];
            $data= json_encode($object);
            echo $data;


}


function getAllAccounts() {
    require_once '../classes/Pdo_methods.php';
    $pdo = new PdoMethods();
    $sql = "SELECT id, name FROM account";
    $records1 = $pdo->selectNotBinded($sql);
    if ($records1 == 'error') {
        echo 'There was an error getting the accounts list';
    } else {
        if (count($records1) != 0) {
            $accounts = '<select id="acclst" class="form-control">
            <option value="0">Select an account</option>';
            foreach ($records1 as $row) {
                $accounts .= "<option value=" . $row['id'] . ">" . $row['name'] . "</option>";
            }
            $accounts .= '</select>';

          
        } else {
            
           
        }
    }
  return $accounts;  
}



/* this is orginal table  working*/
function getAssocTable($dataObj){
          
    require_once '../classes/Pdo_methods.php';
    $pdo = new PdoMethods();
        if (count($records1) != 0) {
            $sql = 'select * from job_contact where contact_id=:id';
            $bindings = array(
                array(':contId', $dataObj->contId, 'int'),
               
            );
        
            $records = $pdo->selectBinded($sql, $bindings);
            
            //check count here and check $table
            if (count($records) == 0) {
                 print_r("php testing");
                  $associations = '<p> there is no assocition added </p>';
            }
                                                              
            else {
                   $associations='<table class="table table-bordered table-striped" id="table">'
                       . '<thead><tr><th>Account Name</th><th>Job</th><th>Delete</th></tr></thead>';
         
                     
                 foreach ($records1 as $row) {
                  
                     $sql = "SELECT account.name AS accountname, job.name AS jobname, job.id AS jobid, account.id AS accountid  FROM account, job, job_contact WHERE account.id = job_contact.account_id AND job.id = job_contact.job_id AND job_contact.contact_id = :id";
                    $bindings = array(
                        
                        array(':acctid', $row['account_id'], 'int'),
                        array(':jobid', $row['job_id'], 'int'),
                    );
                    $records2 = $pdo->selectBinded($sql, $bindings);
                    for ($i = 0; $i < count($records2); $i++) {
                        $associations .= '<tr>
                           <td>' . $records2[$i][0] . '</td>
                           <td>' . $records2[$i][1] . '</td>
                           <td><button id="'.$row['account_id'].'&&&'.$row['job_id'].'" >Delete</button></td>
                        </tr>';
                    }
                }
         print_r("php testing");
                  $associations .= ' </tbody></table>';
                  echo $association;
            }
           
                
            
           // echo $table;
        
        }
        return $associations;
         
        }


  
function addAssoc($dataObj){
    //take the contact id, job id, account id and insert them into the job_contact table;
    //call getAssocTable and send the returned table back as a string.
    //make sure you create the correct object based upon the javascript
   echo "test";
    //print_r("error log here");
   require_once '../classes/Pdo_methods.php';
		$pdo = new PdoMethods();
		/* GET THE FOLDER PATH FROM THE ACCOUNT DATABASE */
		$sql = "SELECT  FROM job_contact WHERE id = :id";
		/* SINCE THERE IS ONLY ONE BINDING I DID NOT  NEED TO USE THE GENERAL CLASS*/
		$bindings = array(
			array(':id',$dataObj->id,'int'),
		);
		
		$sql = "INSERT INTO job_contact (account_id,job_id,contact_id) VALUES (acctId, jobId, contId)";
                
                getAssocTable($dataObj);
                echo"sheebaesting";
                //return '$associations';
               // print_r("hello how r u");
          
             
		
}

     
function contactTable(){
	require '../classes/Pdo_methods.php';
	$pdo = new PdoMethods();
	$sql = "SELECT id, name FROM contact";
	$records = $pdo->selectNotBinded($sql);
	if($records == 'error'){
    	echo 'There has been and error processing your request';
    }
    /* IF EMAIL AND PASSWORD EXIST THEN ALLOW ACCESS */
    else {
    	if(count($records) != 0){
        	$contacts = '<table class="table table-bordered table-striped" id="contacttable">';
        	$contacts .= '<thead><tr><th>Contact  Name</th><th>Delete</th></tr></thead><tbody>';
        	foreach ($records as $row) {
        		$contacts .= "<tr><td style='width: 90%'>".$row['name']."</td>";
        		$contacts .= "<td style='width: 10%'><input type='button' class='btn btn-danger' value='Delete' id='".$row['id']."'></td></tr>";
        	}
        	$contacts .= '</tbody></table>';
        	echo $contacts;
	    }
	    else {
	    	echo 'No Contacts found';
	    }
    }
}








/* THIS FUNCTION WILL DELETE THE NAME AND OTHE INFORMATION FOR THE ROW THAT WAS CLICKED */
function  deleteContact($dataObj){
	$pdo = new PdoMethods();
	$sql = "DELETE FROM contact WHERE id = :id";
	$bindings = array(
		array(':id',$dataObj->contId,'int')
	);
	$result = $pdo->otherBinded($sql, $bindings);
	if($result === 'error'){
		$response = (object) [
	    	'masterstatus' => 'error',
	    	'msg' => 'There was a problem deleting the name',
		];
		echo json_encode($response);
	}
	else {
		$response = (object) [
	    	'masterstatus' => 'success',
	    	'specificaction' => 'reloadpage',
		];
		echo json_encode($response);
	}

}


/*function mcInterface($dataObj) {


    // error_log("---------------------ID is ".$dataObj->id);

    require '../classes/Pdo_methods.php';

    $pdo = new PdoMethods();
    $sql = "SELECT * FROM contact WHERE id=:id";
    $bindings = array(
        array(':id', $dataObj->contId, 'int'),
    );

    $records = $pdo->selectBinded($sql, $bindings);
    if ($records == 'error') {
        $object = (object) [
                    'masterstatus' => 'error',
                    'msg' => 'There was an error with the sql statement',
        ];
        echo json_encode($object);
    } else {
        //check count here
       
        if (count($records) != 0) {
            $sql = 'select * from job_contact where contact_id=:id';
            $bindings = array(
                array(':id', $dataObj->contId, 'int'),
               
            );
        
            $records1 = $pdo->selectBinded($sql, $bindings);
            
            //check count here and check $table
            if (count(records1) == 0) {
                $table = '<p> there is no assocition added </p>';
            }
                                                              
            else {
                $table ='<table><thead><tr><th>Account Name</th><th>Job</th><th>Delete</th></tr></thead>';
    
                  
                      
                
                
                     
                 foreach ($records1 as $row) {
                    $sql = 'select a.name, j.name from job j, account a where a.id=:acctid and j.id=:jobid';
                    $bindings = array(
                        
                        array(':acctid', $row['account_id'], 'int'),
                        array(':jobid', $row['job_id'], 'int'),
                    );
                    $records2 = $pdo->selectBinded($sql, $bindings);
                    for ($i = 0; $i < count($records2); $i++) {
                        $table .= '<tr>
                           <td>' . $records2[$i][0] . '</td>
                           <td>' . $records2[$i][1] . '</td>
                           <td><button id="'.$row['account_id'].'&&&'.$row['job_id'].'" >Delete</button></td>
                        </tr>';
                    }
                }

                $table .= ' </tbody>
                    </table>';
            }
        
            
           $accounts = '<select id="acclst" class="form-control">
            <option value="0">Select an account</option>';
            foreach ($records as $row) {
                $accounts .= "<option value=" . $row['id'] . ">" . $row['name'] . "</option>";
            }
            $accounts .= '</select>';
    
        

            $object = (object) [
                        'masterstatus' => 'success',
                       'name' => $records[0]['name'],
                        'associations' => $table,
                        'accounts' => $accounts
            ];
            echo json_encode($object);
        } else {
            if (count($records) != 0) {
                $object = (object) [
                            'masterstatus' => 'error',
                            'msg' => 'No records found for that account',
                ];
                echo json_encode($object);
            }
        }
    }
}*/





/* this is orginal table now not working not working now*/
/*function getAssocTable($dataObj){
          
   require '../classes/Pdo_methods.php';
	$pdo = new PdoMethods();
	$sql = "SELECT id,  FROM job_contact";
	$records = $pdo->selectNotBinded($sql);
	if($records == 'error'){
    	echo 'There has been and error processing your request';
    }
    /* IF EMAIL AND PASSWORD EXIST THEN ALLOW ACCESS */
   /* else {
    	if(count($records) != 0){
        	$associations = '<table class="table table-bordered table-striped" id="table">';
        	$associations .= '<thead><tr><th>Account Name</th><th>Job</th><th>Delete</th></tr></thead><tbody>';
        	foreach ($records as $row) {
                     $sql = "SELECT account.name AS accountname, job.name AS jobname, job.id AS jobid, account.id AS accountid  FROM account, job, job_contact WHERE account.id = job_contact.account_id AND job.id = job_contact.job_id AND job_contact.contact_id = :id";
        		//$associations .= "<tr><td style='width: 90%'>".$row['name']."</td>";
        		//$associations .= "<td style='width: 10%'><input type='button' class='btn btn-danger' value='Delete' id='".$row['id']."'></td></tr>";
        	}
        	$associations .= '</tbody></table>';
        	echo $associations;
	    }
	    else {
	    	echo 'No Accounts found';
	    }
    }

   return $associations;
}*/
   






?>















