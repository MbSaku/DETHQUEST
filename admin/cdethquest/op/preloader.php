<div id="loadingdiv"></div>
<script type="text/javascript">
  
  function preloadImages( list ){
    var loaded = 0;
    var nopics = 5;  //Files that are not pictures.
    for( var i = 0; i < list.length; i++ ){
      var img = document.createElement( 'img' );
      img.src = list[i];
      img.onload = function(){
        loaded++;
        if( loaded == ( list.length - nopics ) ){
          $( "#loadingdiv" ).animate( { opacity:0 }, 200 );
          backend.link( 'game', 'game' );
        }else{
          percent = ( ( loaded / ( list.length - nopics ) ) * 100 );
          $( "#loadingdiv" ).html( '<p><?php echo Loading; ?>...</p><div class="bar"><div class="bar-prog" style="width:' + percent + '%"></div></div>' );
        }
      }
    }
  }

  var myImages = [
  <?php
  $cachelist = $site->dirList( '../../uploads/'.$module->getFolder().'/' );
  $i = 0;
  foreach ( $cachelist as $file ){
    echo '
    "'.$_GET['root'].'uploads/'.$module->getFolder().'/'.$file.'"';
    $i++;
    if ( $i < count( $cachelist ) ){
      echo ",";
    }
  }
  ?>


  ];

  preloadImages( myImages );

</script>