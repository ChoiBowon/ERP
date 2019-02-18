<?php


define('INSERT_STOCK', 1);
define('INSERT_ORDER', 2);
/*
    $mode : INSERT_STOCK / INSERT_ORDER mode
    $param : 1) INSERT_STOCK mode : dictionary array consisted of product_id, product_name, product_type, product_quantity, product_price from zencart,
             2) INSERT_ORDER mode :
    $conn_weberp : mysql connection of weberp
*/
function insertToWeberp($param, $conn_weberp, $mode){
  if($mode == INSERT_STOCK){
    // **********stock mode************
    $sql = "SELECT categoryid, categorydescription FROM stockcategory WHERE categoryid = ".$param['product_type'].";";
    echo "<br></br>";
    echo $sql;
    $sql_result = mysqli_query($conn_weberp, $sql);

    $count = mysqli_num_rows($sql_result);
    mysqli_data_seek($sql_result, 0);
    $row = mysqli_fetch_row($sql_result);
    $categorydescription = $row[1];
    if($count == 0){
      $sql = "INSERT INTO stockcategory(categoryid, categorydescription) VALUES ('".$param['product_type']."', '".$param['product_name']."');";
      echo "<br></br>";
      echo $sql;
      mysqli_query($conn_weberp, $sql);
    }

    $sql = "INSERT INTO stockmaster(stockid, description, categoryid, materialcost) VALUES ('".$param['product_id']."', '".$param['product_name']."', '".$param['product_type']."', ".$param['product_price'].");";

    echo "<br> </br>";
    echo $sql;
    mysqli_query($conn_weberp, $sql);

    $sql = "INSERT INTO locstock(loccode, stockid, quantity) VALUES ('CA', '".$param['product_id']."', ".$param['product_quantity'].");";
    echo "<br></br>";
    echo $sql;
    mysqli_query($conn_weberp, $sql);

    $sql = "INSERT INTO prices(stockid, typeabbrev, currabrev, price, startdate, enddate) VALUES ('".$param['product_id']."','1','USD',".$param['product_price'].",'2019-02-17','9999-12-31');";
    mysqli_query($conn_weberp, $sql);
  }
  elseif ($mode == INSERT_ORDER) {
    // ***********order mode***********

    // ############################ Need to change typeid, area, salesman, fromstkloc !! #####################################

    // zencart db connection
    $conn_zencart = localdbconnect("zencart");

    // check whether customers_id exists in weberp(debtorno)
    $sql = "SELECT debtorno FROM debtorsmaster WHERE debtorno = " . $param['customers_id'] . ";";
    echo $sql ."<br></br>";
    $sql_result = mysqli_query($conn_weberp, $sql);
    $count = mysqli_num_rows($sql_result);

    if($count == 0){ // if there is no debtorno
      //debtorsmaster insert
      $sql = "INSERT INTO debtorsmaster(debtorno, name, address1, address2, address3, address4, address6, currcode,salestype,holdreason,paymentterms,discount,pymtdiscount,lastpaid,lastpaiddate,creditlimit,invaddrbranch,ediinvoices,ediorders,editransport,customerpoline,typeid,language_id)
              VALUES ('" . $param['customers_id'] . "', '" . $param['customers_name'] . "','" . $param['customers_street_address'] . "','" . $param['customers_suburb'] . "','" . $param['customers_postcode'] . "','" . $param['customers_state'] . "','United States', 'USD', 1,1, 20, 0, 0, 0, NULL, 1000, 0, 0, 0, 'email', 0, 1,'en_US.utf8');";
      echo $sql ."<br></br>";
      mysqli_query($conn_weberp, $sql);

      //custbranch insert
      $sql = "INSERT INTO custbranch(branchcode,debtorno,brname,braddress1,braddress6,lat,lng,estdeliverydays,area,salesman,fwddate,contactname,defaultlocation,taxgroupid,defaultshipvia,deliverblind,disabletrans)
              VALUES ('" . $param['customers_id'] . "','" . $param['customers_id'] . "','Peoplespace','1691 Kettering St, Irvine','United States',0,0,0,'CA','1',0,'Daniel Lee','CA2',1,1,1,0);";
      echo $sql ."<br></br>";
      mysqli_query($conn_weberp, $sql);
    }

    $date = date("Y-m-d"); //current time data
    //get orders_products detail data for salesorderdetails table
    $sql = "SELECT orders_id, orders_products_id, products_id, final_price, products_quantity FROM orders_products WHERE orders_id = " . $param['orders_id'] . ";";
      echo $sql ."<br></br>";
    $sql_result = mysqli_query($conn_zencart, $sql);
    $count = mysqli_num_rows($sql_result);
    $rows = array();
    for($i=0;$i<$count;$i++){
      mysqli_data_seek($sql_result, $i);
      $rows[$i] = mysqli_fetch_row($sql_result);
      //insert to salesorders
      $sql = "INSERT INTO salesorders (orderno, debtorno, branchcode, deliverto, confirmeddate, deladd1, deladd2, deladd3, deladd4, deladd6, fromstkloc, shipvia, salesperson)
      VALUES (" . $rows[$i][1] . ",'". $param['customers_id'] . "','". $param['customers_id'] . "', '" . $param['customers_name'] . "','" . $date . "','" . $param['customers_street_address'] . "','" . $param['customers_suburb'] . "','" . $param['customers_postcode'] . "','" . $param['customers_state'] . "','United States','CA2',1,1);";
      echo $sql ."<br></br>";
      mysqli_query($conn_weberp, $sql);

      //insert to salesorderdetails
      $sql = "INSERT INTO salesorderdetails(orderlineno, orderno, stkcode, qtyinvoiced, unitprice, quantity, estimate, discountpercent, actualdispatchdate, completed, itemdue)
              VALUES (0," . $rows[$i][1] . ",'" . $rows[$i][2] . "',0," . $rows[$i][3] . "," . $rows[$i][4] .  ",0,0,'0000-00-00 00:00:00',0,'" . $date . "');";
      echo $sql ."<br></br>";
      mysqli_query($conn_weberp, $sql);
    }

  }

}

/*
  $dbname : database name,
  $dbid : database id. default = root,
  $dbpw : datapase password. default = ''
*/
function localdbconnect($dbname, $dbid='root', $dbpw=''){
  $conn = mysqli_connect('localhost',$dbid,$dbpw);
  if($conn)
    echo "DB Connected!<br></br>";
  else
    echo "DB Connection Fail!<br></br>";
  mysqli_select_db($conn, $dbname);
  return $conn;
}


?>
