<?php
$varname = str_replace(Array ('.', '-'), '', 'cmsverif'.$_SERVER['SERVER_NAME']);
if (isset ($_POST['verifok'])){
  $_SESSION[$varname] = true;
?>
  <script>
  if(typeof(Storage) !== "undefined") {
    if (!localStorage.<?php echo $varname; ?>){
      localStorage.<?php echo $varname; ?> = true;
    }
  }
  </script>
<?php
}
if (!isset ($_SESSION[$varname])){  //No hemos aceptado las cookies en esta sesión.
?>
<div id="cmsverif">
<div id="cmsverifpop">
<p><?php echo VerifpopText; ?></p>
<form name="verifaccept" method="post" action="">
<input type="submit" name="verifok" value="<?php echo VerifpopOk; ?>">
</form>
</div>
</div>
<script>
if(typeof(Storage) !== "undefined") {
  if (localStorage.<?php echo $varname; ?>){
    document.getElementById ('cmsverif').innerHTML = '';
  }
}
</script>
<?php
}
?>
