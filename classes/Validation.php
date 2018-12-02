<?php
class Validation {
	public function validate($regex, $value){
		switch($regex){
			case "email" : return $this->email($value); break;
			case "password" : return $this->password($value); break;
			case "name" : return $this->name($value); break;
			case "address" : return $this->address($value); break;
			case "state" : return $this->state($value); break;
			case "zip" : return $this->zip($value); break;
			case "namets" : return $this->namets($value); break;
			case "alphaNum" : return $this->alphaNum($value); break;
			case "phone" : return $this->phone($value);break;
			case "phoneOpt" : return $this->phoneOpt($value);break;
			case "dateFormat" : return $this->dateFormat($value);break;
			case "text": return $this->text($value);break;
			case "number": return $this->number($value);break;
			case "timestamp": return $this->timestamp($value);break;
			case "hourlyrate": return $this->hourlyrate($value);break;
			case "hours": return $this->hours($value);break;
			default : return 'not found';
		}
	}

	private function email($value){
	  $regex = '/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i';
	  return preg_match($regex, $value);

	}

	private function password($value){
	  $regex = '/^[a-z]+$/i';
	  
	  return preg_match($regex, $value);
	}

	private function name($value){
	  $regex = '/^[a-z0-9 ]+$/i';
	  return preg_match($regex, $value);
	}

	private function timestamp($value){
	  if(is_null($value)){
	  	return false;
	  }

	  if($value === 'null'){
	  	return false;
	  }
	  $regex = '/^[0-9]+$/i';
	  return preg_match($regex, $value);
	}

	private function alphaNum($value){
	  $regex = '/^[a-z0-9]+$/i';
	  return preg_match($regex, $value);
	}

	private function address($value){
	  $regex = '/^[a-z0-9\,\. ]+$/i';
	  return preg_match($regex, $value);
	}

	private function state($value){
	  $regex = '/^(AL|AK|AZ|AR|CA|CO|CT|DE|FL|GA|HI|ID|IL|IN|IA|KS|KY|LA|ME|MD|MA|MI|MN|MS|MO|MT|NE|NV|NH|NJ|NM|NY|NC|ND|OH|OK|OR|PA|RI|SC|SD|TN|TX|UT|VT|VA|WA|WV|WI|WY)$/';
	  return preg_match($regex, $value);
	}

	private function zip($value){
	  $regex = '/^[1-9]{1}[0-9]{4}$/';
	  return preg_match($regex, $value);
	}

	/* THIS IS A CUSTOM STRING THAT STORES A NAME AND TIMESTAMP */
	private function namets($value){
	  $regex = '/^[a-z0-9]+$/i';
	  return preg_match($regex, $value);
	}

	private function phone($value){
	  $regex = '/^[0-9]{3}\.[0-9]{3}\.[0-9]{4}$/i';
	  return preg_match($regex, $value);  
	}

	/* THIS CHECKS FOR AN OPTIONAL PHONE NUMBER */
	private function phoneOpt($value){
	  if($value === ""){
	    return 1;
	  }
	  else {
	    $regex = '/^[0-9]{3}\.[0-9]{3}\.[0-9]{4}$/i';
	    return preg_match($regex, $value);  
	  }
	}

	private function dateFormat($value){
	  $regex = '/^([12]\\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\\d|3[01]))$/';
	  return preg_match_all($regex, $value);  
	}

	private function text($value){
	  $regex = '/^[a-z0-9\\, \' \. \; \,]+$/i';
	  return preg_match($regex, $value);  
	}

	private function number($value){
	  $regex = '/^([0-9]{1,4})$|^([0-9]{1,4}\.[0-9]{0,2})$|^(\.[0-9]{0,2})$/';
	  return preg_match($regex, $value);
	}

	private function hourlyrate($value){
		$regex = '/^[1-9]{1}[0-9]*$/';
		return preg_match($regex, $value);
	}

	private function hours($value){
		$regex = '/^[1-9.]{1}[0-9.]*$/';
		return preg_match($regex, $value);
	}
}

