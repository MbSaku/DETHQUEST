<?php
$scenario = new Scenario( $site->getDatalink(), $_GET['scenario'] );
$map = new Map( $site->getDatalink(), $scenario->getMap() );
$json = Array();
$json['level'] = $map->getMatrix( 'level' );
$json['graph'] = $map->getMatrix( 'graph' );
$json['weather'] = $map->getMatrix( 'weather' );
$json['doors'] = $map->getMatrix( 'doors' );
$json['sprites'] = $map->getMatrix( 'sprites' );
$json['textures'][0] = $_GET['root'].'admin/'.$module->getFolder().'/css/images/square.png';
$result = $site->getDatalink()->dbQuery( 'select id, image from '.mod.'deth_squares 
where place="'.$map->getPlace().'" order by id asc', 'result' );
foreach( $result as $row ){
  $json['textures'][$row[0]] = $_GET['root'].'uploads/'.$module->getFolder().'/'.$row[1];
}
echo json_encode( $json );
?>