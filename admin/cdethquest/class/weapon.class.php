<?php
class Weapon extends DatabaseObject implements PurchasableItem {

  protected $id = 0;
  protected $name = '';
  protected $description = '';
  protected $type = 'Other';
  protected $close = false;
  protected $attacks = 0;
  protected $atrange = 0;
  protected $impact = 0;
  protected $damage = 0;
  protected $piercing = 0;
  protected $clipsize = 0;
  protected $price = 0;
  protected $premium = 0;
  protected $forsale = true;
  protected $icon = '';
  protected $maleimage = '';
  protected $femaleimage = '';
  protected $hands = 1;

/*
 * CONSTRUCTOR
 */
 
  public function __construct( $datalink, $id = 0 ){
    parent::__construct( $datalink, mod.'deth_item_weapon', get_object_vars( $this ), $id );
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
  public function getType(){
    return $this->type;
  }
  public function getClose(){
    return $this->close;
  }
  public function getAttacks(){
    return $this->attacks;
  }
  public function getRange(){
    return $this->atrange;
  }
  public function getImpact(){
    return $this->impact;
  }
  public function getDamage(){
    return $this->damage;
  }
  public function getPiercing(){
    return $this->piercing;
  }
  public function getClipsize(){
    return $this->clipsize;
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
  public function getHands(){
    return $this->hands;
  }

/*
 * SETTERS
 */
  
  public function setName( $string ){
    $this->name = $string;
  }
  public function setDescription( $text ){
    $this->description = $text;
  }
  public function setType( $string ){
    $this->type = $string;
  }
  public function setClose( $boolean ){
    $this->close = $boolean;
  }
  public function setAttacks( $integer ){
    $this->attacks = $integer;
  }
  public function setRange( $integer ){
    $this->atrange = $integer;
  }
  public function setImpact( $integer ){
    $this->impact = $integer;
  }
  public function setDamage( $integer ){
    $this->damage = $integer;
  }
  public function setPiercing( $integer ){
    $this->piercing = $integer;
  }
  public function setClipsize( $integer ){
    $this->clipsize = $integer;
  }
  public function setPrice( $integer ){
    $this->price = $integer;
  }
  public function setPremium( $integer ){
    $this->premium = $integer;
  }
  public function setForsale( $boolean ){
    $this->forsale = $boolean;
  }
  public function setIcon( $imagename ){
    $this->icon = $imagename;
  }
  public function setMaleimage( $imagename ){
    $this->maleimage = $imagename;
  }
  public function setFemaleimage( $imagename ){
    $this->femaleimage = $imagename;
  }
  public function setHands( $integer ){
    $this->hands = $integer;
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
      values ("'.$this->id.'", "weapon", "'.$_SESSION['lang'].'", "'.$this->name.'", "'.$this->description.'")';
    }else{
      $query = 'update '.mod.'deth_item_trad set 
      name="'.$this->name.'",
      description="'.$this->description.'" 
      where id="'.$this->id.'" and type="weapon" and lang="'.$_SESSION['lang'].'"';
    }
    $this->datalink->dbQuery($query, 'query');
    return $message;
  }
  
  public function delete(){
    $this->datalink->dbQuery('delete from '.mod.'deth_item_trad where id="'.$this->id.'" and type="weapon"', 'query');
    $this->datalink->dbQuery('delete from '.mod.'deth_class_items where item="'.$this->id.'" and type="weapon"', 'query');
    $this->datalink->dbQuery('delete from '.mod.'deth_character_item where item="'.$this->id.'" and type="weapon"', 'query');
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
    $query = 'select name, description from '.mod.'deth_item_trad where id="'.$this->id.'" and lang="'.$lang.'" and type="weapon" limit 1';
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
    $query = 'select id from '.mod.'deth_item_trad where id="'.$this->id.'" and lang="'.$lang.'" and type="weapon"';
    return ($this->datalink->dbQuery($query, 'rows') > 0);
  }

}
?>