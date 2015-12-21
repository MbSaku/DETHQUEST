<?php
if( !function_exists( 'password_hash' )
and !function_exists( 'password_verify' ) ){  //Simple hash function if not defined.
  
  function password_hash( $password, $constant, $array = array() ){
    return( hash( 'sha512', $password ) );
  }
  
  function password_verify( $pass, $hash ){
    return( hash( 'sha512', $pass ) == $hash );
  }
  
  define( 'PASSWORD_BCRYPT', 'PASSWORD_BCRYPT' );
  
}
/*
This means that the passwords will not work when these functions become defined.
Users will need to recover their password by standard means if this happens.
*/
?>