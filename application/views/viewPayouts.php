<link rel="stylesheet" type="text/css" href="/assets/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="/assets/js/jquery.dataTables.js"></script>
<script>
	$(document).ready(function() {
		$('#payoutsTable').DataTable({
			"pageLength": 50
		});
	});
</script>
<div class="col-md-10 col-md-offset-1">
	<div class="row">
		<?php if(count($payouts) > 0){ ?>
			<table class="table table-condensed table-striped" id = "payoutsTable">
				<thead>
					<tr>
						<th>Ship Name</th>
						<?php foreach($payoutTypes->result() as $pt){ ?>
							<th><?php echo $pt->typeName; ?></th>
						<?php } ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach($payouts as $key => $value){ ?>
						<tr>
							<td><?php echo $key; ?></td>
							<?php foreach($value as $k => $v){ ?>
								<td><?php echo number_format($v); ?></td>
							<?php } ?>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php } else { ?>
			<h4>There are no payouts to display.</h4>
		<?php } ?>
	</div>
</div>
