<?php
class Email{  //Manages email sendings
  
  private $dest = '';
  private $orig = '';
  private $subj = '';
  private $body = '';

  public function __construct( $orig, $dest, $subj, $body ){
    $this->orig = $orig;
    $this->dest = $dest;
    $this->subj = $subj;
    $this->body = $body;
  }

  public function send(){
    $header = 'MIME-Version: 1.0'."\n";
    $header .= 'Content-type: text/html; charset=UTF-8'."\n";
    $header .= 'From: '.$this->orig."\n";
    return mail( $this->dest, $this->subj, $this->body, $header );
  }

}
?>