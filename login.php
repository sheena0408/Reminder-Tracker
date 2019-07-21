<?php session_start();
	require('server.php');
	?>
<?php require_once('header.php');?>

<body class="log">
	<div class="text-center container logscreen">
			<h1>Log in</h1>
			<form method="post" action="login.php">
				
				<?php require('errors.php'); ?>
				<div class="input-group">
					<label>Employee Code</label>
					<input type="text" name="empcode" class="">
				</div>

				<div class="input-group">
					<button type="submit" name="login" class="btn btn-info mx-auto">Login</button>
				</div>
				
				<p> Not a member? <a href="register.php">Register</a></p>
			</form>			
	</div>
<?php require('footer.php');?>
