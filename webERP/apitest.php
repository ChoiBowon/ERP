<?php
  echo "Test  API";
  //the xmlrpc class can output some funny warnings so make sure notices are turned off error_reporting (E_ALL & ~E_NOTICE);
  /* you need to include the phpxmlrpc class - see link above - copy the whole directory structure of the class over to your client application from the /xmlrpc directory */

  //include ("xmlrpc/lib/xmlrpc.inc");
  include "xmlrpc/lib/xmlrpc.inc";
  $xmlrpc_internalencoding="UTF-8";
  include "xmlrpc/lib/xmlrpcs.inc";

  //if your  install is on a server at http://www.yourdomain.com/

  $ServerURL = "http://localhost/webERP/api/api_xml-rpc.php";
  $DebugLevel = 2; //Set to 0,1, or 2 with 2 being the highest level of debug info
  $Parameters = array();

  /* The trap for me was that each parameter needs to be run through xmlrpcval() - to create the necessary xml required for the rpc call if one of the parameters required is an array then it needs to be processing into xml for the rpc call through php_xmlrpc_encode()*/
  //$Parameters["StockID"] = new xmlrpcval("DVD-TOPGUN"); //the stockid of the item we wish to know the balance for
  $Parameters["StockID"] = php_xmlrpc_encode("DVD-TOPGUN");
  //assuming the demo username and password will work !
  //$Parameters["Username"] = new xmlrpcval("admin");
  //$Parameters["Password"] = new xmlrpcval("weberp");
  $Parameters["Username"] = php_xmlrpc_encode("admin");
  $Parameters["Password"] = php_xmlrpc_encode("weberp");

  $Msg = new xmlrpcmsg(".xmlrpc_GetStockBalance", $Parameters);

  $Client = new xmlrpc_client($ServerURL);
  $Client->setDebug($DebugLevel);
  $Response = $Client->send($Msg);

  echo "Result of Response : ";

  $Answer = php_xmlrpc_decode($Response->value());
  if ($Answer[0]!=0){ //then the API returned some errors need to figure out what went wrong
      //need to figure out how to return all the error descriptions associated with the codes
  } else { //all went well the returned data is in $answer[1]
      //answer will be an array of the locations and quantity on hand for DVD_TOPGUN so we need to run through the array to print out
      for ($i=0; $i < sizeof($Answer[1]);$i++) {
          echo "[TESTING]";
          echo "" . $Answer[1][$i]["loccode"] . " has " . $Answer[1][$i]["quantity"] . " on hand";
      }
  }
?>
