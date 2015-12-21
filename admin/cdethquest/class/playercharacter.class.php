<?php
class PlayerCharacter extends DatabaseObject {
  
  protected $id = 0;
  protected $name = '';
  protected $class = 0;
  protected $race = 0;
  protected $body = 0;
  protected $head = 0;
  protected $hair = 0;
  protected $face = 0;
  protected $gender = 'male';
  protected $level = 1;
  protected $pc = false;
  protected $health = 0;
  protected $maxhealth = 1;
  protected $speed = 0;
  protected $strength = 0;
  protected $dexterity = 0;
  protected $constitution = 0;
  protected $intelligence = 0;
  protected $coins = 0;
  protected $premium = 0;
  protected $experience = 0;
  protected $kills = 0;
  protected $deaths = 0;
  
/*
 * CONSTRUCTOR
 */
 
  public function __construct( $datalink, $id = 0 ) {
    parent::__construct( $datalink, mod.'deth_characters', get_object_vars( $this ), $id );
  }
  
/*
 * GETTERS
 */
 
  public function getId(){
    return $this->id;
  }
  
  public function getName(){
    return $this->name;
  }
  public function setName( $string ){
    $this->name = $string;
  }
  
  public function getClass(){
    return $this->class;
  }
  
  public function getRace(){
    return $this->race;
  }
  
  public function getGender(){
    return $this->gender;
  }
  
  public function getBody(){
    return $this->body;
  }
  
  public function getHead(){
    return $this->head;
  }
  
  public function getHair(){
    return $this->hair;
  }
  
  public function getFace(){
    return $this->face;
  }
  
  public function getLevel(){
    return $this->level;
  }
  
  public function getHealth(){
    return $this->health;
  }
  public function setHealth( $integer ){
    if( $integer < 0 ){
      $integer = 0; 
    }
    if( $integer > $this->maxhealth ){
      $integer = $this->maxhealth;
    } 
    $this->health = $integer;
  }
  
  public function getMaxhealth(){
    return $this->maxhealth;
  }
  
  public function getSpeed(){
    return $this->speed;
  }
  
  public function getStrength(){
    return $this->strength;
  }
  
  public function getDexterity(){
    return $this->dexterity;
  }
  
  public function getConstitution(){
    return $this->constitution;
  }
  
  public function getIntelligence(){
    return $this->intelligence;
  }
  
  public function getCoins(){
    return $this->coins;
  }
  public function setCoins( $integer ){
    $this->coins = $integer;
  }
  
  public function getPremium(){
    return $this->premium;
  }
  public function setPremium( $integer ){
    $this->premium = $integer;
  }
  
  public function getExperience(){
    return $this->experience;
  }
  
  public function getKills(){
    return $this->kills;
  }
  
  public function getDeaths(){
    return $this->deaths;
  }
  
/*
 * PUBLIC
 */
  
  public function getUser(){
    $result = $this->datalink->dbQuery( 'select user from '.mod.'deth_user where playercharacter="'.$this->id.'"', 'result' );
    if( isset( $result[0] ) ){
      return $result[0][0];
    }else{
      return 0;
    }
  }
 
