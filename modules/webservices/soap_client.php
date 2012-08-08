<?php	
  // Désactivation du cache WSDL
  ini_set("soap.wsdl_cache_enabled", "0");  
  
  $client = new SoapClient("http://192.168.1.32/mediboard/index.php?login=kevin:0xd3v5&m=webservices&a=soap_server&class=CSoapHandler&wsdl_mode=CWSDLRPCEncoded&wsdl", array("trace" => true));
  try { 
    echo "<pre>";
    $res = $client->calculatorAuth2("add", 1, 6);
    print_r($res);
    echo "<br/>Request<br/>";
    print_r($client->__getLastRequest());
    echo "<br/>Request headers<br/>";
    print_r($client->__getLastRequestHeaders());
    echo "<br/>Response<br/>";
    print_r($client->__getLastResponse());
    echo "<br/>Response headers<br/>";
    print_r($client->__getLastResponseHeaders());
    echo "<br/><br/>";
  } catch (SoapFault $e) { 
    echo $e, "\n";
    echo "<br/>Request<br/>";
    echo $client->__getLastRequest();
    echo "<br/>Request headers<br/>";
    echo $client->__getLastRequestHeaders();
    echo "<br/>Response<br/>";
    echo $client->__getLastResponse();
    echo "<br/>Response headers<br/>";
    echo $client->__getLastResponseHeaders();
    echo "<br/><br/>";      
  }
  
  $client = new SoapClient("http://192.168.1.32/mediboard/index.php?login=kevin:0xd3v5&m=webservices&a=soap_server&class=CSoapHandler&wsdl_mode=CWSDLRPCLiteral&wsdl", array("trace" => true));
  try { 
    echo "<pre>";
    $res = $client->calculatorAuth2("add", 1, 5);
    print_r($res);
    echo "<br/>Request<br/>";
    print_r($client->__getLastRequest());
    echo "<br/>Request headers<br/>";
    print_r($client->__getLastRequestHeaders());
    echo "<br/>Response<br/>";
    print_r($client->__getLastResponse());
    echo "<br/>Response headers<br/>";
    print_r($client->__getLastResponseHeaders());
    echo "<br/><br/>";
  } catch (SoapFault $e) { 
    echo $e, "\n";
    echo "<br/>Request<br/>";
    echo $client->__getLastRequest();
    echo "<br/>Request headers<br/>";
    echo $client->__getLastRequestHeaders();
    echo "<br/>Response<br/>";
    echo $client->__getLastResponse();
    echo "<br/>Response headers<br/>";
    echo $client->__getLastResponseHeaders();
    echo "<br/><br/>";      
  } 
?>