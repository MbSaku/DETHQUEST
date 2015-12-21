<?php

  function shutdown( $site ){
    $a = error_get_last();
    if( $a != null ){
      if( $site->getDebug() ){
        echo '<p style="position:fixed;top:auto;bottom:0.5em;right:0.5em;z-index:500;font-size:1em" class="error">'.Shutdown_error.'.<br>
        "<i>'.$a['message'].'</i>"
        </p>';
      }
      echo '<script>
      console.log("Error at file '.$a['file'].' on line '.$a['line'].': '.$a['message'].'");
      </script>';
      $site->addLog( 'ERROR:<br>FILE => '.$a['file'].'<br> Line => '.$a['line'].'<br> Error => '.$a['message'] );
    }
  }
  
  error_reporting(0);
?>