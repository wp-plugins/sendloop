<?php if ($IsSetupCompleted == false): ?>
	&nbsp;
<?php else: ?>
	<link rel="stylesheet" href="<?php print(plugin_dir_url('sendloop/sendloop.php') . 'js/jquery-ui-1.10.2.custom/css/flick/jquery-ui-1.10.2.custom.min.css'); ?>"/>
	<script type="text/javascript" src="<?php print(plugin_dir_url('sendloop/sendloop.php') . 'js/jquery-1.9.1.min.js'); ?>"></script>
	<script type="text/javascript" src="<?php print(plugin_dir_url('sendloop/sendloop.php') . 'js/jquery-ui-1.10.2.custom/js/jquery-ui-1.10.2.custom.min.js'); ?>"></script>

	<aside id="sendloop-subscribe" class="widget">
		<div class="sendloop-widget">
			<?php if (isset($SubscriptionFormSettings->FormTitle) == true && $SubscriptionFormSettings->FormTitle != ''): ?>
				<h3 class="widget-title"><?php print($SubscriptionFormSettings->FormTitle); ?></h3>
			<?php endif; ?>
			<?php if (isset($SubscriptionFormSettings->Message) == true && $SubscriptionFormSettings->Message != ''): ?>
				<p class="sendloop-widget-message"><?php print($SubscriptionFormSettings->Message); ?></p>
			<?php endif; ?>

			<?php if ($SubscriptionFormType == 'Standard'): ?>
				&nbsp;
			<?php elseif ($SubscriptionFormType == 'Link'): ?>
				<?php if (isset($SubscriptionFormSettings->LinkImageURL) == true && $SubscriptionFormSettings->LinkImageURL != ''): ?>
					<p class="sendloop-widget-link"><a href="<?php print($TargetList->SubscriptionFormURL); ?>" class="sendloop-form-trigger" target="_blank"><img src="<?php print($SubscriptionFormSettings->LinkImageURL); ?>" alt="Click here to subscribe now" border="0"></a></p>
				<?php else: ?>
					<p class="sendloop-widget-link">
						<?php if (isset($SubscriptionFormSettings->LinkIconURL) == true && $SubscriptionFormSettings->LinkIconURL != ''): ?>
							<img src="<?php print($SubscriptionFormSettings->LinkIconURL); ?>" alt="" border="0" align="middle">
						<?php endif; ?>
						<a href="<?php print($TargetList->SubscriptionFormURL); ?>" class="sendloop-form-trigger" target="_blank"><?php print($SubscriptionFormSettings->LinkText); ?></a>
					</p>
				<?php endif; ?>
			<?php endif; ?>

			<?php if (isset($SubscriptionFormSettings->DisplayBadge) == true && $SubscriptionFormSettings->DisplayBadge == true): ?>
				<p class="sendloop-badge">Mailing list powered by <a href="http://sendloop.com/?utm_source=wp_subscribe_form&utm_medium=badge&utm_content=widget&utm_campaign=WordPress%2BPlug-In" target="_blank">Sendloop</a>&#8482;</p>
			<?php endif; ?>
		</div>
	</aside>
	<style type="text/css">
		div.sendloop-widget p.sendloop-badge, div.sendloop-widget p.sendloop-badge a {
			font-size:9px;
			text-transform: uppercase;
			font-weight: normal;
			margin:20px 0 0 0;
			color:#ccc;
			text-align: right;
		}

		<?php
			if ($CustomCSS != '')
			{
				print($CustomCSS);
			}
		?>
	</style>
<?php endif; ?>
