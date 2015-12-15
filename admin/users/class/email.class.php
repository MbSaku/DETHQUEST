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

  public function preview(){
    echo '<div style="background:#FFFFFF;border:0.1em solid #000000;color:#000000;padding:0.5em;text-align:left;margin:0.5em;font-size:0.9em;font-family:Arial,sans-serif">
    <pre>
      From:    <b>'.$this->orig.'</b>
      To:      <b>'.$this->dest.'</b>
      Subject: <b>'.$this->subj.'</b>
    </pre>
    '.$this->body.'
    </div>';
  }

  public function send(){
    $header = 'MIME-Version: 1.0'."\n";
    $header .= 'Content-type: text/html; charset=UTF-8'."\n";
    $header .= 'From: '.$this->orig."\n";
    return mail( $this->dest, $this->subj, $this->body, $header );
  }

}
?>