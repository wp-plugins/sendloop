<script type="text/javascript" src="<?php print(plugin_dir_url('sendloop/sendloop.php') .'js/jquery-1.9.1.min.js'); ?>"></script>

<div class="wrap">
	<?php screen_icon('options-general'); ?>
	<h2>Help &amp Documentation</h2>

	<?php if (isset($PageMessage) == true && is_array($PageMessage) == true && isset($PageMessage['Type']) == true && $PageMessage['Type'] == 'error'): ?>
		<div class="error settings-error"><p><strong><?php print($PageMessage['Message']); ?></strong></p></div>
	<?php elseif (isset($PageMessage) == true && is_array($PageMessage) == true && isset($PageMessage['Type']) == true && $PageMessage['Type'] == 'success'): ?>
		<div class="updated settings-error"><p><strong><?php print($PageMessage['Message']); ?></strong></p></div>
	<?php endif; ?>

	<div class="tool-box">
		<p>Detailed documentation can be found on Sendloop's Help section at <a href="http://sendloop.com/help/article/service-integration-009/wordpress-integration" target="_blank">http://sendloop.com/help/article/service-integration-009/wordpress-integration</a>
		<p>For any questions, feedback and comments, feel free to contact us via <a href="mailto:hello@sendloop.com">hello@sendloop.com</a></p>

		<p>Don't you have a Sendloop account? <a href="http://sendloop.com/" target="_blank">Get started within seconds</a>!</p>
	</div>
</div>
