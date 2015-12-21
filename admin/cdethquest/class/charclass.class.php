<?php
class CharacterClass extends DatabaseObject {
  
  protected $id = 0;
  protected $name = '';
  protected $description = '';
  protected $icon = '';
  protected $playable = false;
  protected $health = 0;
  protected $speed = 0;
  protected $strength = 0;
  protected $dexterity = 0;
  protected $constitution = 0;
  protected $intelligence = 0;
  
/*
 * CONSTRUCTOR
 */
  
  public function __construct( $datalink, $id = 0 ){
    parent::__construct( $datalink, mod.'deth_classes', get_object_vars( $this ), $id );
    $this->loadLanguage($_SESSION['lang']);
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
  public function getDescription(){
    return $this->description;
  }
  public function getIcon(){
    return $this->icon;
  }
  public function getPlayable(){
    return $this->playable;
  }
  public function getHealth(){
    return $this->health;
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
  
/*
 * SETTERS
 */
  
  public function setName( $name ){
    $this->name = $name;
  }
  public function setDescription( $description ){
    $this->description = $description;
  }
  public function setIcon( $icon ){
    $this->icon = $icon;
  }
  public function setPlayable( $playable ){
    $this->playable = $playable;
  }
  public function setHealth( $health ){
    $this->health = $health;
  }
  public function setSpeed( $speed ){
    $this->speed = $speed;
  }
  public function setStrength( $strength ){
    $this->strength = $strength;
  }
  public function setDexterity( $dexterity ){
    $this->dexterity = $dexterity;
  }
  public function setConstitution( $constitution ){
    $this->constitution = $constitution;
  }
  public function setIntelligence( $intelligence ){
    $this->intelligence = $intelligence;
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
      $query = 'insert into '.mod.'deth_classes_trad (id, lang, name, description) 
      values ("'.$this->id.'", "'.$_SESSION['lang'].'", "'.$this->name.'", "'.$this->description.'")';
    }else{
      $query = 'update '.mod.'deth_classes_trad set 
      name="'.$this->name.'",
      description="'.$this->description.'" 
      where id="'.$this->id.'" and lang="'.$_SESSION['lang'].'"';
    }
    $this->datalink->dbQuery( $query, 'query' );
    return $message;
  }
  
  public function delete(){
    $message = '';
    $query = 'delete from '.mod.'deth_classes_trad where id="'.$this->id.'"';
    $this->datalink->dbQuery( $query, 'query' );
    $query = 'delete from '.mod.'deth_class_items where class="'.$this->id.'"';
    $this->datalink->dbQuery( $query, 'query' );
    if ( parent::delete() ){
      $message = '<p class="fine">'.Data_deleted.'</p>';
    }
    return $message;
  }
    
  public function calculatePower(){
    return ( $this->health + $this->speed + $this->strength + $this->dexterity + $this->constitution + $this->intelligence );
  }
  
  public function calculateWorth(){
    $worth = 0;
    $result = $this->datalink->dbQuery( 'select type, item, quantity
    from '.mod.'deth_class_items 
    where class="'.$this->id.'" order by type asc', 'result' );
    foreach ( $result as $row ){
      switch( $row[0] ){
        case 'equipment':
          $item = new Equipment( $this->datalink, $row[1] );
        break;
        case 'weapon':
          $item = new Weapon( $this->datalink, $row[1] );
        break;
        case 'armor':
          $item = new Armor( $this->datalink, $row[1] );
        break;
      }
      $worth = $worth + ( $item->getPrice() * $row[2] );
    }
    return $worth;
  }
  
  public function hasItem( $type, $item ){
    $query = 'select item from '.mod.'deth_class_items where class="'.$this->id.'" and type="'.$type.'" and item="'.$item.'"';
    return ( $this->datalink->dbQuery( $query, 'rows' ) > 0 );
  }
  
  public function removeItems(){
    $this->datalink->dbQuery( 'delete from '.mod.'deth_class_items where class="'.$this->id.'"', 'query' );
  }
  
  public function addItem( $type, $itemid, $quantity ){
    $types = Array( "equipment", "weapon", "armor" );
    if( in_array( $type, $types ) ){
      switch($type){
        case 'equipment':
          $item = new Equipment( $this->datalink, $itemid );
        break;
        case 'weapon':
          $item = new Weapon( $this->datalink, $itemid );
        break;
        case 'armor':
          $item = new Armor( $this->datalink, $itemid );
        break;
      }
      if($item->getId() != 0){
        $this->datalink->dbQuery( 'insert into '.mod.'deth_class_items 
        (class, type, item, quantity)
        values
        ("'.$this->id.'", "'.$type.'", "'.$item->getId().'", "'.$quantity.'")', 'query' );
      }
    }
  }
  
  public function isPlayable(){
    return $this->playable;
  }
  
/*
 * PRIVATE METHODS
 */
   
  private function loadLanguage( $lang ){
    $query = 'select name, description from '.mod.'deth_classes_trad where id="'.$this->id.'" and lang="'.$lang.'" limit 1';
    $result = $this->datalink->dbQuery( $query, 'result' );
    if ( isset( $result[0] ) and $row = $result[0] ){
      if ( $row[0] != '' ){
        $this->name = $row[0];
      }
      if ( $row[1] != '' ){
        $this->description = $row[1];
      }
    }
  }

  private function isTranslated( $lang ){
    $query = 'select id from '.mod.'deth_classes_trad where lang="'.$lang.'" and id="'.$this->id.'"';
    return ( $this->datalink->dbQuery( $query, 'rows' ) > 0 );
  }

}
?>