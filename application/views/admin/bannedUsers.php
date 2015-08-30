<script>
	function unBan(id){
		$.ajax({
			url: "<?php echo base_url('admin/unbanUser'); ?>",
			type: 'POST',
			data: {banID: id},
			success: function(msg) {
				alert(msg);
				location.reload();
			}
		});
	}
</script>
<div class="col-md-8 col-md-offset-1">
	<?php if($bannedUsers->num_rows(0) > 0){ ?>
		<h3>Currently Banned Users</h3>
		<?php foreach($bannedUsers->result() as $row){ ?>
			<div class="row">
				<div class="well well-sm">
					<p>User Name: <b><?php echo $row->userName; ?></b> | <button type="button" class="btn btn-xs btn-success" value="Unban" onclick="unBan(<?php echo $row->id; ?>)">Unban</button></p>
					<p>Banned On: <b><?php echo $row->banStart; ?></b></p>
					<p>Ban Expires On: <b><?php echo $row->banEnd; ?></b></p>
					<hr>
					<p>Reason:</p>
					<pre><?php echo $row->reason; ?></pre>
				</div>
			</div>
		<?php } ?>
	<?php } else { ?>
		<h4>There are no banned users currently.</h4>
	<?php }?>
</div>
