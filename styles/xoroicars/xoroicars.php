<?php
echo '<a href="'.$site->getBaselink().'/home">
  <img class="logo" src="'.$site->getBaseroot().'styles/'.$site->getStyle().'/icon.png">
</a>
<h1 class="title">'.$site->getTitle().'</h1>';
?>
<script>
window.onload = function() {
  document.getElementById("content").style.opacity="1";
};
</script>