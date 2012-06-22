<?php

function testHTTPCode( $urls = array(), $type = "Autorisé" ) {
  switch ( $type ) {
    case "Autorisé":
      $startOfCode = "4";
      break;
    
    default:
      $startOfCode = "2";
  }
  
  $result = array();
  foreach ( $urls as $url ) {
    if ( substr( getLastHTTPCode( $url ), 0, 1 ) == $startOfCode ) {
      $result[$url] = array( "type" => $type, "result" => false );
    }
    else {
      $result[$url] = array( "type" => $type, "result" => true );
    }
  }
  
  return $result;
}

function getLastHTTPCode( $url ) {
  $headers = get_headers( $url, 1 );

  foreach ( $headers as $key => $header ) {
    if ( is_int( $key ) ) {
      $response = split( " ", $header );
      $response = $response[1];
    }
  }

  return $response;
}

?>
