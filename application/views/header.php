<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>GSF Affordable Care</title>
	<link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.min.css" />
	<!--<link rel="stylesheet" type="text/css" href="/assets/css/bootstrap-responsive.min.css" />-->
	<link rel="icon" type="image/x-icon" href="/favicon.ico">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
	<script src="/assets/js/jquery-2.1.1.min.js" type="text/javascript"></script>
	<script src="/assets/js/bootstrap.min.js" type="text/javascript"></script>

	<link rel="stylesheet" href="/assets/css/jquery-ui.autocomplete.css" />
	<script src="/assets/js/jquery-ui.js" type="text/javascript"></script>
	<style>
		.ui-datepicker{z-index:115111 !important;}
		.ui-autocomplete{z-index:115111 !important;}
	</style>
	
</head>

<script type="text/javascript">
$(document).ready(function() {
	var skClone = $("#submitKillBody").clone();
	$("#submitKillShow").click(function(){
		$("#submitKill").modal('show');
	})
		$("#banUserShow").click(function(){
			$("#banUser").modal('show');
		})
	$('#submitKill').on('hidden.bs.modal', function () {
		var skClone1 = skClone.clone();
		$("#submitKillBody").replaceWith(skClone1);
	})
	
});

	function submitKill() {
		var crestLink = document.getElementById("crestLink").value;
		var bcast = document.getElementById("bcast").value;
		
		if(crestLink==null || crestLink==""){
			alert("You must enter a CREST Link");
		} else {
			$('#submitKillBody').html('<center><img src="/assets/img/ajax-loader.gif" /></center>')
			$.ajax({
				url: "<?php echo base_url('home/submitKill'); ?>",
				type: 'POST',
				data: {crestLink: crestLink, bcast: bcast},
				success: function(msg) {
					$('#submitKillBody').html(msg);
				}
			});
		}
	}
</script>
<?php if($this->config->item("AUTH_METHOD") == "INTERNAL" && $this->config->item("ALLOW_REGISTRATION")){ ?>
	<script>
		$(document).ready(function() {
			$("#registerAccount").click(function(){
				$("#regUser").modal('show');
			})
		});
		function registerUser(){
			var user = document.getElementById("regUserName").value;
			var pass = document.getElementById("regUserPassword").value;
			$.ajax({
				url: "<?php echo base_url('login/register'); ?>",
				type: 'POST',
				data: {user: user, password: pass},
				success: function(msg) {
					$('#regUserBody').html(msg);
					if(msg == "User Exists. Please refresh the page and try again, or wait 3 seconds for the page to automatically refresh."){
						setTimeout(function(){ location.reload(1); }, 3000);
					}
				}
			});
		}
	</script>
<?php } ?>
<?php
$vars = $this->session->userdata('vars');
if($vars['isReimDir'] == 1){?>
	<script>
		$(document).ready(function() {
			$("#banUserShow").click(function(){
				$("#banUser").modal('show');
			});
			$('#banUser').on('hidden.bs.modal', function () {
				location.reload();
			});
			$("#banUserName").autocomplete({
				source: "<?php echo base_url('/admin/getUserAutocomplete'); ?>",
				cacheLength: 1,
				minLength: 3
			});
			$("#banUserEndDate").datepicker({
				minDate: '+1d',
				dateFormat: 'yy-mm-dd',
				changeMonth: true,
				changeYear: true
			});
		});
		
		function banUser() {
			var userName = document.getElementById("banUserName").value;
			var reason = document.getElementById("banUserReason").value;
			var endDate = document.getElementById("banUserEndDate").value;
			
//			alert(userName+" | "+reason+" | "+endDate);
			if(userName=='' || reason=='' || endDate==''){
				alert("You must specify a user, reason, and end date.");
			} else {
				$.ajax({
					url: "<?php echo base_url('admin/banUser'); ?>",
					type: 'POST',
					data: {userName: userName, reason: reason, expiry: endDate},
					success: function(msg) {
						$('#banUserBody').html(msg);
					}
				});
			}
		}
	</script>
<?php } ?>
<body>
	<nav class="navbar navbar-default" role="navigation">
	  <div class="container-fluid">
	    <!-- Brand and toggle get grouped for better mobile display -->
	    <div class="navbar-header">
	      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
	        <span class="sr-only">Toggle navigation</span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	      </button>
	      <a class="navbar-brand" href="<?php echo base_url('home'); ?>">GSF Affordable Care</a>
	    </div>
	
	    <!-- Collect the nav links, forms, and other content for toggling -->
	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	      <ul class="nav navbar-nav">
	        <li><?php echo anchor('home', 'Home'); ?></a></li>
	        <li><?php echo anchor("home/viewPayouts", 'View Payouts'); ?></li>
	        <?php if($vars['logged_in'] && $vars['isBanned'] == 0){ ?>
		        <li><a href="#" id="submitKillShow">Submit Loss</a></li>
		        <li><?php echo anchor('home/mySubmitted', "My Losses"); ?></li>
		        <li><?php echo anchor('reports', 'Reports'); ?></li>
		        <?php if($vars['isReim'] == 1 || $vars['isReimDir'] == 1){ ?>
		        	<li><?php echo anchor('home/myReserved', 'Reserve Block'); ?></li>
		        <?php } ?>
		        <?php if($vars['isReimDir'] == 1 || $vars['isReim'] == 1){ ?>
			        <li class="dropdown">
			        	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Admin <span class="caret"></span></a>
			        	<ul class="dropdown-menu" role="menu">
			        		<?php if($vars['isReimDir'] == 1){ ?>
			        		<li><?php echo anchor('admin/regionMgmt','Region Management'); ?></li>
			        		<li><?php echo anchor('admin/payoutMgmt', 'Payout Management'); ?></li>
			        		<li><?php echo anchor('admin/payoutTypes', 'Payout Types'); ?></li>
			        		<li><?php echo anchor('admin/viewReserved', 'View Reserved Losses'); ?></li>
			        		<li><?php echo anchor('admin/viewBanned', 'View Banned Users'); ?></li>
			        		<li><?php echo anchor('admin/viewSettings', 'Preferences'); ?></li>
			        		<li><?php echo anchor('admin/viewLog', 'View Logs'); ?></li>
			        		<?php if($this->config->item('AUTH_METHOD') == "INTERNAL"){ ?>
			        			<li><?php echo anchor('admin/userMgmt', "User Management"); ?></li>
			        		<?php } ?>
			        		<?php } ?>
			        		<li><?php echo anchor('admin/viewDenied', 'View Denied Payouts'); ?></li>
			        	</ul>
			        </li>
			        <?php if($vars['isReimDir'] == 1){ ?>
			        <li><a href="#" id="banUserShow">Ban User</a></li>
			    <?php }
					} ?>
		      
		     <?php } ?>
		     </ul>
			<ul class="navbar-form navbar-right">
				<?php if (!$vars['logged_in']) { ?>
				<?php echo form_open('login'); ?>
	    			<div class="form-group">
						<input class="form-control" type="text" id="user" name="user" placeholder="User Name">
					</div>
	    			<div class="form-group">			
						<input class="form-control" type="password" id="password" name="password" placeholder="Password">
					</div>
					<button type="submit" class="btn btn-primary">Login</button>
					<?php
					if($this->config->item('AUTH_METHOD') == "INTERNAL" && $this->config->item("ALLOW_REGISTRATION")){ ?>
						<button type="button" class="btn btn-success" id="registerAccount" value="Register">Register</button>
					<?php } ?>
					</form>
					
				<?php } else { ?>
					<span>Welcome, <?php echo $vars['user']; ?></span>
					<?php echo anchor('login/logout','Logout', 'class="btn btn-danger"');
				} ?>
			</ul>
	    </div><!-- /.navbar-collapse -->
	  </div><!-- /.container-fluid -->
	</nav>
