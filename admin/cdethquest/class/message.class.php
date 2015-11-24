<?php
class Message extends DatabaseObject {
  
  protected $id = 0;
  protected $scenario = 0;
  protected $faction = 0;
  protected $instant = 0;
  protected $player = 0;
  protected $message = '';
  
/*
 * CONSTRUCTOR
 */
 
  public function __construct( $datalink, $id = 0 ){
    parent::__construct( $datalink, mod.'deth_chat', get_object_vars( $this ), $id );
  }
  
/*
 * GETTERS
 */
  
  public function getId(){
    return $this->id;
  }
  
  public function getScenario(){
    return $this->scenario;
  }
  
  public function getFaction(){
    return $this->faction;
  }
  
  public function getInstant(){
    return $this->instant;
  }
  
  public function getPlayer(){
    return $this->player;
  }
  
  public function getMessage(){
    return $this->message;
  }
  
/*
 * PUBLIC METHODS
 */
  
  public function send( $character, $scenario, $faction, $message ){
    $this->player = $character;
    $this->scenario = $scenario;
    $this->faction = $faction;
    $this->message = $message;
    $this->instant = time();
    $this->datalink->dbQuery( 'delete from '.mod.'deth_chat where instant<"'.( time() - 7776000 ).'"', 'query' );
    parent::save();
  }
  
  public function render( $folder ){
    $html = '';
    if( $this->player != 0 ){
      $character = new PlayerCharacter( $this->datalink, $this->player );
      $html .= '<div class="message">
      <div class="charportrait chatportrait">
      <div class="inner">'.$character->renderHtml( $folder, true ).'</div>
      </div>
      <span class="desc">'.$character->getName().'</span><br>'.ucfirst( $this->message ).'
      <div class="messagetime">'.strftime( '%d/%m/%Y %H:%M', $this->instant ).'</div> 
      </div>';
    }else{
      $html .= '<div class="message">
      '.ucfirst( nl2br( $this->message ) ).'
      </div>';
    }
    return $html;
  }
  
}  
