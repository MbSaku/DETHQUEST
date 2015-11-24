<?php
if( $dethuser->getCharacter() == 0 ){
  echo '<script>backend.link("character","character");</script>';
}else{
  $character = new PlayerCharacter( $site->getDatalink(), $dethuser->getCharacter() );
  echo '<h1>'.Inventory.'</h1>';
  $result = $site->getDatalink()->dbQuery( 'select item, type, equipped, value, max from '.mod.'deth_character_item 
  where playercharacter="'.$character->getId().'"' , 'result' );
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
    <div class="field">';
    if ($item->getIcon() != ''){
      echo '<img src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$item->getIcon().'">';
    }
    echo '</div>
    <div class="field">
    <b>'.$item->getName().'</b><br>
    '.$value.'</div>
    </div>';
  }
}
?>