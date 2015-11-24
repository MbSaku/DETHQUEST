<?php
class Scenario extends DatabaseObject {
  
  protected $id = 0;
  protected $map = 0;
  protected $started = 0;
  protected $faction = 0;
  protected $players = 0;
  protected $turntime = 600;
  protected $level = '';
  protected $difficulty = 1;
  
/*
 * CONSTRUCTOR
 */
 
  public function __construct( $datalink, $id = 0 ){
    parent::__construct( $datalink, mod.'deth_scenario', get_object_vars( $this ), $id );
  }
  
  /*
 * GETTERS
 */
  
  public function getId(){
    return $this->id;
  }
  public function getMap(){
    return $this->map;
  }
  public function getStarted(){
    return $this->started;
  }
  public function getFaction(){
    return $this->faction;
  }
  public function getPlayers(){
    return $this->players;
  }
  public function getTurntime(){
    return $this->turntime;
  }
  public function getLevel(){
    return $this->level;
  }
  public function getDifficulty(){
    return $this->difficulty;
  }
  
/*
 * SETTERS
 */
  
  public function setMap( $map ){
    $this->map = $map;
  }
  public function setStarted( $time ){
    $this->started = $time;
  }
  public function setFaction( $factionid ){
    $this->faction = $factionid;
  }
  public function setPlayers( $numplayers ){
    $this->players = $numplayers;
  }
  public function setTurntime( $turntime ){
    $this->turntime = $turntime;
  }
  public function setLevel( $levelstring ){
    $this->level = $levelstring;
  }
  public function setDifficulty( $integer ){
    $this->difficulty = $integer;
  }
  
/*
 * PUBLIC METHODS
 */
  
  public function save(){
    $message = '';
    if ( parent::save() ){
      $message = '<p class="fine">'.Mission_started.'</p>';
    }
    return $message;
  }
  
  public function delete(){
    $message = '';
    $this->datalink->dbQuery( 'delete from '.mod.'deth_scenario_entity where scenario="'.$this->id.'"', 'query' );
    $this->datalink->dbQuery( 'delete from '.mod.'deth_game where scenario="'.$this->id.'"', 'query' );
    $this->datalink->dbQuery( 'delete from '.mod.'deth_chat where scenario="'.$this->id.'"', 'query' );
    if ( parent::delete() ){
      $message = '<p class="fine">'.Mission_ended.'</p>';
    }
    return $message;
  }
  
  public function isEmpty( $x, $y ){
    $map = new Map( $this->datalink, $this->map );
    $spr = $map->getMatrix( 'sprites' );
    $query = 'select entity from '.mod.'deth_scenario_entity 
    where scenario="'.$this->id.'" and coordx="'.$x.'" and coordy="'.$y.'"';
    $empty = ( $this->datalink->dbQuery( $query, 'rows' ) <= 0 );
    return( $empty and $spr[$y][$x] == 0 );
  }
  
