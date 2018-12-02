<?php
/* ALL GET PAGE FUNCTIONS HERE */
function homePage(){
	$pageData['base'] = "../";
	$pageData['title'] = "Home Page";
	$pageData['heading'] = "Job Tracker Home Page";
	$pageData['nav'] = true;
	$pageData['content'] = file_get_contents('views/admin/home.html');
	$pageData['js'] = "Util^general^login";
	$pageData['security'] = true;

	return $pageData;
}

?>