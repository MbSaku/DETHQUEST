<?php
if( $_POST['character'] == 0 ){
  $character = new PlayerCharacter( $site->getDatalink(), $dethuser->getCharacter() );
}else{
  $character = new PlayerCharacter( $site->getDatalink(), $_POST['character'] );
}
$message = new Message( $site->getDatalink() );
$message->send( $character->getId(), $_POST['scenario'], $_POST['faction'], $_POST['message'] );
?>