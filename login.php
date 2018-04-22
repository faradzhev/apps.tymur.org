<?php

session_start();
header('Content-type: text/html; charset=utf-8');
$location = "login";

require_once "common.php";
require_once "ToolsUser.php";

$toolsuser = new ToolsUser($db);

if ($_POST['email']) {
	$result = $toolsuser->login($_POST['email'],$_POST['password']);
	//print_r($result);
	if ($result['code'] == 200) {
		$_SESSION['user'] = $result['data'];
		setcookie("_urtn",$result['data']['token'],(($_POST['remember'])?$result['data']['expire']:(time()+strtotime("+1 day"))));
		header("Location: index.php");
	}
	else {
		$_SESSION['login_error'] = $result['status'];
	}
}

//check for if logged in
$result = $toolsuser->checkToken($_COOKIE['_urtn']);
if ($result['status'] == 'OK') {
	header("Location: index.php");
}

//echo $result['status']; DEBUG ONLY

if($_SESSION['login_error']) { 
	$login_error = "<div class='alert alert-danger'>".$_SESSION['login_error']."</div>";
	unset($_SESSION['login_error']); 
}

?>

<!DOCTYPE html>
<html lang="uk">
  <head>
    
    <title>Вхід | Українські Інструменти</title>
    
    <?php include "head.php"; ?>
    
  </head>
  <body>
    
	<?php include "navbar.php"; ?>

    <div class="container">
		
		<div class="page-header" id="banner">
			<div class="row">
				<div class="col-sm-12">
					<!--<h3>Вхід у систему</h3>-->
					<?php echo "$login_error"; ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="panel panel-success">
					<div class="panel-heading">
						<h3 class="panel-title">Вхід</h3>
					</div>
					<div class="panel-body">
						
						<form class="form-horizontal" method="post">
						  <fieldset>
							<div class="form-group">
							  <label for="inputEmail" class="col-lg-2 control-label">Email</label>
							  <div class="col-lg-10">
								<input class="form-control" name="email" id="loginEmail" placeholder="tymur@example.com" type="email" value="<?php echo $_POST['email'];?>" required>
							  </div>
							</div>
							<div class="form-group">
							  <label for="inputPassword" class="col-lg-2 control-label">Пароль</label>
							  <div class="col-lg-10">
								<input class="form-control" name="password" id="loginPassword" placeholder="**********" type="password" required>
								<div class="checkbox">
								  <label>
									<input type="checkbox" name="remember" checked="checked"> Запам'ятати мене
								  </label>
								</div>
							  </div>
							</div>
							
							<div class="form-group">
							  <div class="col-lg-10 col-lg-offset-2">
								<button type="submit" class="btn btn-success btn-wd">Увійти</button>
								<button type="reset" class="btn btn-danger">Скасувати</button>
							  </div>
							</div>
						  </fieldset>
						</form>
						
					</div>
				</div>
			</div>
			
			<div class="col-md-6">
				<div class="panel panel-warning">
					<div class="panel-heading">
						<h3 class="panel-title">Реєстрація</h3>
					</div>
					<div class="panel-body">
						
						<form class="form-horizontal" method="post" action="register.php">
						  <fieldset>
							<div class="form-group">
							  <label for="inputEmail" class="col-lg-2 control-label">Ім'я</label>
							  <div class="col-lg-10">
								<input class="form-control" name="name" id="loginName" placeholder="Тимур" type="text" required>
							  </div>
							</div>
							<div class="form-group">
							  <label for="inputEmail" class="col-lg-2 control-label">Email</label>
							  <div class="col-lg-10">
								<input class="form-control" name="email" id="registerEmail" placeholder="tymur@example.com" type="email" required>
							  </div>
							</div>
							<div class="form-group" id="regPass">
							  <label for="inputPassword" class="col-lg-2 control-label">Пароль</label>
							  <div class="col-lg-10">
								<input class="form-control" name="password" id="registerPassword" placeholder="**********" type="password" onchange="javascript:checkPass();" onkeyup="javascript:checkPass();" required>
								<small id="passLen" class="text-danger"></small>
							  </div>
							</div>
							<div class="form-group" id="regPassRep">
							  <label for="inputPassword" class="col-lg-2 control-label">Повторіть пароль</label>
							  <div class="col-lg-10">
								<input class="form-control" name="password_repeat" id="registerPasswordRepeat" placeholder="**********" type="password" onchange="javascript:checkPass();" onkeyup="javascript:checkPass();" required>
								<small id="passEq" class="text-danger"></small>
							  </div>
							</div>
							
							<div class="form-group">
							  <div class="col-lg-10 col-lg-offset-2">
								<button type="submit" class="btn btn-primary">Зареєструватися</button>
								<button type="reset" class="btn btn-danger">Скасувати</button>
							  </div>
							</div>
						  </fieldset>
						</form>
						
					</div>
				</div>
			</div>
		
	</div>
	
	<?php include "footer.php"; ?>

    </div>
	
	<script>
		function checkPass() {
			console.log('Check Pass Called');
			
			var pass = document.getElementById('registerPassword');
			var passRep = document.getElementById('registerPasswordRepeat');
			
			var passDiv = document.getElementById('regPass');
			var passRepDiv = document.getElementById('regPassRep');
			
			var passLen = document.getElementById('passLen');
			var passEq = document.getElementById('passEq');
			
			if (pass.value.length < 8) {
				passDiv.className = "form-group has-error";
				passLen.innerHTML = "Пароль має містити не менше 8 символів";
			}
			else {
				passDiv.className = "form-group has-success";
				passLen.innerHTML = "";
			}
			
			if (pass.value){
				if(pass.value != passRep.value) {
					passRepDiv.className = "form-group has-error";
					passEq.innerHTML = "Паролі не співпадають";
				}
				else {
					passRepDiv.className = "form-group has-success";
					passEq.innerHTML = "";
				}
			}
			
		}
	</script>

    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/custom.js"></script>
    <script src="./js/bootstrap.js"></script>
    
    
  </body>
</html>
