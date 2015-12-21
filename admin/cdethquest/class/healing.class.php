<?php
class HealingItem extends DatabaseObject implements PurchasableItem {
  
  protected $id = 0;
  protected $name = '';
  protected $description = '';
  protected $price = 0;
  protected $premium = 0;
  protected $forsale = false;
  protected $health = 0;
  protected $image = '';
  
/*
 * CONSTRUCTOR
 */
  
  public function __construct($datalink, $id = 0){
    parent::__construct( $datalink, mod.'deth_item_healing', get_object_vars( $this ), $id );
    $this->loadLanguage($_SESSION['lang']);
  }

/*
 * GETTERS AND SETTERS
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
  public function setPrice( $double ){
    $this->price = $double;
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
  
  public function getHealth(){
    return $this->health;
  }
  public function setHealth( $integer ){
    $this->health = $integer;
  }
  
  public function getIcon(){
    return $this->image;
  }
  public function setIcon( $string ){
    $this->image = $string;
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
      values ("'.$this->id.'", "healing", "'.$_SESSION['lang'].'", "'.$this->name.'", "'.$this->description.'")';
    }else{
      $query = 'update '.mod.'deth_item_trad set 
      name="'.$this->name.'",
      description="'.$this->description.'" 
      where id="'.$this->id.'" and type="healing" and lang="'.$_SESSION['lang'].'"';
    }
    $this->datalink->dbQuery($query, 'query');
    return $message;
  }
  
  public function delete(){
    $message = '';
    $query = 'delete from '.mod.'deth_item_trad where id="'.$this->id.'" and type="healing"';
    $this->datalink->dbQuery($query, 'query');
    if ( parent::delete() ){
      $message = '<p class="fine">'.Data_deleted.'</p>';
    }
    return $message;
  }

/*
 * PRIVATE METHODS
 */
   
  private function loadLanguage( $lang ){
    $query = 'select name, description from '.mod.'deth_item_trad where id="'.$this->id.'" and lang="'.$lang.'" and type="healing" limit 1';
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

  private function isTranslated( $lang ){
    $query = 'select id from '.mod.'deth_item_trad where id="'.$this->id.'" and lang="'.$lang.'" and type="healing"';
    return ($this->datalink->dbQuery($query, 'rows') > 0);
  }

}
?>