  public function pcGeneration( $classid, $raceid, $gender, $body, $hair, $head, $face, $name ){
    $class = new CharacterClass( $this->datalink, $classid );
    $race = new Race( $this->datalink, $raceid );
    $doable = true;
    $errors = '';
    if ( $class->isPlayable()
    and $race->isPlayable() ){
      $this->race = $race->getId();
      $this->class = $class->getId();
    }else{
      $errors .= Class_or_race_not_playable.'<br>';
      $doable = false;
    }
    if ( $name == 'Dovahkiin' ){
      $errors .= Easter_dovahkiin.'<br>';
      $doable = false;
    }
    if ( $name == 'Shepard' ){
      $errors .= Easter_shepard.'<br>';
      $doable = false;
    }
    if ( $name == '' ){
      $errors .= Name_empty.'<br>';
      $doable = false;
    }
    if ( $gender == 'male' 
    or $gender == 'female' ){
      $this->gender = $gender;
    }else{
      $errors .= Gender_incorrect.'<br>';
      $doable = false;
    }
    if ( $body != 0 ){ $this->body = $body; }else{ $doable = false; }
    if ( $head != 0 ){ $this->head = $head; }else{ $doable = false; }
    if ( $face != 0 ){ $this->face = $face; }else{ $doable = false; }
    if ( $hair != 0 ){ $this->hair = $hair; }else{ $doable = false; }
    if ( $doable ){
      $this->name = $name;
      $this->pc = 1;
      $this->health = round( $class->getHealth() * ( 1 + ($race->getModHealth() / 100) ) );
      $this->maxhealth = $this->health;
      $this->speed = round( $class->getSpeed() * ( 1 + ($race->getModSpeed() / 100) ) );
      $this->strength = round( $class->getStrength() * ( 1 + ($race->getModStrength() / 100) ) );
      $this->dexterity = round( $class->getDexterity() * ( 1 + ($race->getModDexterity() / 100) ) );
      $this->constitution = round( $class->getConstitution() * ( 1 + ($race->getModConstitution() / 100) ) );
      $this->intelligence = round( $class->getIntelligence() * ( 1 + ($race->getModIntelligence() / 100) ) );
      $this->coins = 1000 - $class->calculateWorth();
      if ( $this->coins < 0 ){ $this->coins = 0; }
      $this->save();
      $types = Array( "equipment", "weapon", "armor" );
      foreach ($types as $type){
        $result = $this->datalink->dbQuery('select item from '.mod.'deth_class_items where type="'.$type.'" and class="'.$class->getId().'"', 'result');
        foreach ($result as $row){
          switch($type){
            case 'equipment':
            default:
              $item = new Equipment( $this->datalink, $row[0] );
            break;
            case 'weapon':
              $item = new Weapon( $this->datalink, $row[0] );
            break;
            case 'armor':
              $item = new Armor( $this->datalink, $row[0] );
            break;
          }
          $this->setEquipment( $type, $item );
        }
      }
      return '<p class="fine">'.Character_created.'</p>';
    }else{
      return '<p class="error">'.Some_of_selections_incorrect.'<br>'.$errors.'</p>';
    }
  }
  
  public function save(){
    $message = '';
    if ( parent::save() ){
      $message = '<p class="fine">'.Data_saved.'</p>';
    }
    return $message;
  }
  
