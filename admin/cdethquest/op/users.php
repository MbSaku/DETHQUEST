<?php
if (isset($_POST['pag'])){
  $pag = $_POST['pag'];
}else{
  $pag = 0;
}
if (isset($_POST['filter'])){
  $filter = $_POST['filter'];
}else{
  $filter = '';
}
$regspag = 20;
if (isset ($_POST['charge']) 
and isset ($_POST['user'])){
  $query = 'update '.mod.'deth_user set 
  charge="'.$_POST['charge'].'" 
  where user='.$_POST['user'];
  if ($datalink->dbQuery($query, 'query') > 0){
    echo '<p class="fine">'.Charge_applied.'</p>';
  }
}
?>

  <p><?php echo HelpUsers; ?></p>
  
  <form name="usersearch" onsubmit="event.preventDefault(); backend.post(this);">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <p class="pinput"><?php echo Search_by_name; ?><br>
    <input type="text" name="filter" value="<?php echo $filter; ?>">
    <input type="submit" value="<?php echo Search; ?>"></p>
  </form>

  <div class="editiontitle"><?php echo Users; ?></div>
  <?php
  
  $query = 'select int_user.id from int_user, int_permissions, int_admin 
  where int_admin.url="'.$_GET['module'].'" 
  and int_admin.id=int_permissions.module 
  and int_permissions.charge=int_user.charge
  and int_user.name like "%'.$filter.'%" order by int_user.name asc';
  $numpags = ceil ($datalink->dbQuery($query, 'rows') / $regspag);
  $result = $datalink->dbQuery($query, 'result', ($regspag * $pag));
  $i = 0;
  foreach ($result as $row){
    if ($i < $regspag){
      $auser = new User($datalink, $row[0]);
      $guser = new Dethuser($datalink, $row[0]);
      echo '<div class="editionitem">
      <div class="field"><b>'.$auser->getName().'</b></div>
      <div class="controls">
      <form name="user'.$auser->getId().'" onsubmit="event.preventDefault(); backend.post(this);">
      <input type="hidden" name="pag" value="'.$pag.'">
      <input type="hidden" name="filter" value="'.$filter.'">
      <input type="hidden" name="user" value="'.$auser->getId().'">
      <select name="charge"><option value="0"'; 
      if ($guser->getCharge() == 0){ 
        echo ' selected'; 
      }
      echo '>- '.None.' -</option>';
      $query = 'select id, name 
      from '.mod.'deth_charges
      order by level desc';
      $result2 = $datalink->dbQuery($query, 'result');
      foreach ($result2 as $row2){
        echo '<option value="'.$row2[0].'"'; 
        if ($guser->getCharge() == $row2[0]){ 
          echo ' selected'; 
        }
        echo '>'.$row2[1].'</option>';
      }
      echo '</select> <input type="submit" name="apply" value="'.Apply.'">
      </form>
      </div>
      </div>';
    }
    $i++;
  }
  echo '<div class="pags">';
  $dots = false;
  for ($a = 0; $a < $numpags; $a++){
    if ($a > $numpags - 2
    or $a < 3
    or ($a > $pag - 5 and $a < $pag + 5)){
      echo '<form name="pag'.$a.'" onsubmit="event.preventDefault(); backend.post(this);">
      <input type="hidden" name="pag" value="'.$a.'">
      <input type="hidden" name="filter" value="'.$filter.'">
      <input type="submit" value="';
      if ($a == $pag){ echo '['.$a.']'; }else{ echo $a; }
      echo '">
      </form>';
      $dots = false;
    }else{
      if (!$dots){
        echo ' ... ';
        $dots = true;
      }
    }
  }
  echo '</div>';
?>
