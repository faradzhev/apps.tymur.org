<?php

$PAGE = "Головна";

session_start();
header('Content-type: text/html; charset=utf-8');
$location = "index";

require_once "common.php";
require_once "check_session.php";

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
					<h3>Українські Інтернет-інструменти</h3>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-4">
				<div class="panel panel-warning mb-3">
					<div class="panel-heading">
						<h3 class="panel-title">Linux команди</h3>
					</div>
					<div class="panel-body">
						<p>Перелік команд для оболонки Linux та приклади їх використання</p>
						<a href="linux.php" class="btn btn-warning btn-block btn-sm">Скористатися інструментом</a>
					</div>
					<div class="panel-footer text-muted">
					    без реєстрації
                    </div>
				</div>
			</div>
			
			<div class="col-sm-4">
				<div class="panel panel-primary mb-3">
					<div class="panel-heading">
						<h3 class="panel-title">Генератор імен</h3>
					</div>
					<div class="panel-body">
						<p>Генератор випадкових українських особистостей та їх параметрів</p>
						<a href="ukrname.php" class="btn btn-primary btn-block btn-sm">Скористатися інструментом</a>
					</div>
					<div class="panel-footer text-muted">
					    без реєстрації / з реєстрацією
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
