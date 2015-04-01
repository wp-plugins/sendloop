<script type="text/javascript" src="<?php print(plugin_dir_url('sendloop/sendloop.php') .'js/jquery-1.9.1.min.js'); ?>"></script>

<div class="wrap">
	<?php screen_icon('users'); ?>
	<h2>Recent Subscriptions</h2>

	<p>Browse the most recent 100 subscribers for each your list:</p>

	<div class="tablenav top">
		<div class="alignleft actions">
			<select name="TargetListID" id="TargetListID">
				<?php if (count($SubscriberLists) > 0): ?>
					<?php foreach ($SubscriberLists as $Index=>$EachList): ?>
						<option value="<?php print($EachList['ListID']); ?>" <?php print(((isset($_POST['TargetListID']) == true && $_POST['TargetListID'] == $EachList['ListID']) || (isset($_POST['TargetListID']) == false && get_option('sendloop_target_listid') == $EachList['ListID']) ? 'selected="selected"' : '')); ?>><?php print($EachList['Name']); ?> (<?php print(number_format($EachList['ActiveSubscribers']).' '.($EachList['ActiveSubscribers'] < 2 ? 'subscriber' : 'subscribers')); ?>)</option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
			<input type="submit" name="" id="" class="button action" value="Browse Subscriber List">
			<br class="clear">
		</div>
	</div>

	<table class="wp-list-table widefat fixed posts" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" id="cb" class="manage column column-cb check-column">Deneme</th>
			</tr>
		</thead>
	</table>

</div>