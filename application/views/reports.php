<script type="text/javascript" charset="utf-8" src="https://cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf-8" src="https://cdn.datatables.net/1.10.9/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" charset="utf-8" src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" charset="utf-8" src="https://cdn.datatables.net/buttons/1.0.3/js/buttons.bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.9/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.bootstrap.min.css">
<style>
	.ui-datepicker-calendar {
		display: none;
	}
</style>
<script>
	$(document).ready(function() {
		$("#filDate").datepicker({
			dateFormat: 'yy-mm',
			changeMonth: true,
			changeYear: true,
			onClose: function(dateText, inst){
				var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
				var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
				$(this).datepicker('setDate', new Date(year, month,1));
			}
		});
		$('#byShipTable').DataTable({buttons: ['csv','copy']});
		$('#byCorpTable').DataTable({buttons: ['csv','copy']});
		$('#byUserTable').DataTable({buttons: ['csv','copy']});
		$('#byRegionTable').DataTable({buttons: ['csv','copy']});
		$('#byTypeTable').DataTable({buttons: ['csv','copy']});
		$('#reimCapTable').DataTable({buttons: ['csv','copy']});
		$('#payoutsByUserTable').DataTable({buttons: ['csv','copy']});
		$('#allPayoutsTable').DataTable({
			"pageLength": 50,
			"order": [2, 'desc'],
			"buttons": ['csv','copy']
		});
		
	});
	function setDate(){
		var dt = document.getElementById("filDate").value;
		if(dt==''){
			alert("You must enter a date in format YYYY-MM");
		} else {
			$.ajax({
				url: "<?php echo base_url('reports/setDate'); ?>",
				type: 'POST',
				data: {date: dt},
				success: function(msg) {
					location.reload();
				}
			});
		}
	}
	function showNote(killid){
		var id = "#"+killid + "-NOTE";
		var content = $(id).html();
		$("#payoutNoteBody").html(content);
		$("#payoutNote").modal('show');
	}