<div class="modal fade" id="submitKill" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" style="width: 50%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Submit Kill</h4>
      </div>
      <div class="modal-body" id="submitKillBody">
		<div class="input-group">
			<span class="input-group-addon">CREST Link</span>
			<?php echo form_input(array('name' => 'crestLink', 'id' => 'crestLink', 'class' => 'form-control')); ?>
		</div>
		<br>
		<div class="input-group">
			<span class="input-group-addon">Broadcast or Op Post</span>
			<?php echo form_textarea(array('name' => 'bcast', 'id' => 'bcast', 'class' => 'form-control')); ?>
		</div>
		<br>
		<?php echo form_submit(array('name' => 'submit', 'id' => 'submit', 'value' => 'Submit', 'class' => 'btn btn-success', 'onclick' => 'submitKill()')); ?>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php if($vars["isReimDir"] == 1){ ?>
	<div class="modal fade" id="banUser" tabindex="-1" role="dialog" aria-hidden="true">
	  <div class="modal-dialog" style="width: 50%">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4 class="modal-title">Ban User</h4>
	      </div>
	      <div class="modal-body" id="banUserBody">
			<div class="input-group">
				<span class="input-group-addon">User Name</span>
				<input type="text" class="form-control" id="banUserName" name="banUserName"></input>
			</div>
			<br>
			<div class="input-group">
				<span class="input-group-addon">Reason</span>
				<textarea class="form-control" id="banUserReason" rows="5"></textarea>
			</div>
			<br>
			<div class="input-group">
				<span class="input-group-addon">Ban End Date</span>
				<input type="text" class="form-control" id="banUserEndDate"></input>
			</div>
			<br>
			<button btype="button" class="btn btn-warning" onclick="banUser()" value="Ban User">Ban User</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
<?php } ?>
<?php if($this->config->item("AUTH_METHOD") == "INTERNAL"){ ?>
		<div class="modal fade" id="regUser" tabindex="-1" role="dialog" aria-hidden="true">
	  <div class="modal-dialog" style="width: 50%">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4 class="modal-title">Register</h4>
	      </div>
	      <div class="modal-body" id="regUserBody">
			<div class="input-group">
				<span class="input-group-addon">User Name</span>
				<input type="text" class="form-control" id="regUserName" name="regUserName"></input>
			</div>
			<br>
			<div class="input-group">
				<span class="input-group-addon">Password</span>
				<input type="password" class="form-control" id="regUserPassword"></input>
			</div>
			<br>
			<button btype="button" class="btn btn-success" onclick="registerUser()" value="Register">Register</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
<?php } ?>
