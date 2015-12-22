<?php
class Inventory extends DatabaseObject{
  
  protected $id = 0;
  protected $playercharacter = 0;
  protected $type = '';
  protected $item = 0;
  protected $equipped = 0;
  protected $value = 0;
  protected $max = 0;
  
/*
 * CONSTRUCTOR
 */
 
  public function __construct( $datalink, $id = 0 ){
    parent::__construct( $datalink, mod.'deth_character_item', get_object_vars( $this ), $id );
  }
  
/*
 * GETTERS AND SETTERS
 */
  
  public function getId(){
    return $this->id;
  }
  
  public function getPlayercharacter(){
    return $this->playercharacter;
  }
  public function setPlayercharacter( $integer ){
    $this->playercharacter = $integer;
  }
  
  public function getType(){
    return $this->type;
  }
  public function setType( $string ){
    $this->type = $string;
  }
  
  public function getItem(){
    return $this->item;
  }
  public function setItem( $integer ){
    $this->item = $integer;
  }
  
  public function getEquipped(){
    return $this->equipped;
  }
  public function setEquipped( $boolean ){
    $this->equipped = $boolean;
  }
  
  public function getValue(){
    return $this->value;
  }
  public function setValue( $integer ){
    $this->value = $integer;
  }
  
  public function getMax(){
    return $this->max;
  }
  public function setMax( $integer ){
    $this->max = $integer;
  }

/*
 * PUBLIC
 */
  
  public function save(){
    return parent::save();
  }

  public function delete(){
    return parent::delete();
  }
  
/*
 * PRIVATE
 */
  
}
?>