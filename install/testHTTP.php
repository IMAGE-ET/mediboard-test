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
  $ctx = stream_context_create( array( 
     'http' => array( 
         'timeout' => 5 
         ) 
     ) 
 );

  @file_get_contents( $url, false, $ctx );
  $headers = $http_response_header;

  foreach ( $headers as $key => $header ) {
    if ( substr( $header, 0, 4 ) == "HTTP" ) {
      $response = split( " ", $header );
      $response = $response[1];
    }
  }

  return $response;
}

?>
