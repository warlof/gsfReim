<div class="col-md-12">
	<div class="row">
		<div class="col-md-4 col-md-offset-2">
			<h3>My Losses</h3>
			<?php if($submitted->num_rows() > 0){
				foreach($submitted->result() as $row){
					switch($row->paid){
						case '0':
							$status = "PENDING";
							$note = "Your request has not been processed yet.";
						break;
						case '1':
							$status = "PAID";
							$note = "Payment Sent.<br>Reason Code: <b>" .$row->prefix."-".$row->killID."</b><br>Amount: <b>".number_format($row->payoutAmount)."</b><br>Processed On: <b>".$row->paidOn."</b>";
							if($row->note <> ''){
								$note .= "<br>Note From Reimburser: " . $row->note;
							}
						break;
						case '2':
							$status = "DENIED";
							$note = "Request Denied.<br>Reason: <pre><b>" . $row->note."</b></pre><br>Denied On: <b>".$row->deniedOn."</b>";
					}?>
					<div class="row well well-sm">
						<p>Victim: <b><?php echo $row->victimName; ?></b> <?php if($row->reservedBy != ''){?>| Handled By: <b><?php echo $row->reservedBy; } ?></b></p>
						<p>Loss Date: <b><?php echo $row->killTime; ?></b> | Submitted On: <b><?php echo $row->timestamp; ?></b></p>
						<p>System: <b><?php echo $row->sysName; ?></b> | Region Name: <b><?php echo $row->regName; ?></b> | Ship Type: <b><?php echo $row->shipName; ?></b></p>
						<p>Status: <b><?php echo $status; ?></b></p>
						<p>Note: <?php echo $note; ?></p>
					</div>
				<?php }
			} else { ?>
				<h3>You have not submitted any losses yet.</h3>
			<?php } ?>
		</div>
	</div>
</div>
