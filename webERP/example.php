<?php
    include "xmlrpc/lib/xmlrpc.inc";
    $xmlrpc_internalencoding="UTF-8";
    include "xmlrpc/lib/xmlrpcs.inc";

    function GetLocations() {

    //Encode the user/password combination
    $UserID = php_xmlrpc_encode("admin");
    $Password = php_xmlrpc_encode("webERP");

    //Create a client object to use for xmlrpc call
    $Client = new xmlrpc_client("http://localhost/webERP/api/api_xml-rpc.php");

    //Create a message object, containing the parameters and the function name
    $Message = new xmlrpcmsg("webERP.xmlrpc_GetLocationList", array($UserID, $Password));
}
?>

<html>
    <head>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
            "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    </head>
    <body>
        <form action="index.html" method="post">
            Stock Code:<input type="text" name="StockID" /><br />
            Location:<select name="location">
            <?php // Here will go the available stock locations from webERP?>
            </select><br />
            <input type="submit" name="submit" value="Submit" />
        </form>
    </body>
</html>
