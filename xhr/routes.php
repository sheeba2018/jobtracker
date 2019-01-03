
<?php
$dataObj = $_POST['data'];

/* IF THERE IS A FILE INCLUDED THEN GRAB THE FILE ARRAY */
$file = $_FILES;

$dataObj = json_decode($dataObj);


error_log("hello how r u doing");


  error_log("".$dataObj->flag);
  switch($dataObj->flag){
    case 'login':require '../controller/login.php'; login($dataObj); break;
    

    /* ACCOUNT PHP FILE */
    case 'addAccount': require '../controller/account.php'; addAccount($dataObj); break;
    case 'getAccount': require '../controller/account.php'; getAccountInfo($dataObj); break;
    case 'updateAccount': require '../controller/account.php'; updateAccount($dataObj); break;
    case 'getaccounttable': require '../controller/account.php'; accountTable(); break;
    case 'deleteaccount': require '../controller/account.php'; deleteAccount($dataObj); break;
    case 'addassettoaccount': require '../controller/account.php'; addAsset($dataObj, $file); break;
    case 'viewdeleaccoutassets': require '../controller/account.php';viewDeleteAsset($dataObj);break;
    case 'delaccountasset': require '../controller/account.php';delAsset($dataObj);break;
    
    /* CONTACT PHP FILE */
    case 'addcontact': require '../controller/contact.php'; addContact($dataObj); break;
    //case 'getcontactlist': require '../controller/contact.php'; getContactList($dataObj); break;
    case 'getcontact': require '../controller/contact.php'; getContact($dataObj); break;
    case 'updatecontact': require '../controller/contact.php'; updateContact($dataObj); break;
    case 'managecontactinterface': require '../controller/contact.php';mcInterface($dataObj); break;
    case 'addassoc': require '../controller/contact.php'; addAssoc($dataObj); break;
   // case 'updateAssocTable': require '../controller/contact.php'; updateAssocTable($dataObj); break;
    case 'delAssoc': require '../controller/contact.php'; delAssoc($dataObj); break;
    case 'getcontacttable': require '../controller/contact.php'; contactTable(); break;
    case 'deletecontact': require '../controller/contact.php'; deleteContact($dataObj); break;
    

    /* JOB PHP FILE */
    case 'addjob': require '../controller/job.php'; addJob($dataObj); break;
    case 'addjobasset': require '../controller/job.php'; addAssetJob($dataObj, $file); break;
    case 'viewdelejobassets': require '../controller/job.php';viewDeleteAsset($dataObj);break;
    case 'getjobnotetable': require '../controller/job.php';viewJobNotes($dataObj);break;
    case 'deletenote': require '../controller/job.php';deleteNote($dataObj);break;
    case 'updatenote': require '../controller/job.php';updateNote($dataObj);break;
    case 'updatenoteform': require '../controller/job.php';updateNoteForm($dataObj);break;
    case 'deljobasset': require '../controller/job.php';delAsset($dataObj);break;
    case 'addjobnote': require '../controller/job.php';addJobNote($dataObj);break;
    case 'addhours': require '../controller/job.php';addHours($dataObj);break;
    case 'getjobhours': require '../controller/job.php';getJobHours($dataObj);break;
    case 'updatehours': require '../controller/job.php';updateHours($dataObj);break;
    case 'deletehours': require '../controller/job.php';deleteHours($dataObj);break;
    case 'getjobcontacts': require '../controller/job.php';getjobcontacts($dataObj);break;
    case 'gethoursupdateform': require '../controller/job.php';getHoursUpdateForm($dataObj);break;

    /* GENERAL PHP FILE*/
    case 'getaccountlist': require '../controller/general.php'; accountList(); break;
    case 'getcontactlist': require '../controller/general.php'; contactList(); break;
    case 'getJobList': require '../controller/general.php'; jobList($dataObj); break;
    

}

 /*switch($dataObj->flag){
	
	case 'gethoursupdateform' : require_once '../controller/job.php'; updateName($dataObj); break;
        case 'deletehours': require '../controller/job.php';deleteName($dataObj);break;
}*/