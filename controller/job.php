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
    $pageData['title'] = "PrintInvoice Page";
    $pageData['heading'] = "Job Tracker Add Assets to Acount Page";
    $pageData['nav'] = true;
    $pageData['content'] = file_get_contents('views/admin/print_invoice.html');
    $pageData['js'] = "Util^general^job";
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
    //$timestamp = $records['note_date']/1000;
    //$note_date = date("Y-m-d", $timestamp);

    $sql = "INSERT INTO job_note (job_id, note_date,note_name,note) VALUES (:jobId, :note_date, :note_name, :note)";
    $bindings = array(
       
        array(':jobId', $dataObj->jobid, 'int'),
        array(':note_date', $dataObj->elements[0]->value, 'str'),
        array(':note_name', $dataObj->elements[1]->value, 'str'),
        array(':note', $dataObj->elements[2]->value, 'str'),
        
         //$note_date=date('y-m-d',$note_date);
        
    );
    $result = $pdo->otherBinded($sql, $bindings);
    if ($result == 'noerror') {
        $object = (object) [
                    'masterstatus' => 'success',
                    'msg' => 'Note  has been added'
        ];
        echo json_encode($object);
    } else {
        $object = (object) [
                    'masterstatus' => 'error',
                    'msg' => 'There was an error adding the note'
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
   //$timestamp = $records['note_date']/1000;
   // $jobdate = date("Y-m-d", $timestamp);

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

/* GET JOB CONTACTS FOR JOB*/


function getjobcontacts($dataObj){
     
        require_once '../classes/Pdo_methods.php';
	$pdo = new PdoMethods();
	$sql = "SELECT name, work_phone,mobile_phone,email FROM contact WHERE  id= :id";
       // $sql = "SELECT * from contact where  id=:id";
        
        $bindings = array(
		array(':id',$dataObj->jobId,'int'),
	);
	$records = $pdo->selectBinded($sql, $bindings);
	
          if (count($records) != 0) {   
            $table = '<table class="table table-bordered table-striped" id="contactTable"><thead><tr><th>name</th><th>work_phone</th><th>mobile-phone</th><th>email</th></tr></thead><tbody>';
		foreach($records as $row){
                    $table .= '<tr><td style="width: 20%">'.$row['name'].'</td><td style="width: 20%">'.$row['work_phone'].'</td><td  style="width: 20%">'.$row['mobile_phone'].'
                   <td style="width: 20%">'.$row['email'].'</td></tr>';
			
		}
                                        
		$table .= '</table>';
                  
                  $response = (object) [
				'masterstatus' => 'success',
				'table' => $table,
			];
			echo json_encode($response);
            
        }     
            else{
                $response = (object) [
                    'masterstatus' => 'error',
                    'msg' => 'There was an error with the sql statement',
        ];
        echo json_encode($response);
		}                
       
    } 
    
    


/*  VIEW /CHANGE/ DELETE JOB*/
function viewJobNotes($dataObj){
    require_once '../classes/Pdo_methods.php';
	$pdo = new PdoMethods();
	$sql = "SELECT * FROM job_note WHERE job_id= :id";
	$bindings = array(
		array(':id',$dataObj->jobid,'int'),
	);
	$records = $pdo->selectBinded($sql, $bindings);
	//if(count($records)!='error'){
          if (count($records) != 0) {   
              $noteDate = date('Y-m-d', $row['note_date']/1000);
              $table = '<table class="table table-bordered table-striped" id="jobnotetable"><thead><tr><th>Date</th><th>NoteName</th><th>Note</th><th>Update</th><th>Delete</th></tr></thead><tbody>';
		foreach($records as $row){
                    // $table .= '<tr><td style="width: 20%">'.$row['note_date'].'</td><td style="width: 20%">'.$row['note_name'].'</td><td  style="width: 20%">'.$row['note'].'
                    $table .= '<tr><td style="width: 20%">'.$noteDate.'</td><td style="width: 20%">'.$row['note_name'].'</td><td  style="width: 20%">'.$row['note'].'
                    </td><td  style="width: 20%">.<input type="button" class="btn btn-success" id="'.$row['id'].'" value="Update"></td>.'
                   
                            . '</td><td  style="width: 20%">.<input type="button" class="btn btn-danger" id="'.$row['id'].'" value="Delete"></td></tr>';
			
		}
                                        
		$table .= '</table>';
                  
                  $response = (object) [
				'masterstatus' => 'success',
				'table' => $table,
			];
			echo json_encode($response);
            
        }     
            else{
                $response = (object) [
                    'masterstatus' => 'error',
                    'msg' => 'There was an error with the sql statement',
        ];
        echo json_encode($response);
		}                
       
    } 
    
    /* Delete Job Notes*/ 
    
    function deleteNote($dataObj){
       
      require_once '../classes/Pdo_methods.php';
	
	$pdo = new PdoMethods();
	$sql = "DELETE FROM job_note WHERE id = :id";
	$bindings = array(
		array(':id',$dataObj->id,'int')
	);
	$result = $pdo->otherBinded($sql, $bindings);
	if($result === 'error'){
		$response = (object) [
	    	'masterstatus' => 'error',
	    	'msg' => 'There was a problem deleting the name',
		];
		echo json_encode($response);
	}
	else{
	/*	$response = (object) [
	    	'masterstatus' => 'success',
	    	//'specificaction' => 'reloadpage',
		];
		echo json_encode($response);*/
	
        $object = (object) [
			'masterstatus' => 'success',
			'msg' => 'Record Deleted'
		];
		echo json_encode($object);
	
        }
}
       
    function updateNote($dataObj) {
      /* THIS UPDATES THE DATABASE INFORMATION ONLY. THE FOLDER NAMES STAYS THE SAME. */
	require_once '../classes/Validation.php';
	require '../classes/Pdo_methods.php';
	require_once '../classes/General.php';
	
        
	$validate = new Validation();
	
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
	
		
		/* IF THE ORIGNAME AND NEWNAME DO NOT MATCH THEN A CHECK DUPLIATE WILL BE RUN ON THE NEW NAME TO INSURE THAT IT IS NOT BEING USED BY ANOTHER ACCOUNT. */
		else {
                    $pdo = new PdoMethods();
	            $General = new General();
 		     $sql = "UPDATE job_note SET note_date=:jobDate, note_name=:notename, note=:note WHERE id=:noteId";
                     
                     array_push($bindings, array(':noteId', $dataObj->noteId, 'int'));
                     
                    $elementNames = array('jobDate^^str','notename^^str','note^^str');
                    
                    $bindings = $General->createBindedArray($elementNames, $dataObj);
                    
                
				/* CREATE SQL STATEMENT AND ADD BINDINGS */
			
                                
	         $result = $pdo->otherBinded($sql, $bindings);
		if($result = 'noerror'){
		$response = (object) [
			'masterstatus' => 'success',
			'msg' => 'Note is updated '
		];
		echo json_encode($response);
	}
	else {
		$object = (object) [
			'masterstatus' => 'error',
			'msg' => 'Could not update  note'
		];
		echo json_encode($object);
	}
			
		
                }
    }




  
    /*   UpDating Job*/
    
   function updateNoteForm($dataObj){
              
	require_once '../classes/Pdo_methods.php';

        $pdo = new PdoMethods();
      
        $sql="SELECT * FROM job_note WHERE id=:noteId";
        $bindings = array(
		array(':noteId',$dataObj->noteId,'int'),
	);
	$result = $pdo->selectBinded($sql, $bindings);
        
       
        
        if(count($result) != 0){
            $id=$result[0]['id]'];
            $jobId=$result[0]['job_id'];
            $note_date=$result[0]['note_date']/1000;
            $note_name=$result[0]['note_name'];
            $note=$result[0]['note'];
                               
            $date=date('Y-m-d',$note_date);
           
        }
        
        else{
            
             $object = (object) [
				'masterstatus' => 'error',
				'msg' => 'No records found for that jobnote',
			];
			echo json_encode($object);
                        exit;
            
        }
       
$form=<<<HTML
        
    <div id="temp"class="form">
   <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <label for="jobDate">Date:</label>
        <input type="date" name="$date" class="form-control" id="jobDate" value="$date">
       
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <label for="notename">Note Title:</label>
        <input type="text" name="$note_name" class="form-control" id="notename" value="$note_name">
     
      </div>
    </div>
    </div>
     <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <label for="note">Note:</label>
       <textarea name="$note" id="note" class="form-control">$note</textarea>
        </div>
    </div>
  </div>
    <div class="row">
    <div class="col-md-12">
     <input type="button" class="btn btn-success name="$jobId" id="updatejobnoteBtn" value="updatejobNote">
    </div>
        </div>
         </div>
           </div>
HTML;


             
        
       $object = (object) [
				'masterstatus' => 'success',
				'form' => $form,
                               
			];
			echo json_encode($object);
                        
                        
            
		}
		
	
   
        
            
    
    /* GET JOB HOURS FUNCTION*/
    
    function getJobHours($dataObj){
        
        
        require_once '../classes/Pdo_methods.php';
	$pdo = new PdoMethods();
	$sql = "SELECT * FROM job_hour WHERE job_id= :id";
	$bindings = array(
		array(':id',$dataObj->jobId,'int'),
	);
	$records = $pdo->selectBinded($sql, $bindings);
	//if(count($records)!='error'){
          if (count($records) != 0) {  
              $noteDate = date('Y-m-d', $row['job_date']/1000);
            $table = '<table class="table table-bordered table-striped" id="updateDeleteHours"><thead><tr><th>Date</th><th>Hours</th><th>Rate</th> <th>Description</th><th>Update</th><th>Delete</th></tr></thead><tbody>';
		foreach($records as $row){
                    $table .= '<tr><td style="width: 20%">'.$noteDate.'</td><td style="width: 20%">'.$row['job_hours'].'</td><td  style="width: 20%">'.$row['hourly_rate'].'
                   . </td><td style="width: 20%">'.$row['description'].'</td><td  style="width: 20%">.<input type="button" class="btn btn-success" id="'.$row['id'].'" value="Update"></td>.'
                   
                      . '</td><td  style="width: 20%">.<input type="button" class="btn btn-danger" id="'.$row['id'].'" value="Delete"></td></tr>';
			
		}
                                        
		$table .= '</table>';
                  
                  $response = (object) [
				'masterstatus' => 'success',
				'table' => $table,
			];
			echo json_encode($response);
            
        }     
            else{
                $response = (object) [
                    'masterstatus' => 'error',
                    'msg' => 'There was an error with the sql statement',
        ];
        echo json_encode($response);
		}                
       
    } 
    
        
        
      
    
    /*UPDATE DELETE JOBHOURS*/
    
   
    
    
   function  deleteHours($dataObj){
       
     require_once '../classes/Pdo_methods.php';
	
	$pdo = new PdoMethods();
	$sql = "DELETE FROM job_hour WHERE id = :hourId";
	$bindings = array(
		array(':hourId',$dataObj->hourId,'int')
	);
	$result = $pdo->otherBinded($sql, $bindings);
	if($result === 'error'){
		$response = (object) [
	    	'masterstatus' => 'error',
	    	'msg' => 'There was a problem deleting the name',
		];
		echo json_encode($response);
	}
	else{
	/*	$response = (object) [
	    	'masterstatus' => 'success',
	    	//'specificaction' => 'reloadpage',
		];
		echo json_encode($response);*/
	
        $object = (object) [
			'masterstatus' => 'success',
			'msg' => 'Record Deleted'
		];
		echo json_encode($object);
	
        }
}
    



   function  getHoursUpdateForm($dataObj){
       
       require_once '../classes/Pdo_methods.php';

        $pdo = new PdoMethods();
      
        $sql="SELECT * FROM job_hour WHERE id=:hourId";
        $bindings = array(
		array(':hourId',$dataObj->hourId,'int'),
	);
	$result = $pdo->selectBinded($sql, $bindings);
        
       
        
        if(count($result)>0){
            $id=$result[0]['id]'];
            $jobId=$result[0]['job_id'];
            $job_date=$result[0]['job_date'];
            $date = date('Y-m-d', $timestamp);
            $job_hours=$result[0]['job_hours'];
            $hourly_rate=$result[0]['hourly_rate'];
            $description=$result[0]['description'];
             
            $job_date=date('Y-m-d',$job_date/1000);
            
        }
        
        else{
            
             $object = (object) [
				'masterstatus' => 'error',
				'msg' => 'No records found for that jobnote',
			];
			echo json_encode($object);
                        exit;
            
        }
       
          
$form=<<<HTML
    <div id="updateHoursForm" class="form">
   <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="jobDate">Date:</label>
        <input type="date" name="date" class="form-control" id="jobDate" value="$job_date">
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="hours">Job Hours:</label>
        <input type="text" name="hours" class="form-control" id="hours" value="$job_hours">
     
      </div>
    </div>
    </div>
         <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="hourlyRate">Hourly Rate:</label>
        <input type="text" name="hourlyRate" class="form-control" id="hourlyRate" value="$hourly_rate">
     
      </div>
    </div>
    </div>
     <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="description">Description:</label>
       <textarea rows="10" cols="10" id="description" class="form-control">$description</textarea>
        </div>
    </div>
  </div>
    <div class="row">
    <div class="col-md-12">
     <input type="button" class="btn btn-success"   value="update Hours" id="updatejobhoursBtn">
    </div>
        </div>
         </div>
           </div>
HTML;


             
        
       $object = (object) [
				'masterstatus' => 'success',
				'form' => $form,
                               
			];
			echo json_encode($object);
            
		}
		
	
   
        
            
                
                
  function updateHours($dataObj){
        /* THIS UPDATES THE DATABASE INFORMATION ONLY. THE FOLDER NAMES STAYS THE SAME. */
	require_once '../classes/Validation.php';
        print_r("hello");
	require '../classes/Pdo_methods.php';
	require_once '../classes/General.php';
	
        
	$validate = new Validation();
	
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
	
		
		/* IF THE ORIGNAME AND NEWNAME DO NOT MATCH THEN A CHECK DUPLIATE WILL BE RUN ON THE NEW NAME TO INSURE THAT IT IS NOT BEING USED BY ANOTHER ACCOUNT. */
		else {
                    $pdo = new PdoMethods();
	            $General = new General();
 		     $sql = "UPDATE job_hour SET job_date=:jobDate, job_hours=:hours, hourly_rate=:hourlyRate, description=:description, WHERE id=:hourId";
                     
                     array_push($bindings, array(':hourId', $dataObj->hourId, 'int'));
                     
                    $elementNames = array('jobDate^^str','hours^^double','hourlyRate^^int','description');
                    
                    $bindings = $General->createBindedArray($elementNames, $dataObj);
                    
                
				/* CREATE SQL STATEMENT AND ADD BINDINGS */
			
                                
	         $result = $pdo->otherBinded($sql, $bindings);
		if($result = 'noerror'){
		$response = (object) [
			'masterstatus' => 'success',
			'msg' => 'Hours is updated '
		];
		echo json_encode($response);
	}
	else {
		$object = (object) [
			'masterstatus' => 'error',
			'msg' => 'Could not update  note'
		];
		echo json_encode($object);
	}
			
		
                }
    }

      
      
    ?>
   