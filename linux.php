<?php

$PAGE = "Linux команди";

session_start();
header('Content-type: text/html; charset=utf-8');
$location = "linux";

require_once "common.php";
require_once "Linux.php";
$linux = new Linux($db);
require_once "check_session.php";
//$linux->firstrun();

$EDIT_ALLOWED = ($_SESSION['user'])?true:false;

if($EDIT_ALLOWED) {
    if($_REQUEST['action'] == 'addcom' && $_REQUEST['command']) {
        $linux->addCommand(array('category'=>$_REQUEST['category'],'command'=>$_REQUEST['command'],'rootonly'=>$_REQUEST['rootonly'],'description'=>$_REQUEST['description']));
    } else if($_REQUEST['action'] == 'updcom' && $_REQUEST['command'] && $_REQUEST['category'] && $_REQUEST['rootonly']) {
        $linux->updateCommand($_REQUEST['view'],array('category'=>$_REQUEST['category'],'command'=>$_REQUEST['command'],'rootonly'=>$_REQUEST['rootonly'],'description'=>$_REQUEST['description']));
    } else if($_REQUEST['rm']) {
        $linux->removeCommand($_REQUEST['rm']);
    } else if($_REQUEST['action'] == 'addcat' && $_REQUEST['cat']) {
        $linux->addCategory($_REQUEST['cat']);
    } else if($_REQUEST['action'] == 'updcat' && $_REQUEST['catid'] && $_REQUEST['cat']) {
        $linux->updateCategory($_REQUEST['catid'], $_REQUEST['cat']);
    } else if($_REQUEST['action'] == 'rmcat' && $_REQUEST['catid']) {
        $linux->removeCategory($_REQUEST['catid']);
    } else if($_REQUEST['action'] == 'addexa' && $_REQUEST['example'] && $_REQUEST['view']) {
        $linux->addExample(array('example'=>$_REQUEST['example'],'command'=>$_REQUEST['view'],'description'=>$_REQUEST['description']));
    } else if($_REQUEST['action'] == 'updexa' && $_REQUEST['exaid'] && $_REQUEST['example']) {
        $linux->updateExample($_REQUEST['exaid'],array('example'=>$_REQUEST['example'],'description'=>$_REQUEST['description']));
    } else if($_REQUEST['action'] == 'rmexa' && $_REQUEST['exaid']) {
        $linux->removeExample($_REQUEST['exaid']);
    }
}