</script>
<div class="col-md-12">
	<div class="row">
		<div class="col-md-2 col-md-offset-1">
			<div class="input-group">
				<span class="input-group-addon">Date</span>
				<input type="text" class="form-control" id="filDate" value="" placeholder="Enter Date, YYYY-MM"></input>
			</div>
			<br>
			<button type="button" class="btn btn-success" value="Re-Generate" onclick="setDate()">Re-Generate</button>
		</div>
		<div class="col-md-2 col-md-offset-1">
			<h4>Showing data for <b><?php echo $date; ?></b></h4>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#byUser" data-toggle="tab">Losses by Forum Account</a></li>
				<li><a href="#byShip" data-toggle="tab">Losses by Ship Type</a></li>
				<li><a href="#byCorp" data-toggle="tab">Losses by Corporation</a></li>
				<li><a href="#byRegion" data-toggle="tab">Losses by Region</a></li>
				<li><a href="#reimCap" data-toggle="tab">Reimbursement Cap by Forum Account</a></li>
				<li><a href="#byType" data-toggle="tab">Amounts by Payout Type</a></li>
				<li><a href="#payoutsByUser" data-toggle="tab">Payouts Done by User</a></li>
				<li><a href="#allPayouts" data-toggle="tab">Recent Payouts</a></li>
			</ul>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="tab-content">
				<div class="tab-pane active" id="byUser">
					<?php if($byUser->num_rows() > 0){ ?>
						<table class="table table-condensed table-striped" id="byUserTable">
							<thead>
								<tr>
									<th>Forum Name</th>
									<th>Count of Losses</th>
									<th>Sum of Losses</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($byUser->result() as $row){ ?>
									<tr>
										<td><?php echo $row->submittedBy; ?></td>
										<td><?php echo $row->count; ?></td>
										<td><?php echo number_format($row->total); ?></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<h4>There is no data to display.</h4>
					<?php }?>
				</div>
				<div class="tab-pane" id="byShip">
					<?php if($byShipType->num_rows() > 0){
						$totalc = 0;
						$totali = 0;?>
						<table class="table table-condensed table-striped" id="byShipTable">
							<thead>
								<tr>
									<th>ShipType</th>
									<th>Payout Type</th>
									<th>Count of Losses</th>
									<th>Sum of Losses</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($byShipType->result() as $row){
									$totalc = $totalc+$row->count;
									$totali = $totali+$row->total;
									?>
									<tr>
										<td><?php echo $row->shipName; ?></td>
										<td><?php echo $row->typeName; ?></td>
										<td><?php echo $row->count; ?></td>
										<td><?php echo number_format($row->total); ?></td>
									</tr>
								<?php } ?>
							</tbody>
							<tfoot>
								<tr>
									<td><b>Grand Total</b></td>
									<td></td>
									<td><b><?php echo $totalc; ?></b></td>
									<td><b><?php echo number_format($totali); ?></b></td>
								</tr>
							</tfoot>
						</table>
					<?php } else { ?>
						<h4>There is no data to display.</h4>
					<?php }?>
				</div>
				<div class="tab-pane" id="byCorp">
					<?php if($byCorp->num_rows() > 0){ 
						$totalc = 0;
						$totali = 0;?>
						<table class="table table-condensed table-striped" id="byCorpTable">
							<thead>
								<tr>
									<th>Corporation Name</th>
									<th>Count of Losses</th>
									<th>Sum of Losses</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($byCorp->result() as $row){ 
									$totalc = $totalc+$row->count;
									$totali = $totali+$row->total;?>
									<tr>
										<td><?php echo $row->corpName; ?></td>
										<td><?php echo $row->count; ?></td>
										<td><?php echo number_format($row->total); ?></td>
									</tr>
								<?php } ?>
							</tbody>
							<tfoot>
								<tr>
									<td><b>Grand Total</b></td>
									<td><b><?php echo $totalc; ?></b></td>
									<td><b><?php echo number_format($totali); ?></b></td>
								</tr>
							</tfoot>
						</table>
					<?php } else { ?>
						<h4>There is no data to display.</h4>
					<?php }?>
				</div>
				<div class="tab-pane" id="byRegion">
					<?php if($byRegion->num_rows() > 0){ 
						$totalc = 0;
						$totali = 0;?>
						<table class="table table-condensed table-striped" id="byRegionTable">
							<thead>
								<tr>
									<th>Region Name</th>
									<th>Count of Losses</th>
									<th>Sum of Losses</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($byRegion->result() as $row){
									$totalc = $totalc+$row->count;
									$totali = $totali+$row->total;?>
									<tr>
										<td><?php echo $row->regName; ?></td>
										<td><?php echo $row->count; ?></td>
										<td><?php echo number_format($row->total); ?></td>
									</tr>
								<?php } ?>
							</tbody>
							<tfoot>
								<tr>
									<td><b>Grand Total</b></td>
									<td><b><?php echo $totalc; ?></b></td>
									<td><b><?php echo number_format($totali); ?></b></td>
								</tr>
							</tfoot>
						</table>
					<?php } else { ?>
						<h4>There is no data to display.</h4>
					<?php }?>
				</div>
				<div class="tab-pane" id="reimCap">
					<?php if($capByUser->num_rows() > 0){ ?>
						<table class="table table-condensed table-striped" id="reimCapTable">
							<thead>
								<tr>
									<th>Forum Name</th>
									<th>Count of Losses</th>
									<th>Sum of Losses</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($capByUser->result() as $row){
									if($row->total >= $ptCap){
										$class = 'class="danger"';
									} else {
										$class = '';
									}?>
									<tr <?php echo $class; ?>>
										<td><?php echo $row->submittedBy; ?></td>
										<td><?php echo $row->count; ?></td>
										<td><?php echo number_format($row->total); ?></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<h4>There is no data to display.</h4>
					<?php }?>
				</div>
				<div class="tab-pane" id="byType">
					<?php if($byType->num_rows() > 0){ 
						$totali = 0;?>
						<table class="table table-condensed table-striped" id="byTypeTable">
							<thead>
								<tr>
									<th>Payout Type</th>
									<th>Sum of Losses</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($byType->result() as $row){
									$totali = $totali + $row->total;?>
									<tr>
										<td><?php echo $row->typeName; ?></td>
										<td><?php echo number_format($row->total); ?></td>
									</tr>
								<?php } ?>
							</tbody>
							<tfoot>
								<tr>
									<td><b>Grand Total</b></td>
									<td><b><?php echo number_format($totali); ?></b></td>
								</tr>
							</tfoot>
						</table>
					<?php } else { ?>
						<h4>There is no data to display.</h4>
					<?php }?>
				</div>
				<div class="tab-pane" id="payoutsByUser">
					<?php if($payoutsByUser->num_rows() > 0){ ?>
						<table class="table table-condensed table-striped" id="payoutsByUserTable">
							<thead>
								<tr>
									<th>Forum Name</th>
									<th>Count of Payouts</th>
									<th>Sum of Payouts</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($payoutsByUser->result() as $row){?>
									<tr>
										<td><?php echo $row->paidBy; ?></td>
										<td><?php echo $row->count; ?></td>
										<td><?php echo number_format($row->total); ?></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<h4>There is no data to display.</h4>
					<?php }?>
				</div>
				<div class="tab-pane" id="allPayouts">
					<?php if($allPayouts->num_rows() > 0){ ?>
						<table class="table table-condensed table-striped" id="allPayoutsTable">
							<thead>
								<tr>
									<th></th>
									<th>Forum Name</th>
									<th>Corporation</th>
									<th>Loss Date</th>
									<th>System</th>
									<th>Region</th>
									<th>Ship</th>
									<th>Payout Type</th>
									<th>Payout Amount</th>
									<th>Payment Code</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($allPayouts->result() as $row){?>
									<tr>
										<td><?php if($row->note <> '' && $row->note != NULL){?> <button type="button" class="btn btn-info btn-xs" onclick="showNote(<?php echo $row->killID; ?>)" value="Show Note">Show Note</button><?php } ?></td>
										<td><?php echo $row->submittedBy; ?></td>
										<td><?php echo $row->corpName; ?></td>
										<td><?php echo $row->killTime; ?></td>
										<td><?php echo $row->sysName; ?></td>
										<td><?php echo $row->regName; ?></td>
										<td><?php echo $row->shipName; ?></td>
										<td><?php echo $row->payoutType; ?></td>
										<td><?php echo number_format($row->payoutAmount); ?></td>
										<td><?php echo $row->prefix.'-'.$row->killID; ?></td>
									</tr>
									<?php if($row->note <> '' && $row->note != NULL){ ?>
									<div style="display: none;" id="<?php echo $row->killID; ?>-NOTE">
										<?php print str_replace("\n","<br>",$row->note);?>
									</div>
								<?php } ?>
								<?php } ?>
							</tbody>
						</table>
					<?php } else { ?>
						<h4>There is no data to display.</h4>
					<?php }?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="payoutNote" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" style="width: 30%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Note:</h4>
      </div>
      <div class="modal-body" id="payoutNoteBody">
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
