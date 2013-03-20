<?php if ($IsSetupCompleted == false): ?>
<p style="margin-top:20px; margin-bottom:40px; color:#a20000;">You haven't configured the plug-in yet. Please
	<a style="color:#a20000;" href="<?php print(get_site_url()); ?>/wp-admin/options-general.php?page=sendloop-settings">click here</a> to go to plug-in settings page to start configuring.
</p>
<?php else: ?>

	<p>
		<small>You can customize your subscription form below. If you want to change the form type or target subscriber list, please visit
			<a href="<?php print(get_site_url()); ?>/wp-admin/options-general.php?page=sendloop-settings">plug-in settings page</a>.
		</small>
	</p>

	<?php if (1==2): ?>
		<p>
			<label><strong>Form Type</strong> <small> [<a href="<?php print(get_site_url()); ?>/wp-admin/options-general.php?page=sendloop-settings">change</a>]</small><br></label>
			<?php if($SubscriptionFormType == 'Standard'): ?>
				Standard form
			<?php elseif($SubscriptionFormType == 'Link'): ?>
				Link to subscription form
			<?php endif; ?>
		</p>
	<?php endif; ?>

	<p>
		<label for="FormTitle"><strong>Title</strong><br>
			<small>This is the form title</small>
		</label>
		<input type="text" id="FormTitle" name="FormTitle" value="<?php print((isset($_POST['FormTitle']) == true ? $_POST['FormTitle'] : $SubscriptionFormSettings->FormTitle)); ?>" style="width:100%;">
	</p>

	<p>
		<label for="Message"><strong>Message</strong><br>
			<small>Encourage them to leave their email address with a short message</small>
		</label>
		<input type="text" id="Message" name="Message" value="<?php print((isset($_POST['Message']) == true ? $_POST['Message'] : $SubscriptionFormSettings->Message)); ?>" style="width:100%;">
	</p>

	<?php if ($SubscriptionFormType == 'Standard'): ?>

		<link rel="stylesheet" href="<?php print(plugin_dir_url('sendloop/sendloop.php') . 'js/jquery-ui-1.10.2.custom/css/ui-lightness/jquery-ui-1.10.2.custom.min.css'); ?>"/>
		<script type="text/javascript" src="<?php print(plugin_dir_url('sendloop/sendloop.php') . 'js/jquery-1.9.1.min.js'); ?>"></script>
		<script type="text/javascript" src="<?php print(plugin_dir_url('sendloop/sendloop.php') . 'js/jquery-ui-1.10.2.custom/js/jquery-ui-1.10.2.custom.min.js'); ?>"></script>

		<style type="text/css">
			#sendloop-sortable {
				list-style-type: none;
				margin: 0;
				padding: 0;
			}

			#sendloop-sortable li {
				margin: 0;
				padding: 0 0 0 10px;
			}

			#sendloop-sortable li span {
				position: absolute;
				margin-left: -1.3em;
			}
		</style>

		<script>
			$(document).ready(function () {
//				$(".sendloop-sortable").sortable();
//				$(".sendloop-sortable").disableSelection();

				$('.js-emailaddress-field').click(function() {
					if ($(this).is(':checked') == false) {
						$(this).prop('checked', true);
						alert('You can not remove email address from the subscriber form. It\'s meaningless ;)');
					}
				});

				$('.js-use-ajax').on('click', function() {
					var ParentForm = $(this).parents('form:first');

					if ($(this).is(':checked') == true) {
						$('.js-submit-on-new-window', ParentForm).prop('disabled', true);
						$('.js-submit-on-new-window-label', ParentForm).css('color', '#ccc');
					} else {
						$('.js-submit-on-new-window', ParentForm).prop('checked', false);
						$('.js-submit-on-new-window', ParentForm).prop('disabled', false);
						$('.js-submit-on-new-window-label', ParentForm).css('color', '#000');
					}
				});

				// On-load defaults
				$('.js-use-ajax').each(function() {
					var ParentForm = $(this).parents('form:first');
					if ($(this).is(':checked') == true) {
						$('.js-submit-on-new-window', ParentForm).prop('disabled', true);
						$('.js-submit-on-new-window', ParentForm).prop('checked', false);
						$('.js-submit-on-new-window-label', ParentForm).css('color', '#ccc');
					} else {
						$('.js-submit-on-new-window', ParentForm).prop('disabled', false);
						$('.js-submit-on-new-window', ParentForm).css('color', '#000');
					}
				});
			});
		</script>

		<p>
			<strong>Options</strong><br>
			<label for="UseAJAX"><input class="checkbox js-use-ajax" type="checkbox" id="UseAJAX" value="yes" name="UseAJAX" <?php print(($SubscriptionFormSettings->UseAJAX == true ? 'checked="checked"' : '')); ?>> Subscribe without redirecting</label><br>
			<label class="js-submit-on-new-window-label" for="SubmitOnNewWindow"><input class="checkbox js-submit-on-new-window" type="checkbox" id="SubmitOnNewWindow" value="yes" name="SubmitOnNewWindow" <?php print(($SubscriptionFormSettings->SubmitOnNewWindow == true ? 'checked="checked"' : '')); ?>> Open a new window on form submit</label>
		</p>

		<p>
			<strong>Fields</strong><br>
			<small>Select fields to display on the subscription form:</small>
			<br>
		<ul>
			<li>
				<label for="Fields_EmailAddress"><input class="checkbox js-emailaddress-field" type="checkbox" id="Fields_EmailAddress" name="Fields[]" checked="checked" value="EmailAddress"> Email address</label>
			</li>
			<?php if (count($TargetList['CustomFields']) > 0): ?>
			<?php foreach ($TargetList['CustomFields'] as $Index => $EachCustomField): ?>
				<li>
					<label for="Fields_CustomField<?php print($EachCustomField['CustomFieldID']); ?>">
						<input class="checkbox" type="checkbox" id="Fields_CustomField<?php print($EachCustomField['CustomFieldID']); ?>" name="Fields[]" value="CustomField<?php print($EachCustomField['CustomFieldID']); ?>" <?php print((in_array('CustomField'.$EachCustomField['CustomFieldID'], $SubscriptionFormSettings->Fields) == true ? 'checked="checked"' : '')); ?>> <?php print($EachCustomField['FieldName']); ?>
					</label>
				</li>
				<?php endforeach; ?>
			<?php endif; ?>
		</ul>
		</p>

	<?php elseif ($SubscriptionFormType == 'Link'): ?>
		<p>
			<label for="LinkText"><strong>Link Text</strong><br>
				<small>Explain what link does. Basic HTML is accepted. Example: Click here to subscribe</small>
			</label>
			<input type="text" id="LinkText" name="LinkText" value="<?php print((isset($_POST['LinkText']) == true ? $_POST['LinkText'] : $SubscriptionFormSettings->LinkText)); ?>" style="width:100%;">
		</p>
		<p>
			<label for="LinkIconURL"><strong>Link Icon URL</strong><br>
				<small>The icon URL to be displayed next to the link. Example: http://mysite.com/icon.png</small>
			</label>
			<input type="text" id="LinkIconURL" name="LinkIconURL" value="<?php print((isset($_POST['LinkIconURL']) == true ? $_POST['LinkIconURL'] : $SubscriptionFormSettings->LinkIconURL)); ?>" style="width:100%;">
		</p>
		<p>
			<label for="LinkImageURL"><strong>Link Image URL</strong><br>
				<small>If image URL is entered, instead of the link text, this image will be displayed only. Example: http://mysite.com/subscribe_banner.png</small>
			</label>
			<input type="text" id="LinkImageURL" name="LinkImageURL" value="<?php print((isset($_POST['LinkImageURL']) == true ? $_POST['LinkImageURL'] : $SubscriptionFormSettings->LinkImageURL)); ?>" style="width:100%;">
		</p>

		<p>
			<strong>Options</strong><br>
			<label for="UseOverlay"><input class="checkbox js-use-overlay" type="checkbox" id="UseOverlay" value="yes" name="UseOverlay" <?php print(($SubscriptionFormSettings->UseOverlay == true ? 'checked="checked"' : '')); ?>> Open on overlay window</label><br>
		</p>

	<?php endif; ?>

	<p>
		<strong>Help us</strong><br>
		<small>Help us to spread the word. Thank you :)
		</small>
		<br>
		<label for="DisplayBadge"><input class="checkbox" value="yes" type="checkbox" id="DisplayBadge" name="DisplayBadge" <?php print(($SubscriptionFormSettings->DisplayBadge == true ? 'checked="checked"' : '')); ?>> Add a small badge</label><br>
	</p>

	<?php if ($SubscriptionFormType == 'Link'): ?>
		<p style="margin-top:30px;">
			<small>The subscription form parameters and fields can be customized inside your <a href="http://sendloop.com/login/" target="_blank">Sendloop account &gt; List Settings</a></small>
		</p>
	<?php endif; ?>

	<p>
		<small>Need help? We have a step-by-step <a href="http://sendloop.com/help/article/service-integration-009/wordpress-integration" target="_blank">help article</a> for WordPress plug-in</small>
	</p>


<?php endif; ?>