function Backend( script, root, module, lang, fingerprint, tag ){
  
  this.script = script;
  this.root = root;
  this.module = module;
  this.lang = lang;
  this.fingerprint = fingerprint;
  this.me = '';
  this.op = '';
  this.section = '';
  this.speed = 200;
  this.tag = tag;
  this.adminLinks = ["administration", "characters", "items", "equipment", "account"];
  
  this.link = function( me, op, section ){
    if( typeof( section ) === 'undefined' ){ section = ''; }
    var self = this;
    self.playClickSound();
    if( self.adminLinks.indexOf( me ) > -1 && me != self.me ){
      $( "#context" ).animate( { opacity:0 }, 0 );
      $( "#context" ).html( '<fieldset class="stfield"><legend>' + this.tag + '</legend><div id="wrapper"><div id="main"></div></div></fieldset>' );
      $( "#context" ).animate({ opacity:1 }, self.speed );
    }
    if( self.adminLinks.indexOf( me ) == -1 && me != self.me ){
      $( "#context" ).html( '<div id="wrapper"><div id="main"></div></div>' );
    }
    $( "#wrapper" ).animate( { opacity:0 } , self.speed );
    self.me = me;
    self.op = op;
    self.section = section;
    setTimeout( function(){
      $.ajax( {
        url: self.script + "ajax.php",
        type: 'GET',
        data: {
          root: self.root, 
          module: self.module,
          lang: self.lang, 
          fingerprint: self.fingerprint, 
          me: self.me, 
          op: self.op,
          section: section
        },
        success: function( html ) {
          $( '#main' ).html( html );
          $( "#wrapper" ).animate( { height:$( "#main" ).height() }, self.speed).animate( { opacity:1 }, self.speed );
        },
        tryCount: 0,
        retryLimit: 5,
        error: function( xhr, textStatus, errorThrown ){
          this.tryCount++;
          if( this.tryCount <= this.retryLimit ){
            console.log( 'Ajax retry: ' + this.tryCount );
            setTimeout( function(){ $.ajax( this ); }, 500 );
            return;
          }
          return;
        }
      } );
    }, self.speed );
  };
  
  this.post = function( form, animate ){
    var formData = new FormData($(form)[0]);
    var self = this;
    if( typeof( animate ) === 'undefined' ){ animate = true; $("html, body").animate({ scrollTop: 0 }, self.speed); }
    if( animate ){ $( "#wrapper" ).animate( { opacity:0 } , self.speed ); }
    setTimeout( function(){
      $.ajax( {
        url: self.script + "ajax.php?root=" + self.root + "&module=" + self.module + "&lang=" + self.lang + "&fingerprint=" + self.fingerprint + "&me=" + self.me + "&op=" + self.op + "&section=" + self.section,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function( html ) {
          backend.playPostSound();
          $('#main').html( html );
          if( animate ) $( "#wrapper" ).animate( { height:$( "#main" ).height() }, self.speed ).animate( { opacity:1 }, self.speed );
        },
        tryCount: 0,
        retryLimit: 5,
        error: function( xhr, textStatus, errorThrown ) {
          this.tryCount++;
          if( this.tryCount <= this.retryLimit ){
            console.log( 'Ajax retry: ' + this.tryCount );
            setTimeout( function(){ $.ajax( this ); }, 500 );
            return;
          }
          return;
        }
      } );
    }, self.speed );
  };
  
  this.autocomplete = function( divlist, object, field, search, target ){
    var self = this;
    $.ajax({
      url: self.script + "ajax.php",
      type: 'GET',
      data: {
        root: self.root, 
        module: self.module,
        lang: self.lang, 
        fingerprint: self.fingerprint, 
        ask: 'autocomplete',
        object: object,
        field: field,
        search: search,
        target: target
      },
      success: function( html ) {
        $('#' + divlist).html( html );
        $("#wrapper").animate( { height:$("#main").height() }, self.speed ).animate({ opacity:1 }, self.speed );
      },
      tryCount: 0,
      retryLimit: 5,
      error: function( xhr, textStatus, errorThrown ){
        this.tryCount++;
        if( this.tryCount <= this.retryLimit ){
          console.log( 'Ajax retry: ' + this.tryCount );
          setTimeout( function(){ $.ajax( this ); }, 500 );
          return;
        }
        return;
      }
    } );
  };
  
  this.playHoverSound = function(){
    /*
    audioElement = document.getElementById( "audiohover" );
    if( audioElement ){      
      audioElement.currentTime=0;
      audioElement.play();
    }
    */
  };
  
  this.playClickSound = function(){
    /*
    audioElement = document.getElementById( "audioclick" );
    if( audioElement ){      
      audioElement.currentTime=0;
      audioElement.play();
    }
    */
  };
  
  this.playPostSound = function(){
    /*
    audioElement = document.getElementById( "audiopost" );
    if( audioElement ){      
      audioElement.currentTime=0;
      audioElement.play();
    }
    */
  };
  
  this.showMissionPlace = function( placeid ){
    var self = this;
    $( "#maplist-wrapper" ).animate( { opacity:0 } , self.speed );
    setTimeout( function(){
      $.ajax( {
        url: self.script + "ajax.php",
        type: 'GET',
        data: {
          root: self.root, 
          module: self.module,
          lang: self.lang, 
          fingerprint: self.fingerprint, 
          ask: "missionmaplist",
          place: placeid
        },
        success: function( html ) {
          $( "#maplist" ).html( html );
          $( "#maplist" ).height( $( "#maplist" ).width() );
          $( "#maplist-wrapper" ).animate( { height:$( "#maplist" ).height() }, self.speed).animate( { opacity:1 }, self.speed );
          setTimeout( function(){ $( "#wrapper" ).animate( { height:$( '#main' ).height() }, self.speed ); }, self.speed );
        },
        tryCount: 0,
        retryLimit: 5,
        error: function( xhr, textStatus, errorThrown ) {
          this.tryCount++;
          if( this.tryCount <= this.retryLimit ) {
            console.log( 'Ajax retry: ' + this.tryCount );
            setTimeout( function(){ $.ajax( this ); }, 500 );
            return;
          }          
          return;
        }
      } );
    }, self.speed );
  };
  
  this.toggleMissionPic = function( image ){
    setTimeout( function(){
      $( "#maplist" ).css( "background-image", "url(" + image + ")" );
    }, this.speed );
  };
  
}

