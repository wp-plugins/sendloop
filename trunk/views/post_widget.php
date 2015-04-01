<?php if ($IsSetupCompleted == true): ?>
	<link rel="stylesheet" href="<?php print(plugin_dir_url('sendloop/sendloop.php') . 'js/jquery-ui-1.10.2.custom/css/flick/jquery-ui-1.10.2.custom.min.css'); ?>"/>
	<script type="text/javascript" src="<?php print(plugin_dir_url('sendloop/sendloop.php') . 'js/jquery-1.9.1.min.js'); ?>"></script>
	<script type="text/javascript" src="<?php print(plugin_dir_url('sendloop/sendloop.php') . 'js/jquery-ui-1.10.2.custom/js/jquery-ui-1.10.2.custom.min.js'); ?>"></script>

	<div id="sendloop-modal" title="<?php print($SubscriptionFormSettings->FormTitle); ?>" style="display:none;">
		<iframe width="550" height="550" src="<?php print($TargetList->SubscriptionFormURL); ?>" data-subscription-url="<?php print($TargetList->SubscriptionFormURL); ?>" frameborder="0" scrolling="no"></iframe>
	</div>
	<script type="text/javascript">
		$(document).ready(function() {
			$('.sendloop-form-trigger').on('click', function(ev) {
				ev.preventDefault();

				if ($(this).data('subscription-url') != undefined && $(this).data('subscription-url') != '') {
					$('iframe', '#sendloop-modal').attr('src', $(this).data('subscription-url'));
				} else {
					$('iframe', '#sendloop-modal').attr('src', $('iframe', '#sendloop-modal').data('subscription-url'));
				}

				$('#sendloop-modal').dialog({
					draggable:false,
					height:550,
					width:550,
					modal:true
				});
			});
		});
	</script>

<?php endif; ?>