<?php
class Place extends DatabaseObject {
  
  protected $id = 0;
  protected $name = '';
  protected $description = '';
  protected $image = '';
  
/*
 * CONSTRUCTOR
 */
 
  public function __construct( $datalink, $id = 0 ){
    parent::__construct( $datalink, mod.'deth_places', get_object_vars( $this ), $id );
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

  public function getImage(){
    return $this->image;
  }
  
/*
 * SETTERS
 */
  
  public function setName($name){
    $this->name = $name;
  }
  
  public function setDescription($description){
    $this->description = $description;
  }
  
  public function setImage($image){
    $this->image = $image;
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
      $query = 'insert into '.mod.'deth_places_trad (id, lang, name, description) 
      values ("'.$this->id.'", "'.$_SESSION['lang'].'", "'.$this->name.'", "'.$this->description.'")';
    }else{
      $query = 'update '.mod.'deth_places_trad set 
      name="'.$this->name.'",
      description="'.$this->description.'" 
      where id="'.$this->id.'" 
      and lang="'.$_SESSION['lang'].'"';
    }
    $this->datalink->dbQuery($query, 'query');
    return $message;
  }
  
  public function delete(){
    $this->datalink->dbQuery( 'delete from '.mod.'deth_places_trad where id="'.$this->id.'"', 'query' );
    $message = '';
    if ( parent::delete() ){
      $message = '<p class="fine">'.Data_deleted.'</p>';
    }
    return $message;
  }
  
/*
 * PRIVATE METHODS
 */
  
  private function loadLanguage($lang){
    $query = 'select name, description from '.mod.'deth_places_trad where id="'.$this->id.'" and lang="'.$lang.'" limit 1';
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
    $query = 'select id from '.mod.'deth_places_trad where id="'.$this->id.'" and lang="'.$lang.'"';
    return ($this->datalink->dbQuery($query, 'rows') > 0);
  }
  
}
?>