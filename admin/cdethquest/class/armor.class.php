<?php
class Armor extends DatabaseObject implements PurchasableItem {
  
  protected $id = 0;
  protected $name = '';
  protected $description = '';
  protected $protection = 0;
  protected $hitpoints = 0;
  protected $hashelmet = false;
  protected $price = 0;
  protected $premium = 0;
  protected $forsale = false;
  protected $icon = '';
  protected $maleimage = '';
  protected $femaleimage = '';
  
/*
 * CONSTRUCTOR
 */
 
  public function __construct($datalink, $id = 0){
    parent::__construct( $datalink, mod.'deth_item_armor', get_object_vars( $this ), $id );
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
  public function getProtection(){
    return $this->protection;
  }
  public function getHitpoints(){
    if( $this->hitpoints == 0 ){
      $this->hitpoints = 1;
    }
    return $this->hitpoints;
  }
  public function getHelmet(){
    return $this->hashelmet;
  }
  public function getPrice(){
    return $this->price;
  }
  public function getSellingprice(){
    return floor ($this->price * 0.25);
  }
  public function getPremium(){
    return $this->premium;
  }
  public function getForsale(){
    return $this->forsale;
  }
  public function getIcon(){
    return $this->icon;
  }
  public function getMaleimage(){
    return $this->maleimage;
  }
  public function getFemaleimage(){
    return $this->femaleimage;
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
  public function setProtection( $integer ){
    $this->protection = $integer;
  }
  public function setHitpoints( $integer ){
    $this->hitpoints = $integer;
  }
  public function setPrice( $price ){
    $this->price = $price;
  }
  public function setHelmet( $boolean ){
    $this->hashelmet = $boolean;
  }
  public function setPremium( $premium ){
    $this->premium = $premium;
  }
  public function setForsale( $boolean ){
    $this->forsale = $boolean;
  }
  public function setIcon( $image ){
    $this->icon = $image;
  }
  public function setMaleimage( $maleimage ){
    $this->maleimage = $maleimage;
  }
  public function setFemaleimage( $femaleimage ){
    $this->femaleimage = $femaleimage;
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
      $query = 'insert into '.mod.'deth_item_trad (id, type, lang, name, description) 
      values ("'.$this->id.'", "armor", "'.$_SESSION['lang'].'", "'.$this->name.'", "'.$this->description.'")';
    }else{
      $query = 'update '.mod.'deth_item_trad set 
      name="'.$this->name.'",
      description="'.$this->description.'" 
      where id="'.$this->id.'" and type="armor" and lang="'.$_SESSION['lang'].'"';
    }
    $this->datalink->dbQuery($query, 'query');
    return $message;
  }
  
  public function delete(){
    $message = '';
    $query = 'delete from '.mod.'deth_item_trad where id="'.$this->id.'" and type="armor"';
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
    $query = 'select name, description from '.mod.'deth_item_trad where id="'.$this->id.'" and lang="'.$lang.'" and type="armor" limit 1';
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
    $query = 'select id from '.mod.'deth_item_trad where id="'.$this->id.'" and lang="'.$lang.'" and type="armor"';
    return ($this->datalink->dbQuery($query, 'rows') > 0);
  }

}