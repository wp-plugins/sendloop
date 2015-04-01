<script type="text/javascript" src="<?php print(plugin_dir_url('sendloop/sendloop.php') .'js/jquery-1.9.1.min.js'); ?>"></script>

<div class="wrap">
	<?php screen_icon('options-general'); ?>
	<h2>Sendloop Settings</h2>
	<?php if (isset($PageMessage) == true && is_array($PageMessage) == true && isset($PageMessage['Type']) == true && $PageMessage['Type'] == 'error'): ?>
		<div class="error settings-error"><p><strong><?php print($PageMessage['Message']); ?></strong></p></div>
	<?php elseif (isset($PageMessage) == true && is_array($PageMessage) == true && isset($PageMessage['Type']) == true && $PageMessage['Type'] == 'success'): ?>
		<div class="updated settings-error"><p><strong><?php print($PageMessage['Message']); ?></strong></p></div>
	<?php endif; ?>
	<p>Sendloop Subscribe plug-in will let you to add mail list subscription form to your WordPress powered site or blog as easy as possible. <a href="http://sendloop.com/help/article/service-integration-009/wordpress-integration" target="_blank">Click here to learn more</a> about WordPress plug-in and how to set it up.</p>
	<p>Once you have configured and saved your settings, <a href="<?php print(get_site_url()); ?>/wp-admin/widgets.php">go to Widgets section</a> and add &quot;Sendloop Subscription Form&quot; widget to the sidebar area.</p>
	<form method="post" action="<?php print($_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="FormType" id="FormType" value="Link">
		<?php if ($ShowStep2 == true): ?>
			<h3>Subscription Form Settings</h3>
			<table class="form-table">
				<?php if (1==2): ?>
					<tr valign="top">
						<th scope="row">
							<strong>Subscription form type</strong>
							<p class="description"><small>Select the form type you want to publish on your blog/website.</small></p>
						</th>
						<td>
							<label>
								<input style="float:left;" type="radio" name="FormType" id="FormType_Standard" value="Standard" <?php print(((isset($_POST['FormType']) == true && $_POST['FormType'] == 'Standard') || (isset($_POST['FormType']) == false && get_option('sendloop_form_type') == 'Standard') ? 'checked="checked"' : '')); ?>>
								<div style="float:left;margin-left:10px;margin-top:-2px;">
									<span><strong>Standard form</strong><br>This is the standard form which visitor enters the email address and any other additional information.</span>
								</div>
							</label><br style="clear:both;">
							<label>
								<input style="float:left;" type="radio" name="FormType" id="FormType_Link" value="Link" <?php print(((isset($_POST['FormType']) == true && $_POST['FormType'] == 'Link') || (isset($_POST['FormType']) == false && get_option('sendloop_form_type') == 'Link') ? 'checked="checked"' : '')); ?>>
								<div style="float:left;margin-left:10px;margin-top:-2px;">
									<span><strong>Link to subscription form</strong><br>This form type doesn't include email address or any other fields. It simply shows a link to your subscription form on Sendloop.</span>
								</div>
							</label><br style="clear:both;">
						</td>
					</tr>
				<?php endif; ?>
				<tr valign="top">
					<th scope="row">
						<strong>Custom CSS styling</strong>
						<p class="description"><small>Define your own CSS styling for the subscription widget. Subscription widget is wrapped with <code>div.sendloop-widget</code> block</small></p>
					</th>
					<td>
						<textarea name="CustomCSS" id="CustomCSS" class="large-text code" rows="3"><?php print((isset($_POST['CustomCSS']) == true ? $_POST['CustomCSS'] : get_option('sendloop_customcss'))); ?></textarea>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<strong>Shortcode CSS styling</strong>
						<p class="description"><small>Define your own CSS styling for the shortcode. Shortcode block is wrapped with <code>div.sendloop-shortcode-widget-container</code> block. <a href="options-general.php?page=sendloop-settings&reset_shortcode_css=true">Reset to default CSS</a></small></p>
					</th>
					<td>
						<textarea name="ShortcodeCustomCSS" id="ShortcodeCustomCSS" class="large-text code" rows="3"><?php print((isset($_POST['ShortcodeCustomCSS']) == true ? $_POST['ShortcodeCustomCSS'] : get_option('sendloop_shortcodecustomcss'))); ?></textarea>
					</td>
				</tr>
			</table>
		<?php endif; ?>

		<h3>Connection Settings</h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<strong>Sendloop API key</strong>
					<p class="description"><small>This is the API key which belongs to your Sendloop account. <a href="http://sendloop.com/help/article/api-001/getting-started" target="_blank">Click here</a> to learn how to get your API key.</small></p>
				</th>
				<td>
					<input type="text" class="regular-text code" id="SendloopAPIKey" name="SendloopAPIKey" value="<?php print((isset($_POST['SendloopAPIKey']) == true ? $_POST['SendloopAPIKey'] : get_option('sendloop_apikey'))); ?>">
				</td>
			</tr>
			<?php if (isset($_POST['SaveSettings']) == true || $ShowStep2 == true): ?>
				<tr valign="top">
					<th scope="row">
						<strong>Select target list</strong>
						<p class="description"><small>Select the subscriber list you want to add new subscribers to. If you want to build a separate subscriber mail list, simply create a new one on the right.</small></p>
					</th>
					<td>
						<select name="TargetListID" id="TargetListID">
							<option value="" <?php print(((isset($_POST['TargetListID']) == true && $_POST['TargetListID'] == '') || (isset($_POST['TargetListID']) == false && get_option('sendloop_target_listid') == '') ? 'selected="selected"' : '')); ?>>Please select below</option>
							<option value="New" <?php print(((isset($_POST['TargetListID']) == true && $_POST['TargetListID'] == 'New') || (isset($_POST['TargetListID']) == false && get_option('sendloop_target_listid') == 'New') ? 'selected="selected"' : '')); ?>>+ Create a new list</option>
							<?php if (count($SubscriberLists) > 0): ?>
								<?php foreach ($SubscriberLists as $Index=>$EachList): ?>
									<option value="<?php print($EachList['ListID']); ?>" <?php print(((isset($_POST['TargetListID']) == true && $_POST['TargetListID'] == $EachList['ListID']) || (isset($_POST['TargetListID']) == false && get_option('sendloop_target_listid') == $EachList['ListID']) ? 'selected="selected"' : '')); ?>><?php print($EachList['Name']); ?> (<?php print(number_format($EachList['ActiveSubscribers']).' '.($EachList['ActiveSubscribers'] < 2 ? 'subscriber' : 'subscribers')); ?>)</option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
							<input type="text" id="NewListName" name="NewListName" value="<?php print((isset($_POST['NewListName']) == true ? $_POST['NewListName'] : '')); ?>" placeholder="Enter a name for your new list" style="display:none;">
					</td>
				</tr>
			<?php endif; ?>
		</table>
		<?php if (count($SubscriberLists) > 0): ?>
			<?php submit_button("Save Settings", "primary", "SaveSettings", true); ?>
		<?php else: ?>
			<?php submit_button("Connect and fetch subscriber lists", "primary", "ConnectToSendloop", true); ?>
		<?php endif; ?>
	</form>


</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#TargetListID').on('change', function() {
			if ($('option:selected', this).val() == 'New') {
				$('#NewListName').show();
			} else {
				$('#NewListName').hide();
			}
		});

		if ($('#TargetListID option:selected').val() == 'New') {
//		if ($('input[name="TargetListID"]:checked').val() == 'New') {
			$('#NewListName').show();
		}
		else {
			$('#NewListName').hide();
		}
	});
</script>
