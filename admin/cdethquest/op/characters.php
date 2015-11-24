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
  <p><?php echo HelpCharacters; ?></p>
<?php
if (!isset($_POST['character'])){
?>
  <form name="usersearch" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <p><?php echo Search_by_name; ?><br>
    <input type="text" name="filter" value="<?php echo $filter; ?>">
    <input type="submit" value="<?php echo Search; ?>"></p>
  </form>
  <div class="editiontitle"><?php echo PCs; ?></div>
  <?php
  $query = 'select id from '.mod.'deth_characters where name like "%'.$filter.'%" and pc="1" order by name asc';
  $rows = $site->getDatalink()->dbQuery($query, 'rows');
  $numpags = ceil ($rows / $regspag);
  $result = $site->getDatalink()->dbQuery( $query, 'result', ( $regspag * $pag ) );
  $i = 0;
  if ($rows > 0){
    foreach ($result as $row){
      if ($i < $regspag){
        $character = new PlayerCharacter( $site->getDatalink(), $row[0] );
        $race = new Race($site->getDatalink(), $character->getRace() );
        $class = new CharacterClass( $site->getDatalink(), $character->getClass() );
        echo '<form name="char'.$character->getId().'" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
        <div class="editionitem">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="hidden" name="character" value="'.$character->getId().'">
        <div class="field"><b>'.$character->getName().'</b><br>'.$race->getName().' '.$class->getName().'</div>
        <div class="field">
          <div class="charportrait">
            <div class="inner">'.$character->renderHtml( $module->getFolder(), true ).'</div>
          </div>
        </div>
        <div class="field">';
        $result2 = $site->getDatalink()->dbQuery( 'select user from '.mod.'deth_user where playercharacter="'.$character->getId().'"', 'result' );
        if ( isset( $result2[0] ) ){
          $player = new User( $site->getDatalink(), $result2[0][0]);
          echo Player_name.': <b>'.$player->getName().'</b><br>
          '.Email.': <b>'.$player->getEmail().'</b>';
        }
        echo '</div>
        <div class="field"><input type="submit" value="'.Edit.'"></div>
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
  $character = new PlayerCharacter( $site->getDatalink(), $_POST['character'] );
  if( isset( $_POST['name'] )
  and isset( $_POST['health'] )
  and isset( $_POST['level'] )
  and isset( $_POST['coins'] ) ){
    $character->setName( $_POST['name'] );
    $character->setHealth( $_POST['health'] );
    $character->setCoins( $_POST['coins'] );
    if (isset($_POST['delete'])){
      echo $character->delete();
    }else{
      echo $character->save();
    }
  }
  if( isset( $_POST['operation'] ) 
  and isset( $_POST['invrow'] ) ){
    switch( $_POST['operation'] ){
      case 'unequip':
        echo $character->unequip( $_POST['invrow'] );
      break;
      case 'equip':
        echo $character->equip( $_POST['invrow'] );
      break;
      case 'reload':
        echo $character->reload( $_POST['invrow'] );
      break;
    }
  }
  $race = new Race( $site->getDatalink(), $character->getRace() );
  $class = new CharacterClass( $site->getDatalink(), $character->getClass() );
?>
  <form name="addnew" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
    <input type="hidden" name="pag" value="<?php echo $pag; ?>">
    <p><input type="submit" value="<?php echo Back_to_characters; ?>"></p>
  </form>
  <div class="charactersheet">
    <div class="stats">
      <p><?php 
        echo '<span class="desc">'.$character->getName().'</span><br>
        <b>'.$race->getName().' '.$class->getName().'</b>';
      ?></p>
      <?php
      $result = $site->getDatalink()->dbQuery( 'select user from '.mod.'deth_user where playercharacter="'.$character->getId().'"', 'result' );
      if ( isset( $result[0] ) ){
        $player = new User( $site->getDatalink(), $result[0][0]);
        echo '<p>'.Player_name.': <b>'.$player->getName().'</b><br>
        '.Email.': <b>'.$player->getEmail().'</b></p>';
      }
      ?>
      <p><?php 
        echo Level.' <span class="out">'.$character->getLevel().'</span><br>
        '.Experience.': <b>'.$character->getExperience().' / '.$character->expNextLevel().'</b>'; 
      ?></p>
      <p><?php 
        echo Wealth.': <span class="out">'.number_format($character->getCoins(), 0, ',', '.').Coins.'</span>'; 
      ?></p>
      <div class="charstats">
        <div class="stat">
          <div class="name"><?php echo Health; ?></div>
          <div class="value"><?php echo $character->getHealth().' / '.$character->getMaxhealth(); ?></div>
        </div>
        <div class="stat">
          <div class="name"><?php echo Speed; ?></div>
          <div class="value"><?php echo $character->getSpeed(); ?></div>
        </div>
        <div class="stat">
          <div class="name"><?php echo Strength; ?></div>
          <div class="value"><?php echo $character->getStrength(); ?></div>
        </div>
        <div class="stat">
          <div class="name"><?php echo Dexterity; ?></div>
          <div class="value"><?php echo $character->getDexterity(); ?></div>
        </div>
        <div class="stat">
          <div class="name"><?php echo Constitution; ?></div>
          <div class="value"><?php echo $character->getConstitution(); ?></div>
        </div>
        <div class="stat">
          <div class="name"><?php echo Intelligence; ?></div>
          <div class="value"><?php echo $character->getIntelligence(); ?></div>
        </div>
      </div>
    </div>
    <div class="character">
      <?php echo $character->renderHtml( $module->getFolder() ); ?>
    </div>
    <div class="equipsheet">      
      <form name="equip" method="post" action="" onsubmit="event.preventDefault();backend.post(this, false);">
      <input type="hidden" name="filter" value="<?php echo $filter; ?>">
      <input type="hidden" name="pag" value="<?php echo $pag; ?>">
      <input type="hidden" name="character" value="<?php echo $character->getId(); ?>">
      <input type="hidden" name="operation" value="equip">
      <p><?php echo Hands.': <b>'.$character->remainingHands().'</b>'; ?><br>
      <?php 
      echo Equip.': <select name="invrow">
      <option value="0">'.Select_item.'</option>';
      $result = $site->getDatalink()->dbQuery( 'select id, item, type, value, max 
      from '.mod.'deth_character_item 
      where playercharacter="'.$character->getId().'"
      and (type="weapon" or type="armor" or type="equipment")
      and equipped=0
      order by type asc' , 'result' );
      foreach ($result as $row){
        $info = '';
        switch( $row[2] ){
          case 'armor':
          default:
            $item = new Armor( $site->getDatalink(), $row[1] );
            $info = $row[3].'/'.$item->getHitpoints();
          break;
          case 'weapon':
            $item = new Weapon( $site->getDatalink(), $row[1] );
            if( $item->getClipsize() > 0 ){
              $info = $row[3].'/'.$item->getClipsize().' ('.$row[4].')';
            }
          break;
          case 'equipment':
            $item = new Equipment( $site->getDatalink(), $row[1] );
          break;
        }
        echo '<option value="'.$row[0].'"><b>'.$item->getName().'</b> '.$info.'</option>';
      }
      echo '</select> <input type="submit" value="'.Equip.'">';
      ?>
      </p>
      </form>
      <?php
      echo '<div class="equip-title">'.Weapons.'</div>';
      $weapons = $character->getEquipment( 'weapon' );
      foreach( $weapons as $weapondata ){
        $weapon = new Weapon( $site->getDatalink(), $weapondata[1] );
        echo '<div class="equip-row">
        <div class="equip-block name">';
        if( $weapon->getIcon() != '' ){
          echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$weapon->getIcon().'" title="'.$weapon->getDescription().'">';
        }
        echo '</div>
        <div class="equip-block">
        <b>'.$weapon->getName().'</b>';
        if( $weapon->getClipsize() > 0 ){
          echo '<br>'.Ammo.': <b>'.$weapondata[2].'</b> / '.$weapon->getClipsize().'<br>'.Clips.': <b>'.$weapondata[3].'</b>';
        }
        echo '</div>
        <div class="equip-block">';
        if( $weapon->getClipsize() > 0 and $weapondata[2] < $weapon->getClipsize() and $weapondata[3] > 0 ){
          echo '<form name="reload'.$weapondata[0].'" method="post" action="" onsubmit="event.preventDefault();backend.post(this,false);">
          <input type="hidden" name="filter" value="'.$filter.'">
          <input type="hidden" name="pag" value="'.$pag.'">
          <input type="hidden" name="character" value="'.$character->getId().'">
          <input type="hidden" name="operation" value="reload">
          <input type="hidden" name="invrow" value="'.$weapondata[0].'">
          <input type="submit" value="'.Reload.'">
          </form>';
        }
        echo '<form name="unequip'.$weapondata[0].'" method="post" action="" onsubmit="event.preventDefault();backend.post(this,false);">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="character" value="'.$character->getId().'">
        <input type="hidden" name="operation" value="unequip">
        <input type="hidden" name="invrow" value="'.$weapondata[0].'">
        <input type="submit" value="'.Unequip.'">
        </form>
        </div>
        </div>';
      }
      echo '<div class="equip-title">'.Armor.'</div>';
      $armors = $character->getEquipment( 'armor' );
      foreach( $armors as $armordata ){
        $armor = new Armor( $site->getDatalink(), $armordata[1] );
        echo '<div class="equip-row">
        <div class="equip-block name">';
        if( $armor->getIcon() != '' ){
          echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$armor->getIcon().'" title="'.$armor->getDescription().'">';
        }
        echo '</div>
        <div class="equip-block">
        <b>'.$armor->getName().'</b>
        <br>'.Hitpoints.': <b>'.$armordata[2].'</b> / '.$armor->getHitpoints().'<br>'.Protection.': <b>'.$armor->getProtection().'</b>
        </div>
        <div class="equip-block">
        <form name="unequip'.$armordata[0].'" method="post" action="" onsubmit="event.preventDefault();backend.post(this,false);">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="character" value="'.$character->getId().'">
        <input type="hidden" name="operation" value="unequip">
        <input type="hidden" name="invrow" value="'.$armordata[0].'">
        <input type="submit" value="'.Unequip.'">
        </form>
        </div>
        </div>';
      }
      echo '<div class="equip-title">'.Equipment.'</div>';
      $equipment = $character->getEquipment( 'equipment' );
      foreach( $equipment as $equipmentdata ){
        $item = new Equipment( $site->getDatalink(), $equipmentdata[1] );
        echo '<div class="equip-row">
        <div class="equip-block name">';
        if( $item->getIcon() != '' ){
          echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$item->getIcon().'" title="'.$item->getDescription().'">';
        }
        echo '</div>
        <div class="equip-block">
        <b>'.$item->getName().'</b>';
        echo '</div>
        <div class="equip-block">
        <form name="unequip'.$equipmentdata[0].'" method="post" action="" onsubmit="event.preventDefault();backend.post(this,false);">
        <input type="hidden" name="filter" value="'.$filter.'">
        <input type="hidden" name="pag" value="'.$pag.'">
        <input type="hidden" name="character" value="'.$character->getId().'">
        <input type="hidden" name="operation" value="unequip">
        <input type="hidden" name="invrow" value="'.$equipmentdata[0].'">
        <input type="submit" value="'.Unequip.'">
        </form>
        </div>
        </div>';
      }
      ?>
    </div>
    <div class="equipsheet">
      <?php
      echo '<h1>'.Inventory.'</h1>';
      $result = $site->getDatalink()->dbQuery( 'select item, type, equipped, value, max from '.mod.'deth_character_item 
      where playercharacter="'.$character->getId().'" and equipped=0' , 'result' );
      foreach ($result as $row){
        $value = '';
        switch($row[1]){
          case 'weapon':
            $item = new Weapon( $site->getDatalink(), $row[0] );
            if( $item->getClipsize() > 0 ){
              $value = Ammo.': <b>'.$row[3].'</b> / '.$item->getClipsize().'<br>'.Clips.': <b>'.$row[4].'</b>';
            }
          break;
          case 'armor':
            $item = new Armor( $site->getDatalink(), $row[0] );
            $value = Hitpoints.': <b>'.$row[3].'</b> / '.$item->getHitpoints().'<br>'.Protection.': <b>'.$item->getProtection().'</b>';
          break;
          case 'equipment':
          default:
            $item = new Equipment( $site->getDatalink(), $row[0] );
        }
        echo '<div class="editionitem">
        <div class="field">
        <b>'.$item->getName().'</b><br>
        '.$value.'</div>
        </div>';
      }
      ?>
    </div>
  </div>
  <form name="editchar" onsubmit="event.preventDefault(); backend.post(this);" method="post" action="">
  <input type="hidden" name="filter" value="<?php echo $filter; ?>">
  <input type="hidden" name="pag" value="<?php echo $pag; ?>">
  <input type="hidden" name="character" value="<?php echo $character->getId(); ?>">
  <p class="pinput"><?php echo Character_name; ?>:<br>
  <input type="text" name="name" value="<?php echo $character->getName(); ?>"></p>
  <p class="pinput"><?php echo Health; ?>:<br>
  <input type="number" style="width:6em" name="health" value="<?php echo $character->getHealth(); ?>"></p>
  <p class="pinput"><?php echo Level; ?>:<br>
  <input type="number" style="width:6em" name="level" value="<?php echo $character->getLevel(); ?>"></p>
  <p class="pinput"><?php echo Wealth; ?>:<br>
  <input type="number" style="width:6em" name="coins" value="<?php echo $character->getCoins(); ?>"></p>
  <p><input type="submit" value="<?php echo Save_character; ?>">
  <input type="checkbox" name="delete" value="1"><?php echo Delete_character; ?></p>
  </form>
<?php
}
?>