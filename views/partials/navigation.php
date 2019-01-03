
<?php
	/*THIS PAGE HAD TO BE A PHP PAGE BECAUSE WE NEEDED TO INJECT THE BASE PATH INTO EVERY LINK.  BY DOING THIS IF WE MOVE TO ANOTHER SERVER ALL WE NEED TO DO IS REPLACE THIS WITH THAT SERVERS ADDRESS.*/
	$baseURL = "http://198.211.114.100/job_tracker_student_version_php/";
?>
<div id="mainnav">
	<div class="container">
		<div class="row">
			<div class="col-xs-2">
				<button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="glyphicon glyphicon-align-justify"></span>
				</button>
			</div>
			<div class="col-md-12 col-sm-12 col-xs-12">
			
				<nav class="navbar-collapse collapse">
				<ul class="nav navbar-nav auto">
					<li><a href=<?php echo '"'.$baseURL.'home/">'; ?>Home</a></li>

				<!-- DROPDOWN -->
					<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Accounts<span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href=<?php echo '"'.$baseURL.'addaccount/">'; ?>Add Account</a></li>
							<li><a href=<?php echo '"'.$baseURL.'updateaccount/">'; ?>Update Account</a></li>
							<li><a href=<?php echo '"'.$baseURL.'addassetsaccount/">'; ?>Add Assets to Account</a></li>
							<li><a href=<?php echo '"'.$baseURL.'viewdeleteaccountasset/">'; ?>View Delete Account Asset</a></li>
						</ul></li>

					<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Contacts<span class="caret"></span></a>
						<ul class="dropdown-menu">
						<li><a href=<?php echo '"'.$baseURL.'addcontact/">'; ?>Add Contact</a></li>
						<li><a href=<?php echo '"'.$baseURL.'updatecontact/">'; ?>Update Contact</a></li>
						<li><a href=<?php echo '"'.$baseURL.'managecontact/">'; ?>Manage Contacts</a></li>
						<li><a href=<?php echo '"'.$baseURL.'deletecontact/">'; ?>Delete Contacts</a></li>
					</ul></li>

					<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Jobs<span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href=<?php echo '"'.$baseURL.'addjob/">'; ?>Add Job</a></li>
							<li><a href=<?php echo '"'.$baseURL.'viewjobcontacts/">'; ?>View Job Contacts</a></li>
							<li><a href=<?php echo '"'.$baseURL.'addjobnote/">'; ?>Add Job Notes</a></li>
							<li><a href=<?php echo '"'.$baseURL.'viewupdatedeletejobnote/">'; ?>View/Update/Delete Job Notes</a></li>
							<li><a href=<?php echo '"'.$baseURL.'addjobasset/">'; ?>Add Job Asset</a></li>
							<li><a href=<?php echo '"'.$baseURL.'viewdeletejobasset/">'; ?>View/Delete Job Assets</a></li>
							<li><a href=<?php echo '"'.$baseURL.'addjobhours/">'; ?>Add Job Hours</a></li>
							<li><a href=<?php echo '"'.$baseURL.'updatedeletejobhours/">'; ?>Update/Delete Job Hours</a></li>
							<li><a href=<?php echo '"'.$baseURL.'printinvoice/">'; ?>Print Invoice</a></li>
						</ul></li>
					<li><a href=<?php echo '"'.$baseURL.'logout/">'; ?>Logout</a></li>
				</ul></nav>
			</div><!-- end div that contains columns-->
		</div>
	</div>
</div><!-- end mainnav -->