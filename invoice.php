<?php 
require_once 'classes/Pdo_methods.php';
require_once 'classes/Validation.php';
$pdo = new PdoMethods();
$Validation = new Validation();
setlocale(LC_MONETARY, 'en_US');
$id = $_GET['id'];
$begdate = $_GET['begdate'];
$enddate = $_GET['enddate'];
$tempArr = array($id, $begdate, $enddate);
$i = 0;
/* CHECK VALIDATION ON ALL PARAMETER VALUES IF ANY DON'T PASS REDIRECT TO INVOICE PAGE*/
while($i < count($tempArr)){
	if(!$Validation->validate('timestamp',$tempArr[$i])){
		/* WHEN CHANGING THE IP ADDRESS DON'T INCLUDE THE ANGLE BRACKETS*/
		header("Location: http://198.211.114.100/job_tracker_student_version_php/printinvoice/");
		break;
	}
	$i++;
}
/* IF THE BEGINNING DATE IS GREATER THAN ENDING DATE REDIRECT TO INVOICE PAGE */
if($begdate > $enddate){
	/*WHEN CHANGING THE IP ADDRESS DON'T INCLUDE THE ANGLE BRACKETS*/
	header("Location: http://198.211.114.100/job_tracker_student_version_php/printinvoice/");
}
$sql = "SELECT account.name, account.address, account.city, account.state, account.zip FROM account, job WHERE account.id = job.account_id AND job.id = :id";
$bindings = array(
	array(':id',$id,'str'),
);
$accountInfo = $pdo->selectBinded($sql, $bindings);
foreach($accountInfo as $row){
	$billto = '<h2>'.$row['name'].'</h2>';
	$billto .= '<address>'.$row['address'].'<br>'.$row['city'].' '.$row['state'].' '.$row['zip'].'</address>';
}
$sql = "SELECT job_date, job_hours, hourly_rate, description FROM job_hour WHERE job_id = :id AND job_date >= :begdate AND job_date <= :enddate";
$bindings = array(
	array(':id',$id,'int'),
	array(':begdate',$begdate,'str'),
	array(':enddate',$enddate,'str')
);
$jobHours = $pdo->selectBinded($sql, $bindings);
$table='<tbody>';
$grandtotal = 0;
foreach($jobHours as $row){
	
	$timestamp = $row['job_date']/1000;
	$date = date("m-d-Y", $timestamp);
	$sum = $row['job_hours'] * $row['hourly_rate'];
	$grandtotal += $sum;
	$formatsum = money_format('%.2n', $sum);
	$table .= '<tr><td>'.$date.'</td><td>'.$row['description'].'</td><td>'.$row['job_hours'].'</td><td>'.$row['hourly_rate'].'</td><td>'.$formatsum.'</td></tr>';
}
	$grandtotal = money_format('%.2n', $grandtotal);
	$table .= '</tbody><tfoot><tr><td colspan="4">Grand Total</td><td>'.$grandtotal.'</td></tr></tfoot>';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Invoice</title>
	
    <!--below is a script which allows IE 8 to understand HTML 5 elements.-->
    <!--[if lt IE 9]>
      <script>
        var elementsArray = ['abbr', 'article', 'aside', 'audio', 'bdi', 'canvas', 'data', 'datalist', 'details', 'figcaption', 'figure', 'footer', 'header', 'main', 'mark', 'meter', 'nav', 'output', 'progress', 'section', 'summary', 'template', 'time', 'video'];
        var len = elementsArray.length;
        for(i = 0; i < len; i++){
	      document.createElement(elementsArray[i]);
       }
      </script>
    <![endif]-->

    <!--CSS style sheets are here-->
    <link rel="stylesheet" href="public/css/invoice.css" />
  </head>
  <body>
    <div id="wrapper">
	    <header>
	    	<div id="se">
		    	<h1>CPS276</h1>
		    	<address>
		    		123 Anyplace, Somewhere MI<br>
		    		Phone: 999-999-9999 Email: person@cps276.com
		    	</address>
	    	</div>
	    	<div id="billto">
	    		<?php echo $billto; ?>
	    	</div>
	    </header>
	    <main>
	    	<table>
				<thead>
					<tr>
						<th>Date</th>
						<th>Description</th>
						<th>Hours</th>
						<th>Hourly Rate</th>
						<th>Total</th>
					</tr>
				</thead>
			
				<?php echo $table; ?>	
			
			</table>	
	    </main>
	 </div>
  </body>
</html>