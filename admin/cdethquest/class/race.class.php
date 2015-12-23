<?php
class Race extends DatabaseObject {
  
  protected $id = 0;
  protected $name = '';
  protected $description = '';
  protected $playable = false;
  protected $hands = 2;
  protected $armor = true;
  protected $xscaling = 0.9;
  protected $yscaling = 0.9;
  protected $icon = '';
  protected $modhealth = 0;
  protected $modspeed = 0;
  protected $modstrength = 0;
  protected $moddexterity = 0;
  protected $modconstitution = 0;
  protected $modintelligence = 0;
  
/*
 * CONSTRUCTOR
 */
  
  public function __construct($datalink, $id = 0){
    parent::__construct( $datalink, mod.'deth_races', get_object_vars( $this ), $id );
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
    
  public function getPlayable(){
    return $this->playable;
  }
  public function setPlayable( $boolean ){
    $this->playable = $boolean;
  }
  
  public function getHands(){
    if( $this->hands < 0 ){
      return 0;
    }
    if ($this->hands > 2){
      return 2;
    }
    return $this->hands;
  }
  public function setHands( $integer ){
    if( $integer > 2 ){
      $integer = 2;
    }
    if( $integer < 0 ){
      $integer = 0;
    }
    $this->hands = $integer;
  }
  
  public function getArmor(){
    return $this->armor;
  }
  public function setArmor( $boolean ){
    $this->armor = $boolean;
  }
    
  public function getXscale(){
    return $this->xscaling;
  }
  public function setXscale( $double ){
    $this->xscaling = $double;
  }
    
  public function getYscale(){
    return $this->yscaling;
  }
  public function setYscale( $double ){
    $this->yscaling = $double;
  }
  
  public function getModHealth(){
    return $this->modhealth;
  }
  public function setModHealth( $integer ){
    $this->modhealth = $integer;
  }
  
  public function getModSpeed(){
    return $this->modspeed;
  }
  public function setModSpeed( $integer ){
    $this->modspeed = $integer;
  }
  
  public function getModStrength(){
    return $this->modstrength;
  }
  public function setModStrength( $integer ){
    $this->modstrength = $integer;
  }
  
  public function getModDexterity(){
    return $this->moddexterity;
  }
  public function setModDexterity( $integer ){
    $this->moddexterity = $integer;
  }
  
  public function getModConstitution(){
    return $this->modconstitution;
  }
  public function setModConstitution( $integer ){
    $this->modconstitution = $integer;
  }
  
  public function getModIntelligence(){
    return $this->modintelligence;
  }
  public function setModIntelligence( $integer ){
    $this->modintelligence = $integer;
  }
  
  public function getIcon(){
    return $this->icon;
  }
  public function setIcon( $string ){
    $this->icon = $string;
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
      $query = 'insert into '.mod.'deth_races_trad (id, lang, name, description) 
      values ("'.$this->id.'", "'.$_SESSION['lang'].'", "'.$this->name.'", "'.$this->description.'")';
    }else{
      $query = 'update '.mod.'deth_races_trad set 
      name="'.$this->name.'",
      description="'.$this->description.'" 
      where id="'.$this->id.'" and lang="'.$_SESSION['lang'].'"';
    }
    $this->datalink->dbQuery($query, 'query');
    return $message;
  }
  
  public function delete(){
    $this->datalink->dbQuery('delete from '.mod.'deth_races_trad where id="'.$this->id.'"', 'query');
    $this->datalink->dbQuery('delete from '.mod.'deth_race_names where race="'.$this->id.'"', 'query');
    $this->datalink->dbQuery('delete from '.mod.'deth_race_dialog where race="'.$this->id.'"', 'query');
    $this->datalink->dbQuery('delete from '.mod.'deth_race_class where race="'.$this->id.'"', 'query');
    $this->datalink->dbQuery('delete from '.mod.'deth_race_body where race="'.$this->id.'"', 'query');
    $this->datalink->dbQuery('delete from '.mod.'deth_race_hair where race="'.$this->id.'"', 'query');
    $this->datalink->dbQuery('delete from '.mod.'deth_race_face where race="'.$this->id.'"', 'query');
    $this->datalink->dbQuery('delete from '.mod.'deth_race_head where race="'.$this->id.'"', 'query');
    $message = '';
    if ( parent::delete() ){
      $message = '<p class="fine">'.Data_deleted.'</p>';
    }
    return $message;
  }
  
