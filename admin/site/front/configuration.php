<?php
if (isset($_POST['savesettings'])
and isset($_POST['sitename'])
and isset($_POST['code'])
and isset($_POST['manintenancetext'])
and isset($_POST['username'])
and isset($_POST['email'])
and isset($_POST['password'])){
  $errors = '';
  $site->setTitle($_POST['sitename']);
  $site->setFooter($_POST['code']);
  $site->setMaintenancetext($_POST['manintenancetext']);
  if ($_POST['username'] == ''){
    $errors .= Admin_username_empty.'<br>';
  }
  if ($_POST['email'] == ''){
    $errors .= Admin_email_empty.'<br>';
  }
  if ($_POST['password'] == ''){
    $errors .= Admin_password_empty.'<br>';
  }
  if ($errors != ''){
    echo '<p class="error">'.$errors.'</p>';
  }else{
    echo $site->saveConfiguration();
    $admin = new User($site->getDatalink());
    $errormsg = $admin->register($_POST['username'], 1, $_POST['email'], $_POST['password']);
    if ($errormsg != ''){
      echo $errormsg;
    }else{
      $admin->validate();
      $admin->login($_POST['username'], $_POST['password'], $site);
      echo '<script>location.replace("'.$site->getBaselink().'/login");</script>';
    }
  }
}
?>
<form name="julius-config" method="post" action="">
  <fieldset><legend><?php echo $site->getTitle(); ?></legend>
    
    <p><?php echo Here_you_can_configure_the_basics_of_your_website; ?></p>
    
    <p class="pinput"><?php echo Site_name; ?><br>
    <input type="text" name="sitename" value="<?php echo $site->getTitle(); ?>"></p>
    
    <p class="pinputwide"><?php echo Site_footer; ?><br>
    <textarea name="code" class="tmcebasic"><?php echo $site->getFooter(); ?></textarea></p>
    
    <p class="pinputwide"><?php echo Maintenance_text; ?><br>
    <textarea name="manintenancetext"><?php echo $site->getMaintenancetext(); ?></textarea></p>
    
    <p class="pinput"><?php echo Administration_name; ?><br>
    <input type="text" name="username" value=""></p>
    
    <p class="pinput"><?php echo Administration_email; ?><br>
    <input type="email" name="email" value=""></p>
    
    <p class="pinput"><?php echo Administration_password; ?><br>
    <input type="text" name="password" value=""></p>
        
    <p><input type="submit" name="savesettings" value="<?php echo Save_settings_and_proceed; ?>"></p>
    
  </fieldset>
</form>

<script type="text/javascript">
  
tinymce.init({
  selector: "textarea.tmcebasic",
  plugins: [ "code link table"],
  menu: "false",
  toolbar: "styleselect | undo redo code | alignleft aligncenter alignright alignjustify | link unlink bold italic underline strikethrough subscript superscript selectall removeformat"
});
  
</script>
