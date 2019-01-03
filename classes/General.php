

<?php
class General {
	public function security(){
		session_start();
		If($_SESSION['access'] != "approved"){
			header( 'Location: /var/www/html/job_tracker_student_version_php') ;
                        //header("Location: http://198.211.114.100/job_tracker_student_version/printinvoice/");

		}
	}

	/*THIS FUNCTION WILL TAKE THE ELEMENT NAMES ARRAY AND THE OBJECT OF ALL THE ELEMENT IDS AND VALUES.  IT WILL RETURN A BINDED ARRAY THAT CAN BE INSERTED INTO A DATABASE USING PDO */
	public function createBindedArray($elementNames, $dataObj){
		$bindings = array();
		$i = 0;
		$j = 0;

		while($i < count($elementNames)){
			$j = 0;
			while($j < count($dataObj->elements)){
				$elementNameArr = explode("^^", $elementNames[$i]);
				if($dataObj->elements[$j]->id === $elementNameArr[0]){
					$tempArray = array();
					array_push($tempArray, ":".$elementNameArr[0],$dataObj->elements[$j]->value, $elementNameArr[1]);
					array_push($bindings,$tempArray);
					break;
				}
				$j++;
			}
			$i++;
		}

		return $bindings;		
	}

	
	public function checkDuplicates($dataObj, $table, $pdo){
		/* GET THE VALUES TO CHECK DUPLICATES FROM. IN THIS CASE IT IS THE NAME VALUE */
		$i = 0;
		$duplicateValue = '';
		while($i < count($dataObj->elements)){
			
			/* CHECKS TO SEE IF THE PROPERTY EXISTS FIRST THEN IT GETS THE VALUE OF THE PROPERTY */
			
			if(property_exists($dataObj->elements[$i], 'duplicate')){
				$duplicateValue = $dataObj->elements[$i]->value;
				break;
			}

			$i++;
		}

						
		/* THIS WILL CREATE THE SQL COMMAND AND THE BINDINGS ARRAY NEEDED FOR THE PDO REQUEST */
		switch($table){
			case 'account' : $sql = "SELECT name FROM account WHERE name = :name";$bindings = array(array(':name', $duplicateValue,'str')); break;
			case 'contact' : $sql = "SELECT email FROM contact WHERE email = :email";$bindings = array(array(':email', $duplicateValue,'str')); break;
			case 'job' : $sql = "SELECT name FROM job WHERE name = :name";$bindings = array(array(':name', $duplicateValue,'str')); break;
		}

		/* RETURN THE RESULT */
		return $pdo->selectBinded($sql, $bindings);
	}
}