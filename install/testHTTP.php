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
    $lastHTTPCode = getLastHTTPCode( $url );
    
    if ($lastHTTPCode) {
      if ( substr( $lastHTTPCode, 0, 1 ) == $startOfCode ) {
        $result[$url] = array( "type" => $type, "result" => false );
      }
      else {
        $result[$url] = array( "type" => $type, "result" => true );
      }
    }    
  }
  
  return $result;
}

function getLastHTTPCode( $url ) {
  
  ini_set( "default_socket_timeout", 2 );
  @file_get_contents( $url );
  $headers = $http_response_header;

  if ( is_array( $headers ) ) {
    foreach ( $headers as $key => $header ) {
      if ( substr( $header, 0, 4 ) == "HTTP" ) {
        $response = split( " ", $header );
        $response = $response[1];
      }
    }
  }
  else {
    $response = false;
  }
  

  return $response;
}

?>
