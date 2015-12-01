
function cssOpen(divid) {
  var block = document.getElementById(divid);
  if (block != null) {
    if (block.style.maxHeight == "0px") {
      block.style.maxHeight = ($('#' + divid + "-in").height() + 50) + "px";
    }else{
      block.style.maxHeight = "0px";
    }
  }
}

//Standalone mode
if(("standalone" in window.navigator) && window.navigator.standalone){
  // If you want to prevent remote links in standalone web apps opening Mobile Safari, change 'remotes' to true
  var noddy, remotes = false;
  document.addEventListener('click', function(event) {
    noddy = event.target;
    // Bubble up until we hit link or top HTML element. Warning: BODY element is not compulsory so better to stop on HTML
    while(noddy.nodeName !== "A" && noddy.nodeName !== "HTML") {
      noddy = noddy.parentNode;
    }
    if('href' in noddy && noddy.href.indexOf('http') !== -1 && (noddy.href.indexOf(document.location.host) !== -1 || remotes)){
      event.preventDefault();
      document.location.href = noddy.href;
    }
  },false);
}

function setHoverables( selector ){
  $( selector ).each( function() {
    $( this ).click( function() {
      $( this ).toggleClass( "hovered" );
    } );
  });
}

function initiateTcEditors(){
  while( tinymce.editors.length > 0 ){ 
    tinymce.remove( tinymce.editors[0] ); 
  }
  tinymce.init( {
    selector: "textarea.tceditor",
    setup: function( editor ) {
      editor.on( 'change', function () {
        tinymce.triggerSave();
      } );
    },
    plugins: [ "code link, textcolor"],
    menu: "false",
    height : 200,
    toolbar: "styleselect | undo redo code | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | bold italic underline strikethrough subscript superscript forecolor | link unlink selectall removeformat"
  } );
}
