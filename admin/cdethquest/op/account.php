<?php
if (isset($_POST['email'])
and isset($_POST['emailchange'])){
  $user->setEmail($_POST['email']);
  echo $user->save();
}
if (isset($_POST['password'])
and isset($_POST['passwordchange'])){
  echo $user->passwordChange($_POST['password']);
}
?>

<div class="pinput">
  <form name="emailchange" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <div class="editiontitle"><?php echo Email_change_help; ?></div>
    <p class="pinput"><?php echo User_email; ?>:<br>
    <input type="email" name="email" value="<?php echo $user->getEmail(); ?>"></p>
    <p><input type="submit" name="emailchange" value="<?php echo Save_email; ?>"></p>
  </form>
</div>

<div class="pinput">
  <form name="passchange" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <div class="editiontitle"><?php echo Password_change_help; ?></div>
    <p class="pinput"><?php echo New_password; ?>:<br>
    <input type="password" name="password" value=""></p>
    <p><input type="submit" name="passwordchange" value="<?php echo Save_password; ?>"></p>
  </form>
</div>

<form name="logout" method="post" action="<?php echo $_GET['root'].'index.php/'.$module->getUrl(); ?>">
  <p><input type="submit" name="logout" value="<?php echo Log_out; ?>"></p>
</form>