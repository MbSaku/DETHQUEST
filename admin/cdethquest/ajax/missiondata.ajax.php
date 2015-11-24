<?php
$scenario = new Scenario( $site->getDatalink(), $_GET['scenario'] );
$map = new Map( $site->getDatalink(), $scenario->getMap() );
$character = new PlayerCharacter( $site->getDatalink(), $_GET['character'] );
$level = $map->getMatrix( 'level' );
$json = Array();
$json['doorslog'] = $scenario->getMatrix( 'level' );
$json['view'] = $character->getFow();
for( $y = 0; $y < $map->getHeight(); $y++ ){
  for( $x = 0; $x < $map->getWidth(); $x++ ){
    $json['movement'][$y][$x] = 0;
    if( !isset( $json['view'][$y][$x] ) ){
      $json['view'][$y][$x] = 0;
    }
  }
}
$coords = $character->getCoordinates();
$json['character']['x'] = $coords[0];
$json['character']['y'] = $coords[1];
$json['character']['portrait'] = $character->renderHtml( $module->getFolder(), true );
$json['character']['bars'] = $character->renderBars();
$json['character']['overview'] = '<p><span class="desc">'.$character->getName().'</span><br>
'.Level.' <span class="out">'.$character->getLevel().'</span><br>
'.Experience.': <b>'.$character->getExperience().' / '.$character->expNextLevel().'</b></p>
<div class="xpbar"><div class="bar">
<div class="bar-grey" style="width:'.( ( $character->getExperience() / $character->expNextLevel() ) * 100 ).'%"></div>
</div></div>';
for( $y = 0; $y < $map->getHeight(); $y++ ){
  for( $x = 0; $x < $map->getWidth(); $x++ ){
    if( $level[$y][$x] < 2 ){
      $distance = $scenario->calculateDistance( $map, $coords[0], $coords[1], $x, $y, false );
      if( $distance < 1000 ){
        $json['view'][$y][$x] = 2;
      }
    }
  }
}
$json['entities'] = Array();
$result = $site->getDatalink()->dbQuery( 'select coordy, coordx, type, entity from '.mod.'deth_scenario_entity 
where scenario="'.$scenario->getId().'" order by coordy asc, coordx asc', 'result' );
foreach( $result as $row ){
  if( isset( $json['view'][$row[0]][$row[1]] ) 
  and $json['view'][$row[0]][$row[1]] == 2 ){
    switch( $row[2] ){
      case 'char':
        $char = new PlayerCharacter( $site->getDatalink(), $row[3] );
        $json['entities'][$row[0]][$row[1]] = $char->renderHtml( $module->getFolder() );
      break;
    }
  }
}
$remtime = $scenario->getTurntime() - $scenario->elapsedTime();
if( $remtime < 0 ){
  $remtime = 0;
}
$mins = ceil( $remtime / 60 );
$hours = '0'.floor ( $mins / 60 );
if( $mins < 10 ){
  $mins = '0'.$mins;
}
if( $character->isActive() ){
  $json['character']['active'] = true;
  $json['actions'] = $character->getActions();
  $json['actionform'] = ' <input type="button" value="'.End_turn.'" onclick="mission.perform('."'endturn'".')">';
  $movement = $character->calculateMovement();
  $maximum = $movement * $json['actions'];
  for( $a = -$maximum; $a <= $maximum; $a++ ){
    for( $b = -$maximum; $b <= $maximum; $b++ ){
      $x = $coords[0] + $b;
      $y = $coords[1] + $a;
      if( $x < 0 ){ $x = 0; }
      if( $x >= $map->getWidth() ){ $x = $map->getWidth() - 1; }
      if( $y < 0 ){ $y = 0; }
      if( $y >= $map->getHeight() ){ $y = $map->getHeight() - 1; }
      $distance = $scenario->calculateDistance( $map, $coords[0], $coords[1], $x, $y );
      if( isset( $json['movement'][$y][$x] ) 
      and $distance <= $maximum
      and $json['doorslog'][$y][$x] == 0 ){
        $json['movement'][$y][$x] = ceil( $distance / $movement );
      }
    }
  }
  if( $scenario->numPlayers() == 1 ){
    $json['actionform'] .= ' <input type="button" value="'.Escape.'" onclick="mission.perform('."'escape'".')">';
  }
  $json['actionform'] .= '<p>'.Actions.': <span class="turnaction">'.$json['actions'].'</span><br>
  '.Remaining_time.': <b>'.$hours.':'.$mins.'</b></p>';
  $character->updateFow( $json['view'] );
}else{
  $json['character']['active'] = false;
  $json['actionform'] = '<input type="button" value="'.Escape.'" onclick="mission.perform('."'escape'".')">';
  $activechar = new PlayerCharacter( $site->getDatalink(), $scenario->getActualPlayer() );
  $user = new User( $site->getDatalink(), $activechar->getUser() );
  if( $scenario->elapsedTime() > $scenario->getTurntime() ){
    $json['actionform'] .= ' <input type="button" value="'.PassTurn.'" onclick="mission.perform('."'passturn'".')">';
  }
  $json['actionform'] .= '<p>'.Current_turn.': <b>'.$activechar->getName().'</b><br>
  '.Remaining_time.': <b>'.$hours.':'.$mins.'</b></p>';
}
echo json_encode( $json );
?>