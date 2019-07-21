
	<?php 
	if(count($errors) > 0): ?>
		<div class="error">
			<?php foreach ($errors as $e): ?>
				<p><?php print $e; ?></p>
			<?php endforeach ?>
		</div>

	<?php endif ?>

	<?php 
	if(count($pass) > 0): ?>
		<div class="pass">
			<?php foreach ($pass as $p): ?>
				<p><?php print $p; ?></p>
			
			<?php endforeach ?>
		</div>

	<?php endif ?>

	<script>
		setTimeout( function(){
			$('.error').fadeOut("slow");} , 2000);
		setTimeout( function(){
			$('.pass').fadeOut("slow");} , 2000);
	</script>