function CharacterGen( script, root, module, lang, fingerprint, targetdiv, targetrace, targetclass, race, gender, body, hair, head, face, charclass ){ 
  this.script = script;
  this.root = root;
  this.module = module;
  this.lang = lang;
  this.fingerprint = fingerprint;
  this.speed = 250;
  this.race = race;
  this.gender = gender;
  this.body = body;
  this.head = head;
  this.hair = hair;
  this.face = face;
  this.appearance = Array();
  this.charclass = charclass;
  this.targetdiv = targetdiv;
  this.targetrace = targetrace;
  this.targetclass = targetclass;
    
  this.loadRace = function( race, body, hair, face, head ){
    this.race = race;
    this.body = body;
    this.hair = hair;
    this.face = face;
    this.head = head;
    $( '#inprace' ).val( race );
    $( '.genicon.race' ).attr( 'class', 'genicon race' );
    $( '#ra' + race ).attr( 'class', 'genicon race highlighted' );
    $( '#inphair' ).val( hair );
    $( '#inpface' ).val( face );
    $( '#inphead' ).val( head );
    $( '#inpbody' ).val( body );
    this.loadGraphics();
  };
  
  this.loadGender = function( gender ){
    this.gender = gender;
    this.loadGraphics();
  };
  
  this.loadClass = function( charclass ){
    this.charclass = charclass;
    $( '#inpclass' ).val( charclass );
    $( '.genicon.class' ).attr( 'class', 'genicon class' );
    $( '#cl' + charclass ).attr( 'class', 'genicon class highlighted' );
    this.loadGraphics();
  };
  
  this.previousAppearance = function ( type ){
    var value = 0;
    var index = 0;
    if( typeof this.appearance[type] !== 'undefined' ){
      switch( type ){
        case 'hair': index = jQuery.inArray( this.hair, this.appearance[type] ); break;
        case 'face': index = jQuery.inArray( this.face, this.appearance[type] ); break;
        case 'head': index = jQuery.inArray( this.head, this.appearance[type] ); break;
        case 'body': index = jQuery.inArray( this.body, this.appearance[type] ); break;
      }
      if( index >= 0 && typeof this.appearance[type][index - 1] !== 'undefined' ){
        value = this.appearance[type][index - 1];
      }else if ( typeof this.appearance[type][this.appearance[type].length - 1] !== 'undefined' ){
        value = this.appearance[type][this.appearance[type].length - 1];
      }else if( typeof this.appearance[type][0] !== 'undefined' ){
        value = this.appearance[type][0];
      }
      switch( type ){
        case 'hair': this.hair = value; break;
        case 'face': this.face = value; break;
        case 'head': this.head = value; break;
        case 'body': this.body = value; break;
      }
    }
    $( '#inp' + type ).val( value );
    this.loadGraphics();
  };
  
  this.nextAppearance = function ( type ){
    var value = 0;
    var index = 0;
    if( typeof this.appearance[type] !== 'undefined' ){
      switch( type ){
        case 'hair': index = jQuery.inArray( this.hair, this.appearance[type] ); break;
        case 'face': index = jQuery.inArray( this.face, this.appearance[type] ); break;
        case 'head': index = jQuery.inArray( this.head, this.appearance[type] ); break;
        case 'body': index = jQuery.inArray( this.body, this.appearance[type] ); break;
      }
      if( index >= 0 && typeof this.appearance[type][index + 1] !== 'undefined' ){
        value = this.appearance[type][index + 1];
      }else if( typeof this.appearance[type][0] !== 'undefined' ){
        value = this.appearance[type][0];
      }
      switch( type ){
        case 'hair': this.hair = value; break;
        case 'face': this.face = value; break;
        case 'head': this.head = value; break;
        case 'body': this.body = value; break;
      }
    }
    $( '#inp' + type ).val( value );
    this.loadGraphics();
  };
  
  this.loadGraphics = function(){    
    var self = this;
    document.getElementById( 'character' ).className = 'divchar waiting';
    $( '#character' ).html( '' );
    $.ajax( {
      url: self.script + "ajax.php",
      type: 'GET',
      data: {
        root: self.root, 
        module: self.module,
        lang: self.lang, 
        fingerprint: self.fingerprint, 
        ask: "chargen",
        race: self.race,
        gender: self.gender,
        body: self.body,
        hair: self.hair,
        head: self.head,
        face: self.face,
        charclass: self.charclass
      },
      success: function( data ) {
        document.getElementById( 'character' ).className = 'divchar';
        try {
          var response = jQuery.parseJSON( data );
          var stats = response['stats'];
          self.appearance = response['appearance'];
          var tablehtml = '<table>';
          for( stat in stats ){
            statbarhtml = '<div class="bar">';
            statbarhtml += '<div class="bar-green" style="width:' + stats[stat]['value'] + '%"></div>';
            statbarhtml += '</div>';
            tablehtml += '<tr><th class="stat">' + stats[stat]['name'] + '</th><td class="value">' + statbarhtml + '</td></tr>';
          }
          tablehtml += '</table>';
          $( self.targetdiv ).html( response['charhtml'] );
          $( "#chargen-portrait" ).html( response['porthtml'] );
          if( self.charclass != 0 ){
            $( self.targetrace ).html( response['chardesc'] + tablehtml + response['racedesc'] );
          }else{
            if( self.race != 0 ){
              $( self.targetrace ).html( response['chardesc'] + response['racedesc'] );
            }
          }
          $( self.targetclass ).html( response['classdesc'] );
          $( "#wrapper" ).animate( { height:$( "#main" ).height() }, self.speed ).animate( { opacity:1 }, self.speed );
        }catch( error ){
          console.log( error.message );
          console.log( data );
        }
      },
      tryCount: 0,
      retryLimit: 5,
      error: function( xhr, textStatus, errorThrown ) {
        this.tryCount++;
        if( this.tryCount <= this.retryLimit ) {
          console.log( 'Ajax retry: ' + this.tryCount );
          setTimeout( function(){ $.ajax( this ); }, 500 );
          return;
        }
        return;
      }
    } );
  };
  
  this.loadRandomName = function( input ){
    var self = this;
    $.ajax( {
      url: self.script + "ajax.php",
      type: 'GET',
      data: {
        root: self.root, 
        module: self.module,
        lang: self.lang, 
        fingerprint: self.fingerprint, 
        ask: "randomname",
        gender: self.gender,
        race: self.race
      },
      success: function( data ) {
        try{
          input.val( data );
        }catch( error ){
          console.log( error.message );
          console.log( data );
        }
      },
      tryCount: 0,
      retryLimit: 5,
      error: function( xhr, textStatus, errorThrown ){
        this.tryCount++;
        if( this.tryCount <= this.retryLimit ){
          console.log( 'Ajax retry: ' + this.tryCount );
          setTimeout( function(){ $.ajax( this ); }, 500 );
          return;
        }
        return;
      }
    });
  };

}


