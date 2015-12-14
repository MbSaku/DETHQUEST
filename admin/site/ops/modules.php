<?php
if (isset($_POST['uploadmodule'])
and isset($_FILES['zip'])){
  echo $site->addModule($_FILES['zip']);
}
if (isset($_POST['modulef'])
and isset($_POST['deletemodule'])){
  echo '<div class="error">
  <form name="confirmation" method="post" action="">
  <p>'.You_will_delete_this_module_are_you_sure.'</p>
  <p><input type="hidden" name="modulef" value="'.$_POST['modulef'].'">
  <input type="submit" name="confirmdeletion" value="'.Yes_delete_module.'">
  <input type="submit" name="cancel" value="'.No_delete_module.'">
  </form>
  </div>';
}
if (isset($_POST['modulef'])
and isset($_POST['confirmdeletion'])){
  $query = 'delete from int_admin 
  where active!=1
  and folder="'.$_POST['modulef'].'"';
  if ($site->getDatalink()->dbQuery($query, 'query') > 0){
    echo $site->delModule($_POST['modulef']);
  }
}
if( isset( $_POST['module'] )
and isset( $_POST['name'] )
and isset( $_POST['savemodule'] ) ){
  $moduled = new Module( $site->getDatalink(), $_POST['module'] );
  $moduled->setName( $_POST['name'] );
  if( $moduled->getId() > 3
  and isset( $_POST['url'] ) ){
    $moduled->setUrl( $_POST['url'] );
    if( isset( $_POST['active'] ) ){
      $moduled->setActive( true );
    }else{
      $moduled->setActive( false );
    }
    if( isset( $_POST['shortcut'] ) ){
      $moduled->setShortcut( true );
    }else{
      $moduled->setShortcut( false );
    }
  }
  echo $moduled->saveConfig();
}
if( isset( $_POST['module'] )
and isset( $_POST['moveup'] ) ){
  $moduled = new Module( $site->getDatalink(), $_POST['module'] );
  $moduled->moveUp();
}
if( isset( $_POST['module'] )
and isset( $_POST['movedown'] ) ){
  $moduled = new Module( $site->getDatalink(), $_POST['module'] );
  $moduled->moveDown();
}
?>
<fieldset><legend><?php echo $module->getName().' - '.Modules; ?></legend>
  <p><?php echo Modules_help; ?></p>
  <p><?php echo Active_language; ?> <b><?php echo $site->activeLanguage(); ?></b></p>
  <div class="editiontitle"><?php echo Activated_modules; ?></div>
  <?php
  $query = 'select id 
  from int_admin
  where active=1
  order by corder asc';
  $result = $site->getDatalink()->dbQuery($query, 'result');
  foreach ($result as $row){
    $moduled = new Module( $site->getDatalink(), $row[0] );
    echo '<div class="editionitem">
    <form name="module'.$row[0].'" method="post" action="">
    <input type="hidden" name="module" value="'.$row[0].'">
    <div class="field">';
    if( file_exists( 'admin/'.$moduled->getFolder().'/icon.png' ) ){
      echo '<img src="'.$site->getBaseroot().'admin/'.$moduled->getFolder().'/icon.png">';
    }
    echo '</div>
    <div class="field">
    '.Module_name.'<br>
    <input type="text" name="name" value="'.$moduled->getName().'">
    </div>';
    if ($moduled->getId() > 3){  //The 3 first modules are part of the system installation
      echo '    <div class="field">
      '.Module_url.'<br>
      <input type="text" name="url" value="'.$moduled->getUrl().'">
      </div>
      <div class="field">
      <input type="checkbox" name="shortcut" value="1"'; if ($moduled->getShortcut()){ echo ' checked'; } echo '>
      '.Module_shortcut.'<br>
      <input type="checkbox" name="active" value="1"'; if ($moduled->getActive()){ echo ' checked'; } echo '>
      '.Module_active.'
      </div>';
    }
    echo '<div class="field">
    <input type="submit" name="savemodule" value="'.Save_module.'">
    <input type="submit" name="moveup" value="'.Move_up.'">
    <input type="submit" name="movedown" value="'.Move_down.'"></div>
    </form>
    </div>';
  }
  ?>
  <div class="editiontitle"><?php echo Installed_modules; ?></div>
  <?php
  $query = 'select id 
  from int_admin
  where active=0
  order by id asc';
  $result = $site->getDatalink()->dbQuery($query, 'result');
  foreach ($result as $row){
    $moduled = new Module($site->getDatalink(), $row[0]);
    echo '<div class="editionitem">
    <form name="module'.$row[0].'" method="post" action="">
    <input type="hidden" name="module" value="'.$row[0].'">
    <input type="hidden" name="modulef" value="'.$moduled->getFolder().'">
    <div class="field">';
    if( file_exists( 'admin/'.$moduled->getFolder().'/icon.png' ) ){
      echo '<img src="'.$site->getBaseroot().'admin/'.$moduled->getFolder().'/icon.png" style="float:left;height:2.5em">';
    }
    echo '</div>
    <div class="field">'.Module_name.'<br>
    <input type="text" name="name" value="'.$moduled->getName().'">
    </div>
    <div class="field">
    '.Module_url.'<br>
    <input type="text" name="url" value="'.$moduled->getUrl().'">
    </div>
    <div class="field">
    <input type="checkbox" name="shortcut" value="1"'; if ($moduled->getShortcut()){ echo ' checked'; } echo '>
    '.Module_shortcut.'<br>
    <input type="checkbox" name="active" value="1"'; if ($moduled->getActive()){ echo ' checked'; } echo '>
    '.Module_active.'
    </div>
    <div class="field">
    <input type="submit" name="savemodule" value="'.Save_module.'">
    <input type="submit" name="deletemodule" value="'.Uninstall_module.'">
    </div>
    </form>
    </div>';
  }
  ?>
  <form name="uploadmodule" method="post" enctype="multipart/form-data" action="">
    <p class="pinput"><b><?php echo New_module.'</b><br>'.Upload_zip_module_files; ?><br>
    <input type="file" name="zip"></p>
    <p><input type="submit" name="uploadmodule" value="<?php echo Upload_module_file; ?>"></p>
  </form>
</fieldset>