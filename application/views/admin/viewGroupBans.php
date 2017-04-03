<script>
	function unBan(id){
		$.ajax({
			url: "<?php echo base_url('admin/unbanGroup'); ?>",
			type: 'POST',
			data: {banID: id},
			success: function(msg) {
				if (msg.length > 0) {
					alert(msg);
				}
				location.reload();
			}
		});
	}

	function banGroup(){
		var groupName = document.getElementById("groupName").value;
		var reason = document.getElementById("banReason").value;

		if (reason.length < 5) {
			alert("You must enter a reason for the ban.");
		} else {
			$.ajax({
				url: "<?php echo base_url('admin/banGroup'); ?>",
				type: 'POST',
				data: {groupName: groupName, reason: reason},
				success: function(msg) {
					if (msg.length > 0) {
						alert(msg);
					}
					location.reload();
				}
			});
		}
	}

</script>
<div class="col-md-12">
	<div class="row">
		<div class="col-md-8 col-md-offset-1">
			<h4>Add New Ban</h4>
			<div class="input-group">
				<span class="input-group-addon">Group Name</span>
				<input type="text" class="form-control" id="groupName" name="groupName"></input>
			</div>
			<br>
			<div class="input-group">
				<span class="input-group-addon">Reason</span>
				<textarea class="form-control" id="banReason" rows="5"></textarea>
			</div>
			<br>
			<button class="btn btn-success" type="button" onclick="banGroup()" value="Ban">Ban</button>
		</div>
	</div>
	<hr>
	<div class="row">
		<div class="col-md-8 col-md-offset-1">
			<?php if($groupBans->num_rows() > 0){ ?>
				<h3>Currently Banned Groups</h3>
				<?php foreach($groupBans->result() as $row){ ?>
					<div class="row">
						<div class="well well-sm">
							<p>Group Name: <b><?php echo $row->groupName; ?></b> | <button type="button" class="btn btn-xs btn-success" value="Unban" onclick="unBan(<?php echo $row->id; ?>)">Unban</button></p>
							<p>Banned On: <b><?php echo $row->banDate; ?></b></p>
							<p>Banned By: <b><?php echo $row->bannedBy; ?></b></p>
							<hr>
							<p>Reason:</p>
							<pre><?php echo $row->reason; ?></pre>
						</div>
					</div>
				<?php } ?>
			<?php } else { ?>
				<h4>There are no banned groups currently.</h4>
			<?php }?>
		</div>
	</div>
</div>