function MapEditor( map ){
  
  this.mouseDown = false;
  this.graphic = '0';
  this.level = '0';
  this.weather = '-1';
  this.door = false;
  this.sprite = false;
  this.size = 1;
  this.map = map;
  
  this.select = function( gr, lv, pic ){
    this.graphic = gr;
    this.level = lv;
    this.weather = '-1';
    this.door = false;
    this.sprite = false;
    document.getElementById( "brush" ).src = pic;
  };
  
  this.setWeather = function( weather, pic ){
    this.weather = weather;
    this.door = false;
    this.sprite = false;
    document.getElementById( "brush" ).src = pic;
  };
  
  this.setDoor = function( gr, pic ){
    this.graphic = gr;
    this.weather = '-1';
    this.door = true;
    this.sprite = false;
    document.getElementById( "brush" ).src = pic;
  };
  
  this.setSprite = function( gr, pic ){
    this.graphic = gr;
    this.weather = '-1';
    this.door = false;
    this.sprite = true;
    document.getElementById( "brush" ).src = pic;
  };
  
  this.updateSquare = function( acord, bcord, lev, gra, pic, click ){
    click = typeof click !== 'undefined' ? click : false;
    if( ( this.mouseDown || click ) ){
      if( this.weather == '-1' && !this.door && !this.sprite ){
        document.getElementById( "sq" + acord + "," + bcord ).style.backgroundImage = "url(" + pic + ")";
        var seclass = '';
        switch( this.level ){
          case '0': seclass = 'efloor'; break;
          case '1': seclass = 'epit'; break;
          case '2': seclass = 'ewall'; break;
        }
        document.getElementById( "sq" + acord + "," + bcord ).className = 'esquare ' + seclass;
        this.map.level[acord][bcord] = lev;
        this.map.graph[acord][bcord] = gra;
        var txtlevel = "";
        var txtgraph = "";
        var txtweather = "";
        for( i = 0; i < this.map.level.length; i++ ){
          for( j = 0; j < this.map.level[i].length; j++ ){
            txtlevel = txtlevel + this.map.level[i][j];
            txtgraph = txtgraph + this.map.graph[i][j];
            txtweather = txtweather + this.map.weather[i][j];
            if( j < ( this.map.level[i].length - 1 ) ){
              txtlevel = txtlevel + ".";
              txtgraph = txtgraph + ".";
            }
          }
          if( i < ( this.map.level.length - 1 ) ){
            txtlevel = txtlevel + ":";
            txtgraph = txtgraph + ":";
          }
        }
        document.getElementById( "inplevel" ).value = txtlevel;
        document.getElementById( "inpgraph" ).value = txtgraph;
      }else{
        if( this.door && click){ 
          if( this.map.doors[acord][bcord] == 0 ){
            this.map.doors[acord][bcord] = this.graphic;
            document.getElementById( "dt" + acord + "," + bcord ).style.backgroundImage = "url(" + pic + ")";
            document.getElementById( "dt" + acord + "," + bcord ).className = "esquare edoor";
          }else{
            this.map.doors[acord][bcord] = 0;
            document.getElementById( "dt" + acord + "," + bcord ).className = "";
          }
          var txtdoors = "";
          for( i = 0; i < this.map.level.length; i++ ){
            for( j = 0; j < this.map.level[i].length; j++ ){
              txtdoors = txtdoors + this.map.doors[i][j];
              if( j < ( this.map.level[i].length - 1 ) ){
                txtdoors = txtdoors + ".";
              }
            }
            if( i < ( this.map.level.length - 1 ) ){ txtdoors = txtdoors + ":"; }
          }
          document.getElementById( "inpdoors" ).value = txtdoors;
        }else if( this.sprite && click ){
          if( this.map.sprites[acord][bcord] == 0 ){
            this.map.sprites[acord][bcord] = this.graphic;
            document.getElementById( "st" + acord + "," + bcord ).style.backgroundImage = "url(" + pic + ")";
            document.getElementById( "st" + acord + "," + bcord ).className = "esquare esprite";
          }else{
            this.map.sprites[acord][bcord] = 0;
            document.getElementById( "st" + acord + "," + bcord ).className = "";
          }
          var txtsprites = "";
          for( i = 0; i < this.map.level.length; i++ ){
            for( j = 0; j < this.map.level[i].length; j++ ){
              txtsprites = txtsprites + this.map.sprites[i][j];
              if( j < ( this.map.level[i].length - 1 ) ){
                txtsprites = txtsprites + ".";
              }
            }
            if( i < ( this.map.level.length - 1 ) ){ txtsprites = txtsprites + ":"; }
          }
          document.getElementById( "inpsprites" ).value = txtsprites;
        }else if( this.wheather != '-1' && ( this.mouseDown || click ) ){
          this.map.weather[acord][bcord] = this.weather;
          switch( this.weather ){
            case '0':
              document.getElementById( "wt" + acord + "," + bcord ).className = "";
            break;
            case '1':
              document.getElementById( "wt" + acord + "," + bcord ).className = "eweather erain";
            break;
            case '2':
              document.getElementById( "wt" + acord + "," + bcord ).className = "eweather esnow";
            break;
          }
          var txtweather = "";
          for( i = 0; i < this.map.level.length; i++ ){
            for( j = 0; j < this.map.level[i].length; j++ ){
              txtweather = txtweather + this.map.weather[i][j];
              if( j < ( this.map.level[i].length - 1 ) ){
                txtweather = txtweather + ".";
              }
            }
            if( i < ( this.map.level.length - 1 ) ){ txtweather = txtweather + ":"; }
          }
          document.getElementById( "inpweather" ).value = txtweather;
        }
      }
    }
  };
  
  this.increaseZoom = function( speed ){
    this.size = this.size + 0.25;
    $("#map").animate({ fontSize: this.size.toFixed(2) + "em" }, speed);
  };
  
  this.decreaseZoom = function( speed ){
    this.size = this.size - 0.25;
    if( this.size < 0.25 ){
      this.size = 0.25;
    }
    $("#map").animate({ fontSize: this.size.toFixed(2) + "em" }, speed);
  };
  
}

