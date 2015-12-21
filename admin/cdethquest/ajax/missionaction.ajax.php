<?php
$scenario = new Scenario( $site->getDatalink(), $_POST['scenario'] );
$map = new Map( $site->getDatalink(), $scenario->getMap() );
$character = new PlayerCharacter( $site->getDatalink(), $_POST['character'] );
$isacting = ( $character->getUser() == $dethuser->getUser() and $character->isPlaying() == $scenario->getId() and $character->isActive() );
switch( $_POST['action'] ){
  case 'endturn':
    $scenario->runTurns();
  break;
  case 'passturn':
    if( $scenario->elapsedTime() > $scenario->getTurntime() ){
      $scenario->runTurns();
    }
  break;
  case 'escape':
    $character->flee();
    $message = new Message( $site->getDatalink() );
    $message->send( 0, $scenario->getId(), 0, $character->getName().' '.fled );
    if( $scenario->numPlayers() == 0 
    and $scenario->numTargets() <= 0 ){
      $scenario->delete();
    }
    echo '<script>backend.link("game","game");</script>';
  break;
  case 'move':
    if( $isacting 
    and $scenario->isEmpty( $_POST['x2'] , $_POST['y2'] ) ){
      $character->moveTo( $_POST['x1'], $_POST['y1'], $_POST['x2'], $_POST['y2'] );
    }
  break;
  case 'door':
    $pos = $character->getCoordinates();
    if( $isacting
    and $character->getActions() >= 1 
    and $scenario->calculateDistance( $map, $pos[0], $pos[1], $_POST['x'], $_POST['y'], false ) <= 2 
    and $scenario->isEmpty( $_POST['x'], $_POST['y'] ) ){
      $level = $scenario->getMatrix( 'level' );
      if( $level[$_POST['y']][$_POST['x']] != 2 ){
        $level[$_POST['y']][$_POST['x']] = 2;
      }else{
        $level[$_POST['y']][$_POST['x']] = 0;
      }
      $scenario->updateLogic( $map, $level );
      $character->spendAction( 1 );
    }
  break;
}
if( $isacting and $character->getActions() <= 0 ){
  $scenario->runTurns();
}
?>