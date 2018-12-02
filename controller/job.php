<?php

/* ALL GET PAGE FUNCTIONS HERE */

function addJobPage() {
    $pageData['base'] = "../";
    $pageData['title'] = "Add Contact Page";
    $pageData['heading'] = "Job Tracker Add Job Page";
    $pageData['nav'] = true;
    $pageData['content'] = file_get_contents('views/admin/add_job.html');
    $pageData['js'] = "Util^general^job";
    $pageData['security'] = true;

    return $pageData;
}

function viewJobContactsPage() {
    $pageData['base'] = "../";
    $pageData['title'] = "Job Contacts Page";
    $pageData['heading'] = "Job Tracker ViewJobContactsPage";
    $pageData['nav'] = true;
    $pageData['content'] = file_get_contents('views/admin/view_job_contacts.html');
    $pageData['js'] = "Util^general^job";
    $pageData['security'] = true;

    return $pageData;
}

function addJobNotePage() {
    $pageData['base'] = "../";
    $pageData['title'] = "Add Job NotesPage";
    $pageData['heading'] = "Job Tracker Add Job Notes Page";
    $pageData['nav'] = true;
    $pageData['content'] = file_get_contents('views/admin/add_job_notes.html');
    $pageData['js'] = "Util^general^job";
    $pageData['security'] = true;

    return $pageData;
}

function viewUpdateDeleteNotePage() {
    $pageData['base'] = "../";
    $pageData['title'] = "Update or Delete Job Page";
    $pageData['heading'] = "Job Tracker Add Assets to Acount Page";
    $pageData['nav'] = true;
    $pageData['content'] = file_get_contents('views/admin/view_delete_job_notes.html');
    $pageData['js'] = "Util^general^job";
    $pageData['security'] = true;

    return $pageData;
}

function addJobAssetPage() {
    $pageData['base'] = "../";
    $pageData['title'] = "Add Assets Job Page";
    $pageData['heading'] = "Job Tracker Add Assets to Job Page";
    $pageData['nav'] = true;
    $pageData['content'] = file_get_contents('views/admin/add_job_assets.html');
    $pageData['js'] = "Util^general^job";
    $pageData['security'] = true;

    return $pageData;
}

function viewDeleteJobAssetPage() {
    $pageData['base'] = "../";
    $pageData['title'] = "Add Assets Job Page";
    $pageData['heading'] = "Job Tracker Add Assets to Job Page";
    $pageData['nav'] = true;
    $pageData['content'] = file_get_contents('views/admin/view_delete_job_assets.html');
    $pageData['js'] = "Util^general^job";
    $pageData['security'] = true;

    return $pageData;
}

function addJobHoursPage() {
    $pageData['base'] = "../";
    $pageData['title'] = "Add hHours Job Page";
    $pageData['heading'] = "Job Tracker Add Hours  to Job Page";
    $pageData['nav'] = true;
    $pageData['content'] = file_get_contents('views/admin/add_job_hours.html');
    $pageData['js'] = "Util^general^job";
    $pageData['security'] = true;

    return $pageData;
}

function updateDeleteJobHoursPage() {
    $pageData['base'] = "../";
    $pageData['title'] = "Add Assets Account Page";
    $pageData['heading'] = "Job Tracker Add Assets to Acount Page";
    $pageData['nav'] = true;
    $pageData['content'] = file_get_contents('views/admin/update_delete_hours.html');
    $pageData['js'] = "Util^general^job";
    $pageData['security'] = true;

    return $pageData;
}

function printInvoicePage() {
    $pageData['base'] = "../";
    $pageData['title'] = "Add Assets Account Page";
    $pageData['heading'] = "Job Tracker Add Assets to Acount Page";
    $pageData['nav'] = true;
    $pageData['content'] = file_get_contents('views/admin/print_invoice.html');
    $pageData['js'] = "Util^general^account";
    $pageData['security'] = true;

    return $pageData;
}

/* ALL XHR FUNCTIONS HERE */