  public function place( $x, $y, $type, $entity, $value = 0 ){
    $this->datalink->dbQuery( 'delete from '.mod.'deth_scenario_entity
    where scenario="'.$this->id.'" 
    and type="'.$type.'"
    and entity="'.$entity.'"', 'query' );
    $this->datalink->dbQuery( 'insert into '.mod.'deth_scenario_entity (
    scenario, coordx, coordy, type, entity, value
    )values (
    "'.$this->id.'", "'.$x.'", "'.$y.'", "'.$type.'", "'.$entity.'", "'.$value.'"
    )', 'query' );
  }
  
  public function addPlayer( $charid ){
    $order = 0;
    $new = false;
    $result = $this->datalink->dbQuery( 'select pjorder from '.mod.'deth_game where scenario="'.$this->id.'" order by pjorder desc limit 1', 'result' );
    if( isset( $result[0] ) ){
      $order = $result[0][0] + 1;
    }else{
      $new = true;
    }
    $this->datalink->dbQuery( 'insert into '.mod.'deth_game (pchar, scenario, pjorder)
    values ("'.$charid.'", "'.$this->id.'", "'.$order.'")', 'query' );
    if( $new ){
      $this->runTurns();
    }
  }

  public function runTurns(){
    $active = -1;
    $result = $this->datalink->dbQuery( 'select pjorder from '.mod.'deth_game where scenario="'.$this->id.'" and active=1 limit 1', 'result' );
    if( isset( $result[0] ) ){
      $active = $result[0][0];
    }
    $result = $this->datalink->dbQuery( 'update '.mod.'deth_game set active=0 where scenario="'.$this->id.'" and active=1', 'query' );
    $result = $this->datalink->dbQuery( 'select pchar from '.mod.'deth_game where scenario="'.$this->id.'" and pjorder>"'.$active.'" limit 1', 'result' );
    if( isset( $result[0] ) ){
      $character = new PlayerCharacter( $this->datalink, $result[0][0] );
      $this->datalink->dbQuery( 'update '.mod.'deth_game set active=1, instant="'.time().'", 
      actions="'.$character->calculateActions().'"
      where pchar="'.$character->getId().'" and scenario="'.$this->id.'"', 'query' );
    }else{
      $result = $this->datalink->dbQuery( 'select pchar from '.mod.'deth_game where scenario="'.$this->id.'" order by pjorder asc limit 1', 'result' );
      if( isset( $result[0] ) ){
        $character = new PlayerCharacter( $this->datalink, $result[0][0] );
        $this->datalink->dbQuery( 'update '.mod.'deth_game set active=1, instant="'.time().'", 
        actions="'.$character->calculateActions().'"
        where pchar="'.$character->getId().'" and scenario="'.$this->id.'"', 'query' );
      }
    }
  }

  public function numPlayers(){
    return $this->datalink->dbQuery( 'select pchar from '.mod.'deth_game where scenario="'.$this->id.'"', 'rows' );
  }

  public function numTargets(){
    return 0;
  }

  public function calculateDistance( $map, $x0, $y0, $x1, $y1, $moving = true ){
    $lev = $this->getMatrix( 'level' );
    $whe = $map->getMatrix( 'weather' );
    $x = $x0;
    $y = $y0;
    $nowalk = false;
    $noshoot = false;
    $difficulty = 0;
    $deltaX = $x1 - $x;
    $deltaY = $y1 - $y;
    $error = 0;
    $keep = true;
    if( $deltaX != 0 and $deltaY != 0){
      if( $deltaX > $deltaY ){
        $deltaerror = abs( $deltaY / $deltaX );
        while( ( $x != $x1 ) and $keep ){
          if( $moving and $whe[$y][$x] > 0 ){ $difficulty += $whe[$y][$x]; }
          if( isset( $lev[$y][$x] ) ){
            switch( $lev[$y][$x] ){
              case '1':
                $nowalk = true;
                if( $moving ){ $keep = false; }
              break;
              case '2':
                $nowalk = true;
                $noshoot = true;
                $keep = false;
              break;
            }
          }
          $error = $error + $deltaerror;
          while( $error >= 0.5 and $keep ){
            if( $moving and $whe[$y][$x] > 0 ){ $difficulty += $whe[$y][$x]; }
            switch( $lev[$y][$x] ){
              case '1':
                $nowalk = true;
                if( $moving ){ $keep = false; }
              break;
              case '2':
                $nowalk = true;
                $noshoot = true;
                $keep = false;
              break;
            }
            if( $deltaY < 0 ){ $y = $y - 1; }else{ $y = $y + 1; }
            $error = $error - 1;
          }
          if( $deltaX < 0 ){ $x = $x - 1; }else{ $x = $x + 1; }
        }
      }else{
        $deltaerror = abs( $deltaX / $deltaY );
        while( ( $y != $y1 ) and $keep ){
          if( $moving and $whe[$y][$x] > 0 ){ $difficulty += $whe[$y][$x]; }
          switch( $lev[$y][$x] ){
            case '1':
              $nowalk = true;
              if( $moving ){ $keep = false; }
            break;
            case '2':
              $nowalk = true;
              $noshoot = true;
              $keep = false;
            break;
          }
          $error = $error + $deltaerror;
          while( $error >= 0.5 and $keep ){
            if( $moving and $whe[$y][$x] > 0 ){ $difficulty += $whe[$y][$x]; }
            switch( $lev[$y][$x] ){
              case '1':
                $nowalk = true;
                if( $moving ){ $keep = false; }
              break;
              case '2':
                $nowalk = true;
                $noshoot = true;
                $keep = false;
              break;
            }
            if( $deltaX < 0 ){ $x = $x - 1; }else{ $x = $x + 1; }
            $error = $error - 1;
          }
          if( $deltaY < 0 ){ $y = $y - 1; }else{ $y = $y + 1; }
        }
      }
    }else{
      if( $deltaX != 0 ){
        if( $deltaX > 0 ){ $inc = 1; }else{ $inc = -1; }
        while( ( $x != $x1 ) and $keep ){
          if( $moving and $whe[$y][$x] > 0 ){ $difficulty += $whe[$y][$x]; }
          switch( $lev[$y][$x] ){
            case '1':
              $nowalk = true;
              if( $moving ){ $keep = false; }
            break;
            case '2':
              $nowalk = true;
              $noshoot = true;
              $keep = false;
            break;
          }
          $x = $x + $inc;
        }
      }else{
        if( $deltaY > 0 ){ $inc = 1; }else{ $inc = -1; }
        while( ( $y != $y1 ) and $keep ){
          if( $moving and $whe[$y][$x] > 0 ){ $difficulty += $whe[$y][$x]; }
          switch( $lev[$y][$x] ){
            case '1':
              $nowalk = true;
              if( $moving ){ $keep = false; }
            break;
            case '2':
              $nowalk = true;
              $noshoot = true;
              $keep = false;
            break;
          }
          $y = $y + $inc;
        }
      }
    }
    if( !$keep ){
      $distance =  1000;
    }else{
      $distance = sqrt ( ( ( $x1 - $x0 ) * ( $x1 - $x0 ) ) + ( ( $y1 - $y0 ) * ( $y1 - $y0 ) ) );
    }
    return round( $distance * 2 ) + $difficulty;
  }

  public function setDoors( $map ){
    $mlevel = $map->getMatrix( 'level' );
    $dlevel = $map->getMatrix( 'doors' );
    $level = '';
    for( $y = 0; $y < $map->getHeight(); $y++ ){
      for( $x = 0; $x < $map->getWidth(); $x++ ){
        if( $dlevel[$y][$x] != 0 ){
          $level .= '2';
        }else{
          $level .= $mlevel[$y][$x];
        }
        if( $x < ( $map->getWidth() - 1 ) ){
         $level .= '.';
        }
      }
      $level .= ':';
    }
    $this->level = $level;
  }

  public function updateLogic ( $map, $matlevel ){
    $level = '';
    for( $y = 0; $y < $map->getHeight(); $y++ ){
      for( $x = 0; $x < $map->getWidth(); $x++ ){
        $level .= $matlevel[$y][$x];
        if( $x < ( $map->getWidth() - 1 ) ){ $level .= '.'; }
      }
      if( $y < ( $map->getHeight() - 1 ) ){ $level .= ':'; }
    }
    $this->level = $level;
    $this->save();
  }

  public function getActualPlayer(){
    $result = $this->datalink->dbQuery( 'select pchar from '.mod.'deth_game where scenario="'.$this->id.'" and active=1 limit 1' , 'result' );
    if( isset( $result[0] ) ){
      return $result[0][0];
    }else{
      return 0;
    }
  }

  public function elapsedTime(){
    $result = $this->datalink->dbQuery( 'select instant from '.mod.'deth_game where scenario="'.$this->id.'" and active=1 limit 1' , 'result' );
    if( isset( $result[0] ) ){
      return ( time() - $result[0][0] );
    }else{
      return 0;
    }
  }

/*
 * PRIVATE METHODS
 */
  
  public function getMatrix( $type = 'level' ){
    switch( $type ){
      case 'level':
      default:
        $mat = explode( ":", $this->level );
    }
    foreach( $mat as $k => $v ){
      $mat[$k] = explode( '.', $v );
    }
    return $mat;
  }
  
}
?>