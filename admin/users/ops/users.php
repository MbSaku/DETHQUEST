<?php
if (isset($_GET['pag'])){
  $pag = $_GET['pag'];
}else{
  $pag = 0;
}
if (isset($_REQUEST['filter'])){
  $filter = $_REQUEST['filter'];
}else{
  $filter = '';
}
$regspag = 10;
?>
<fieldset><legend><?php echo $module->getName().' - '.Users; ?></legend>
  <p><?php echo Users_help; ?></p>
<?php 
if (!isset($_GET['user'])){ 
?>
  <p><a href="<?php echo $basiclink.'&pag='.$pag.'&user=0'; ?>"><input type="button" value="<?php echo Add_new_user; ?>"></a></p>
  <?php
  if ($pag == 0){
    echo '<div class="editiontitle">'.Active_users.'</div>';
    $query = 'select id from int_user where lastlogin>'.(time() - 600).' order by lastlogin asc limit 5';
    $result = $site->getDatalink()->dbQuery($query, 'result');
    foreach ($result as $row){
      $auser = new User($site->getDatalink(), $row[0]);
      echo '<a href="'.$basiclink.'&filter='.$filter.'&pag='.$pag.'&user='.$auser->getId().'">
      <div class="editionitem">
      <div class="field"><b>'.$auser->getName().'</b></div>
      <div class="field">'.$auser->getEmail().'</div>
      <div class="field">'.$auser->chargeName().'</div>
      <div class="field">'.Last_active_on.' '.strftime('%d/%m/%Y %H:%M', $auser->getLastlogin()).'</div>
      </div></a>';
    }
  }
  ?>
  <form name="usersearch" method="post" action="">
    <p class="pinput"><?php echo Username_email_filter; ?><br>
    <input type="text" name="filter" value="<?php echo $filter; ?>">
    <input type="submit" value="<?php echo Filter_users; ?>"></p>
  </form>
  <div class="editiontitle"><?php echo All_users; ?></div>
  <?php
  $query = 'select id from int_user where (name like "%'.$filter.'%" or email like "%'.$filter.'%") order by name asc';
  $numpags = ceil ($site->getDatalink()->dbQuery($query, 'rows') / $regspag);
  $result = $site->getDatalink()->dbQuery($query, 'result', ($regspag * $pag));
  $i = 0;
  foreach ($result as $row){
    if ($i < $regspag){
      $auser = new User($site->getDatalink(), $row[0]);
      echo '<a href="'.$basiclink.'&filter='.$filter.'&pag='.$pag.'&user='.$auser->getId().'">
      <div class="editionitem">
      <div class="field"><b>'.$auser->getName().'</b></div>
      <div class="field">'.$auser->getEmail().'</div>
      <div class="field">'.$auser->chargeName().'</div>
      <div class="field">'.Last_active_on.' '.strftime('%d/%m/%Y %H:%M', $auser->getLastlogin()).'</div>
      </div>
      </a>';
    }
    $i++;
  }
  echo '<div class="pags">';  //Paginador.
  $dots = false;
  for ($a = 0; $a < $numpags; $a++){
    if ($a > $numpags - 2
    or $a < 3
    or ($a > $pag - 5 and $a < $pag + 5)){
      echo '<a href="'.$basiclink.'&filter='.$filter.'&pag='.$a.'">';
      if ($a == $pag){ echo '<b>['.$a.']</b>'; }else{ echo $a; }
      echo '</a>';
      $dots = false;
    }else{
      if (!$dots){
        echo ' ... ';
        $dots = true;
      }
    }
  }
  echo '</div>';
}else{ 
  $auser = new User($site->getDatalink(), $_GET['user']);
  if (isset($_POST['name'])
  and isset($_POST['email'])
  and isset($_POST['charge'])
  and isset($_POST['password'])
  and isset($_POST['saveuser'])){
    $auser->setEmail($_POST['email']);
    $auser->setName($_POST['name']);
    $auser->setCharge($_POST['charge']);
    if (
    ($_POST['charge'] <= $user->getCharge()
    and $user->getId() != $auser->getId())
    or
    ($user->getLevel() >= $auser->getLevel()
    and $user->getId() != $auser->getId())
    ){
      echo '<p class="error">'.Operation_not_permitted.'</p>';
    }else{
      if ($auser->getId() == 0){
        $errormsg = $auser->register($_POST['name'], $_POST['charge'], $_POST['email'], $_POST['password']);
        if ($errormsg != ''){
          echo $errormsg;
        }else{
          if (isset($_POST['active'])){
            $auser->validate();
          }
          echo '<p class="fine">'.User_registered.'</p>';
        }
      }else{
        if ($_POST['password'] != ''){
          echo $auser->passwordChange($_POST['password']);
        }
        if (isset($_POST['active'])){
          echo $auser->validate();
        }
        echo $auser->save();
      }
    }
  }
  if (isset($_POST['deleteuser'])
  or isset($_POST['confirmdeletion'])){
    if (($user->getLevel() >= $auser->getLevel()
    and $user->getId() != $auser->getId())
    or $user->getId() == $auser->getId()){
      echo '<p class="error">'.Operation_not_permitted.'</p>';
    }else{
      if (isset($_POST['confirmdeletion'])){
        echo $auser->delete();
      }else{
        echo '<div class="error">
        <form name="confirmation" method="post" action="">
        <p>'.You_will_delete_this_user_are_you_sure.'</p>
        <p><input type="submit" name="confirmdeletion" value="'.Yes_delete_user.'">
        <input type="submit" name="cancel" value="'.No_delete_user.'"></p>
        </form>
        </div>';
      }
    }
  }
?>
  <h2><?php echo Editing_user.' '.$auser->getName(); ?></h2>
  <p><a href="<?php echo $basiclink.'&filter='.$filter.'&pag='.$pag; ?>"><input type="button" value="<?php echo Back_to_users; ?>"></a></p>
  <form name="edituser" method="post" action="<?php echo $basiclink.'&filter='.$filter.'&pag='.$pag.'&user='.$auser->getId(); ?>">
     <p class="pinput"><?php echo User_name; ?><br>
     <input type="text" name="name" value="<?php echo $auser->getName(); ?>"></p>
     <p class="pinput"><?php echo Email; ?><br>
     <input type="text" name="email" value="<?php echo $auser->getEmail(); ?>"></p>
     <p class="pinput"><?php echo Charge; ?><br>
     <select name="charge">
      <?php
      $query = 'select distinct id, name from int_charges order by level asc, name asc';
      $result = $site->getDatalink()->dbQuery($query, 'result');
      foreach($result as $row){
        echo '<option value="'.$row[0].'"'; if ($auser->getCharge() == $row[0]){ echo ' selected'; } echo '>'.$row[1].'</option>';
      }
      ?>
    </select></p>
    <?php
    if ($auser->getId() == 0 or !$auser->getActivated()){
      echo '<p><input type="checkbox" name="active" value="true"> '.User_active.'</p>';
    }
    ?>
    <p class="pinput"><?php if ($auser->getId() == 0){ echo New_password; }else{ echo Reset_password; }?><br>
    <input type="text" name="password" value=""></p>
    <p><input type="submit" name="saveuser" value="<?php echo Save_user; ?>">
    <?php
    if ($auser->getId() != 0){
      echo '<input type="submit" name="deleteuser" value="'.Delete_user.'"></p>';
    }
    ?>
    </p>
  </form>
<?php 
}
?>
</fieldset>