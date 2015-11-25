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
<fieldset><legend><?php echo $module->getName().' - '.Edition; ?></legend>
  <p><?php echo Content_help; ?></p>
  <p><?php echo Active_language; ?> <b><?php echo $site->activeLanguage(); ?></b></p>
<?php
if (!isset($_GET['content'])){
  if (isset($_POST['mcontent'])){
    $mcont = new Content($site->getDatalink(), $_POST['mcontent']);
    if (isset($_POST['moveup'])){
      $mcont->moveUp();
    }
    if (isset($_POST['movedown'])){
      $mcont->moveDown();
    }
  }
?>
  <form name="usersearch" method="post" action="">
    <p class="pinput"><?php echo Url_filter; ?><br>
    <input type="text" name="filter" value="<?php echo $filter; ?>">
    <input type="submit" value="<?php echo Filter_content; ?>"></p>
  </form>
  <p class="pinput"><a href="<?php echo $basiclink.'&filter='.$filter.'&pag='.$pag.'&content=0'; ?>"><input type="button" value="<?php echo Add_new_content; ?>"></a></p>
  <div class="editiontitle"><?php echo Contents; ?></div>
  <?php
  $query = 'select id from int_content where url like "%'.$filter.'%" order by corder asc';
  $numpags = ceil ($site->getDatalink()->dbQuery($query, 'rows') / $regspag);
  $result = $site->getDatalink()->dbQuery($query, 'result', ($regspag * $pag));
  $i = 0;
  foreach ($result as $row){
    if ($i < $regspag){
      $content = new Content($site->getDatalink(), $row[0]);
      echo '<div class="editionitem">
      <div class="field">';
      if ($content->getFather() != 0){
        $dad = new Content($site->getDatalink(), $content->getFather());
        echo $dad->getTitle().' / ';
      }
      echo '<b>'.$content->getTitle().'</b></div>
      <div class="field">'.$content->getUrl().'</div>
      <div class="controls">
      <form name="movecontent'.$content->getId().'" method="post" action="">
      <input type="hidden" name="mcontent" value="'.$content->getId().'">
      <a href="'.$basiclink.'&op=edition&filter='.$filter.'&pag='.$pag.'&content='.$content->getUrl().'">
      <input type="button" value="'.Edit_content.'"></a>
      <input type="submit" name="moveup" value="'.Move_up.'">
      <input type="submit" name="movedown" value="'.Move_down.'">
      </form>
      </div>
      </div>';
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
  $content = new Content($site->getDatalink(), $_GET['content']);
  if (isset($_POST['title'])
  and isset($_POST['url'])
  and isset($_POST['father'])
  and isset($_POST['code'])
  and isset($_POST['savecontent'])){
    $content->setTitle($_POST['title']);
    $content->setUrl($_POST['url']);
    $content->setFather($_POST['father']);
    $content->setCode($_POST['code']);
    if (isset($_POST['menu'])){
      $content->setMenu(1);
    }else{
      $content->setMenu(0);
    }
    echo $content->save();
  }
  if (isset($_POST['deletecontent'])
  or isset($_POST['confirmdeletion'])){
    if (isset($_POST['confirmdeletion'])){
      echo $content->delete();
    }else{
      echo '<div class="error">
      <form name="confirmation" method="post" action="">
      <p>'.You_will_delete_this_content_are_you_sure.'</p>
      <p><input type="submit" name="confirmdeletion" value="'.Yes_delete_content.'">
      <input type="submit" name="cancel" value="'.No_delete_content.'"></p>
      </form>
      </div>';
    }
  }
?>
  <h2><?php echo Editing_content.' '.$content->getTitle(); ?></h2>
  <p><a href="<?php echo $basiclink.'&filter='.$filter.'&pag='.$pag; ?>"><input type="button" value="<?php echo Back_to_contents; ?>"></a></p>
  <form name="content" method="post" action="<?php echo $basiclink.'&filter='.$filter.'&pag='.$pag.'&content='.$content->getUrl(); ?>">
    <p class="pinput"><?php echo Content_title; ?><br>
    <input type="text" name="title" value="<?php echo $content->getTitle(); ?>"></p>
    <p class="pinput"><?php echo Content_url; ?><br>
    <input type="text" name="url" value="<?php echo $content->getUrl(); ?>"></p>
    <p class="pinput"><?php echo Content_father; ?><br>
    <select name="father"><option value="0"><?php echo No_content_father; ?></option>
    <?php
    $query = 'select id from int_content where id!='.$content->getId().' and father=0';
    $result = $site->getDatalink()->dbQuery($query, 'result');
    foreach ($result as $row){
      $dad = new Content($site->getDatalink(), $row[0]);
      echo '<option value="'.$dad->getId().'"'; 
      if ($content->getFather() == $dad->getId()){
         echo ' selected'; 
      } 
      echo '>'.$dad->getTitle().'</option>';
    }
    ?>
    </select></p>
    <p><input type="checkbox" name="menu" value="true"
    <?php 
    if ($content->getMenu()){
      echo ' checked';
    }   
    echo '>'.Menu_appearance; 
    ?><br></p>
    <p><?php echo Content_code; ?><br>
    <textarea name="code" class="tceditor"><?php echo $content->getCode(); ?></textarea></p>
    <p><input type="submit" name="savecontent" value="<?php echo Content_save; ?>"></p>
    <?php
    if ($content->getId() > 0){
      echo '<p><input type="submit" name="deletecontent" value="'.Content_delete.'"></p>';
    }
    ?>
  </form>
<?php
}
?>
</fieldset>
<script type="text/javascript">
tinymce.init({
  selector: "textarea.tceditor",
  plugins: [ "code link table image textcolor"],
  menu: "false",
  height : 400,
    image_list: [<?php
$imgs = $site->dirList('uploads/content/');
$i = 0;
foreach ($imgs as $k => $v){
  echo "
      {title: '".$v."', value: '".$site->getBaseroot()."uploads/content/".$v."'}";
  $i++;
  if ($i < count ($imgs)){ echo ','; }
}
?>

    ],
  toolbar: "styleselect table | undo redo code | alignleft aligncenter alignright alignjustify bullist numlist outdent indent forecolor | image link unlink bold italic underline strikethrough subscript superscript selectall removeformat"
});
</script>