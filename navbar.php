<?php

?>

<div class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a href="/" class="navbar-brand"><?php echo $WEBSITE;?></a>
          <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="navbar-collapse collapse" id="navbar-main">
          <ul class="nav navbar-nav">
            <li <?php if($location=='index')echo'class="active"';?>>
              <a href="/">Головна</a>
            </li>
            <li <?php if($location=='linux')echo'class="active"';?>>
              <a href="linux.php">Linux команди</a>
            </li>
            <li <?php if($location=='ukrname')echo'class="active"';?>>
              <a href="ukrname.php">Генератор імен</a>
            </li>
			  <!--
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="themes">Всі Інструменти <span class="caret"></span></a>
              <ul class="dropdown-menu" aria-labelledby="themes">
                <li><a href="#">Генератор імен</a></li>
                <li><a href="#">Більш нема</a></li>
                <li class="divider"></li>
                <li><a href="#" class="disabled">Рулетка переможців</a></li>
              </ul>
            </li>
            -->
            <li <?php if($location=='api')echo'class="active"';?>>
              <a href="#">APIv1.0</a>
            </li>
          </ul>

          <ul class="nav navbar-nav navbar-right">
			<?php if ($_SESSION['user']) { ?>
				<li class="dropdown <?php if($location=='account')echo'active';?>">
				  <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="themes"><?php echo $_SESSION['user']['name']; ?> <span class="caret"></span></a>
				  <ul class="dropdown-menu" aria-labelledby="themes">
					<li><a href="#">Сховище даних</a></li>
					<li class="divider"></li>
					<li><a href="account.php">Мій аккаунт</a></li>
					<li><a href="settings.php">Налаштування</a></li>
					<li class="divider"></li>
					<li><a href="logout.php">Вихід</a></li>
				  </ul>
				</li>
            <?php } else {?>
				<li <?php if($location=='login')echo'class="active"';?>><a href="login.php">Вхід/Реєстрація</a></li>
			<?php } ?>
          </ul>

        </div>
      </div>
    </div>
