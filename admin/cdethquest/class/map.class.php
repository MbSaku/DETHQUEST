<?php
class Map extends DatabaseObject {
  
  protected $id = 0;
  protected $place = 0;
  protected $playable = false;
  protected $name = '';
  protected $description = '';
  protected $width = 0;
  protected $height = 0;
  protected $level = '';
  protected $graph = '';
  protected $weather = '';
  protected $doors = '';
  protected $sprites = '';
  
/*
 * CONSTRUCTOR
 */
  
  public function __construct( $datalink, $id = 0 ){
    parent::__construct( $datalink, mod.'deth_maps', get_object_vars( $this ), $id );
    $this->loadLanguage( $_SESSION['lang'] );
    $this->matrixSetup();
  }
  
/*
 * GETTERS
 */
  
  public function getId(){
    return $this->id;
  }
  
  public function getPlace(){
    return $this->place;
  }
  
  public function getPlayable(){
    return $this->playable;
  }
  
  public function getName(){
    return $this->name;
  }
  
  public function getDescription(){
    return $this->description;
  }
  
  public function getWidth(){
    return $this->width;
  }
  
  public function getHeight(){
    return $this->height;
  }
  
  public function getLevel(){
    return $this->level;
  }
  
  public function getGraph(){
    return $this->graph;
  }
  
  public function getWeather(){
    return $this->weather;
  }
  
  public function getDoors(){
    return $this->doors;
  }
  
  public function getSprites(){
    return $this->sprites;
  }
  
/*
 * SETTERS
 */
  
  public function setPlace( $place ){
    $this->place = $place;
  }
  
  public function setPlayable( $playable ){
    $this->playable = $playable;
  }
  
  public function setName( $name ){
    $this->name = $name;
  }
  
  public function setDescription( $description ){
    $this->description = $description;
  }
  
  public function setWidth( $width ){
    if( $width < 20 ){ $width = 20; }
    if( $width > 40 ){ $width = 40; }
    $this->width = $width;
  }
  
  public function setHeight( $height ){
    if( $height < 20 ){ $height = 20; }
    if( $height > 40 ){ $height = 40; }
    $this->height = $height;
  }
  
  public function setLevel( $levelstring ){
    $this->level = $levelstring;
  }
  
  public function setGraph( $graphstring ){
    $this->graph = $graphstring;
  }
  
  public function setWeather( $weatherstring ){
    $this->weather = $weatherstring;
  }
  
  public function setDoors( $doorstring ){
    $this->doors = $doorstring;
  }
  
  public function setSprites( $spritestring ){
    $this->sprites = $spritestring;
  }
  
/*
 * PUBLIC METHODS
 */
  
  public function save(){
    $message = '';
    if ( parent::save() ){
      $message = '<p class="fine">'.Data_saved.'</p>';
    }
    if (!$this->isTranslated($_SESSION['lang'])){
      $query = 'insert into '.mod.'deth_maps_trad (id, lang, name, description) 
      values ("'.$this->id.'", "'.$_SESSION['lang'].'", "'.$this->name.'", "'.$this->description.'")';
    }else{
      $query = 'update '.mod.'deth_maps_trad set 
      name="'.$this->name.'",
      description="'.$this->description.'" 
      where id="'.$this->id.'" and lang="'.$_SESSION['lang'].'"';
    }
    $this->datalink->dbQuery($query, 'query');
    $this->matrixSetup();
    return $message;
  }
  
  public function delete(){
    $message = '';
    $query = 'delete from '.mod.'deth_maps_trad where id="'.$this->id.'"';
    $this->datalink->dbQuery($query, 'query');
    if ( parent::delete() ){
      $message = '<p class="fine">'.Data_deleted.'</p>';
    }
    return $message;
  }
  
  public function getMatrix( $asked = 'level' ){
    switch( $asked ){
      case 'graph':     $mat = explode( ":", $this->graph );    break;
      case 'weather':   $mat = explode( ":", $this->weather );  break;
      case 'doors':     $mat = explode( ":", $this->doors );    break;
      case 'sprites':   $mat = explode( ":", $this->sprites );  break;
      case 'level':
      default:
        $mat = explode (":", $this->level);
    }
    foreach ($mat as $k => $v){
      $mat[$k] = explode ('.', $v);
    }
    return $mat;
  }
    
/*
 * PRIVATE METHODS
 */
   
  private function loadLanguage($lang){
    $query = 'select name, description from '.mod.'deth_maps_trad where id="'.$this->id.'" and lang="'.$lang.'" limit 1';
    $result = $this->datalink->dbQuery($query, 'result');
    if (isset($result[0]) and $row = $result[0]){
      if ($row[0] != ''){
        $this->name = $row[0];
      }
      if ($row[1] != ''){
        $this->description = $row[1];
      }
    }
  }

  private function isTranslated($lang){
    $query = 'select id from '.mod.'deth_maps_trad where id="'.$this->id.'" and lang="'.$lang.'"';
    return ($this->datalink->dbQuery($query, 'rows') > 0);
  }
  
  private function matrixSetup(){
    if( $this->level == '' ){
      for( $a = 0; $a < $this->height; $a++ ){
        for( $b = 0; $b < $this->width; $b++ ){
          $this->level .= "0";
          if( $b < ( $this->width - 1 ) ){ $this->level .= "."; }
        }
        if( $a < $this->height - 1){ $this->level .= ":"; }
      }
    }
    if( $this->graph == '' ){
      for( $a = 0; $a < $this->height; $a++ ){
        for( $b = 0; $b < $this->width; $b++ ){
          $this->graph .= "0";
          if( $b < ( $this->width - 1 ) ){ $this->graph .= "."; }
        }
        if( $a < $this->height - 1){ $this->graph .= ":"; }
      }
    }
    if( $this->weather == '' ){
      for( $a = 0; $a < $this->height; $a++ ){
        for( $b = 0; $b < $this->width; $b++ ){
          $this->weather .= "0";
          if( $b < ( $this->width - 1 ) ){ $this->weather .= "."; }
        }
        if( $a < $this->height - 1){ $this->weather .= ":"; }
      }
    }
    if( $this->doors == '' ){
      for( $a = 0; $a < $this->height; $a++ ){
        for( $b = 0; $b < $this->width; $b++ ){
          $this->doors .= "0";
          if( $b < ( $this->width - 1 ) ){ $this->doors .= "."; }
        }
        if( $a < $this->height - 1){ $this->doors .= ":"; }
      }
    }
    if( $this->sprites == '' ){
      for( $a = 0; $a < $this->height; $a++ ){
        for( $b = 0; $b < $this->width; $b++ ){
          $this->sprites .= "0";
          if( $b < ( $this->width - 1 ) ){ $this->sprites .= "."; }
        }
        if( $a < $this->height - 1){ $this->sprites .= ":"; }
      }
    }
  }

}