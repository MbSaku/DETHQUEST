<?php
$scenario = new Scenario( $site->getDatalink(), $_GET['scenario'] );
$map = new Map( $site->getDatalink(), $scenario->getMap() );
$character = new PlayerCharacter( $site->getDatalink(), $_GET['character'] );
switch( $_GET['type'] ){
  case 'door':
    $doors = $map->getMatrix( 'doors' );
    $level = $scenario->getMatrix( 'level' );
    $square = new Square( $site->getDatalink(), $doors[$_GET['y']][$_GET['x']] );
    echo '<h2>'.Door.'</h2>
    <div align="center">
    <img class="interaction-image" src="'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$square->getTexture().'"><br>';
    $pos = $character->getCoordinates();
    if( $character->getActions() >= 1 
    and $scenario->calculateDistance( $map, $pos[0], $pos[1], $_GET['x'], $_GET['y'], false ) <= 2 
    and $scenario->isEmpty( $_GET['x'], $_GET['y'] ) ){
      if( $level[$_GET['y']][$_GET['x']] != 2 ){
        echo '<input type="button" value="'.Close_door.'" onclick="mission.hideInteractions();mission.doorAction('.$_GET['x'].','.$_GET['y'].');">';
      }else{
        echo '<input type="button" value="'.Open_door.'" onclick="mission.hideInteractions();mission.doorAction('.$_GET['x'].','.$_GET['y'].');">';
      }
      echo '<br><span class="turnaction">-1</span>';
    }
    echo '</div>';
  break;
}
?>