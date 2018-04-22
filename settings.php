<?php

session_start();
header('Content-type: text/html; charset=utf-8');
$location = "account";

require_once "common.php";
require_once "check_session.php";

if($_SESSION['settings_error']) { 
	$settings_error = "<div class='alert alert-danger'>".$_SESSION['settings_error']."</div>";
	unset($_SESSION['settings_error']); 
}

$toolsuser->selectUser($_SESSION['user']['email']);
$all_tokens = $toolsuser->getAllTokens();

if ($_REQUEST['terminate']) {
	foreach($all_tokens as $user_token) {
		if ($user_token['id'] == $_REQUEST['terminate']) {
			$toolsuser->terminateToken($user_token['token']);
			$all_tokens = $toolsuser->getAllTokens();
			break;
		}
	}
}

if ($_REQUEST['generate_api']) {
	$new_api = $toolsuser->generateAPI();
	$_SESSION['user']['api'] = $new_api['data']['api'];
	header("Location: settings.php");
}

if ($_POST['name']) {
	$toolsuser->updateSetting('name',$_POST['name']);
	$_SESSION['user']['name'] = $_POST['name'];
	header("Location: settings.php");
}

if ($_POST['new_password']) {
	$toolsuser->updateSetting('password',$_POST['new_password']);
	header("Location: settings.php");
}

if ($_REQUEST['autosave'] == 'always' || $_REQUEST['autosave'] == 'optional' || $_REQUEST['autosave'] == 'never') {
	$toolsuser->updateSetting('autosave',$_REQUEST['autosave']);
	$_SESSION['user']['autosave'] = $_REQUEST['autosave'];
	header("Location: settings.php");
}

?>

<!DOCTYPE html>
<html lang="uk">
  <head>
    
    <title>Налаштування | Українські Інструменти</title>
    
    <?php include "head.php"; ?>
    
  </head>
  <body>
    
	<?php include "navbar.php"; ?>

    <div class="container">
		
		<div class="page-header" id="banner">
			<div class="row">
				<div class="col-sm-12">
					<!--<h3>Налаштування</h3>-->
					<?php echo "$settings_error"; ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">Система</h3>
					</div>
					<div class="panel-body">
						
						<form class="" method="get" action="">
						  <fieldset>

							<div class="form-group">
								<label class="control-label">Автоматично зберігати згенеровані імена</label>
								<div class="radio">
								  <label>
									<input name="autosave" id="alwaysRadio" value="always" type="radio" <?php if($_SESSION['user']['autosave']=='always')echo"checked";?>>
									Завжди
								  </label>
								</div>
								<div class="radio">
								  <label>
									<input name="autosave" id="optionalRadio" value="optional" type="radio" <?php if($_SESSION['user']['autosave']=='optional')echo"checked";?>>
									За підтвердженням
								  </label>
								</div>
								<div class="radio">
								  <label>
									<input name="autosave" id="neverRadio" value="never" type="radio" <?php if($_SESSION['user']['autosave']=='never')echo"checked";?>>
									Ніколи
								  </label>
								</div>
							</div>
							
							<div class="form-group">
							  <button type="submit" class="btn btn-success btn-wd">Зберегти</button>
							  <button type="reset" class="btn btn-danger">Скасувати</button>
							</div>
						  </fieldset>
						</form>
						
					</div>
				</div>
				
				<div class="panel panel-success">
					<div class="panel-heading">
						<h3 class="panel-title">API <a href='?generate_api=true' class='btn btn-xs btn-primary' onclick="return confirm('Ви впевнені? Це знищить існуючий ключ.')">Згенерувати</a></h3>
					</div>
					<div class="panel-body">
						
						<div class="form-group">
							<label class="control-label" for="apiKey">Нікому не передавайте цей API Ключ! <a href="#">Деталі</a></label>
							<div class="input-group">
								<input class="form-control input-sm" id="apiKey" type="text" value="<?php echo $_SESSION['user']['api'];?>" readonly>
								<span class="input-group-btn">
									<!--<button class="btn btn-default btn-sm" type="button" onclick="document.getElementById('apiKey').select();">Виділити</button>-->
									<button class="btn btn-default btn-sm" type="button" onclick="window.prompt('Натисніть Ctrl+C, щоб скопіювати', document.getElementById('apiKey').value);">Копіювати</button>
								</span>
							</div>
						</div>
						
					</div>
				</div>

			</div>
			
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Аккаунт</h3>
					</div>
					<div class="panel-body">
						
						<form class="" method="post" action="">
						  <fieldset>
							
							<div class="form-group">
								<label class="control-label" for="emailInput">Email</label>
								<input class="form-control" id="emailInput" placeholder="tymur@example.com" type="email" value="<?php echo $_SESSION['user']['email'];?>" readonly>
								<small>Email не можна змінити</small>
							</div>
							<div class="form-group">
								<label class="control-label" for="nameInput">Ім'я</label>
								<input class="form-control" id="nameInput" placeholder="Ім'я" type="text" name="name" value="<?php echo $_SESSION['user']['name'];?>" required>
							</div>
							<div class="form-group">
								<label class="control-label" for="newPasswordInput">Новий пароль</label>
								<input class="form-control" id="newPasswordInput" placeholder="Не менше 8 символів" name="new_password" type="password">
								<small>Залиште поле пустим, якщо не хочете змінювати пароль</small>
							</div>

							<div class="form-group">
							  <button type="submit" class="btn btn-success btn-wd">Зберегти</button>
							  <button type="reset" class="btn btn-danger">Скасувати</button>
							</div>
						  </fieldset>
						</form>
						
					</div>
				</div>
				
			</div>
			
		</div>
			
		<div class="row">
			
			<div class="col-md-12">
				<div class="panel panel-warning">
					<div class="panel-heading">
						<h3 class="panel-title">Сесії</h3>
					</div>
					<div class="panel-body">
						
						<table class="table table-striped table-hover">
						  <thead>
							<tr>
							  <th>IP</th>
							  <th>Пристрій</th>
							  <th>Дата входу</th>
							  <th>Активність</th>
							  <th></th>
							</tr>
						  </thead>
						  <tbody>
							<?php foreach($all_tokens as $user_token) { ?>
								<tr <?php echo ($user_token['expire']<time())?"class='danger'":(($user_token['token']==$_SESSION['user']['token'])?"class='success'":"");?>>
								  <td><?php echo $user_token['ip'];?></td>
								  <td><?php echo $user_token['agent'];?></td>
								  <td><?php echo date("Y-m-d H:i:s",$user_token['date']);?></td>
								  <td><?php echo date("Y-m-d H:i:s",$user_token['last_active']);?></td>
								  <td><?php echo ($user_token['token']==$_SESSION['user']['token'])?"Поточна":(($user_token['expire']<time())?"Закрита":"<a href='?terminate=".$user_token['id']."' class='btn btn-danger btn-xs'>Закрити</a>");?></td>
								</tr>
							<?php } ?>
						  </tbody>
						</table> 
						
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