if($_REQUEST['view']) {
    $command = $linux->getCommand($_REQUEST['view']);
}
if($command) {
    $examples = $linux->getExamples($_REQUEST['view']);
} else {
    $commands = $linux->getCommands($_REQUEST['q'], $_REQUEST['c']);
}
$categories = $linux->getCategories();

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
					<h3>Команди оболонки Linux і приклади їх використання</h3>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-body">
					    <div class="col-lg-10 col-lg-offset-1">
					      <?php if(!$command) { ?>
					      <form class="form-horizontal" method="get">
    						  <fieldset>
    							<div class="form-group">
    							    <div class="input-group">
                                        <select id="select" class="form-control" name="c">
        									<option value="">Будь-яка категорія</option>
        									<?php foreach($categories as $category) { ?>
        										<option value="<?php echo $category['id'];?>" <?php if($_REQUEST['c']==$category['id'])echo'selected';?>><?php echo $category['title'];?></option>
        									<?php } ?>
        								</select>
        								<?php if($EDIT_ALLOWED) { ?>
                                        <span class="input-group-btn">
                                          <a class="btn btn-default" href="#" data-toggle="modal" data-target=".cat-modal">
                                            <span class="fa fa-cog"></span></a>
                                        </span>
                                        <?php } ?>
                                    </div>
    							</div>
    							<div class="form-group">
    						        <div class="input-group">
                                        <input class="form-control" type="text" name="q" placeholder="Пошук команд" value="<?php echo $_REQUEST['q']?>">
                                        <span class="input-group-btn">
                                          <a class="btn btn-warning" href="?">Скинути</a>
                                        </span>
                                    </div>
    							</div>
    							<div class="form-group">
    							      <input type="submit" class="btn btn-info btn-block" value="Пошук">
    							</div>
    						  </fieldset>
    						</form>
					      <?php } else { ?>
    					  <form class="form-horizontal" method="get">
    					  <input type="hidden" name="view" value="<?php echo $command['id'];?>">
    					  <input type="hidden" name="action" value="updcom">
    						  <fieldset>
    							<div class="form-group has-success">
    						        <input class="form-control" type="text" name="command" placeholder="Команда" value="<?php echo $command['command']?>" <?php if(!$EDIT_ALLOWED) echo "readonly"; ?>>
    							</div>
    							<div class="form-group">
    							    <select class="form-control" name="category" <?php if(!$EDIT_ALLOWED) echo "disabled"; ?>>
        									<?php foreach($categories as $category) { ?>
        										<option value="<?php echo $category['id'];?>" <?php if($command['category']==$category['id'])echo'selected';?>><?php echo $category['title'];?></option>
        									<?php } ?>
        								</select>
    							</div>
    							<div class="form-group">
    							    <select class="form-control" name="rootonly" <?php if(!$EDIT_ALLOWED) echo "disabled"; ?>>
        									<option value="0" <?php if($command['rootonly']==0)echo'selected';?>>Не лише SUDO</option>
        									<option value="1" <?php if($command['rootonly']==1)echo'selected';?>>Лише SUDO</option>
        								</select>
    							</div>
    							<div class="form-group">
    							    <textarea class="form-control" name="description" rows="3" <?php if(!$EDIT_ALLOWED) echo "readonly"; ?>><?php echo $command['description']; ?></textarea>
    							</div>
    							<?php if($EDIT_ALLOWED) { ?>
    							<div class="form-group">
    							      <input type="submit" class="btn btn-success btn-block" value="Зберегти">
    							</div>
    							<?php } ?>
    						  </fieldset>
    						</form>
    						<?php } ?>
    					</div>
					</div>
				</div>
				
			</div>
			
    			<div class="col-md-8">
    				<div class="panel panel-primary">
    					<div class="panel-body">
    						<?php if(!$command) { ?>
    						<table class="table table-striped table-hover">
    						  <thead>
    							<tr>
    							  <th>Команда</th>
    							  <th title="Лише SUDO">SU*</th>
    							  <th>Категорія</th>
    							  <th>Опис</th>
    							  <th></th>
    							</tr>
    						  </thead>
    						  <tbody>
    						    <?php foreach($commands as $c) { ?>
								<tr class='su ccess'>
								  <td><a href='?view=<?php echo $c['id'];?>' class='btn btn-primary btn-xs btn-block' title="Переглянути"><b><?php echo $c['command'];?></b></a></td>
								  <td><?php echo ($c['rootonly'])?'<span class="text-danger">Так</span>':'<span class="text-success">Ні</span>';?></td>
								  <td><?php echo $categories[$c['category']-1]['title'];?></td>
								  <td><?php echo $c['description'];?></td>
								  <td><?php if($EDIT_ALLOWED) { ?> <a href='?rm=<?php echo $c['id'];?>' class='btn btn-danger btn-xs' title="Видалити"><span class='fa fa-trash'></span></a><?php } ?></td>
								</tr>
							    <?php } if (empty($commands)) { ?>
							    <tr class='warning'>
							        <td colspan="4"><b>За вашим запитом нічого не знайдено</b></td>
							    </tr>
							    <?php } if($EDIT_ALLOWED) { ?>
							    <tr class="active has-success"><form><input type="hidden" name="action" value="addcom">
							        <td><input type="text" name="command" class="form-control input-sm" placeholder="Команда" required></td>
							        <td><select class="form-control input-sm" name="rootonly">
									    <option value="0" selected>Ні</option>
									    <option value="1">Так</option>
								        </select></td>
							        <td><select class="form-control input-sm" name="category">
									<?php foreach($categories as $category) { ?>
										<option value="<?php echo $category['id'];?>" <?php if($_REQUEST['category']==$category['id'])echo'selected';?>><?php echo $category['title'];?></option>
									<?php } ?>
								</select></td>
							        <td><input type="text" name="description" class="form-control input-sm" placeholder="Опис"></td>
							        <td><button type="submit" class="btn btn-sm btn-success"><span class="fa fa-plus"></span></button></td>
							    </form>
							    </tr>
							    <?php } ?>
    						  </tbody>
    						</table> 
    						
    						<?php } else { ?>
    						
    						<table class="table table-striped table-hover">
    						  <thead>
    							<tr>
    							  <th>Приклад</th>
    							  <th>Опис</th>
    							  <th></th>
    							</tr>
    						  </thead>
    						  <tbody>
    						    <?php foreach($examples as $c) { ?>
            					<tr>
            					    <form><input type="hidden" name="action" value="updexa"><input type="hidden" name="exaid" value="<?php echo $c['id'];?>"><input type="hidden" name="view" value="<?php echo $command['id'];?>">
            					  <td><input type="text" name="example" class="form-control input-sm" value="<?php echo $c['example'];?>" required <?php if(!$EDIT_ALLOWED) echo "readonly"; ?>></td>
            					  <td><input type="text" name="description" class="form-control input-sm" value="<?php echo $c['description'];?>" <?php if(!$EDIT_ALLOWED) echo "readonly"; ?>></td>
            					  <td><?php if($EDIT_ALLOWED) { ?><button type="submit" class='btn btn-info btn-xs' title="Оновити"><span class='fa fa-wrench'></span></button> <a href='?view=<?php echo $command['id'];?>&action=rmexa&exaid=<?php echo $c['id'];?>' class='btn btn-danger btn-xs' title="Видалити"><span class='fa fa-trash'></span></a><?php } ?></td>
            					  </form>
            					</tr>
            				    <?php } if (empty($examples)) { ?>
            				    <tr class='warning'>
            				        <td colspan="4"><b>Прикладів немає</b></td>
            				    </tr>
            				    <?php } if($EDIT_ALLOWED) { ?>
            				    <tr class='active'><form><input type="hidden" name="view" value="<?php echo $command['id'];?>"><input type="hidden" name="action" value="addexa">
            				        <td class="has-success"><input type="text" name="example" class="form-control input-sm" placeholder="Новий приклад" required></td>
            				        <td class="has-success"><input type="text" name="description" class="form-control input-sm" placeholder="Опис"></td>
            				        <td><button type="submit" class="btn btn-sm btn-success"><span class="fa fa-plus"></span></button></td>
            				    </form>
            				    </tr>
            				    <?php } ?>
    						</table> 
    						
    					    <?php }  ?>	
    					</div>
    				</div>
    			</div>
				
		</div>
	
	<?php include "footer.php"; ?>

    </div>
    
    
    
    
    <div class="modal fade cat-modal" tabindex="-1" role="dialog" aria-labelledby="catModal">>
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Редагування категорій</h4>
          </div>
          <div class="modal-body">
              <table class="table table-striped table-hover">
				  <thead>
					<tr>
					  <th>Категорія</th>
					  <th></th>
					</tr>
				  </thead>
				  <tbody>
				    <?php foreach($categories as $c) { ?>
					<tr>
					    <form><input type="hidden" name="action" value="updcat"><input type="hidden" name="catid" value="<?php echo $c['id'];?>">
					  <td><input type="text" name="cat" class="form-control input-sm" value="<?php echo $c['title'];?>" required></td>
					  <td><button type="submit" class='btn btn-info btn-xs' title="Оновити"><span class='fa fa-wrench'></span></button> <a href='?action=rmcat&catid=<?php echo $c['id'];?>' class='btn btn-danger btn-xs' title="Видалити"><span class='fa fa-trash'></span></a></td>
					  </form>
					</tr>
				    <?php } if (empty($categories)) { ?>
				    <tr class='warning'>
				        <td colspan="4"><b>Категорій не існує</b></td>
				    </tr>
				    <?php } ?>
				    <tr class='active'><form><input type="hidden" name="catid" value="<?php echo $c['id'];?>"><input type="hidden" name="action" value="addcat">
				        <td class="has-success"><input type="text" name="cat" class="form-control input-sm" placeholder="Нова категорія" required></td>
				        <td><button type="submit" class="btn btn-sm btn-success"><span class="fa fa-plus"></span></button></td>
				    </form>
				    </tr>
				  </tbody>
				</table>
          </div>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/custom.js"></script>
    <script src="./js/bootstrap.js"></script>
    
    <script type="text/javascript">
        addcat() {
            var cat = window.prompt("Введіть назву категорії:","");
            window.location.assign("/linux.php?action=addcat&cat="+cat);
        }
    </script>
  </body>
</html>
