<?php
$race = new Race( $site->getDatalink(), $_GET['race'] );
$class = new CharacterClass( $site->getDatalink(), $_GET['charclass'] );
$json = Array();
switch( $_GET['gender'] ){
  case 'male':
  default:
    if ($race->getMaleVariant( $_GET['body'], 'body' ) != ''){
      $imgbody = $_GET['root'].'uploads/'.$module->getFolder().'/'.$race->getMaleVariant( $_GET['body'], 'body' );
    }else{
      $imgbody = $_GET['root'].'admin/'.$module->getFolder().'/css/images/generic-male.png';
    }
    if ($race->getMaleVariant( $_GET['hair'], 'hair' ) != ''){
      $imghair = $_GET['root'].'uploads/'.$module->getFolder().'/'.$race->getMaleVariant( $_GET['hair'], 'hair' );
    }else{
      $imghair = $_GET['root'].'admin/'.$module->getFolder().'/css/images/transparent.png';
    }
    if ($race->getMaleVariant( $_GET['head'], 'head' ) != ''){
      $imghead = $_GET['root'].'uploads/'.$module->getFolder().'/'.$race->getMaleVariant( $_GET['head'], 'head' );
    }else{
      $imghead = $_GET['root'].'admin/'.$module->getFolder().'/css/images/transparent.png';
    }
    if ($race->getMaleVariant( $_GET['face'], 'face' ) != ''){
      $imgface = $_GET['root'].'uploads/'.$module->getFolder().'/'.$race->getMaleVariant( $_GET['face'], 'face' );
    }else{
      $imgface = $_GET['root'].'admin/'.$module->getFolder().'/css/images/transparent.png';
    }
  break;
  case 'female':
    if ($race->getFemaleVariant( $_GET['body'], 'body' ) != ''){
      $imgbody = $_GET['root'].'uploads/'.$module->getFolder().'/'.$race->getFemaleVariant( $_GET['body'], 'body' );
    }else{
      $imgbody = $_GET['root'].'admin/'.$module->getFolder().'/css/images/generic-female.png';
    }
    if ($race->getFemaleVariant( $_GET['hair'], 'hair' ) != ''){
      $imghair = $_GET['root'].'uploads/'.$module->getFolder().'/'.$race->getFemaleVariant( $_GET['hair'], 'hair' );
    }else{
      $imghair = $_GET['root'].'admin/'.$module->getFolder().'/css/images/transparent.png';
    }
    if ($race->getFemaleVariant( $_GET['head'], 'head' ) != ''){
      $imghead = $_GET['root'].'uploads/'.$module->getFolder().'/'.$race->getFemaleVariant( $_GET['head'], 'head' );
    }else{
      $imghead = $_GET['root'].'admin/'.$module->getFolder().'/css/images/transparent.png';
    }
    if ($race->getFemaleVariant( $_GET['face'], 'face' ) != ''){
      $imgface = $_GET['root'].'uploads/'.$module->getFolder().'/'.$race->getFemaleVariant( $_GET['face'], 'face' );
    }else{
      $imgface = $_GET['root'].'admin/'.$module->getFolder().'/css/images/transparent.png';
    }
  break;
}
$charwidth = 90 * $race->getXscale();
$charheight = 90 * $race->getYscale();
$charpwidth = 100;
$charpheight = 100;
$charhands = 0;
$charhtml = '';
$porthtml = '';
$charlayers['2handed'] = '';
$portlayers['2handed'] = '';
$charlayers['body'] = "<div class='charlayer' style='background-image:url(".$imgbody.");background-size:".$charwidth."% ".$charheight."%'>          
<div class='charlayer' style='background-image:url(".$imgface.");background-size:".$charwidth."% ".$charheight."%'>
<div class='charlayer' style='background-image:url(".$imghead.");background-size:".$charwidth."% ".$charheight."%'>";
$portlayers['body'] = "<div class='charlayer' style='background-image:url(".$imgbody.");background-size:".$charpwidth."% ".$charpheight."%'>          
<div class='charlayer' style='background-image:url(".$imgface.");background-size:".$charpwidth."% ".$charpheight."%'>
<div class='charlayer' style='background-image:url(".$imghead.");background-size:".$charpwidth."% ".$charpheight."%'>";
$charlayers['hair'] = "<div class='charlayer' style='background-image:url(".$imghair.");background-size:".$charwidth."% ".$charheight."%'>";
$portlayers['hair'] = "<div class='charlayer' style='background-image:url(".$imghair.");background-size:".$charpwidth."% ".$charpheight."%'>";
$charlayers['equipment'] = '';
$portlayers['equipment'] = '';
$charlayers['armor'] = '';
$portlayers['armor'] = '';
$charlayers['1handed'] = '';
$portlayers['1handed'] = '';
$charlayers['insignia'] = '';
$portlayers['insignia'] = '';
$closedivs = 1;
$types = Array("equipment", "weapon", "armor");
foreach ($types as $type){
  $result = $site->getDatalink()->dbQuery( 'select item from '.mod.'deth_class_items where type="'.$type.'" and class="'.$class->getId().'"', 'result' );
  foreach ($result as $row){
    switch($type){
      case 'equipment':
        $item = new Equipment( $site->getDatalink(), $row[0] );
      break;
      case 'weapon':
        $item = new Weapon( $site->getDatalink(), $row[0] );
      break;
      case 'armor':
        $item = new Armor( $site->getDatalink(), $row[0] );
        if( $item->getHelmet() ){
          $charlayers['hair'] = '';
          $portlayers['hair'] = '';
          $closedivs--;
        }
      break;
    }
    $image = '';
    switch($_GET['gender']){
      case 'male':
      default:
        if ($item->getMaleimage() != ''){
          $image = $_GET['root'].'uploads/'.$module->getFolder().'/'.$item->getMaleimage();
        }
      break;
      case 'female':
        if ($item->getFemaleimage() != ''){
          $image = $_GET['root'].'uploads/'.$module->getFolder().'/'.$item->getFemaleimage();
        }
      break;
    }
    if($image != ''){
      if ($type == 'weapon'){
        if ($item->getHands() < 2){
          if ($charhands == 1){
            $charlayers['1handed'] .= "<div class='charlayer flip' style='background-image:url(".$image.");background-size:".$charwidth."% ".$charheight."%'>";
            $portlayers['1handed'] .= "<div class='charlayer flip' style='background-image:url(".$image.");background-size:".$charpwidth."% ".$charpheight."%'>";
          }else{
            $charlayers['1handed'] .= "<div class='charlayer' style='background-image:url(".$image.");background-size:".$charwidth."% ".$charheight."%'>";
            $portlayers['1handed'] .= "<div class='charlayer' style='background-image:url(".$image.");background-size:".$charpwidth."% ".$charpheight."%'>";
          }
        }else{
          $charlayers['2handed'] .= "<div class='charlayer' style='background-image:url(".$image.");background-size:".$charwidth."% ".$charheight."%'>";
          $portlayers['2handed'] .= "<div class='charlayer' style='background-image:url(".$image.");background-size:".$charpwidth."% ".$charpheight."%'>";
        }
        $charhands = $charhands + $item->getHands();
      }else{
        $charlayers[$type] .= "<div class='charlayer' style='background-image:url(".$image.");background-size:".$charwidth."% ".$charheight."%'>";
        $portlayers[$type] .= "<div class='charlayer' style='background-image:url(".$image.");background-size:".$charpwidth."% ".$charpheight."%'>";
      }
      $closedivs++;
    }
  }
}
$charhtml .= $charlayers['2handed'].$charlayers['body'].$charlayers['equipment'].$charlayers['armor'].$charlayers['hair'].$charlayers['1handed'].$charlayers['insignia'];
$porthtml .= $portlayers['2handed'].$portlayers['body'].$portlayers['equipment'].$portlayers['armor'].$portlayers['hair'].$portlayers['1handed'].$portlayers['insignia'];
for ($a = 0; $a < $closedivs; $a++){
  $charhtml .= "</div>";
  $porthtml .= "</div>";
}
$json['charhtml'] = $charhtml;
$json['porthtml'] = $porthtml;
$json['chardesc'] = '<p class="highlight">'.$race->getName().' '.$class->getName().'</p>';
$json['racedesc'] = '<p>'.nl2br($race->getDescription()).'</p>';
$json['classdesc'] = '<p>'.nl2br($class->getDescription()).'</p>';          
$json['stats']['health']['name'] = Health;
$json['stats']['health']['value'] = $class->getHealth() *  (1 + ($race->getModHealth() / 100));
$json['stats']['speed']['name'] = Speed;
$json['stats']['speed']['value'] = $class->getSpeed() *  (1 + ($race->getModSpeed() / 100));
$json['stats']['strength']['name'] = Strength;
$json['stats']['strength']['value'] = $class->getStrength() * (1 + ($race->getModStrength() / 100));
$json['stats']['dexterity']['name'] = Dexterity;
$json['stats']['dexterity']['value'] = $class->getDexterity() * (1 + ($race->getModDexterity() / 100));
$json['stats']['constitution']['name'] = Constitution;
$json['stats']['constitution']['value'] = $class->getConstitution() * (1 + ($race->getModConstitution() / 100));
$json['stats']['intelligence']['name'] = Intelligence;
$json['stats']['intelligence']['value'] = $class->getIntelligence() * (1 + ($race->getModIntelligence() / 100));
$json['appearance'] = $race->appearanceMatrix();
echo json_encode($json);
?>