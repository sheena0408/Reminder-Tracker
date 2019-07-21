<?php 
	session_start();
	require('server.php'); 

	//tasks table
	$tno = $tdesc = $attach = $rname = $rcode = $rmail = $aname = $acode = $amail = $rdate = $cdate = $comments = '';
	$status = "Open";

	if(!isset($_SESSION['empcode'])){
		array_push($errors, "You must login/register first");
		header('location:main.php');
	}

	// delete task
	if (isset($_GET['del_task'])) {
		$id = $_GET['del_task'];
		$x="SELECT * FROM tasks WHERE id=$id";
		$xx = mysqli_query($db, $x);
		
		while($y=mysqli_fetch_assoc($xx)){
			$status = "Closed";
		}
		$q = "UPDATE tasks SET cldate=now(), status='Closed' WHERE id=$id";
		mysqli_query($db, $q);
		array_push($errors, "deleted task");
	}

	
	if(isset($_POST['submit'])) {
		$tdesc = mysqli_real_escape_string($db,$_POST['tdesc']);
		$acode = mysqli_real_escape_string($db,$_POST['tcode']);
		$comments = mysqli_real_escape_string($db,$_POST['comment']);

		//attach upload
		$file = $_FILES["tfile"];
		if(is_uploaded_file($_FILES["tfile"]["tmp_name"])){
			//$ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
			//$allowed = array('doc','docx','pdf','xl','xlsx');

			//if(in_array($ext,$allowed)) {
				if($file["error"] === 0) {
					move_uploaded_file($file["tmp_name"],"uploads/". $file["name"]);
					$attach = "uploads/". $file["name"];
					array_push($pass,"Upload success");
				} 
				else{
					array_push($errors,"Error uploading this file");
				}
			//} else if(!in_array($ext,$allowed)){
				//	array_push($errors,"You can't upload files of this type");
			//}
		}

		$sql_u = "SELECT * FROM users WHERE empcode='$acode'";
		$res_u = mysqli_query($db, $sql_u);

		if(empty($tdesc) OR empty($acode)){
			array_push($errors, "You must fill in the Desc and Code fields");
		} else if(empty(mysqli_num_rows($res_u))){
			array_push($errors, "User doesn't exists");
		}
		else {
			if(count($errors) == 0){
				//register details
				$rname = $_SESSION['empname'];
				$rcode = $_SESSION['empcode'];
				$rmail = $_SESSION['empmail'];

				//tno details
				$rdate= date('Y-m-d');
				
				$jd = gregoriantojd(date('m'),date('d'),date('Y'));
				
				$result = mysqli_query($db,"SELECT * FROM tasks");
				$num_rows = mysqli_num_rows($result);

				$tno =  jdmonthname($jd,0).'-'.str_pad($num_rows , 4,'0',STR_PAD_LEFT);

				//actionee details
				$x="SELECT * FROM users WHERE empcode=$acode";
				$xx = mysqli_query($db, $x);
				while($y=mysqli_fetch_assoc($xx)){
					$aname = $y['empname'];
					$amail = $y['empmail'];
				}
		
				$sql = "INSERT INTO tasks VALUES ('', '$tno','$tdesc','$attach','$rcode','$rname','$rmail','$acode','$aname','$amail','$rdate','$cdate','$status','$comments')";
				mysqli_query($db,$sql);

				array_push($pass,"Task assigned successfully");

			}
		}
	}
