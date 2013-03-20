<script type="text/javascript" src="<?php print(plugin_dir_url('sendloop/sendloop.php') .'js/jquery-1.9.1.min.js'); ?>"></script>

<div class="wrap">
	<?php screen_icon('users'); ?>
	<h2>Sendloop Subscribe</h2>

	<?php if ($IsSetupCompleted == true): ?>
		instlaled
	<?php else: ?>
		<div class="tool-box">
			<h3 class="title">Welcome!</h3>
			<p>bla bla bla</p>
		</div>
	<?php endif; ?>
</div>