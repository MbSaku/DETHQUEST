<?php
if (isset($_POST['charge'])
and isset($_POST['name'])
and isset($_POST['level'])
and isset($_POST['savecharge'])){
  if ($_POST['level'] <= $user->getLevel()){
    echo '<p class="error">'.Operation_not_petrmitted.'</p>';
  }else{
    $query = 'select id from int_charges where id="'.$_POST['charge'].'" limit 1';
    if ($site->getDatalink()->dbQuery($query, 'rows') > 0){
      $query = 'update int_charges set name="'.$_POST['name'].'", level="'.$_POST['level'].'" where id="'.$_POST['charge'].'"';
    }else{
      $query = 'insert into int_charges (name, level) values ("'.$_POST['name'].'", "'.$_POST['level'].'")';
    }
    if ($site->getDatalink()->dbQuery($query, 'query') > 0){
      echo '<p class="fine">'.Charge_saved.'</p>';
    }else{
      echo '<p class="error">'.Charge_not_saved.'</p>';
    }
  }
}
if (isset($_POST['charge'])
and isset($_POST['deletecharge'])){
  if ($site->getDatalink()->dbQuery('delete from int_charges where id="'.$_POST['charge'].'" and level>'.$user->getLevel(), 'query') > 0){
    echo '<p class="fine">'.Charge_deleted.'</p>';
  }else{
    echo '<p class="error">'.Operation_not_petrmitted.'</p>';
  }
}
?>
<fieldset><legend><?php echo $module->getName().' - '.Charges; ?></legend>
  <p><?php echo Charge_help; ?></p>
  <div class="editiontitle"><?php echo Available_charges; ?></div>
  <div class="editionitem">
    <form name="charge0" method="post" action="">
    <input type="hidden" name="charge" value="0">
    <div class="field">
    <?php echo Charge_name; ?><br>
    <input type="text" name="name" value="">
    </div>
    <div class="field">
    <?php echo Charge_level; ?><br>
    <input type="number" step="1" min="<?php ($user->getLevel() + 1); ?>" name="level" value="<?php echo ($user->getLevel() + 1); ?>" style="width:4em">
    </div>
    <div class="field">
    <input type="submit" name="savecharge" value="<?php echo Add_new_charge; ?>"></div>
    </form>
  </div>
  <?php
  $query = 'select id, name, level from int_charges where level > '.$user->getLevel().' order by level asc';
  $result = $site->getDatalink()->dbQuery($query, 'result');
  foreach ($result as $row){
    echo '<div class="editionitem">
    <form name="charge'.$row[0].'" method="post" action="">
    <input type="hidden" name="charge" value="'.$row[0].'">
    <div class="field">
    '.Charge_name.'<br>
    <input type="text" name="name" value="'.$row[1].'">
    </div>
    <div class="field">
    '.Charge_level.'<br>
    <input type="number" step="1" min="'.($user->getLevel() + 1).'" name="level" value="'.$row[2].'" style="width:4em">
    </div>
    <div class="controls">
    <input type="submit" name="savecharge" value="'.Save_charge.'">
    <input type="submit" name="deletecharge" value="'.Delete_charge.'"></div>
    </form>
    </div>';
  }
  ?>
</fieldset>