<?php
switch ($site->getDevice()){
  case 'mobile':
  case 'tablet':
    echo '<img id="navbutton" src="'.$site->getBaseroot().'styles/'.$site->getStyle().'/images/mobilemenu.png" onclick="$('."'#navigation'".').slideToggle();"><a href="'.$site->getBaselink().'/home">
    <img class="logo" src="'.$site->getBaseroot().'styles/'.$site->getStyle().'/icon.png">
    </a>';
  break;
  default:
    echo '<a href="'.$site->getBaselink().'/home">
    <img class="logo" src="'.$site->getBaseroot().'styles/'.$site->getStyle().'/images/logo.png">
    </a>';
}
?>
<script>
window.onload = function() {
  var h = document.getElementsByTagName('h1')[0], d = document.createElement('span');
  if (h) {
    while(h.firstChild) d.appendChild(h.firstChild);
    h.appendChild(d);
    d.className = "h1wrap";
  }
  <?php
  if ($site->getDevice() != 'desktop'){
   ?>
   $('#navigation').css('height', (screen.availHeight - $("#header").height()) + "px");
   $('#navigation').slideToggle(0);
   <?php
  }
  ?>
}
</script>
