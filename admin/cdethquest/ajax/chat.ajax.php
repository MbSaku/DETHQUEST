<?php
if( $_GET['character'] == 0 ){
  $character = new PlayerCharacter( $site->getDatalink(), $dethuser->getCharacter() );
}else{
  $character = new PlayerCharacter( $site->getDatalink(), $_GET['character'] );
}
$json = Array();
$json['name'] = $character->getName();
$json['chat'] = '';
$result = $site->getDatalink()->dbQuery( 'select id from '.mod.'deth_chat 
where scenario="'.$_GET['scenario'].'" and faction="'.$_GET['faction'].'"
order by instant desc limit 25', 'result' );
foreach( $result as $row ){
  $message = new Message( $site->getDatalink(), $row[0] );
  $json['chat'] .= $message->render( $module->getFolder() );
}
echo json_encode( $json );
?>