<?php
switch ($site->getDevice()){
  case 'mobile':
  case 'tablet':
    echo '<a href="'.$site->getBaselink().'/login">
    <img src="'.$site->getBaseroot().'styles/'.$site->getStyle().'/images/logo.png">
    <img class="name" src="'.$site->getBaseroot().'styles/'.$site->getStyle().'/images/fincas.png">
    </a>';
  break;
  case 'desktop':
  default:
    echo '<a href="'.$site->getBaselink().'/login"><img class="logo" src="'.$site->getBaseroot().'styles/'.$site->getStyle().'/images/logofincas.png"></a>';
  break;
}
?>
<script>
window.onload = function() {
  document.getElementById("content").style.opacity="1";
};
</script>
