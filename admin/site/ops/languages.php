<?php
if (isset($_POST['reference'])
and isset($_POST['name'])
and isset($_POST['savelang'])){
  $errors = $site->saveLang($_POST['reference'], $_POST['name']);
  if ($errors != 0){
    echo '<p class="error">'.$errors.'</p>';
  }else{
    echo '<p class="fine">'.Language_saved.'</p>';
  }
  if (isset($_POST['forced'])){
    $site->forceLanguage($_POST['reference']);
  }else{
    $site->forceLanguage();
  }
}
if (isset($_POST['reference'])
and isset($_POST['deletelang'])){
  $errors = $site->deleteLang($_POST['reference']);
  if ($errors != ''){
    echo '<p class="error">'.$errors.'</p>';
  }else{
    echo '<p class="fine">'.Language_deleted.'</p>';
  }
}
?>
<fieldset><legend><?php echo $module->getName().' - '.Languages; ?></legend>
  <p><?php echo Language_configuration_help; ?></p>
  <div class="editiontitle"><?php echo Available_languages; ?></div>
  <div class="editionitem">
  <form name="addlang" method="post" action="">
  <div class="field"><input type="text" name="reference" placeholder="<?php echo Language_reference; ?>" style="width:4em;"></div>
  <div class="field"><input type="text" name="name" placeholder="<?php echo Language_name; ?>" value=""></div>
  <div class="field"><input type="checkbox" name="forced" value="1"> <?php echo Force_display; ?></div>
  <div class="field"><input type="submit" name="savelang" value="<?php echo Add_new_language; ?>"></div>
  </form>
  </div>
  <?php
  $query = 'select lang, name, forced
  from int_lang
  order by name asc';
  $result = $site->getDatalink()->dbQuery($query, 'result');
  foreach ($result as $language){
    echo '<div class="editionitem">
    <form name="editlang'.$language[0].'" method="post" action="">
    <input type="hidden" name="reference" value="'.$language[0].'">
    <div class="field"><b>'.$language[0].'</b></div>
    <div class="field"><input type="text" name="name" value="'.$language[1].'"></div>
    <div class="field"><input type="checkbox" name="forced" value="1"';
    if ($language[0] == $site->getForcedLang()){
      echo ' checked';
    }
    echo '> '.Force_display.'</div>
    <div class="field">
    <input type="submit" name="savelang" value="'.Save_language.'">';
    if (count($result) > 1){
      echo ' <input type="submit" name="deletelang" value="'.Delete_language.'">';
    }
    echo '</div>
    </form>
    </div>';
  }
  ?>
</fieldset>