<?php if ($IsLink == true): ?>
	<div class="sendloop-shortcode-widget-container">
		<a href="#" class="sendloop-form-trigger" data-subscription-url="<?php print((isset($TargetList->SubscriptionFormURL) == true && $TargetList->SubscriptionFormURL != '' ? $TargetList->SubscriptionFormURL : '')); ?>"><?php print($LinkText); ?></a>
	</div>
	<style type="text/css">
		.sendloop-shortcode-widget-container {
			<?php print($ShortcodeCustomCSS); ?>
		}
	</style>
<?php else: ?>
<?php endif; ?>