function Mission( scenario, character, script, root, module, lang, fingerprint ){
  
  this.scenario = scenario;
  this.character = character;
  this.script = script;
  this.root = root;
  this.module = module;
  this.lang = lang;
  this.fingerprint = fingerprint;
  this.logic = Array();
  this.data = Array();
  this.pendingdata = false;
  this.pendingaction = false;
  this.size = 1;
  this.focused = false;
  this.walls = true;
  this.speed = 250;
  this.debug = true;
  this.mapdata = '';
  this.scLeft = false;
  this.scUp = false;
  this.scRight = false;
  this.scDown = false;
  
  this.getLogic = function(){
    var self = this;
    if( !self.pendingdata ){
      if( self.debug ){ console.log( 'Fetching logic: scenario ' + self.scenario + ', character ' + self.character + ' ...' ); }
      self.pendingdata = true;
      $.ajax({
        url: self.script + "ajax.php",
        type: 'GET',
        data: {
          root: self.root, 
          module: self.module,
          lang: self.lang, 
          fingerprint: self.fingerprint, 
          ask: "missionlogic",
          scenario: self.scenario
        },
        success: function( data ) {
          if( self.debug ){ console.log( 'Logic obtained' ); }
          self.pendingdata = false;
          try {
            self.logic = JSON.parse( data );
            self.getData();
          }catch ( error ){
            console.log( error.message );
            console.log( data );
          }
        },
        tryCount: 0,
        retryLimit: 5,
        error: function( xhr, textStatus, errorThrown ) {
          this.tryCount++;
          if( this.tryCount <= this.retryLimit ) {
            console.log( 'Ajax retry: ' + this.tryCount );
            setTimeout( function(){ $.ajax( this ); }, 500 );
            return;
          }
          self.pendingdata = false;
          return;
        }
      });
    }else{
      if( self.debug ){ console.log( 'Expecting data: scenario ' + self.scenario + ', character ' + self.character + ' ...' ); }
    }
  };
  
  this.getData = function(){
    var self = this;
    if( !self.pendingdata ){
      if( self.debug ){ console.log( 'Fetching data: scenario ' + self.scenario + ', character ' + self.character + ' ...' ); }
      self.pendingdata = true;
      $.ajax({
        url: self.script + "ajax.php",
        type: 'GET',
        data: {
          root: self.root, 
          module: self.module,
          lang: self.lang, 
          fingerprint: self.fingerprint, 
          ask: "missiondata",
          scenario: self.scenario,
          character: self.character
        },
        success: function( data ) {
          if( self.debug ){ console.log( 'Data obtained' ); }
          self.pendingdata = false;
          try {
            self.data = JSON.parse( data );
          }catch ( error ){
            console.log( error.message );
            console.log( data );
          }
          self.update();
        },
        tryCount: 0,
        retryLimit: 5,
        error: function( xhr, textStatus, errorThrown ) {
          this.tryCount++;
          if( this.tryCount <= this.retryLimit ) {
            console.log( 'Ajax retry: ' + this.tryCount );
            setTimeout( function(){ $.ajax( this ); }, 500 );
            return;
          }
          self.pendingdata = false;
          return;
        }
      });
    }else{
      if( self.debug ){ console.log( 'Expecting data: scenario ' + self.scenario + ', character ' + self.character + ' ...' ); }
    }
  };
    
  this.update = function(){
    this.renderMap();
    this.renderHud();
    if( !this.focused ){
      this.focusTo( this.data['character']['x'], this.data['character']['y'] );
      this.focused = true;
    }
  };
  
  this.renderMap = function(){
    var y;
    var x;
    var maphtml = '';
    var minimaphtml = '';
    var squz = 99;
    var rowz = 100;
    var zindex = 0;
    for( y in this.logic['level'] ){
      for( x in this.logic['level'][y] ){
        if( this.data['view'][y][x] == 2 && this.logic['doors'][y][x] == 0 || this.logic['level'][y][x] == 2 ){
          for( a = -1; a <= 1; a++ ){
            for( b = -1; b <= 1; b++ ){
              if( this.logic['level'][y][x] == 2 ){
                if( typeof( this.data['view'][parseInt(y) + a] ) !== 'undefined'
                && typeof( this.data['view'][parseInt(y) + a][parseInt(x) + b] ) !== 'undefined' 
                && this.data['view'][parseInt(y) + a][parseInt(x) + b] == 1
                && typeof( this.logic['level'][parseInt(y) + a] ) !== 'undefined'
                && typeof( this.logic['level'][parseInt(y) + a][parseInt(x) + b] ) !== 'undefined' 
                && this.logic['level'][parseInt(y) + a][parseInt(x) + b] < 2 ){
                  this.data['view'][y][x] = 1;
                }
              }else{
                if( typeof( this.data['view'][parseInt(y) + a] ) !== 'undefined'
                && typeof( this.data['view'][parseInt(y) + a][parseInt(x) + b] ) !== 'undefined' 
                && this.data['view'][parseInt(y) + a][parseInt(x) + b] == 0 ){
                  this.data['view'][parseInt(y) + a][parseInt(x) + b] = 1;
                }
              }
            }
          }
        }
      }
    }
    for( y in this.logic['level'] ){
      maphtml += '<div class="maprow" style="width:' + ( 4.5 * this.logic['level'][y].length ) + 'em;margin-left:' + ( y * 3 ) + 'em">';
      minimaphtml += '<div class="minimaprow" style="width:' + this.logic['level'][y].length + 'em">';
      for( x in this.logic['level'][y] ){
        var entity = false;
        var sprite = false;
        if( typeof( this.data['entities'][y] ) !== 'undefined' 
        && typeof( this.data['entities'][y][x] ) !== 'undefined' ){
          var entity = true;
        }
        if( typeof( this.logic['sprites'][y] ) !== 'undefined' 
        && typeof( this.logic['sprites'][y][x] ) !== 'undefined' 
        && this.logic['sprites'][y][x] != '0' ){
          var sprite = true;
        }
        if( typeof( this.logic['textures'][this.logic['graph'][y][x]] ) === 'undefined' ){
          var texture = this.logic['textures'][0];
        }else{
          var texture = this.logic['textures'][this.logic['graph'][y][x]];
        }
        zindex = squz + rowz;
        maphtml += '<div class="mapsquare l' + this.logic['level'][y][x] + '" id="' + [x] + ',' + [y] + '" style="z-index:' + zindex + '">';
        if( this.data['view'][y][x] > 0 ){
          switch( this.logic['level'][y][x] ){
            case '0':  //Floor
              maphtml += '<div class="floor" style="background-image:url(' + "'" + texture + "'" + ')">';
              minimaphtml += '<div class="mini-floor">';
              if( this.data['movement'][y][x] > 0 && !entity && !sprite ){
                maphtml += '<div class="moveto" onclick="mission.moveTo(' + x + ',' + y + ')">';
                maphtml += '<span class="turnaction">-' + this.data['movement'][y][x] + '</span>';
                maphtml += '</div>';
              }
              if( this.data['view'][y][x] == 1 ){
                maphtml += '<div class="shadow sview"></div>';
              }              
              maphtml += '</div>';
              if( this.logic['doors'][y][x] != 0 ){
                if( typeof( this.logic['textures'][this.logic['doors'][y][x]] ) === 'undefined' ){
                  var doortex = this.logic['textures'][0];
                }else{
                  var doortex = this.logic['textures'][this.logic['doors'][y][x]];
                }
                var clickevent = "mission.askInteraction('door'," + x + "," + y +")";
                if( typeof( this.logic['level'][y][parseInt(x) - 1] ) !== 'undefined'&& typeof( this.logic['level'][y][parseInt(x) + 1] ) !== 'undefined' 
                && this.logic['level'][y][parseInt(x) - 1] == '2' && this.logic['level'][y][parseInt(x) + 1] == '2' ){
                  if( this.data['doorslog'][y][x] == '2' ){
                    maphtml += '<div class="fdoor" style="background-image:url(' + "'" + doortex + "'" + ')" onclick="' + clickevent + '">';
                    var framecss = ' style="left:1em;height:2em"';
                    if( parseInt( this.data['character']['y'] ) <= y ){
                      maphtml += '<div class="shadow sview"></div>';
                      var framecss = '';
                      var floorshadow = '<div class="sview fdoorshadow"></div>';
                    }else{
                      var framecss = ' style="left:1em;height:2em"';
                      var floorshadow = '';
                    }
                    maphtml += '</div>';
                    if( this.data['view'][y][x] == 2 ){
                      maphtml += floorshadow;
                    }
                  }else{
                    maphtml += '<div class="fdoor-opleft" style="background-image:url(' + "'" + doortex + "'" + ')" onclick="' + clickevent + '">';
                    if( parseInt( this.data['character']['y'] ) <= y || this.data['view'][y][x] == 1 ){
                      maphtml += '<div class="shadow sview"></div>';
                    }
                    maphtml += '</div>';
                    maphtml += '<div class="fdoor-opright" style="background-image:url(' + "'" + doortex + "'" + ')" onclick="' + clickevent + '">';
                    if( parseInt( this.data['character']['y'] ) <= y || this.data['view'][y][x] == 1 ){
                      maphtml += '<div class="shadow sview"></div>';
                    }
                    maphtml += '</div>';
                    maphtml += '<div class="fdoor-oprightp"></div>';
                    var framecss = '';
                  }
                  maphtml += '<div class="fframe sblack"' + framecss + ' style="z-index:' + ( zindex + 1 ) + '"></div>';
                }else{
                  if( this.data['doorslog'][y][x] == '2' ){
                    maphtml += '<div class="ldoor" style="background-image:url(' + "'" + doortex + "'" + ')" onclick="' + clickevent + '">';
                    if( parseInt( this.data['character']['x'] ) >= x ){
                      maphtml += '<div class="shadow sview"></div>';
                      var framecss = '';
                      var floorshadow = '<div class="sview ldoorshadow"></div>';
                    }else{
                      var framecss = ' style="width:3em"';
                      var floorshadow = '';
                    }
                    maphtml += '</div>';
                    if( this.data['view'][y][x] == 2 ){
                      maphtml += floorshadow;
                    }
                  }else{
                    maphtml += '<div class="ldoor-optop" style="background-image:url(' + "'" + doortex + "'" + ')" onclick="' + clickevent + '">';
                    if( parseInt( this.data['character']['x'] ) >= x || this.data['view'][y][x] == 1 ){
                      maphtml += '<div class="shadow sview"></div>';
                    }
                    maphtml += '</div>';
                    maphtml += '<div class="ldoor-optopp"></div>';
                    maphtml += '<div class="ldoor-opbottom" style="background-image:url(' + "'" + doortex + "'" + ')" onclick="' + clickevent + '">';
                    if( parseInt( this.data['character']['x'] ) >= x || this.data['view'][y][x] == 1 ){
                      maphtml += '<div class="shadow sview"></div>';
                    }
                    maphtml += '</div>';
                    var framecss = '';
                  }
                  maphtml += '<div class="lframe sblack"' + framecss + ' style="z-index:' + ( zindex + 1 ) + '"></div>';
                }
              }
            break;
            case '1':  //Pit
              minimaphtml += '<div class="mini-pit">';
              if( typeof( this.logic['level'][parseInt(y) - 1] ) !== 'undefined' && this.logic['level'][parseInt(y) - 1][x] != '1' ){
                maphtml += '<div class="rpit" style="background-image:url(' + "'" + texture + "'" + ')"><div class="shadow spit">';
                if( this.data['view'][y][x] == 1 ){
                  maphtml += '<div class="shadow sview"></div>';
                }
                maphtml += '</div></div>';
              }
              if( typeof( this.logic['level'][y] ) !== 'undefined' && this.logic['level'][y][parseInt(x) + 1] != '1' ){
                maphtml += '<div class="lpit" style="background-image:url(' + "'" + texture + "'" + ')"><div class="shadow spit">';
                if( this.data['view'][y][x] == 1 ){
                  maphtml += '<div class="shadow sview"></div>';
                }
                maphtml += '</div></div>';
              }
            break;
            case '2':  //Wall
              minimaphtml += '<div class="mini-wall">';
              if( ( typeof( this.logic['level'][parseInt(y) + 1] ) !== 'undefined' && this.logic['level'][parseInt(y) + 1][x] != '2' )
              || typeof( this.logic['level'][parseInt(y) + 1] ) === 'undefined' ){
                if( ( parseInt( this.data['character']['y'] ) <= y  && this.data['view'][y][x] == 0 ) 
                || typeof( this.logic['level'][parseInt(y) + 1] ) === 'undefined' || ( typeof( this.data['view'][parseInt(y) + 1] ) !== 'undefined' 
                && this.data['view'][parseInt(y) + 1][x] == 0 ) ){
                  maphtml += '<div class="fwall sblack">';
                }else{
                  maphtml += '<div class="fwall" style="background-image:url(' + "'" + texture + "'" + ')"><div class="shadow swallf"></div>';
                }
                maphtml += '</div>';
              }
              if( ( typeof( this.logic['level'][y] ) !== 'undefined'
              && this.logic['level'][y][parseInt(x) - 1] != '2' )
              || typeof( this.logic['level'][y][parseInt(x) - 1] ) === 'undefined' ){
                if( ( parseInt( this.data['character']['x'] ) >= x && this.data['view'][y][x] == 0 ) 
                || typeof( this.logic['level'][y][parseInt(x) - 1] ) === 'undefined' || ( typeof( this.data['view'][y][parseInt(x) - 1] ) !== 'undefined' 
                && this.data['view'][y][parseInt(x) - 1] == 0 ) ){
                  maphtml += '<div class="lwall sblack">';
                }else{
                  maphtml += '<div class="lwall" style="background-image:url(' + "'" + texture + "'" + ')"><div class="shadow swalll"></div>';
                }
                maphtml += '</div>';
              }
              maphtml += '<div class="twall"></div>';
            break;
          }
          if( this.data['view'][y][x] == 2 ){
            if( entity ){  //Entity placed here
              maphtml += '<div class="entity">' + this.data['entities'][y][x] + '</div>';
              if( this.data['character']['y'] == y
              && this.data['character']['x'] == x ){
                minimaphtml += '<div class="mini-entity player"></div>';
              }else{
                minimaphtml += '<div class="mini-entity character"></div>';
              }
            }
            if( this.logic['sprites'][y][x] != 0 ){  //Sprite placed here
              var texture = this.logic['textures'][this.logic['sprites'][y][x]];
              maphtml += '<div class="sprite" style="background-image:url(' + "'" + texture + "'" + ')"></div>';
            }
            if( this.logic['level'][y][x] < 2 ){
              switch( this.logic['weather'][y][x] ){  //Weather effects
                case '1':
                  maphtml += '<div class="rain"></div>';
                break;
                case '2':
                  maphtml += '<div class="snow"></div>';
                break;
              }
            }
          }
        }else{
          minimaphtml += '<div class="mini-black">';
          switch( this.logic['level'][y][x] ){  //Black squares
            case '0':  //Black floor
              maphtml += '<div class="floor sblack"></div>';
            break;
            case '1':  //Black pit
              if( typeof( this.logic['level'][parseInt(y) - 1] ) !== 'undefined' 
              && this.logic['level'][parseInt(y) - 1][x] != '1' ){
                maphtml += '<div class="rpit sblack"></div>';
              }
              if( typeof( this.logic['level'][y] ) !== 'undefined'
              && this.logic['level'][y][parseInt(x) + 1] != '1' ){
                maphtml += '<div class="lpit sblack"></div>';
              }
            break;
            case '2':  //Black wall
              if( ( typeof( this.logic['level'][parseInt(y) + 1] ) !== 'undefined' 
              && this.logic['level'][parseInt(y) + 1][x] != '2' )
              || typeof( this.logic['level'][parseInt(y) + 1] ) === 'undefined' ){
                maphtml += '<div class="fwall sblack"></div>';
              }
              if( typeof( this.logic['level'][y] ) !== 'undefined'
              && this.logic['level'][y][parseInt(x) - 1] != '2' ){
                maphtml += '<div class="lwall sblack"></div>';
              }
              maphtml += '<div class="twall"></div>';
            break;
          }
        }
        maphtml += '</div>';
        minimaphtml += '</div>';
        squz = squz - 1;
      }
      squz = 99;
      rowz = rowz + 100;
      maphtml += '</div>';
      minimaphtml += '</div>';
    }
    var topl =  9 + ( 3 * this.data['character']['y'] ) - 150;
    var leftl = 3 + 3 * this.data['character']['y'] + ( 4.5 * this.data['character']['x'] ) - 225;
    maphtml += '<div id="lighting" style="top:' + topl + 'em;left:' + leftl + 'em"></div>';
    if( this.debug ){ console.log( 'Updating map' ); }
    this.mapdata = maphtml;
    $( "#map" ).html( maphtml );
    $( "#minimap" ).html( minimaphtml );
  };
  
  this.renderHud = function(){
    var hudhtml = '<div class="maptools">'
    + '<input type="button" class="inczoom" value="+" onclick="mission.increaseZoom()">'
    + '<input type="button" class="deczoom" value="-" onclick="mission.decreaseZoom()">'
    + '<input type="button" class="walltoggle" value="W" onclick="mission.toggleWalls()">'
    + '</div>';
    hudhtml += '<div class="activechar">'
    + '<div class="charportrait" onclick="mission.focusTo(' + this.data['character']['x'] + ',' + this.data['character']['y'] + ')">' 
    + '<div class="inner">' 
    + this.data['character']['portrait'] + '</div></div>'
    + this.data['character']['bars'] 
    + '</div>' 
    + '<div class="charoverview">' + this.data['character']['overview'] + '</div>';
    if( this.data['character']['active'] ){
      hudhtml += '<div class="actions">';
      hudhtml += this.data['actionform'];
      hudhtml += '</div>';
    }else{
      hudhtml += '<div class="actions">';
      hudhtml += this.data['actionform'];
      hudhtml += '</div>';
    }
    if( this.debug ){ console.log( 'Updating hud' ); }
    this.hudHtml = hudhtml;
    $("#mission-hud").html( hudhtml );
  };
    
  this.increaseZoom = function(){
    var increase = 1.25;
    var top = $( "#missionmap" ).scrollTop();
    var left = $( "#missionmap" ).scrollLeft();
    this.size = this.size * increase;
    $("#map").animate({ fontSize: mission.size + "em" }, this.speed);
    $("#missionmap").animate({ 
      scrollTop: ( top * increase + $( "#missionmap" ).height() * 0.125 ),
      scrollLeft: ( left * increase + $( "#missionmap" ).width() * 0.125 )
    }, this.speed);
  };
  
  this.decreaseZoom = function(){
    var decrease = 0.75;
    var top = $( "#missionmap" ).scrollTop();
    var left = $( "#missionmap" ).scrollLeft();
    this.size = this.size * decrease;
    $( "#map" ).animate( { fontSize: this.size + "em" }, this.speed );
    $( "#missionmap" ).animate( { 
      scrollTop: ( top * decrease - $( "#missionmap" ).height() * 0.125 ),
      scrollLeft: ( left * decrease - $( "#missionmap" ).height() * 0.125 )
    }, this.speed);
  };
  
  this.focusTo = function( x, y ){
    if( this.debug ){ console.log( 'Focusing camera to (' + x + ',' + y + ')' ); }
    pxEm = parseFloat( $( "#map" ).css( "font-size" ) );
    var top = ( y * 3 ) * pxEm - ( $( "#missionmap" ).height() / 2  - 7 * pxEm ) + $( "#mission-hud" ).height();
    var left = ( x * 4.5 + y * 3 ) * pxEm - ( $( "#missionmap" ).width() / 2  - 4.5 * pxEm );
    $( "#missionmap" ).animate({ 
      scrollTop: top,
      scrollLeft: left 
    }, this.speed);
  };

  this.perform = function( action ){
    var self = this;
    if( !self.pendingaction ){
      if( self.debug ){ console.log( 'Executing: (' + action + ')' ); }
      self.pendingaction = true;
      $.ajax( {
        url: self.script + "ajax.php?root=" + self.root + "&module=" + self.module + "&lang=" + self.lang + "&fingerprint=" + self.fingerprint + "&ask=missionaction",
        type: 'POST',
        data: {
          scenario: self.scenario,
          character: self.character,
          action: action
        },
        success: function( data ){
          self.pendingaction = false;
          try {
            if( self.debug ){ console.log( data ); }
            $( '#gamemsg' ).html( data );
            self.getData();
          }catch ( error ){
            console.log( error.message );
            console.log( data );
          }
        },
        tryCount: 0,
        retryLimit: 5,
        error: function( xhr, textStatus, errorThrown ) {
          this.tryCount++;
          if( this.tryCount <= this.retryLimit ) {
            console.log( 'Ajax retry: ' + this.tryCount );
            setTimeout( function(){ $.ajax( this ); }, 500 );
            return;
          }
          self.pendingaction = false;
          return;
        }
      } );
    }
  };
  
  this.moveTo = function( x2, y2 ){
    var self = this;
    if( !self.pendingaction ){
      if( self.debug ){ console.log( 'Moving character ' + self.character + ' to (' + x2 + ', ' + y2 + ')' ); }
      self.pendingaction = true;
      $.ajax( {
        url: self.script + "ajax.php?root=" + self.root + "&module=" + self.module + "&lang=" + self.lang + "&fingerprint=" + self.fingerprint + "&ask=missionaction",
        type: 'POST',
        data: {
          scenario: self.scenario,
          character: self.character,
          action: 'move',
          x1: self.data['character']['x'],
          y1: self.data['character']['y'],
          x2: x2,
          y2: y2
        },
        success: function( data ){
          self.pendingaction = false;
          try {
            if( self.debug ){ console.log( data ); }
            $( '#gamemsg' ).html( data );
            self.getData();
            self.focusTo( x2, y2 );
          }catch ( error ){
            console.log( error.message );
            console.log( data );
          }
        },
        tryCount: 0,
        retryLimit: 5,
        error: function( xhr, textStatus, errorThrown ) {
          this.tryCount++;
          if( this.tryCount <= this.retryLimit ) {
            console.log( 'Ajax retry: ' + this.tryCount );
            setTimeout( function(){ $.ajax( this ); }, 500 );
            return;
          }
          self.pendingaction = false;
          return;
        }
      } );
    }else{
      if( self.debug ){ console.log( 'Expecting movement...' ); }
    }
  };
  
  this.doorAction = function( x, y ){
    var self = this;
    if( !self.pendingaction ){
      if( self.debug ){ console.log( 'Interacting with door at (' + x + ', ' + y + ')' ); }
      self.pendingaction = true;
      $.ajax( {
        url: self.script + "ajax.php?root=" + self.root + "&module=" + self.module + "&lang=" + self.lang + "&fingerprint=" + self.fingerprint + "&ask=missionaction",
        type: 'POST',
        data: {
          scenario: self.scenario,
          character: self.character,
          action: 'door',
          x: x,
          y: y
        },
        success: function( data ){
          self.pendingaction = false;
          try {
            if( self.debug ){ console.log( data ); }
            $( '#gamemsg' ).html( data );
            self.focusTo( x, y );
            self.getData();
          }catch ( error ){
            console.log( error.message );
            console.log( data );
          }
        },
        tryCount: 0,
        retryLimit: 5,
        error: function( xhr, textStatus, errorThrown ) {
          this.tryCount++;
          if( this.tryCount <= this.retryLimit ) {
            console.log( 'Ajax retry: ' + this.tryCount );
            setTimeout( function(){ $.ajax( this ); }, 500 );
            return;
          }
          self.pendingaction = false;
          return;
        }
      } );
    }else{
      if( self.debug ){ console.log( 'Expecting door interaction...' ); }
    }
  };
  
  this.askInteraction = function( type, x, y ){
    var self = this;
    if( !self.pendingaction ){
      if( self.debug ){ console.log( 'Asked for interaction: ' + type + ' at ' + x + ',' + y ); }
      self.pendingaction = true;
      $.ajax( {
        url: self.script + "ajax.php",
        type: 'GET',
        data: {
          root: self.root, 
          module: self.module,
          lang: self.lang, 
          fingerprint: self.fingerprint, 
          ask: "askinteraction",
          scenario: self.scenario,
          character: self.character,
          type: type,
          x: x,
          y: y
        },
        success: function( data ){
          self.pendingaction = false;
          try {
            $( '#mission-interaction-data' ).html( data );
            $( "#mission-interaction" ).animate( { width: '20em' }, 250 );
          }catch ( error ){
            console.log( error.message );
            console.log( data );
          }
        },
        tryCount: 0,
        retryLimit: 5,
        error: function( xhr, textStatus, errorThrown ) {
          this.tryCount++;
          if( this.tryCount <= this.retryLimit ) {
            console.log( 'Ajax retry: ' + this.tryCount );
            setTimeout( function(){ $.ajax( this ); }, 500 );
            return;
          }
          self.pendingaction = false;
          return;
        }
      } );
    }else{
      if( self.debug ){ console.log( 'Expecting interaction...' ); }
    }
  };
  
  this.hideInteractions = function(){
    $( "#mission-interaction" ).animate( { width:0 }, 250 );
  };
  
  this.toggleWalls = function(){
    if( this.walls ){ if( this.debug ){ console.log( 'Translucent walls' ); }
      this.walls = false;
      document.getElementById( 'missionmap' ).className = 'transwalls';
    }else{ if( this.debug ){ console.log( 'Opaque walls' ); }
      this.walls = true;
      document.getElementById( 'missionmap' ).className = '';
    }
  };
  
  this.keyEvent = function( keyCode, type ){
    if( this.debug && type == 'down' ){ console.log( "Key " + type + ": " +  keyCode ); }
    switch( keyCode ){
      case 37:  //Left arrow
        if( type == 'down' && !this.scLeft ){
          $( "#missionmap" ).animate({ scrollLeft: -$( "#map" ).width() }, 3000 );
          this.scLeft = true;
        }
        if( type == 'up' && this.scLeft){
          $( "#missionmap" ).stop();
          this.scLeft = false;
        }
      break;
      case 39:  //Right arrow
        if( type == 'down' && !this.scRight ){
          $( "#missionmap" ).animate({ scrollLeft: $( "#map" ).width() }, 3000 );
          this.scRight = true;
        }
        if( type == 'up' && this.scRight ){
          $( "#missionmap" ).stop();
          this.scRight = false;
        }
      break;
      case 38:  //Up arrow
        if( type == 'down' && !this.scUp ){
          $( "#missionmap" ).animate({ scrollTop: -$( "#map" ).height() }, 3000 );
          this.scUp = true;
        }
        if( type == 'up' && this.scUp ){
          $( "#missionmap" ).stop();
          this.scUp = false;
        }
      break;
      case 40:  //Down arrow
        if( type == 'down' && !this.scDown ){
          $( "#missionmap" ).animate({ scrollTop: $( "#map" ).height() }, 3000 );
          this.scDown = true;
        }
        if( type == 'up' && this.scDown ){
          $( "#missionmap" ).stop();
          this.scDown = false;
        }
      break;
      case 107:  //Plus
        if( type == 'down' ){ this.increaseZoom(); }
      break;
      case 109:  //Minus
        if( type == 'down' ){ this.decreaseZoom(); }
      break;
    }
  };
  
  this.toggleMinimap = function(){
    if( document.getElementById('mappanel').className == 'closed' ){
      document.getElementById('mappanel').className = 'opened';
      document.getElementById('mapicon').className = 'mapclose';
    }else{
      document.getElementById('mappanel').className = 'closed';
      document.getElementById('mapicon').className = 'mapopen';      
    }
  };
  
}

