<?php

session_start();
header('Content-type: text/html; charset=utf-8');
$location = "account";

require_once "UkrName.php";
require_once "check_session.php";

if($_SESSION['account_error']) { 
	$account_error = "<div class='alert alert-danger'>".$_SESSION['account_error']."</div>";
	unset($_SESSION['account_error']); 
}

?>

<!DOCTYPE html>
<html lang="uk">
  <head>
    
    <title>Аккаунт | Українські Інструменти</title>
    
    <?php include "head.php"; ?>
    
  </head>
  <body>
    
	<?php include "navbar.php"; ?>

    <div class="container">
		
		<div class="page-header" id="banner">
			<div class="row">
				<div class="col-sm-12">
					<!--<h3>Аккаунт</h3>-->
					<?php echo "$account_error"; ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-success">
					<div class="panel-heading">
						<h3 class="panel-title">Аккаунт</h3>
					</div>
					<div class="panel-body">
						
						
						
					</div>
				</div>
			</div>
			
	</div>
	
	<?php include "footer.php"; ?>

    </div>
	
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/custom.js"></script>
    <script src="./js/bootstrap.js"></script>
    
    
  </body>
</html>
