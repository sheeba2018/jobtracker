<?php
if(isset($_GET['page'])){
	$page = $_GET['page'];
}
else {
	$page = '';
}

switch($page){
	case "home": require 'controller/home.php'; $pageData = homePage(); break;
	/* ACCOUNTS */
	case "addaccount": require 'controller/account.php'; $pageData = addAccountPage(); break;
	case "updateaccount": require 'controller/account.php'; $pageData = updateAccountPage(); break;
	case "addassetsaccount": require 'controller/account.php'; $pageData = addAssetsAccountPage(); break;
	case "viewdeleteaccountasset": require 'controller/account.php'; $pageData = viewDeleteAccountAsset(); break;

	/* CONTACTS */
	case "addcontact": require 'controller/contact.php'; $pageData = addContactPage(); break;
	case "updatecontact": require 'controller/contact.php'; $pageData = updateContactPage(); break;
	case "managecontact": require 'controller/contact.php'; $pageData = manageContactPage(); break;
	case "deletecontact": require 'controller/contact.php'; $pageData = deleteContactPage(); break;
	
	/* JOBS */
	case "addjob": require 'controller/job.php'; $pageData = addJobPage(); break;
	case "viewjobcontacts": require 'controller/job.php'; $pageData = viewJobContactsPage(); break;
	case "addjobnote": require 'controller/job.php'; $pageData = addJobNotePage(); break;
	case "viewupdatedeletejobnote": require 'controller/job.php'; $pageData = viewUpdateDeleteNotePage(); break;
	case "addjobasset": require 'controller/job.php'; $pageData = addJobAssetPage(); break;
	case "viewdeletejobasset": require 'controller/job.php'; $pageData = viewDeleteJobAssetPage(); break;
	case "addjobhours": require 'controller/job.php'; $pageData = addJobHoursPage(); break;
	case "updatedeletejobhours": require 'controller/job.php'; $pageData = updateDeleteJobHoursPage(); break;
	case "printinvoice": require 'controller/job.php'; $pageData = printInvoicePage(); break;
	case "logout": require 'controller/login.php'; $pageData = logout(); break;
	default:  require 'controller/login.php'; $pageData = loginPage(); break;
}

?>