<?php
if (isset($_POST['sitename'])
and isset($_POST['manintenancetext'])
and isset($_POST['code'])
and isset($_POST['savesettings'])){
  $errors = '';
  $site->setTitle($_POST['sitename']);
  $site->setFooter($_POST['code']);
  $site->setMaintenancetext($_POST['manintenancetext']);
  if (isset($_POST['freereg'])){
    $site->setFreereg(1);
  }else{
    $site->setFreereg(0);
  }
  if (isset($_POST['maintenance'])){
    $site->setMaintenance(1);
  }else{
    $site->setMaintenance(0);
  }
  $errors .= $site->saveConfiguration();
  $metas = Array ();
  foreach ($_POST as $key => $value){
    $bits = explode('-', $key);
    if (count($bits) == 2
    and $bits[0] == 'meta'){
      if (isset($_POST['metaname'.$bits[1]])
      and isset($_POST['metadesc'.$bits[1]])){
        $metas[$_POST['metaname'.$bits[1]]] = $_POST['metadesc'.$bits[1]];
      }
    }
  }
  $errors .= $site->saveMetas($metas);
  if ($errors != ''){
    echo '<p class="error">'.$errors.'</p>';
  }else{
    echo '<p class="fine">'.Site_configuration_saved.'</p>';
  }
}
?>
<fieldset><legend><?php echo $module->getName().' - '.General; ?></legend>
  <form name="general" method="post" action="">
    
    <p><?php echo General_configuration_help; ?></p>
    
    <p class="pinput"><?php echo Site_name; ?><br>
    <input type="text" name="sitename" value="<?php echo $site->getTitle(); ?>"></p>
    
    <div class="editiontitle"><?php echo Metatags; ?></div>
    <div id="metatags">
    <?php
      $metas = $site->getMetas();
      $i = 0;
      foreach ($metas as $name => $value){
        echo '
        <div class="editionitem"><input type="hidden" name="meta-'.$i.'" value="true">
        <div class="field"><input type="text" name="metaname'.$i.'" value="'.$name.'"></div>
        <div class="field"><input type="text" name="metadesc'.$i.'" value="'.$value.'"></div>
        </div>';
        $i++;
      }
    ?>
    </div>
    <p><input type="button" name="addmeta" value="<?php echo Metatagadd; ?>" onclick="javascript:addMetas(tagmany)"></p>
    
    <p class="pinputwide"><?php echo Maintenance_text; ?><br>
    <textarea name="manintenancetext"><?php echo $site->getMaintenancetext(); ?></textarea></p>
    
    <p class="pinput"><input type="checkbox" name="maintenance" value="true" <?php if ($site->getMaintenance()){ echo ' checked'; } ?>>
    <?php echo Maintenance_mode; ?></p>
    
    <p class="pinputwide"><?php echo Site_footer; ?><br>
    <textarea name="code" class="tmcebasic"><?php echo $site->getFooter(); ?></textarea></p>
    
    <p class="pinput"><input type="checkbox" name="freereg" value="true" <?php if ($site->getFreereg()){ echo ' checked'; } ?>>
    <?php echo Allow_free_registry; ?></p>
    
    <p><input type="submit" name="savesettings" value="<?php echo Save_settings; ?>"></p>
    
  </form>
</fieldset>
<script type="text/javascript">
var tagmany = <?php echo count($metas); ?>;

function addMetas(tagindex){
  if (tagmany < 10) {
    var existingmetas = document.getElementById("metatags").innerHTML;
    var newmeta = '<div class="editionitem"><input type="hidden" name="meta-' + tagmany + '" value="true">';
    newmeta += '<div class="field"><input type="text" name="metaname' + tagmany + '" placeholder="<?php echo Metatagname; ?>"></div>';
    newmeta += '<div class="field"><input type="text" name="metadesc' + tagmany + '" placeholder="<?php echo Metatagdesc; ?>"></div>';
    newmeta += '</div>';
    document.getElementById("metatags").innerHTML = existingmetas + newmeta;
    tagmany++;
  }
}

tinymce.init({
  selector: "textarea.tmcebasic",
  plugins: [ "code link table"],
  menu: "false",
  toolbar: "styleselect | undo redo code | alignleft aligncenter alignright alignjustify | link unlink bold italic underline strikethrough subscript superscript selectall removeformat"
});

</script>