function addJob($dataObj) {
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

        $result = $General->checkDuplicates($dataObj, 'job', $pdo);

        /* BASED UPON THE RESULT CREATE A CUSTOM OBJECT CONTAINING THE MASTERSTATUS AND MESSAGE.  ON THE JAVASCRIPT END I CREATE A RESPONSE THAT BASED UPON WHAT IS SENT WILL DISPLAY A MESSAGE BOX SHOWING THE MESSAGE.  THIS IS A NICE WAY OF A CUSTOM MESSAGES BOX FOR THE USER BASED UPON WHAT HAD HAPPENED ON THE SERVER */
        if ($result != 'error') {
            if (count($result) != 0) {
                $response = (object) [
                            'masterstatus' => 'error',
                            'msg' => 'There is already an job by that name',
                ];
                echo json_encode($response);
            } else {
                /* GET THE ACCOUNT NAME FROM THE DATAOBJ */
                $i = 0;
                $name = '';
                while ($i < count($dataObj->elements)) {
                    if ($dataObj->elements[$i]->id === 'name') {
                        $name = $dataObj->elements[$i]->value;
                        break;
                    }
                    $i++;
                }
                /* CREATE AND THE FOLDER BY ADDING THE NAME AND A TIMESTAMP TO KEEP IT UNIQUE */
                $foldername = $name . time();
                $foldername = str_replace(" ", "_", $foldername);
                $foldername = strtolower($foldername);

                /* ADD THE FOLDER TO THE SERVER GIVE IT 777 PERMISSIONS */
                $path = '../public/account_folders/' . $foldername;
                $dir = mkdir($path, 0777);
                /* ADD ACCOUNT TO DATABASE 
                  IMPORTANT NOTE:  THE ORDER OF THE
                 */



                $sql = "INSERT INTO job (account_id, name, folder) VALUES (:accountId, :name, :folder)";

                /* HERE I CREATE AN ARRAY THAT LISTS THE ELEMENT NAME, WHICH IS THE ID AND THE DATATYPE NEEDED BY PDO.  THEY ARE SEPERATED BY A ^^.  WHEN THIS IS RUN THROUGH THE CREATEBINDEDARRAY OF THE GENERAL CLASS, THAT MEHTHOD WILL CREATE A BINDED ARRAY */
                $bindings = array(
                    array(':accountId', $dataObj->accountId, 'int'),
                    array(':name', $dataObj->elements[0]->value, 'str'),
                    array(':folder', $path, 'str')
                );

                /* CREATE BINDINGS NEEDED FOR PDO QUERY.  I CREATED A METHOD IN THE GENERAL CLASS THAT DOES THIS AUTOMATICALLY BY SENDING IN THE ELEMENTNAMES ARRAY AND THE DATAOBJ.  FOR THIS TO WORK YOU JUST HAVE THE CORRECT DATAOBJ STRUCTURE */
                //$bindings = $General->createBindedArray($elementNames, $dataObj);
                /* ADD THE FOLDER TO THE BINDINGS ARRAY */
                //array_push($bindings, array(':folder', $path, 'str'));


                /* IF THE DIRECTORY WAS CREATED THEN ADD TO THE DATABASE OTHERWISE SEND ERROR MESSAGE */
                if ($dir) {
                    $result = $pdo->otherBinded($sql, $bindings);

                    if ($result == 'noerror') {
                        $response = (object) [
                                    'masterstatus' => 'success',
                                    'msg' => 'The job has been added',
                        ];
                        echo json_encode($response);
                    } else {
                        $response = (object) [
                                    'masterstatus' => 'error',
                                    'msg' => 'There was a problem adding the job',
                        ];
                        echo json_encode($response);
                    }
                } else {
                    $response = (object) [
                                'masterstatus' => 'error',
                                'msg' => 'There was a problem making the directory',
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

function addAssetJob($dataObj, $file) {
    require '../classes/Validation.php';

    $Validation = new Validation();
    if (!$Validation->validate($dataObj->elements[0]->regex, $dataObj->elements[0]->value)) {
        $dataObj->masterstatus = 'fielderrors';
        $dataObj->elements[0]->status = 'error';
    }
    if (empty($_FILES)) {
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
    if (finfo_file($finfo, $filetempname) !== "application/pdf") {
        finfo_close($finfo);
        $dataObj->masterstatus = 'fielderrors';
        $dataObj->elements[1]->msg = 'File is wrong type';
        $dataObj->elements[1]->status = 'error';
        echo json_encode($dataObj);
    } else if ($filesize > 1000000) {
        $dataObj->masterstatus = 'fielderrors';
        $dataObj->elements[1]->msg = 'File size is too big';
        $dataObj->elements[1]->status = 'error';
        echo json_encode($dataObj);
    }
    /* IF ALL IS GOOD THEN ADD FILE AND UPDATE DATABASE */ else {
        require_once '../classes/Pdo_methods.php';
        $pdo = new PdoMethods();
        /* GET THE FOLDER PATH FROM THE ACCOUNT DATABASE */
        $sql = "SELECT folder FROM job WHERE id = :id";
        /* SINCE THERE IS ONLY ONE BINDING I DID NOT  NEED TO USE THE GENERAL CLASS */
        $bindings = array(
            array(':id', $dataObj->jobId, 'int'),
        );
        $records = $pdo->selectBinded($sql, $bindings);
//        foreach ($records as $row) {
//            $folder = $row['folder'];
//        }
        $folder = $records[0]['folder'];
        /* REMOVE ALL SPACES FROM THE FILE NAME AND ADD UNDERSCORES */
        $filename = str_replace(" ", "_", $filename);
        $path = $folder . "/" . $filename;

        if (!move_uploaded_file($filetempname, $path)) {
            $dataObj->masterstatus = 'fielderrors';
            $dataObj->elements[1]->msg = 'There was an problem with the file ';
            $dataObj->elements[1]->status = 'error';
            echo json_encode($dataObj);
            exit;
        }
        
        $sql = "INSERT INTO job_asset (job_id, name, file) VALUES (:jobId, :name, :file)";
        $bindings = array(
            array(':jobId', $dataObj->jobId, 'int'),
            array(':name', $dataObj->elements[0]->value, 'str'),
            array(':file', $path, 'str')
        );
        $result = $pdo->otherBinded($sql, $bindings);
        if ($result == 'noerror') {
            $object = (object) [
                        'masterstatus' => 'success',
                        'msg' => 'Asset has been added'
            ];
            echo json_encode($object);
        } else {
            $object = (object) [
                        'masterstatus' => 'error',
                        'msg' => 'There was an error adding the asset'
            ];
            echo json_encode($object);
        }
    }
}


/* add job notes*/

function addJobNote($dataObj) {

    require_once '../classes/Pdo_methods.php';
    $pdo = new PdoMethods();
    /* GET THE FOLDER PATH FROM THE ACCOUNT DATABASE */
    $sql = "SELECT id FROM job WHERE id = :id";
    /* SINCE THERE IS ONLY ONE BINDING I DID NOT  NEED TO USE THE GENERAL CLASS */
    $bindings = array(
        array(':id', $dataObj->jobid, 'int'),
    );
    $records = $pdo->selectBinded($sql, $bindings);
   

    $sql = "INSERT INTO job_note (job_id, note_date,note_name,note) VALUES (:jobId, :note_date, :note_name, :note)";
    $bindings = array(
        array(':jobId', $dataObj->jobid, 'int'),
        array(':note_date', $dataObj->elements[0]->value, 'str'),
        array(':note_name', $dataObj->elements[1]->value, 'str'),
        array(':note', $dataObj->elements[2]->value, 'str'),
    );
    $result = $pdo->otherBinded($sql, $bindings);
    if ($result == 'noerror') {
        $object = (object) [
                    'masterstatus' => 'success',
                    'msg' => 'Asset has been added'
        ];
        echo json_encode($object);
    } else {
        $object = (object) [
                    'masterstatus' => 'error',
                    'msg' => 'There was an error adding the asset'
        ];
        echo json_encode($object);
    }
}

function addHours($dataObj) {
   
 require_once '../classes/Pdo_methods.php';
    $pdo = new PdoMethods();
    /* GET THE FOLDER PATH FROM THE ACCOUNT DATABASE */
    $sql = "SELECT id FROM job WHERE id = :id";
    /* SINCE THERE IS ONLY ONE BINDING I DID NOT  NEED TO USE THE GENERAL CLASS */
    $bindings = array(
        array(':id', $dataObj->jobId, 'int'),
    );
    $records = $pdo->selectBinded($sql, $bindings);
   

    $sql = "INSERT INTO job_hour (job_id, job_date,job_hours,hourly_rate,description) VALUES (:jobId, :jobdate, :jobhours,:hourlyrate, :descrip)";
    $bindings = array(
        array(':jobId', $dataObj->jobId, 'int'),
        array(':jobdate', $dataObj->elements[0]->value, 'str'),
        array(':jobhours', $dataObj->elements[1]->value, 'str'),
        array(':hourlyrate', $dataObj->elements[2]->value, 'str'),
        array(':descrip', $dataObj->elements[3]->value, 'str'),
    );
    $result = $pdo->otherBinded($sql, $bindings);
    if ($result == 'noerror') {
        $object = (object) [
                    'masterstatus' => 'success',
                    'msg' => 'Asset has been added'
        ];
        echo json_encode($object);
    } else {
        $object = (object) [
                    'masterstatus' => 'error',
                    'msg' => 'There was an error adding the asset'
        ];
        echo json_encode($object);
    }
}





function viewDeleteAsset($dataObj){

   require_once '../classes/Pdo_methods.php';
	$pdo = new PdoMethods();
	$sql = "SELECT job_id, name, file FROM job_asset WHERE job_id= :id";
	$bindings = array(
		array(':id',$dataObj->jobId,'int'),
	);
	$records = $pdo->selectBinded($sql, $bindings);
	if(count($records) == 0){
        $response = (object) [
                    'masterstatus' => 'error',
                    'msg' => 'There was an error with the sql statement',
        ];
        echo json_encode($response);
    } 
    
    else {
        
            $table = '<table class="table table-bordered table-striped" id="viewdelassetstable"><thead><tr><th>Name</th><th>Delete</th></tr></thead><tbody>';
		foreach($records as $row){
			$table .= '<tr><td style="width: 80%"><a href="../docs/'.$row['file'].'">'.$row['name'].'</a></td>';
			$table .= '<td style="width: 20%"><input type="button" class="btn btn-danger" id="'.$row['job_id'].'" value="Delete"></td></tr>';
		}
		$table .= '</table>';
        
            
                  $response = (object) [
				'masterstatus' => 'success',
				'table' => $table,
			];
			echo json_encode($response);
            
		}
                
        }
   



/*

function viewDeleteAsset($dataObj){
	require_once '../classes/Pdo_methods.php';
	$pdo = new PdoMethods();
	$sql = "SELECT job_id, name, file FROM job_asset WHERE job_id = :id";
	$bindings = array(
		array(':id',$dataObj->jobId,'int'),
	);
	$records = $pdo->selectBinded($sql, $bindings);
	if(count($records)== 0){
            
             $response = (object) [
                    'masterstatus' => 'error',
                    'msg' => 'There was an error with the sql statement',
        ];
        echo json_encode($response);
        } 
          
	else{
		  
            $table = '<table class="table table-bordered table-striped" id="viewdelassetstable"><thead><tr><th>Name</th><th>Delete</th></tr></thead><tbody>';
		foreach($records as $row){
                    
			$table .= '<tr><td style="width: 80%"><a href="../docs/'.$row['file'].'">'.$row['name'].'</a></td>';
			$table .= '<td style="width: 20%"><input type="button" class="btn btn-danger" id="'.$row['job_id'].'" value="Delete"></td></tr>';
		}
		$table .= '</table>';
		
                  $response = (object) [
				'masterstatus' => 'success',
				'table' => $table,
			];
		echo json_encode($response);
                             
                
	}
		
	}*/


    
            
function delAsset($dataObj){
    
	require_once '../classes/Pdo_methods.php';
	$pdo = new PdoMethods();
	$sql = "SELECT file FROM job_asset WHERE job_id = :id";
	$bindings = array(
		array(':id',$dataObj->assetId,'int'),
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
	$sql = "DELETE FROM job_asset WHERE job_id=:id";
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