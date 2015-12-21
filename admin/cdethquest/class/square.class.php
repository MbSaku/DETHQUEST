<?php
class Square extends DatabaseObject {
  
  protected $id = 0;
  protected $place = 0;
  protected $type = 'floor';
  protected $image = '';
  
/*
 * CONSTRUCTOR
 */
 
  public function __construct( $datalink, $id = 0 ){
    parent::__construct( $datalink, mod.'deth_squares', get_object_vars( $this ), $id );
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

  public function getType(){
    return $this->type;
  }

  public function getTexture(){
    return $this->image;
  } 
  
/*
 * SETTERS
 */
    
  public function setPlace($place){
    $this->place = $place;
  }
  
  public function setType($type){
    $this->type = $type;
  }
  
  public function setTexture($texture){
    $this->image = $texture;
  }
  
/*
 * PUBLIC METHODS
 */
  
  public function save(){
    $message = '';
    if ( parent::save() ){
      $message = '<p class="fine">'.Data_saved.'</p>';
    }
    return $message;
  }
  
  public function delete(){
    $message = '';
    if ( parent::delete() ){
      $message = '<p class="fine">'.Data_deleted.'</p>';
    }
    return $message;
  }
  
  public function numericType(){
    switch( $this->type ){
      case 'floor':
      case 'door':
        return 0;
      break;
      case 'pit':
        return 1;
      break;
      case 'wall':
      default:
        return 2;
    }
  }
  
/*
 * PRIVATE METHODS
 */
  
}
?>