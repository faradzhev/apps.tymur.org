<?php
$WEBSITE = "Apps.Tymur";
$PAGE = "Головна";

session_start();
header('Content-type: text/html; charset=utf-8');
$location = "ukrname";

require_once "common.php";
require_once "UkrName.php";
$ukrname = new UkrName($db);
require_once "check_session.php";
require_once "URLify.php";

?>

<!DOCTYPE html>
<html lang="uk">
  <head>
    
    <title><?php echo $PAGE.' | '.$WEBSITE; ?></title>
    
    <?php include "head.php"; ?>
    
  </head>
  <body>
    
	<?php include "navbar.php"; ?>

    <div class="container">
		
		<div class="page-header" id="banner">
			<div class="row">
				<div class="col-sm-12">
					<?php if($_SESSION['new_user']) { ?>
						<div class='well well-lg'>Дякуємо за реєстрацію! Ви можете налаштувати систему на свій смак на <a href="settings.php">сторінці налаштувань</a>. Приємного використання ;)</div>
					<?php unset($_SESSION['new_user']); } ?>
					<h3>Ваша випадково згенерована особистість</h3>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-body">
					  <form class="form-horizontal" method="get">
						  <fieldset>
							<div class="form-group">
							  <label for="select" class="col-lg-4 control-label">Стать</label>
							  <div class="col-lg-8">
								<select class="form-control" name="gender">
								  <option value="">Байдуже</option>
								  <option value="m" <?php if($_REQUEST['gender']=='m')echo'selected';?>>Чоловіча</option>
								  <option value="f" <?php if($_REQUEST['gender']=='f')echo'selected';?>>Жіноча</option>
								</select>
							  </div>
							</div>
							<div class="form-group">
							  <label for="select" class="col-lg-4 control-label">Область</label>
							  <div class="col-lg-8">
								<select class="form-control" name="region">
									<option value="">Байдуже</option>
									<?php foreach($ukrname->getAllRegions() as $region) { ?>
										<option value="<?php echo $region;?>" <?php if($_REQUEST['region']==$region)echo'selected';?>><?php echo $region;?></option>
									<?php } ?>
								</select>
							  </div>
							</div>
							<input id="options" name="options" type="hidden" value="<?php echo $_REQUEST['options']?>">
							<div class="form-group">
							  <div class="col-lg-10 col-lg-offset-2">
								<input type="submit" class="btn btn-success" value="Генерувати">
								<!--<button onclick="javascript:document.getElementById('options').value = true;" class="btn btn-default" value="Більше опцій">-->
								<a href="#" onclick="javascript:window.alert('Скоро буде більше опцій ;)');" class="btn btn-default">Більше опцій</a>
							  </div>
							</div>
						  </fieldset>
						</form>
					</div>
				</div>
				<?php if(!$_SESSION['user']) { ?>
				<div class="alert alert-info">
					<a href="login.php" class="alert-link">Увійдіть в систему</a>, щоб зберігати згенеровані обобистості.
				</div>
				<?php } ?>
				<div class="alert alert-warning">
					Використовуючи сервіс, ви погоджуєтеся з <a href="#" class="alert-link" onclick="javascript:window.alert('Я їх ще не вигадав')">цими правилами</a>.
				</div>
				
			</div>
		
		
		<?php
		$gender = $_REQUEST['gender'];
		if ($gender != 'm' && $gender != 'f') {
			$gender = $ukrname->getRandomGender();
		}
		$name_array = $ukrname->getRandomName($gender,true);
		$random_name = $name_array['surname']." ".$name_array['name']." ".$name_array['fathername'];
		
		$mother_surname = $ukrname->getRandomSurname($gender);
		
		//$address = $ukrname->getRandomAddress($_REQUEST['region']);
		$street = $ukrname->getRandomStreet();
		$city = $ukrname->getRandomCity($_REQUEST['region'],true);
		
		$phone = $ukrname->getRandomPhone();
		
		$passport_old = $ukrname->getRandomPassport();
		$passport_new = $ukrname->getRandomPassport(true);
		
		$dob = $ukrname->getRandomDOB();
		$age = $ukrname->getAgeFromDOB($dob);
		
		$email = $ukrname->getRandomEmail(URLify::downcode(/*$name_array['name'].' '.*/$name_array['surname']));
		$username = $ukrname->getRandomUsername(URLify::downcode($name_array['name']/*.' '.$name_array['surname']*/),$dob);
		$website = $ukrname->getRandomWebsite(URLify::downcode($name_array['surname']));
		$password = $ukrname->getRandomPassword();
		
		$cc = $ukrname->getRandomCreditCard();
		?>
		
			<div class="col-md-8">
				
				
				<div class="panel panel-primary">
					<div class="panel-body" style="margin-left:20px">
						<div class="col-sm-9">
							<!--<h3><?php echo $name_array['surname']." <a href='https://uk.wikipedia.org/wiki/".$name_array['name']."' target='_blank' title='Хочете дізнатися, що означає ім`я ".$name_array['name']."?'><u>".$name_array['name']."</u></a> ".$name_array['fathername'];?></h3>-->
							<h3 id="name"><?php echo $random_name;?></h3>
							<h4 id="latin_name"><?php echo URLify::downcode($random_name);?></h4>
							<p id="street"><?php echo $street;?></p>
							<p id="city"><?php echo "м. ".$city['city'].", ".$city['region']." обл.";?></p>
						</div>
						<div class="col-sm-3 hidden-xs" align="right">
							<img id="avatar" src="./images/<?php echo $gender;?>.png" style="border-radius:5px;">
						</div>
					</div>
				</div>
			
				<div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title">Персональна інформація</h3>
					</div>
					<div class="panel-body">
						<table class="table table-striped">
						  <tbody>
							<tr>
							  <td>Прізвище по матері</td>
							  <th id="mother_surname"><?php echo $mother_surname;?></th>
							</tr>
							<tr>
							  <td>Паспорт (старого зразка)</td>
							  <th id="passport_old"><?php echo $passport_old;?></th>
							</tr>
							<tr>
							  <td>ID-паспорт (нового зразка)</td>
							  <th id="passport"><?php echo $passport_new;?></th>
							</tr>
							<tr>
							  <td>Телефон</td>
							  <th id="phone"><?php echo $phone;?></th>
							</tr>
							<tr>
							  <td>День народження</td>
							  <th id="dob"><?php echo $dob;?></th>
							</tr>
							<tr>
							  <td>Вік</td>
							  <th id="age"><?php echo $age.' '.$ukrname->getTitleForYears($age);?></th>
							</tr>
							<tr>
							  <td>Знак зодіаку</td>
							  <th id="zodiac"><?php echo $ukrname->getZodiacFromDOB($dob);?></th>
							</tr>
						  </tbody>
						</table> 
					</div>
				</div>
				
				<div class="panel panel-success">
					<div class="panel-heading">
						<h3 class="panel-title">Інтернет</h3>
					</div>
					<div class="panel-body">
						<table class="table table-striped">
						  <tbody>
							<tr>
							  <td>Електронна пошта</td>
							  <th id="email"><?php echo $email;?></th>
							</tr>
							<tr>
							  <td>Ім'я користувача</td>
							  <th id="username"><?php echo $username;?></th>
							</tr>
							<tr>
							  <td>Пароль</td>
							  <th id="password"><?php echo $password;?></th>
							</tr>
							<tr>
							  <td>Веб-сайт</td>
							  <th id="website"><a href="https://uahosting.com.ua/domain.php" target="_blank" title="Хочете дешево придбати цей домен, чи якийсь інший?">https://<?php echo $website;?>/</a></th>
							</tr>
						  </tbody>
						</table> 
					</div>
				</div>
				
				<div class="panel panel-danger">
					<div class="panel-heading">
						<h3 class="panel-title">Фінанси</h3>
					</div>
					<div class="panel-body">
						<table class="table table-striped">
						  <tbody>
							<tr>
							  <td>Банківська картка</td>
							  <th id="cc_number"><?php echo $cc['number'].' ('.strtoupper($cc['type']).')';?></th>
							</tr>
							<tr>
							  <td>Спливає</td>
							  <th id="cc_expire"><?php echo $cc['expire']['month'].'/'.$cc['expire']['year'];?></th>
							</tr>
							<tr>
							  <td>CVV2</td>
							  <th id="cc_cvv"><?php echo $cc['cvv'];?></th>
							</tr>
						  </tbody>
						</table> 
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