function Chat( script, root, module, lang, fingerprint ){
  
  this.script = script;
  this.root = root;
  this.module = module;
  this.lang = lang;
  this.fingerprint = fingerprint;
  this.scenario = 0;
  this.faction = 0;
  this.character = 0;
  this.data = '';
  this.pendingdata = false;
  this.debug = true;
  this.docTitle = document.title;
  
  this.load = function(){ 
    var self = this;
    if( self.debug ){ console.log( 'Loading chat data...' ); }
    if( !self.pendingdata ){
      self.pendingdata = true;
      $.ajax({
        url: self.script + "ajax.php",
        type: 'GET',
        data: {
          root: self.root, 
          module: self.module,
          lang: self.lang, 
          fingerprint: self.fingerprint, 
          ask: "chat",
          scenario: self.scenario,
          faction: self.faction,
          character: self.character
        },
        success: function( data ) {  if( self.debug ){ console.log( 'Chat data loaded' ); }
          try {
            self.pendingdata = false;
            data = JSON.parse( data );
            $( "#chatname" ).html( data['name'] + ':' );
            if( self.data != data['chat'] ){
              if( self.debug ){ console.log( 'Updating chat, new data detected' ); }
              self.data = data['chat'];
              $( "#chat" ).html( data['chat'] );
              if( document.getElementById('chatpanel').className == 'closed' ){
                document.getElementById('chaticon').className = 'chatunread';
                document.title = " * " + self.docTitle;
              }
            }
          }catch ( error ){
            console.log( error.message );
            console.log( data );
          }
        },
        tryCount: 0,
        retryLimit: 5,
        error: function( xhr, textStatus, errorThrown ) {
          this.tryCount++;
          if( this.tryCount <= this.retryLimit ) {
            console.log( 'Ajax retry: ' + this.tryCount );
            setTimeout( function(){ $.ajax( this ); }, 500 );
            return;
          }
          self.pendingdata = false;
          return;
        }
      });
    }else{ if( self.debug ){ console.log( 'Expecting chat data...' ); } }
  };
  
  this.send = function( message ){
    var self = this;
    if( message != "" ){  
      if( self.debug ){ console.log( 'Message sent' ); }
      $( "#message" ).val( "" );
      $.ajax( {
        url: self.script + "ajax.php?root=" + self.root + "&module=" + self.module + "&lang=" + self.lang + "&fingerprint=" + self.fingerprint + "&ask=talk",
        type: 'POST',
        data: {
          message: message,
          scenario: self.scenario,
          faction: self.faction,
          character: self.character
        },
        success: function( data ){
          try {
            self.load();
          }catch ( error ){
            console.log( error.message );
            console.log( data );
          }
        },
        tryCount: 0,
        retryLimit: 5,
        error: function( xhr, textStatus, errorThrown ) {
          this.tryCount++;
          if( this.tryCount <= this.retryLimit ) {
            console.log( 'Ajax retry: ' + this.tryCount );
            setTimeout( function(){ $.ajax( this ); }, 500 );
            return;
          }
          return;
        }
      } );
    }
  };
  
  this.toggle = function(){
    if( document.getElementById('chatpanel').className == 'closed' ){
      document.getElementById('chatpanel').className = 'opened';
      document.getElementById('chaticon').className = 'chatclose';
      document.title = this.docTitle;
    }else{
      document.getElementById('chatpanel').className = 'closed';
      document.getElementById('chaticon').className = 'chatopen';      
    }
  };
    
}