<?php
class Image {

  private $image = '';
  private $image_type = '';

  public function __construct( $filename ) {
    $image_info = getimagesize($filename);
    $this->image_type = $image_info[2];
    if( $this->image_type == IMAGETYPE_JPEG ) {
      $this->image = imagecreatefromjpeg($filename);
    }elseif ($this->image_type == IMAGETYPE_GIF){
      $this->image = imagecreatefromgif($filename);
    }elseif( $this->image_type == IMAGETYPE_PNG ){
      $this->image = imagecreatefrompng($filename);
    }
  }

  function save( $filename, $compression=75 ){
    if( $this->image_type == IMAGETYPE_JPEG ){
      imagejpeg( $this->image, $filename, $compression );
      return true;
    }elseif( $this->image_type == IMAGETYPE_GIF ){
      imagegif( $this->image, $filename );
      return true;
    }elseif( $this->image_type == IMAGETYPE_PNG ){
      imagealphablending( $this->image, false );
      imagesavealpha( $this->image, true );
      imagepng( $this->image, $filename );
      return true;
    }
    return false;
  }

  function output( $image_type = IMAGETYPE_JPEG ) {
    if( $image_type == IMAGETYPE_JPEG ){
      imagejpeg( $this->image );
    }elseif( $image_type == IMAGETYPE_GIF ){
      imagegif( $this->image );
    }elseif( $image_type == IMAGETYPE_PNG ){
      imagepng( $this->image );
    }
  }
  
  function getWidth(){
    return imagesx( $this->image );
  }
  
  function getHeight(){
    return imagesy($this->image);
  }

  function resizeToHeight( $height ){
    $ratio = $height / $this->getHeight();
    $width = $this->getWidth() * $ratio;
    $this->resize( $width, $height );
  }

  function resizeToWidth( $width ){
    $ratio = $width / $this->getWidth();
    $height = $this->getheight() * $ratio;
    $this->resize( $width, $height );
  }
 
  function scale( $scale ){
    $width = $this->getWidth() * $scale / 100;
    $height = $this->getheight() * $scale / 100;
    $this->resize( $width, $height );
  }
 
  function resize( $width, $height ){
    $new_image = imagecreatetruecolor( $width, $height );
    $type = image_type_to_mime_type( $this->image_type );
    
    if( ( $type == "image/gif" ) or ( $type == "image/png") ){
      $transindex = imagecolortransparent( $new_image );
      if( $transindex >= 0 ){
        $transcol = imagecolorsforindex( $this->image, $transindex );
        $transindex = imagecolorallocatealpha( $new_image, $transcol['red'], $transcol['green'], $transcol['blue'], 127 );
        imagefill( $new_image, 0, 0, $transindex );
        imagecolortransparent( $new_image, $transindex );
      }elseif( $type == "image/png" or $type == "image/x-png" ){
        imagealphablending($new_image, false);
        $color = imagecolorallocatealpha( $new_image, 0, 0, 0, 127 );
        imagefill( $new_image, 0, 0, $color );
        imagesavealpha( $new_image, true );
      }
    }
    imagecopyresampled( $new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight() );
    $this->image = $new_image;
  }      
 
}
?>