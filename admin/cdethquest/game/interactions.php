<?php
if( isset( $_POST['operation'] ) ){
  $player = new PlayerCharacter( $site->getDatalink(), $dethuser->getCharacter() );
  if( ( $player->isPlaying() and $player->spendAction( 1 ) )
  or !$player->isPlaying() ){
    switch( $_POST['operation'] ){
      case 'unequip':
        echo $player->unequip( $_POST['invrow'] );
      break;
      case 'equip':
        echo $player->equip( $_POST['invrow'] );
      break;
      case 'reload':
        echo $player->reload( $_POST['invrow'] );
      break;
      case 'heal':
        echo $player->heal( $_POST['invrow'] );
      break;
      case 'repair':
        echo $player->repair( $_POST['invrow'] );
      break;
    }
  }
  if( $player->isActive() and $player->getActions() == 0 ){
    $scenario = new Scenario( $site->getDatalink(), $player->isPlaying() );
    $scenario->runTurns();
  }
}
?>