  public function appearanceMatrix(){
    $matrix = array( 'hair' => Array(), 'face' => array(), 'head' => array(), 'body' => array() );
    $result = $this->datalink->dbQuery( 'select distinct id from '.mod.'deth_race_hair where race="'.$this->id.'" order by name asc', 'result' );
    foreach( $result as $row ){
      $matrix['hair'][] = $row[0];
    }
    $result = $this->datalink->dbQuery( 'select distinct id from '.mod.'deth_race_face where race="'.$this->id.'" order by name asc', 'result' );
    foreach( $result as $row ){
      $matrix['face'][] = $row[0];
    }
    $result = $this->datalink->dbQuery( 'select distinct id from '.mod.'deth_race_head where race="'.$this->id.'" order by name asc', 'result' );
    foreach( $result as $row ){
      $matrix['head'][] = $row[0];
    }
    $result = $this->datalink->dbQuery( 'select distinct id from '.mod.'deth_race_body where race="'.$this->id.'" order by name asc', 'result' );
    foreach( $result as $row ){
      $matrix['body'][] = $row[0];
    }
    return $matrix;
  }
  
  public function getMaleVariant( $bodyid = 0, $type = 'body' ){
    $result = $this->datalink->dbQuery( 'select maleimage from '.mod.'deth_race_'.$type.' 
    where race="'.$this->id.'" and id="'.$bodyid.'"', 'result' );
    if( isset( $result[0] ) ){
      return $result[0][0];
    }else{
      return '';
    }
  }
  
  public function getFemaleVariant( $bodyid = 0, $type = 'body' ){
    $result = $this->datalink->dbQuery( 'select femaleimage from '.mod.'deth_race_'.$type.' 
    where race="'.$this->id.'" and id="'.$bodyid.'"', 'result' );
    if( isset( $result[0] ) ){
      return $result[0][0];
    }else{
      return '';
    }
  }
    
  public function setVariantImages( $maleimage, $femaleimage, $bodyid, $type = 'body' ){
    if( $bodyid == 0 ){
      $this->datalink->dbQuery( 'insert into '.mod.'deth_race_'.$type.' (
      race, maleimage, femaleimage
      ) values (
      "'.$this->id.'", "'.$maleimage.'", "'.$femaleimage.'"
      )', 'query' );
    }else{
      $this->datalink->dbQuery( 'update '.mod.'deth_race_'.$type.' set 
      maleimage="'.$maleimage.'", femaleimage="'.$femaleimage.'"
      where race="'.$this->id.'" and id="'.$bodyid.'"', 'query' );
    }
  }

  public function setMaleVariant( $maleimage, $bodyid, $type = 'body' ){
    if( $bodyid == 0 ){
      $this->datalink->dbQuery( 'insert into '.mod.'deth_race_'.$type.' (
      race, maleimage, femaleimage
      ) values (
      "'.$this->id.'", "'.$maleimage.'", ""
      )', 'query' );
    }else{
      $this->datalink->dbQuery( 'update '.mod.'deth_race_'.$type.' set maleimage="'.$maleimage.'"
      where race="'.$this->id.'" and id="'.$bodyid.'"', 'query' );
    }
  }
  
  public function setFemaleVariant( $femaleimage, $bodyid, $type = 'body' ){
    if( $bodyid == 0 ){
      $this->datalink->dbQuery( 'insert into '.mod.'deth_race_'.$type.' (
      race, maleimage, femaleimage
      ) values (
      "'.$this->id.'", "", "'.$femaleimage.'"
      )', 'query' );
    }else{
      $this->datalink->dbQuery( 'update '.mod.'deth_race_'.$type.' set femaleimage="'.$femaleimage.'"
      where race="'.$this->id.'" and id="'.$bodyid.'"', 'query' );
    }
  }
      
  public function getVariantName( $bodyid = 0, $type = 'body' ){
    $result = $this->datalink->dbQuery( 'select name from '.mod.'deth_race_'.$type.' where race="'.$this->id.'" and id="'.$bodyid.'"', 'result' );
    if( isset( $result[0] ) ){
      return $result[0][0];
    }else{
      return '';
    }
  }
  
  public function setVariantName( $bodyid, $name, $type = 'body' ){
    $this->datalink->dbQuery( 'update '.mod.'deth_race_'.$type.' set name="'.$name.'"
    where race="'.$this->id.'" and id="'.$bodyid.'"', 'query' );
  }
  
  public function deleteVariant( $bodyid, $type = 'body' ){
    $this->datalink->dbQuery( 'delete from '.mod.'deth_race_'.$type.' 
    where race="'.$this->id.'" and id="'.$bodyid.'"', 'query' );
  }
  
  public function calculateOffset(){
    return( $this->modhealth + $this->moddexterity + $this->modstrength + $this->modconstitution + $this->modspeed + $this->modintelligence );
  }
  
  public function addName( $name, $gender ){
    $name = str_replace( array("\n", "\r" ), " ", $name );
    $query = 'insert into '.mod.'deth_race_names (race, gender, name) values ("'.$this->id.'", "'.$gender.'", "'.$name.'")';
    $this->datalink->dbQuery( $query, 'query' );
  }
  
  public function delName( $idname ){
    $query = 'delete from '.mod.'deth_race_names where race="'.$this->id.'" and id="'.$idname.'"';
    $this->datalink->dbQuery( $query, 'query' );
  }
  
  public function addDialog( $quote, $gender ){
    $quote = str_replace( array( "\n", "\r" ), " ", $quote );
    $query = 'insert into '.mod.'deth_race_dialog (race, gender, quote) values ("'.$this->id.'", "'.$gender.'", "'.$quote.'")';
    $this->datalink->dbQuery( $query, 'query' );
  }
  
  public function delDialog( $idname ){
    $query = 'delete from '.mod.'deth_race_dialog where race="'.$this->id.'" and id="'.$idname.'"';
    $this->datalink->dbQuery( $query, 'query' );
  }
  
  public function hasClassAvailable( $classid ){
    return( $this->datalink->dbQuery( 'select class from '.mod.'deth_race_class where race="'.$this->id.'" and class="'.$classid.'"', 'rows' ) > 0 );
  }

  public function clearClasses(){
    $this->datalink->dbQuery('delete from '.mod.'deth_race_class where race="'.$this->id.'"', 'query');
  }
  
  public function addClass( $classid ){
    $class = new CharacterClass($this->datalink, $classid);
    if ($class->getId() != 0){
      $this->datalink->dbQuery('insert into '.mod.'deth_race_class (race, class) values ("'.$this->id.'", "'.$class->getId().'")', 'query');
    }
  }
  
  public function getRandomClass(){
    $result = $this->datalink->dbQuery('select class 
    from '.mod.'deth_race_class 
    where race="'.$this->id.'" 
    order by RAND() 
    limit 1', 'result');
    if (isset($result[0])){
      return $result[0][0];
    }else{
      return 0;
    }
  }
  
  public function getClasses(){
    $classes = array();
    $result = $this->dbQuery('select '.mod.'deth_race_class.class 
    from '.mod.'deth_race_class,  '.mod.'deth_classes
    where '.mod.'deth_race_class.race="'.$this->id.'" 
    and '.mod.'deth_race_class.class='.mod.'deth_classes.id
    order by '.mod.'deth_classes.name asc', 'result');
    foreach( $result as $row ){
      $classes[] = new CharacterClass( $this->datalink, $row[0] );
    }
    return $classes;
  }
  
  public function getRandomName( $gender ){
    $result = $this->datalink->dbQuery('select name 
    from '.mod.'deth_race_names 
    where race="'.$this->id.'" 
    and gender="'.$gender.'" 
    order by RAND() 
    limit 1', 'result');
    if (isset($result[0])){
      return $result[0][0];
    }else{
      return $this->name;
    }
  }
  
  public function isPlayable(){
    return $this->playable;
  }

  public function randomAppearance( $type = 'body' ){
    $query = 'select id from '.mod.'deth_race_'.$type.' where race="'.$this->id.'"';
    $rows = $this->datalink->dbQuery( $query, 'rows' );
    if( $rows > 0 ){
      $result = $this->datalink->dbQuery( $query, 'result', mt_rand( 0, ( $rows - 1 ) ) );
      if( isset( $result[0] ) ){
        return $result[0][0];
      }
    }
    return 0;
  }
    
/*
 * PRIVATE METHODS
 */
   
  private function loadLanguage($lang){
    $query = 'select name, description from '.mod.'deth_races_trad where id="'.$this->id.'" and lang="'.$lang.'" limit 1';
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
    $query = 'select id from '.mod.'deth_races_trad where lang="'.$lang.'" and id="'.$this->id.'"';
    return ($this->datalink->dbQuery($query, 'rows') > 0);
  }

}
?>