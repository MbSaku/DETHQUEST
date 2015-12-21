<?php
if (isset ($_POST['newname']) 
and isset ($_POST['newlevel'])){
  $query = 'insert into '.mod.'deth_charges (
  name, 
  level
  ) values (
  "'.$_POST['newname'].'", 
  "'.$_POST['newlevel'].'"
  ) ';
  if ($datalink->dbQuery($query, 'query') > 0){
    echo '<p class="fine">'.Charge_created.'</p>';
  }
}
if (isset ($_POST['charge']) 
and isset ($_POST['name']) 
and isset ($_POST['level'])){
  $query = 'update '.mod.'deth_charges set 
  name="'.$_POST['name'].'", 
  level="'.$_POST['level'].'" 
  where id='.$_POST['charge'];
  if ($datalink->dbQuery($query, 'query') > 0){
    echo '<p class="fine">'.Charge_updated.'</p>';
  }
}
if (isset($_POST['charge'])
and (isset($_POST['delete'])
or isset($_POST['confirmdeletion']))){
  if (isset($_POST['confirmdeletion'])){
    $query = 'delete from '.mod.'deth_charges
    where id='.$_POST['charge'];
    if ($datalink->dbQuery($query, 'query') > 0){
      echo '<p class="fine">'.Charge_deleted.'</p>';
    }
  }else{
    echo '<div class="error">
    <p>'.You_will_delete_this_charge_are_you_sure.'</p>
    <p>
    <form name="confirmation" onsubmit="event.preventDefault(); backend.post(this);">
    <input type="hidden" name="charge" value="'.$_POST['charge'].'">
    <input type="hidden" name="confirmdeletion" value="1">
    <input type="submit" value="'.Yes_delete_charge.'">
    </form>
    <form name="confirmation" onsubmit="event.preventDefault(); backend.post(this);">
    <input type="submit" name="cancel" value="'.No_delete_charge.'">
    </form>
    </p>
    </div>';
  }
}
?>

  <p><?php echo HelpCharges; ?></p>
  
  <div class="editionitem">
  <div class="field edhead"><b><?php echo Charge; ?></b></div>
  <div class="field edhead"><b><?php echo Level; ?></b></div>
  </div>
  
  <form name="newgrup" onsubmit="event.preventDefault(); backend.post(this);">
    <div class="editionitem">
    <div class="field"><input type="text" name="newname" value=""></div>
    <div class="field"><input type="text" name="newlevel" value="" style="width:4em"></div>
    <div class="field"><input type="submit" value="<?php echo Create_charge; ?>"></div>
    </div>
  </form>

<?php
  $query = 'select id, name, level 
  from '.mod.'deth_charges
  order by level asc';
  $result = $datalink->dbQuery($query, 'result');
  foreach ($result as $row){  //Lista de grupos editables.
    echo '<form name="editgrup'.$row[0].'" onsubmit="event.preventDefault(); backend.post(this);">
    <input type="hidden" name="charge" value="'.$row[0].'">
    <div class="editionitem">
    <div class="field"><input type="text" name="name" value="'.$row[1].'"></div>
    <div class="field"><input type="text" name="level" value="'.$row[2].'" style="width:4em"></div>
    <div class="field"><input type="submit" value="'.Change_charge.'">
    <input type="checkbox" name="delete" value="1"> '.Delete_charge.'</div>
    </div>
    </form>';

  }
?>