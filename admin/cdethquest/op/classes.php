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
?>
  <p><?php echo HelpClasses; ?></p>
  <p><?php echo Active_language; ?> <b><?php echo $site->activeLanguage(); ?></b></p>
<?php
if (!isset($_POST['class'])){
?>
  <form name="usersearch" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <p class="pinput"><?php echo Search_by_name; ?><br>
    <input type="text" name="filter" value="<?php echo $filter; ?>">
    <input type="submit" value="<?php echo Search; ?>"></p>
  </form>
  <form name="addnew" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <input type="hidden" name="class" value="0">
    <p class="pinput"><input type="submit" value="<?php echo Add_class; ?>"></p>
  </form>
  <div class="editiontitle"><?php echo Classes; ?></div>
  <?php
  $query = 'select id from '.mod.'deth_classes 
  where name like "%'.$filter.'%" 
  order by playable desc, name asc';
  $rows = $site->getDatalink()->dbQuery($query, 'rows');
  $numpags = ceil ($rows / $regspag);
  $result = $site->getDatalink()->dbQuery($query, 'result', ($regspag * $pag));
  $i = 0;
  if ($rows > 0){
    foreach ($result as $row){
      if ($i < $regspag){
        $class = new CharacterClass($site->getDatalink(), $row[0]);
        echo '<form name="race'.$class->getId().'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
        <div class="editionitem">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="hidden" name="class" value="'.$class->getId().'">
        <div class="field"><input type="submit" value="'.$class->getName().'"></div>
        <div class="field">';
        if ($class->getIcon() != ''){
          echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$class->getIcon().'">';
        }
        echo '</div>
        <div class="field">'.Class_power.': <b>'.$class->calculatePower().'</b><br>
        '.Equipment_worth.': <b>'.$class->calculateWorth().Coins.'</b></div>
        <div class="field">'.Class_playable.': <b>'; 
        if ($class->getPlayable()){ echo Yes_playable; }else{ echo Not_playable; } 
        echo '</b></div>
        </div>
        </form>';
      }
      $i++;
    }
    echo '<div class="pags">';
    $dots = false;
    for ($a = 0; $a < $numpags; $a++){
      if ($a > $numpags - 2
      or $a < 3
      or ($a > $pag - 5 and $a < $pag + 5)){
        echo '<form name="pag'.$a.'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
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
  }else{
    echo '<p class="error">'.No_data_to_show.'</p>';
  }
}else{
  $class = new CharacterClass($site->getDatalink(), $_POST['class']);
  if (isset($_POST['name'])
  and isset($_POST['description'])
  and isset($_POST['health'])
  and isset($_POST['speed'])
  and isset($_POST['strength'])
  and isset($_POST['dexterity'])
  and isset($_POST['constitution'])
  and isset($_POST['intelligence'])){
    $class->setName($_POST['name']);
    $class->setDescription($_POST['description']);
    $class->setHealth($_POST['health']);
    $class->setSpeed($_POST['speed']);
    $class->setStrength($_POST['strength']);
    $class->setDexterity($_POST['dexterity']);
    $class->setConstitution($_POST['constitution']);
    $class->setIntelligence($_POST['intelligence']);
    if (isset($_FILES['icon'])){
      $class->setIcon($site->imageUpload($_FILES['icon'], $module->getFolder()));
    }
    if (isset($_POST['playable'])){
      $class->setPlayable(true);
    }else{
      $class->setPlayable(false);
    }
    if (isset($_POST['delete'])){
      echo $class->delete();
    }else{
      echo $class->save();
    }
  }
  if (isset($_POST['deleteicon'])){
    if(file_exists($site->getRoot().'uploads/'.$module->getFolder().'/'.$class->getIcon())){
      unlink($site->getRoot().'uploads/'.$module->getFolder().'/'.$class->getIcon());
    }
    $class->setIcon('');
    $class->save();
  }
  if (isset($_POST['setequipment'])){
    $class->removeItems();
    foreach ($_POST as $key => $value){
      $data = explode('-', $key);
      if(count($data) == 2){
        $class->addItem($data[0], $data[1], 1);
      }
    }
  }
  ?>
  <form name="addnew" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <p><input type="submit" value="<?php echo Back_to_classes; ?>"></p>
  </form>
  <h1><?php echo Editing_class.' '.$class->getName(); ?></h1>
  <form name="editrace" onsubmit="event.preventDefault(); backend.post(this);" enctype="multipart/form-data" method="post" action="">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <input type="hidden" name="class" value="<?php echo $class->getId(); ?>">
    <div class="edblock">
      <p class="pinput"><?php echo Class_name; ?><br>
      <input type="text" name="name" value="<?php echo $class->getName(); ?>"></p>
      <p class="pinput">
      <input type="checkbox" name="playable" value="1"<?php if ($class->getPlayable()){ echo ' checked'; } ?>> <?php echo Class_playable; ?></p>
      <p><?php echo Class_description; ?><br>
      <textarea name="description"><?php echo $class->getDescription(); ?></textarea></p>
    </div>
    <div class="edblock">
      <h2><?php echo Base_stats; ?></h2>
      <p><?php echo HelpClassStats; ?></p>
      <p><?php echo Class_power; ?>: <span id="classpower"><?php echo $class->calculatePower(); ?></span></p>
      <table>
        <tr>
          <th>
            <?php echo Health; ?>
          </th>
          <td>
            <input type="number" name="health" id="health" value="<?php echo $class->getHealth(); ?>" onkeyup="calcClassPower()">
          </td>
        </tr>
        <tr>
          <th>
            <?php echo Speed; ?>
          </th>
          <td>
            <input type="number" name="speed" id="speed" value="<?php echo $class->getSpeed(); ?>" onkeyup="calcClassPower()">
          </td>
        </tr>
        <tr>
          <th>
            <?php echo Strength; ?>
          </th>
          <td>
            <input type="number" name="strength" id="strength" value="<?php echo $class->getStrength(); ?>" onkeyup="calcClassPower()">
          </td>
        </tr>
        <tr>
          <th>
            <?php echo Dexterity; ?>
          </th>
          <td>
            <input type="number" name="dexterity" id="dexterity" value="<?php echo $class->getDexterity(); ?>" onkeyup="calcClassPower()">
          </td>
        </tr>
        <tr>
          <th>
            <?php echo Constitution; ?>
          </th>
          <td>
            <input type="number" name="constitution" id="constitution" value="<?php echo $class->getConstitution(); ?>" onkeyup="calcClassPower()">
          </td>
        </tr>
        <tr>
          <th>
            <?php echo Intelligence; ?>
          </th>
          <td>
            <input type="number" name="intelligence" id="intelligence" value="<?php echo $class->getIntelligence(); ?>" onkeyup="calcClassPower()">
          </td>
        </tr>
      </table>
    </div>
    <p><input type="submit" value="<?php echo Save_class; ?>">
    <input type="checkbox" name="delete" value="1"><?php echo Delete_class; ?></p>
    <?php 
    if ($class->getIcon() == ''){
      echo '<div class="formimg">'.Class_image.'<br>
      <input type="file" name="icon"></div>';
    }
    ?>
  </form>
  <?php
  if ($class->getIcon() != ''){
    echo '<form name="icon" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="'.$filter.'">
    <input type="hidden" name="pag" value="'.$pag.'">
    <input type="hidden" name="class" value="'.$class->getId().'">
    <input type="hidden" name="deleteicon" value="1">
    <div class="formimg">
    <img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$class->getIcon().'"><br>
    <input type="submit" value="'.Delete_picture.'">
    </div>
    </form>';
  }
  if($class->getId() == 0){
    echo '<p>'.Save_class_to_manage_starting_equipment.'</p>';
  }else{
    $types = Array( "equipment", "armor" );
    echo '<form name="equipment" onsubmit="event.preventDefault(); backend.post(this);" enctype="multipart/form-data" method="post" action="">
    <p>'.Equipment_worth.': <span id="classworth">'.$class->calculateWorth().Coins.'</span> / 1000</p>
    <input type="hidden" name="pag" value="'.$pag.'">
    <input type="hidden" name="filter" value="'.$filter.'">
    <input type="hidden" name="class" value="'.$class->getId().'">
    <input type="hidden" name="setequipment" value="true">';
    foreach( $types as $type ){
      echo '<div class="pinput">
      <div class="editiontitle">'.ucfirst( $type ).'</div>';
      $result = $site->getDatalink()->dbQuery( 'select id from '.mod.'deth_item_'.$type.' order by price asc', 'result' );
      foreach( $result as $row ){
        switch( $type ){
          case 'equipment':
            $item = new Equipment( $site->getDatalink(), $row[0] );
          break;
          case 'armor':
            $item = new Armor( $site->getDatalink(), $row[0] );
          break;
        }
        echo '<div class="editionitem">
        <div class="field"><input type="checkbox" name="'.$type.'-'.$item->getId().'" class="equipment"';
        if( $class->hasItem( $type, $item->getId() ) ){
          echo ' checked';
        }
        echo ' value="'.$item->getPrice().'">'.$item->getName().' <b>'.$item->getPrice().Coins.'</b><br>';
        if( $item->getIcon() != '' ){
          echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$item->getIcon().'"><br>';
        }
        echo $item->getPremium().Premium_coins.'</div>
        </div>';
      }
      echo '</div>
      </div>';
    }
    $result = $site->getDatalink()->dbQuery( 'select id, type from '.mod.'deth_item_weapon order by type asc, price asc, name asc', 'result' );
    $type = '';
    echo '<div class="pinput">';
    foreach( $result as $row ){
      if( $row[1] != $type ){
        if( $type != '' ){
          echo '</div>';
        }
        $type = $row[1];
        echo '<div class="pinput">
        <div class="editiontitle">'.ucfirst( $type ).'</div>';
      }
      $item = new Weapon( $site->getDatalink(), $row[0] );
      echo '<div class="editionitem">
      <div class="field"><input type="checkbox" name="weapon-'.$item->getId().'" class="equipment"';
      if($class->hasItem( 'weapon' , $item->getId() ) ){
        echo ' checked';
      }
      echo ' value="'.$item->getPrice().'">'.$item->getName().' ('.$item->getHands().' '.Item_hands.') <b>'.$item->getPrice().Coins.'</b><br>';
      if( $item->getIcon() != '' ){
        echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$item->getIcon().'"><br>';
      }
      echo $item->getPremium().Premium_coins.'</div>
      </div>';
    }
    echo '</div>
    <p><input type="submit" value="'.Save_equipment.'">
    </form>';
  }
  ?>
  <script>
  
  function calcClassPower(){
    var power = parseInt($("#health").val()) 
    + parseInt($("#speed").val()) 
    + parseInt($("#strength").val()) 
    + parseInt($("#dexterity").val()) 
    + parseInt($("#constitution").val()) 
    + parseInt($("#intelligence").val());
    $("#classpower").html(power);
  };
  
  var worth = <?php echo $class->calculateWorth(); ?>;
  var inputs = document.getElementsByClassName('equipment');
  for (var i=0; i < inputs.length; i++) {
    inputs[i].onchange = function() {
      var add = this.value * (this.checked ? 1 : -1);
      worth = worth + add;
      $("#classworth").html(worth + "<?php echo Coins; ?>");
    }
  }
  </script>
  <?php
}
?>