<link rel="stylesheet" type="text/css" href="/assets/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="/assets/js/jquery.dataTables.js"></script>
<script>
	$(document).ready(function() {
		$('#logTable').DataTable({
			"pageLength": 100,
			"order": [3, 'desc']
		});
	});
</script>
<div class="col-md-12">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<?php if($logdata->num_rows() > 0){ ?>
				<table calss="table table-condensed table-striped" id="logTable">
					<thead>
						<tr>
							<th>User</th>
							<th>Type</th>
							<th>Data</th>
							<th>Date</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($logdata->result() as $row){ ?>
							<tr>
								<td><?php echo $row->user; ?></td>
								<td><?php echo $row->type; ?></td>
								<td><?php echo $row->data; ?></td>
								<td><?php echo $row->eventtimedate; ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } else { ?>
				<h4>There is no log data to display here.</h4>
			<?php } ?>
		</div>
	</div>
</div>
