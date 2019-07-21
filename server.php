	<?php
		//users table
		$empcode = "";
		$empmail = "";
		$empname = "";

		$errors = array();
		$pass = array();
		
		//connect to db
		$db = mysqli_connect('localhost', 'root', '', 'master');

		// LOGIN USER
		if (isset($_POST['login'])) {
		  $empcode = mysqli_real_escape_string($db, $_POST['empcode']);

		  if (empty($empcode)) {
		  	array_push($errors, "Emp Code is required");
		  }
		 
		  if (count($errors) == 0) {
		  	$query = "SELECT * FROM users WHERE empcode=$empcode";
		  	$results = mysqli_query($db, $query);

		  	if (mysqli_num_rows($results) == 1) {  			
		  		$_SESSION['success'] = "You are now logged in";
		  		$_SESSION['empcode'] = $empcode;
		  		
		  		$query="SELECT * FROM users WHERE empcode='$empcode'";
				$r1 = mysqli_query($db, $query);
				while($result=mysqli_fetch_assoc($r1)){
					$_SESSION['empname'] = $result['empname'];
					$_SESSION['empmail'] = $result['empmail'];
				}

				header('location: index.php');
				$_SESSION['success'] = "You are now logged in";
		  	}else {
		  		array_push($errors, "Wrong emp code");
		  	}
		  }
		}

		//logout
		if(isset($_GET['logout'])) {
			unset($_SESSION['empcode']); //removes session variables
			unset($_SESSION['empmail']);
			unset($_SESSION['empname']);
			session_destroy(); //destroys seesion
			
			//going back to main page
			header('location: main.php');
		}
	?>