?>
<?php require('header.php');?>
<body class="ind">

	<?php if (isset($_SESSION['success'])): ?>
		<div class="error success">
			<h3>
				<?php
					echo $_SESSION['success'];
					unset($_SESSION['success']);
				?>
			</h3>
		</div>
	<?php endif ?>
	<!--page content-->
	<div class="content col-12">
		<?php if (isset($_SESSION['empcode'])): ?>
			
			<p class="welcome">Welcome <?php echo $_SESSION['empname'];?>
			<a href="index.php?logout='1'" class="logout btn btn-danger">Log out</a></p>

			<hr/>
			<hr/>

			<ul class="navigation">
				<li><button class="btn btn-info activeT">My Active Tasks</button></li>
				<li><button class="btn btn-info assignT">Tasks assigned by me</button></li>	
				<li><button class="btn btn-info closeT">My Closed Tasks</button></li>
				<li><button onclick="document.getElementById('id01').style.display='block'" class="btn btn-info float-right" style="margin-right: 0.8em;">Assign Task</button></li>
			</ul>
	
			<div id="id01" class="modal">
				<form class="modal-content animate" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post" enctype="multipart/form-data">
					
					<div class="input-group">
						<label>Task Desc</label>
						<input type="text" name="tdesc" class="">
					</div>

					<div class="input-group">
						<label for="fileToUpload">Select file to upload</label>
						<input type="file" name="tfile" class="file_upload">
					</div>

					<hr style="border: 1px solid #999; width: 100%;" />

					<label><b>Task Actionee</b></label>
					<div class="input-group">
						<label>Actionee EmpCode</label>
						<input type="text" name="tcode" class="">
					</div>

					<div class="input-group">
						<label>Comments</label>
						<textarea name="comment" class=""></textarea>
					</div>

					<div class="input-group">
						<button type="submit" name="submit" class="btn btn-info mx-auto">Submit</button>
						<button type="submit" name="cancel" class="btn btn-danger mx-auto" onclick="document.getElementById('id01').style.display='none'">Cancel</button>
					</div>
				</form>
			</div>

			<div class="row">
			<div class="left_side col-9">
				<div class="err">
					<?php include('errors.php')?>
				</div>
				<div id="activeTasks"><!--current users to do tasks-->
					<table class="table table-striped">
						<thead>
							<tr>
								<th>T No.</th>
								<th>T Desc</th>
								<th>Attachment</th>
								<th>R Code</th>
								<th>R Name</th>
								<th>R Mail</th>
								<th>R Date</th>
								<th>Comments</th>
								<th>Delete task</th>
							</tr>
						</thead>

						<tbody>
							<?php 
							$q = "SELECT id,task_no,tdesc,attach,tregcode,tregname,tregmail,stdate,comments FROM tasks WHERE tactcode = '{$_SESSION['empcode']}' AND status= 'Open'";
							$tasks = mysqli_query($db,$q );

							while ($row = mysqli_fetch_array($tasks)) { ?>
								<tr>
									
									<td class="task"> <?php echo $row['task_no']; ?> </td>
									<td class="task"> <?php echo $row['tdesc']; ?> </td>
									<td class="task">
										<?php if(!empty($row['attach'])):?>
											<a href="<?php echo $row['attach']; ?>" target="_new">Download</a>
										<?php endif?>
									</td>
									<td class="task"> <?php echo $row['tregcode']; ?> </td>
									<td class="task"> <?php echo $row['tregname']; ?> </td>
									<td class="task"> <?php echo $row['tregmail']; ?> </td>
									<td class="task"> <?php echo $row['stdate']; ?> </td>
									<td class="task"> <?php echo $row['comments']; ?> </td>
									<td class="delete"><a href="index.php?del_task=<?php echo $row['id'] ?>">x</a> </td>
								</tr>
							 <?php } ?>	
						</tbody>
					</table>
				</div>

				<div id="closeTasks"><!--current users completed tasks-->
					<table class="table table-striped">
						<thead>
							<tr>
								<th>T No.</th>
								<th>T Desc</th>
								<th>R Code</th>
								<th>R Name</th>
								<th>R Mail</th>
								<th>R Date</th>
								<th>Closed date</th>
							</tr>
						</thead>

						<tbody>
							<?php 
							$q = "SELECT task_no,tdesc,tregcode,tregname,tregmail,stdate,cldate FROM tasks WHERE tactcode = '{$_SESSION['empcode']}' AND status = 'Closed'";
							$tasks = mysqli_query($db,$q);

							while ($row = mysqli_fetch_array($tasks)) { ?>
								<tr>
									<td class="task"> <?php echo $row['task_no']; ?> </td>
									<td class="task"> <?php echo $row['tdesc']; ?> </td>
									<td class="task"> <?php echo $row['tregcode']; ?> </td>
									<td class="task"> <?php echo $row['tregname']; ?> </td>
									<td class="task"> <?php echo $row['tregmail']; ?> </td>
									<td class="task"> <?php echo $row['stdate']; ?> </td>
									<td class="task"> <?php echo $row['cldate']; ?> </td>
								</tr>
							 <?php } ?>	
						</tbody>
					</table>
				</div>

				<div id="assignTasks"><!--current users asiigned to others tasks-->
					<table class="table table-striped">
						<thead>
							<tr>
								<th>T No</th>
								<th>T Desc</th>
								<th>Attachment</th>
								<th>A Code</th>
								<th>A Name</th>
								<th>A Mail</th>
								<th>R Date</th>
								<th>Closed date</th>
								<th>Status</th>
								<th>Comments</th>
								<!--th>Delete</th-->
							</tr>
						</thead>

						<tbody>
							<?php 
							$q = "SELECT id,task_no,tdesc,attach,tactcode,tactname,tactmail,stdate,cldate,status,comments FROM tasks WHERE tregcode = '{$_SESSION['empcode']}'";
							$tasks = mysqli_query($db,$q );

							while ($row = mysqli_fetch_array($tasks)) { ?>
								<tr>
									
									<td class="task"> <?php echo $row['task_no']; ?> </td>
									<td class="task"> <?php echo $row['tdesc']; ?> </td>
									<td class="task"> 
										<?php if(!empty($row['attach'])):?>
											<a href="<?php echo $row['attach']; ?>" target="_new">Download</a>
										<?php endif?>
									</td>
									<td class="task"> <?php echo $row['tactcode']; ?> </td>
									<td class="task"> <?php echo $row['tactname']; ?> </td>
									<td class="task"> <?php echo $row['tactmail']; ?> </td>
									<td class="task"> <?php echo $row['stdate']; ?> </td>
									<td class="task"> <?php echo $row['cldate']; ?> </td>
									<td class="task"> <?php echo $row['status']; ?> </td>
									<td class="task"> <?php echo $row['comments']; ?> </td>
									<!--td class="delete">
										<?//php if($row['status'] == 'Open'):?>
											<a href="index.php?del_task=<?php //echo $row['id'] ?>">x</a> 
										<?//php endif?>
									</td-->
								</tr>
							 <?php } ?>	
						</tbody>
					</table>
				</div>
			

				<!--div id="motivate">
					<p>some motivationall quotes</p>
					<h3></h3>
				</div-->

			</div>

			<div class="right_side col-3">
				<div class="infoBits text-center">
					<div class="cntAct">
						<d3>ACTIVE TASKS</d3>
						<h1><?php 
								$q = "SELECT * FROM tasks WHERE tactcode = '{$_SESSION['empcode']}' AND status= 'Open'";
								$res = mysqli_query($db,$q );
								echo mysqli_num_rows($res);
							?></h1>
					</div>
					<div class="cntAss ">
						<d3>ASSIGNED TASKS</d3>
						<h1><?php 
								$q = "SELECT * FROM tasks WHERE tregcode = '{$_SESSION['empcode']}' AND status= 'Open'";
								$res = mysqli_query($db,$q );
								echo mysqli_num_rows($res);
							?></h1>
					</div>
					<div class="cntCl ">
						<d3>CLOSED TASKS</d3>
						<h1><?php 
								$q = "SELECT * FROM tasks WHERE tactcode = '{$_SESSION['empcode']}' AND status= 'Closed'";
								$res = mysqli_query($db,$q );
								echo mysqli_num_rows($res);
							?></h1>
					</div>
				</div>
			</div>
		</div>

			
		<?php endif ?>
	</div>
				

	<script>
	// Get the modal
	var modal = document.getElementById('id01');

	// When the user clicks anywhere outside of the modal, close it
	window.onclick = function(event) {
	    if (event.target == modal) {
	        modal.style.display = "none";
	    }
	}

	$(document).ready(
	    function(){
	        $(".activeT").click(function () {
	            $("#activeTasks").fadeIn("slow");
	            $("#assignTasks").hide();
	            $("#closeTasks").hide();
	            
	        });

	    });

	$(document).ready(
	    function(){
	        $(".assignT").click(function () {
	            $("#assignTasks").fadeIn("slow");
	            $("#activeTasks").hide();
	            $("#closeTasks").hide();
	            
	        });

	    });

	$(document).ready(
	    function(){
	        $(".closeT").click(function () {
	            $("#closeTasks").fadeIn("slow");
	            $("#assignTasks").hide();
	            $("#activeTasks").hide();
	            
	        });

	    });

	$(document).ready(function() {
	    $('#content').fadeIn();
	});
	</script>

<?php require('footer.php') ?>