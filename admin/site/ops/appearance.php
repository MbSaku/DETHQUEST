<?php
if (isset($_POST['uploadtheme'])
and isset($_FILES['zip'])){
  echo $site->addTheme($_FILES['zip']);
}
if (isset($_POST['theme'])
and isset($_POST['deletetheme'])){
  echo '<div class="error">
  <form name="confirmation" method="post" action="">
  <p>'.You_will_delete_this_theme_are_you_sure.'</p>
  <p><input type="hidden" name="theme" value="'.$_POST['theme'].'">
  <input type="submit" name="confirmdeletion" value="'.Yes_delete_theme.'">
  <input type="submit" name="cancel" value="'.No_delete_theme.'">
  </form>
  </div>';
}
if (isset($_POST['theme'])
and isset($_POST['confirmdeletion'])){
  $query = 'delete from int_styles 
  where folder="'.$_POST['theme'].'"
  and active!=1';
  if ($site->getDatalink()->dbQuery($query, 'query') > 0){
    echo $site->delTheme($_POST['theme']);
  }
}
if (isset($_POST['theme'])
and isset($_POST['activatetheme'])){
  $query = 'update int_styles 
  set active=0
  where folder!="'.$_POST['theme'].'"';
  $site->getDatalink()->dbQuery($query, 'query');
  $query = 'update int_styles 
  set active=1
  where folder="'.$_POST['theme'].'"';
  if ($site->getDatalink()->dbQuery($query, 'query') > 0){
    echo '<p class="fine">'.Theme_activated.'</p>
    <script>location.reload();</script>';
  }
}
?>
<fieldset><legend><?php echo $module->getName().' - '.Appearance; ?></legend>
  <p><?php echo Appearance_help; ?></p>
  <div class="editiontitle"><?php echo Active_theme; ?></div>
  <?php
  $query = 'select id, folder, name, active
  from int_styles
  where active=1
  limit 1';
  $result = $site->getDatalink()->dbQuery($query, 'result');
  foreach ($result as $row){
    echo '<div class="editionitem">
    <form name="theme'.$row[0].'" method="post" action="">
    <input type="hidden" name="theme" value="'.$row[1].'">
    <div class="field"><img src="'.$site->getBaseroot().'styles/'.$row[1].'/icon.png"></div>
    <div class="field"><b>'.$row[2].'</b></div>
    </form>
    </div>';
  }
  ?>
  <div class="editiontitle"><?php echo Available_themes; ?></div>
  <?php
  $query = 'select id, folder, name, active
  from int_styles
  where active=0
  order by name asc';
  $result = $site->getDatalink()->dbQuery($query, 'result');
  foreach ($result as $row){
    echo '<div class="editionitem">
    <form name="theme'.$row[0].'" method="post" action="">
    <input type="hidden" name="theme" value="'.$row[1].'">
    <div class="field"><img src="'.$site->getBaseroot().'styles/'.$row[1].'/icon.png"></div>
    <div class="field"><b>'.$row[2].'</b></div>
    <div class="field">
    <input type="submit" name="activatetheme" value="'.Activate_theme.'">
    <input type="submit" name="deletetheme" value="'.Delete_theme.'">
    </div>
    </form>
    </div>';
  }
  ?>
  <form name="uploadtheme" method="post" enctype="multipart/form-data" action="">
    <p class="pinput"><b><?php echo New_theme.'</b><br>'.Upload_zip_theme_files; ?><br>
    <input type="file" name="zip"></p>
    <p><input type="submit" name="uploadtheme" value="<?php echo Upload_zip_file; ?>"></p>
  </form>
</fieldset>