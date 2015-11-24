<fieldset><legend><?php echo $module->getName().' - '.Images; ?></legend>
  <?php
   
  if (isset($_FILES['images'])
  and isset($_POST['uploadimages'])){
    $numfiles = count($_FILES['images']['name']);
    $images = Array ();
    for ($a = 0; $a < $numfiles; $a++){
      $images[$a]['name'] = $_FILES['images']['name'][$a];
      $images[$a]['type'] = $_FILES['images']['type'][$a];
      $images[$a]['tmp_name'] = $_FILES['images']['tmp_name'][$a];
      $images[$a]['error'] = $_FILES['images']['error'][$a];
      $images[$a]['size'] = $_FILES['images']['size'][$a];
    }
    foreach ($images as $image){
      $site->imageUpload($image, 'content');
    }
  }
  
  if (isset($_POST['image'])
  and isset($_POST['deleteimage'])){
    if(!unlink('uploads/'.$module->getFolder().'/'.$_POST['image'])){
      echo '<p class="fine">'.Image_not_deleted.'</p>';
    }
  }
  
  $images = $site->dirList('uploads/content');
  $i = 0;
  foreach ($images as $image){
    echo '<div class="edmedia">
    <form name="edim'.$i.'" method="post" action="">
    '.$image.'<br>
    <img src="'.$site->getBaseroot().'uploads/'.$module->getFolder().'/'.$image.'"><br>
    <input type="hidden" name="image" value="'.$image.'">
    <input type="submit" name="deleteimage" value="'.Delete_image.'">
    </form>
    </div>';
    $i++;
  }
  
  ?>
  <form name="uploadpic" method="post" enctype="multipart/form-data" action="">
    
    <p class="pinput"><?php echo Upload_image; ?><br>
    <input type="file" name="images[]" multiple></p>
    
    <p><input type="submit" name="uploadimages" value="<?php echo Upload_image_files; ?>"></p>
    
  </form>
  
</fieldset>