  public function delete(){
    $this->datalink->dbQuery( 'update '.mod.'deth_user set 
      playercharacter=0 
      where playercharacter="'.$this->id.'"', 'query' );
    $this->datalink->dbQuery( 'delete from '.mod.'deth_character_item 
      where playercharacter="'.$this->id.'"', 'query' );
    $this->datalink->dbQuery( 'delete from '.mod.'deth_scenario_entity 
      where type="char" and entity="'.$this->id.'"', 'query' );
    $this->datalink->dbQuery( 'delete from '.mod.'deth_game 
      where pchar="'.$this->id.'"', 'query' );
    if ( parent::delete() ){
      $message = '<p class="fine">'.Data_deleted.'</p>';
    }
    return $message;
  }

  public function isPlayer(){
    return $this->pc;
  }

  public function renderHtml( $gamefolder , $portrait = false ){
    $race = new Race( $this->datalink, $this->race );
    if ($portrait){
      $charwidth = 100;
      $charheight = 100;
    }else{
      $charwidth = 90 * $race->getXscale();
      $charheight = 90 * $race->getYscale();
    }
    switch($this->gender){
      case 'male':
      default:
        if ($race->getMaleVariant( $this->body, 'body' ) != ''){
          $imgbody = $_GET['root'].'uploads/'.$gamefolder.'/'.$race->getMaleVariant( $this->body, 'body' );
        }else{
          $imgbody = $_GET['root'].'admin/'.$gamefolder.'/css/images/generic-male.png';
        }
        if ($race->getMaleVariant( $this->hair, 'hair' ) != ''){
          $imghair = $_GET['root'].'uploads/'.$gamefolder.'/'.$race->getMaleVariant( $this->hair, 'hair' );
        }else{
          $imghair = $_GET['root'].'admin/'.$gamefolder.'/css/images/transparent.png';
        }
        if ($race->getMaleVariant( $this->head, 'head' ) != ''){
          $imghead = $_GET['root'].'uploads/'.$gamefolder.'/'.$race->getMaleVariant( $this->head, 'head' );
        }else{
          $imghead = $_GET['root'].'admin/'.$gamefolder.'/css/images/transparent.png';
        }
        if ($race->getMaleVariant( $this->face, 'face' ) != ''){
          $imgface = $_GET['root'].'uploads/'.$gamefolder.'/'.$race->getMaleVariant( $this->face, 'face' );
        }else{
          $imgface = $_GET['root'].'admin/'.$gamefolder.'/css/images/transparent.png';
        }
      break;
      case 'female':
        if ($race->getFemaleVariant( $this->body, 'body' ) != ''){
          $imgbody = $_GET['root'].'uploads/'.$gamefolder.'/'.$race->getFemaleVariant( $this->body, 'body' );
        }else{
          $imgbody = $_GET['root'].'admin/'.$gamefolder.'/css/images/generic-female.png';
        }
        if ($race->getFemaleVariant( $this->hair, 'hair' ) != ''){
          $imghair = $_GET['root'].'uploads/'.$gamefolder.'/'.$race->getFemaleVariant( $this->hair, 'hair' );
        }else{
          $imghair = $_GET['root'].'admin/'.$gamefolder.'/css/images/transparent.png';
        }
        if ($race->getFemaleVariant( $this->head, 'head' ) != ''){
          $imghead = $_GET['root'].'uploads/'.$gamefolder.'/'.$race->getFemaleVariant( $this->head, 'head' );
        }else{
          $imghead = $_GET['root'].'admin/'.$gamefolder.'/css/images/transparent.png';
        }
        if ($race->getFemaleVariant( $this->face, 'face' ) != ''){
          $imgface = $_GET['root'].'uploads/'.$gamefolder.'/'.$race->getFemaleVariant( $this->face, 'face' );
        }else{
          $imgface = $_GET['root'].'admin/'.$gamefolder.'/css/images/transparent.png';
        }
      break;
    }
    $charhands = 0;
    $charhtml = '';
    $charlayers['shadow'] = "<div class='charlayer' style='background-image:url(".$_GET['root'].'admin/'.$gamefolder."/css/images/charshadow.png);background-size:".$charwidth."% ".$charheight."%'>";
    $charlayers['2handed'] = '';
    $charlayers['body'] = "<div class='charlayer' style='background-image:url(".$imgbody.");background-size:".$charwidth."% ".$charheight."%'>
    <div class='charlayer' style='background-image:url(".$imghead.");background-size:".$charwidth."% ".$charheight."%'>
    <div class='charlayer' style='background-image:url(".$imgface.");background-size:".$charwidth."% ".$charheight."%'>";
    $charlayers['hair'] = "<div class='charlayer' style='background-image:url(".$imghair.");background-size:".$charwidth."% ".$charheight."%'>";
    $charlayers['equipment'] = '';
    $charlayers['armor'] = '';
    $charlayers['1handed'] = '';
    $charlayers['insignia'] = '';
    $closedivs = 5;
    if( $this->isWounded() ){
      $charlayers['body'] .= "<div class='charlayer' style='background-image:url(".$_GET['root'].'admin/'.$gamefolder."/css/images/wounds.png);background-size:".$charwidth."% ".$charheight."%'>";
      $closedivs++;
    }
    $types = Array("equipment", "weapon", "armor");
    foreach ($types as $type){
      $result = $this->datalink->dbQuery('select item from '.mod.'deth_character_item 
      where playercharacter="'.$this->id.'" 
      and type="'.$type.'" 
      and equipped="1"', 'result');
      foreach ($result as $row){
        switch($type){
          case 'equipment':
          default:
            $item = new Equipment( $this->datalink, $row[0] );
          break;
          case 'weapon':
            $item = new Weapon( $this->datalink, $row[0] );
          break;
          case 'armor':
            $item = new Armor( $this->datalink, $row[0] );
            if( $item->getHelmet() ){
              $charlayers['hair'] = "";
              $closedivs--;
            }
          break;
        }
        $image = '';
        switch($this->gender){
          case 'male':
          default:
            if ($item->getMaleimage() != ''){
              $image = $_GET['root'].'uploads/'.$gamefolder.'/'.$item->getMaleimage();
            }
          break;
          case 'female':
            if ($item->getFemaleimage() != ''){
              $image = $_GET['root'].'uploads/'.$gamefolder.'/'.$item->getFemaleimage();
            }
          break;
        }
        if($image != ''){
          if ($type == 'weapon'){
            if ($item->getHands() < 2){
              if ($charhands == 1){
                $charlayers['1handed'] .= "<div class='charlayer flip' style='background-image:url(".$image.");background-size:".$charwidth."% ".$charheight."%'>";
              }else{
                $charlayers['1handed'] .= "<div class='charlayer' style='background-image:url(".$image.");background-size:".$charwidth."% ".$charheight."%'>";
              }
            }else{
              $charlayers['2handed'] .= "<div class='charlayer' style='background-image:url(".$image.");background-size:".$charwidth."% ".$charheight."%'>";
            }
            $charhands = $charhands + $item->getHands();
          }else{
            $charlayers[$type] .= "<div class='charlayer' style='background-image:url(".$image.");background-size:".$charwidth."% ".$charheight."%'>";
          }
          $closedivs++;
        }
      }
    }
    $charhtml .= $charlayers['shadow'].$charlayers['2handed'].$charlayers['body'].$charlayers['equipment'].$charlayers['armor'].$charlayers['hair'].$charlayers['1handed'].$charlayers['insignia'];
    for ($a = 0; $a < $closedivs; $a++){
      $charhtml .= "</div>";
    }
    return $charhtml;
  }

  public function renderBars(){
    $barhtml = '';
    if( $this->isWounded() ){
      $barhtml .= '<div class="bar"><div class="bar-red" style="width:'.( ( $this->health / $this->maxhealth ) * 100 ).'%"></div></div>';
    }else{
      if( $this->maxhealth <= 0 ){
        $this->maxhealth = 0.01;
      }
      $barhtml .= '<div class="bar"><div class="bar-green" style="width:'.( ( $this->health / $this->maxhealth ) * 100 ).'%"></div></div>';
    }
    $armordata = $this->getEquipment( 'armor' );
    if( isset( $armordata[0] ) ){
      $armor = new Armor( $this->datalink, $armordata[0][1] );
      $barhtml .= '<div class="bar"><div class="bar-blue" style="width:'.( ( $armordata[0][2] / $armor->getHitpoints() ) * 100 ).'%"></div></div>';
    }
    return $barhtml;
  }

  public function expNextLevel() {
    return ( ( $this->level ) * 100 );
  }

  public function setEquipment ( $type, $item ){
    $errors = '';
    $added = false;
    if ( $item->getId() != 0 ){
      switch($type){
        case 'equipment':
          $added = $this->datalink->dbQuery( 'insert into '.mod.'deth_character_item (
          playercharacter, 
          type, 
          item, 
          equipped,
          value,
          max
          ) values (
          "'.$this->id.'",
          "'.$type.'",
          "'.$item->getId().'",
          "0",
          "1",
          "0"
          )', 'query' );
        break;
        case 'armor':
          $added = $this->datalink->dbQuery( 'insert into '.mod.'deth_character_item (
          playercharacter, 
          type, 
          item, 
          equipped,
          value,
          max
          ) values (
          "'.$this->id.'",
          "'.$type.'",
          "'.$item->getId().'",
          "0",
          "'.$item->getHitpoints().'",
          "0"
          )', 'query' );
        break;
        case 'weapon':
          if( $item->getClipsize() > 0 ){
            $max = 3;
          }else{
            $max = 0;
          }
          $added = $this->datalink->dbQuery( 'insert into '.mod.'deth_character_item (
          playercharacter, 
          type, 
          item, 
          equipped,
          value,
          max
          ) values (
          "'.$this->id.'",
          "'.$type.'",
          "'.$item->getId().'",
          "0",
          "'.$item->getClipsize().'",
          "'.$max.'"
          )', 'query');
        break;
      }
      if( $added ){
        $result = $this->datalink->dbQuery( 'select id from '.mod.'deth_character_item 
        where playercharacter="'.$this->id.'" 
        order by id desc', 'result' );
        if( isset( $result[0] ) ){
          $this->equip( $result[0][0] );
        }
      }
    }else{
      $errors .= Item_not_exists;
    }
    if ( $errors != '' ){
      return '<p class="error">'.$errors.'</p>';
    }else{
      return '<p class="fine">'.Item_equipped.'</p>';
    }
  }

  public function remainingHands() {
    $race = new Race( $this->datalink, $this->race );
    $hands = $race->getHands();
    $result = $this->datalink->dbQuery( 'select '.mod.'deth_item_weapon.hands 
    from '.mod.'deth_character_item, '.mod.'deth_item_weapon
    where '.mod.'deth_character_item.playercharacter="'.$this->id.'"
    and '.mod.'deth_character_item.equipped="1"
    and '.mod.'deth_character_item.type="weapon"
    and '.mod.'deth_character_item.item='.mod.'deth_item_weapon.id', 'result' );
    foreach( $result as $row ){
      $hands = $hands - $row[0];
    }
    return( $hands );
  }

  public function isPlaying(){
    $result = $this->datalink->dbQuery( 'select scenario from '.mod.'deth_game where pchar="'.$this->id.'"', 'result' );
    if( isset( $result[0] ) ){
      return $result[0][0];
    }else{
      return 0;
    }
  }

  public function isActive(){
    $result = $this->datalink->dbQuery( 'select active from '.mod.'deth_game where pchar="'.$this->id.'"', 'result' );
    if( isset( $result[0] ) ){
      return $result[0][0];
    }else{
      return 0;
    }
  }
  
  public function isWounded(){
    $race = new Race( $this->datalink, $this->race );
    return ( $race->getArmor() and $this->health < ( $this->maxhealth / 3 ) );
  }
  
  //Game-related
  
  public function getCoordinates(){
    $coordinates[0] = 0;
    $coordinates[1] = 0;
    $result = $this->datalink->dbQuery( 'select coordy, coordx from '.mod.'deth_scenario_entity 
    where scenario="'.$this->isPlaying().'" and type="char" and entity="'.$this->id.'" limit 1', 'result' );
    if( isset( $result[0] ) ){
      $coordinates[0] = $result[0][1];
      $coordinates[1] = $result[0][0];
    }
    return $coordinates;
  }
  
  public function calculateMovement(){
    return ( floor( $this->speed / 5 ) );
  }
  
  public function calculateActions(){
    if( $this->isWounded() ){
      return 2;
    }else{
      return 3;
    }
  }
  
  public function getActions(){
    $result = $this->datalink->dbQuery( 'select actions from '.mod.'deth_game where pchar="'.$this->id.'"', 'result' );
    if( isset( $result[0] ) ){
      return $result[0][0];
    }else{
      return 0;
    }
  }
  
  public function spendAction( $num ){
   
   if( $this->getActions() > 0 
   and $this->datalink->dbQuery( 'update '.mod.'deth_game set actions="'.( $this->getActions() - $num ).'" where pchar="'.$this->id.'"', 'query' ) > 0 ){
     return true;
   }else{
     return false;
   }
  }
  
  public function moveTo( $x1, $y1, $x2, $y2 ){
    $scenario = new Scenario( $this->datalink, $this->isPlaying() );
    $map = new Map( $this->datalink, $scenario->getMap() );
    $distance = $scenario->calculateDistance( $map, $x1, $y1, $x2, $y2 );
    $actions = $this->getActions();
    $movement = $this->calculateMovement();
    $maxdist = $distance * $actions;
    $req = ceil( $distance / $movement );
    if( $distance <= $maxdist 
    and $req <= $actions ){
      $this->datalink->dbQuery( 'update '.mod.'deth_scenario_entity set coordx="'.$x2.'", coordy="'.$y2.'" 
      where type="char" and entity="'.$this->id.'"', 'query' );
      $this->spendAction( $req );
    }
  }
  
  public function getFow(){
    $mat = Array();
    $result = $this->datalink->dbQuery( 'select fow from '.mod.'deth_game where pchar="'.$this->id.'"', 'result' );
    if( isset( $result[0] ) ){
      $mat = explode( ":", $result[0][0] );
      foreach( $mat as $k => $v ){
        $mat[$k] = explode ('.', $v);
      }
    }
    return $mat;
  }
  
  public function updateFow( $matrix ){
    $fow = '';
    $height = count( $matrix );
    $width = count( $matrix[0] );
    for( $y = 0; $y < $height; $y++ ){
      for( $x = 0; $x < $width; $x++ ){
        if( $matrix[$y][$x] > 0 ){
          $fow .= '1';
        }else{
          $fow .= '0';
        }
        if( $x < ( $width - 1 ) ){ $fow .= '.'; }
      }
      if( $y < ( $height - 1 ) ){ $fow .= ':'; }
    }
    $this->datalink->dbQuery( 'update '.mod.'deth_game set fow="'.$fow.'" where pchar="'.$this->id.'"', 'query' );
  }
  
  public function deploy( $scenarioid, $sendmessage = false ){
    $scenario = new Scenario( $this->datalink, $scenarioid );
    $map = new Map( $this->datalink, $scenario->getMap() );
    $lev = $scenario->getMatrix( 'level' );
    $deployed = false;
    $tries = 0;
    while( !$deployed and $tries < 10 ){
      $x = mt_rand( 0, 9 );
      $y = mt_rand( 0, 9 );
      if( $scenario->isEmpty( $x, $y ) 
      and isset( $lev[$y][$x] ) 
      and $lev[$y][$x] == 0 ){
        $scenario->place( $x, $y, 'char', $this->id );
        $deployed = true;
      }
      $tries++;
    }
    if( $deployed ){
      if( $sendmessage ){
        $message = new Message( $this->datalink );
        $message->send( 0, $scenario->getId(), 0, $this->name.' '.arrived_at.' '.$map->getName() );
      }
      $scenario->addPlayer( $this->id );
    }
  }
  
  public function flee(){
    $this->datalink->dbQuery( 'delete from '.mod.'deth_scenario_entity where type="char" and entity="'.$this->id.'"', 'query' );
    $this->datalink->dbQuery( 'delete from '.mod.'deth_game where pchar="'.$this->id.'"', 'query' );
  }
  
  //Inventory related
  
  public function getEquipment( $type ){
    $equipment = Array();
    $result = $this->datalink->dbQuery( 'select id, item, value, max from '.mod.'deth_character_item 
    where type="'.$type.'" and equipped=1 and playercharacter="'.$this->id.'"', 'result' );
    foreach( $result as $row ){
      $equipment[] = $row;
    }
    return( $equipment );
  }

  public function getInventory( $type ){
    $inventory = Array();
    $result = $this->datalink->dbQuery( 'select id from '.mod.'deth_character_item 
    where type="'.$type.'" and equipped=0 and playercharacter="'.$this->id.'"', 'result' );
    foreach( $result as $row ){
      $inventory[] = $row[0];
    }
    return( $inventory );
  }
  
  public function equip( $inventoryid ){
    $message = '';
    $doable = false;
    $result = $this->datalink->dbQuery( 'select item, type from '.mod.'deth_character_item
    where playercharacter="'.$this->id.'"
    and equipped="0"
    and id="'.$inventoryid.'"
    limit 1', 'result' );
    if( isset( $result[0] ) ){
      switch( $result[0][1] ){
        case 'weapon':
          $weapon = new Weapon( $this->datalink, $result[0][0] );
          if ( $weapon->getHands() <= $this->remainingHands() ){
            $doable = true;
          }else{
            $message = '<p class="error">'.No_room_to_equip_weapon.'</p>';
          }
        break;
        case 'armor':
          $race = new Race( $this->datalink, $this->race );
          if( $race->getArmor() ){
            $result = $this->datalink->dbQuery( 'select id from '.mod.'deth_character_item
            where playercharacter="'.$this->id.'"
            and equipped="1"
            and type="armor"
            limit 1', 'result' );
            if( count( $result ) <= 0){
              $doable = true;
            }
          }
          if( !$doable ){
            $message = '<p class="error">'.You_cant_wear_that.'</p>';
          }
        break;
        case 'equipment':
          $race = new Race( $this->datalink, $this->race );
          if( $race->getArmor() ){
            $result = $this->datalink->dbQuery( 'select id from '.mod.'deth_character_item
            where playercharacter="'.$this->id.'"
            and equipped="1"
            and type="equipment"
            and item="'.$result[0][0].'"
            limit 1', 'result' );
            if( count( $result ) <= 0){
              $doable = true;
            }else{
              $message = '<p class="error">'.Already_wearing_that.'</p>';
            }
          }else{
            $message = '<p class="error">'.You_cant_wear_that.'</p>';
          }
        break;
      }
      if( $doable ){
        if( $this->datalink->dbQuery( 'update '.mod.'deth_character_item set 
        equipped="1" 
        where id="'.$inventoryid.'"', 'query' ) > 0 ){
          $message = '<p class="fine">'.Item_equipped.'</p>';
        }
      }
    }
    return $message;
  }

  public function unequip( $inventoryid ){
    $message = '';
    $doable = false;
    $result = $this->datalink->dbQuery( 'select item, type from '.mod.'deth_character_item
    where playercharacter="'.$this->id.'"
    and equipped="1"
    and id="'.$inventoryid.'"
    limit 1', 'result' );
    if( isset( $result[0] ) ){
      switch( $result[0][1] ){
        case 'weapon':
        case 'armor':
          $doable = true;
        break;
        case 'equipment':
          $equipment = new Equipment( $this->datalink, $result[0][0] );
          if( $equipment->getPermanent() ){
            $message = '<p class="error">'.This_piece_of_equipment_cant_be_removed.'</p>';
          }else{
            $doable = true;
          }
        break;
      }
    }
    if( $doable ){
      if ( $this->datalink->dbQuery( 'update '.mod.'deth_character_item set 
      equipped="0" 
      where id="'.$inventoryid.'"', 'query' ) > 0 ){
        $message = '<p class="fine">'.Item_unequipped.'</p>';
      }
    }
    return $message;
  }
  
  public function reload( $inventoryid ){
    $message = '';
    $doable = false;
    $result = $this->datalink->dbQuery( 'select item, type, value, max from '.mod.'deth_character_item
    where playercharacter="'.$this->id.'"
    and equipped="1"
    and id="'.$inventoryid.'"
    and type="weapon"
    limit 1', 'result' );
    if( isset( $result[0] ) ){
      $weapon = new Weapon( $this->datalink, $result[0][0] );
      if( $result[0][3] > 0 and $result[0][2] < $weapon->getClipsize() ){
        $doable = true;
        $remain = $result[0][3] - 1;
      }
    }
    if( $doable ){
      if ( $this->datalink->dbQuery( 'update '.mod.'deth_character_item set 
      value="'.$weapon->getClipsize().'",
      max="'.$remain.'"
      where id="'.$inventoryid.'"', 'query' ) > 0 ){
        $message = '<p class="fine">'.Item_reloaded.'</p>';
      }
    }
    return $message;
  }
  
  public function buy( $type, $item ){
    $errors = '';
    $done = false;
    if( $item->getId() != 0 ){
      $invrow = new Inventory( $this->datalink );
      $invrow->setPlayercharacter( $this->id );
      $invrow->setType( $type );
      $invrow->setItem( $item->getId() );
      switch( $type ){
        case 'weapon':
          if( $item->getClipsize() > 0 ){
            $invrow->setValue( $item->getClipsize() );
            $invrow->setMax( 5 );
          }
        break;
        case 'armor':
          $invrow->setValue( $item->getHitpoints() );
          $invrow->setMax( $item->getHitpoints() );
        break;
      }
      if( $this->coins >= $item->getPrice() 
      and $this->premium >= $item->getPremium() ){
        $this->coins = $this->coins - $item->getPrice();
        $this->premium = $this->premium - $item->getPremium();
        $this->save();
        $invrow->save();
      }else{
        $errors .= Not_enough_money;
      }
    }
    if( $errors != '' ){
      return '<p class="error">'.$errors.'</p>';
    }else{
      return '';
    }
  }
  
  public function sell( $rowid ){
    $invrow = new Inventory( $this->datalink, $rowid );
    if( $this->id == $invrow->getPlayercharacter() ){
      switch( $invrow->getType() ){
        case 'weapon':
          $item = new Weapon( $this->datalink, $invrow->getItem() );
        break;
        case 'armor':
          $item = new Armor( $this->datalink, $invrow->getItem() );
        break;
        case 'healing':
          $item = new HealingItem( $this->datalink, $invrow->getItem() );
        break;
        case 'repairing':
          $item = new RepairingItem( $this->datalink, $invrow->getItem() );
        break;
      }
      $this->coins = $this->coins + $item->getSellingprice();
      $this->save();
      $invrow->delete();
    }
  }
  
/*
 * PRIVATE
 */
  
}
?>