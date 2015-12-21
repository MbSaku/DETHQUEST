<?php
class Equipment extends DatabaseObject implements PurchasableItem {
  
  protected $id = 0;
  protected $name = '';
  protected $description = '';
  protected $price = 0;
  protected $premium = 0;
  protected $forsale = false;
  protected $icon = '';
  protected $maleimage = '';
  protected $femaleimage = '';
  protected $permanent = false;
  
/*
 * CONSTRUCTOR
 */
 
  public function __construct($datalink, $id = 0){
    parent::__construct( $datalink, mod.'deth_item_equipment', get_object_vars( $this ), $id );
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
  public function setName( $string ){
    $this->name = $string;
  }
  
  public function getDescription(){
    return $this->description;
  }
  public function setDescription( $text ){
    $this->description = $text;
  }
  
  public function getPrice(){
    return $this->price;
  }
  public function setPrice( $integer ){
    $this->price = $integer;
  }
  
  public function getSellingprice(){
    return floor ($this->price * 0.25);
  }
  
  public function getPremium(){
    return $this->premium;
  }
  public function setPremium( $integer ){
    $this->premium = $integer;
  }
  
  public function getForsale(){
    return $this->forsale;
  }
  public function setForsale( $boolean ){
    $this->forsale = $boolean;
  }
  
  public function getIcon(){
    return $this->icon;
  }
  public function setIcon( $string ){
    $this->icon = $string;
  }
  
  public function getMaleimage(){
    return $this->maleimage;
  }
  public function setMaleimage( $string ){
    $this->maleimage = $string;
  }
  
  public function getFemaleimage(){
    return $this->femaleimage;
  }
  public function setFemaleimage( $string ){
    $this->femaleimage = $string;
  }
  
  public function getPermanent(){
    return $this->permanent;
  }
  public function setPermanent( $boolean ){
    $this->permanent = $boolean;
  }
  
/*
 * SETTERS
 */
 
  
  
  
  
  
  
  
  
  
/*
 * PUBLIC METHODS
 */
  
  public function save(){
    $message = '';
    if ( parent::save() ){
      $message = '<p class="fine">'.Data_saved.'</p>';
    }
    if (!$this->isTranslated($_SESSION['lang'])){
      $query = 'insert into '.mod.'deth_item_trad (id, type, lang, name, description) 
      values ("'.$this->id.'", "equipment", "'.$_SESSION['lang'].'", "'.$this->name.'", "'.$this->description.'")';
    }else{
      $query = 'update '.mod.'deth_item_trad set 
      name="'.$this->name.'",
      description="'.$this->description.'" 
      where id="'.$this->id.'" and type="equipment" and lang="'.$_SESSION['lang'].'"';
    }
    $this->datalink->dbQuery($query, 'query');
    return $message;
  }
  
  public function delete(){
    $message = '';
    $query = 'delete from '.mod.'deth_item_trad where id="'.$this->id.'" and type="equipment"';
    $this->datalink->dbQuery($query, 'query');
    if ( parent::delete() ){
      $message = '<p class="fine">'.Data_deleted.'</p>';
    }
    return $message;
  }

/*
 * PRIVATE METHODS
 */
   
  private function loadLanguage($lang){
    $query = 'select name, description from '.mod.'deth_item_trad where id="'.$this->id.'" and lang="'.$lang.'" and type="equipment" limit 1';
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
    $query = 'select id from '.mod.'deth_item_trad where id="'.$this->id.'" and lang="'.$lang.'" and type="equipment"';
    return ($this->datalink->dbQuery($query, 'rows') > 0);
  }

}