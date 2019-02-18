// product_id, product_type, product_quantity, product_price
/*
  key of $param -> product_id, product_type, product_quantity, product_price, product_description
*/
$param = array();




function insertToWeberp($param){
  $sql = "INSERT INTO stockmaster (stockid, description, categoryid, materialcost)
  VALUES ('".$param['product_id']."', '".$param['product_description']."', '".$param['product_type'].
          "', ".$param['product_price'].");";

  $sql_result = mysqli_query($conn_weberp, $sql);

  $sql = "INSERT INTO locstock(loccode, stockid, quantity) VALUES ('CA', '".$param['product_id']."', ".$param['product_quantity'].");";

  $sql_result = mysqli_query($conn_weberp, $sql);

  $sql = "SELECT categoryid, categorydescription FROM stockcategory WHERE categoryid = ".$param['product_type'];

  $sql_result = mysqli_query($conn_weberp, $sql);
  $count = mysqli_num_rows($sql_result);
  mysqli_data_seek($sql_result, 0);
  $row = mysqli_fetch_row($sql_result);
  $categorydescription = $row[1];
  if($count == 0){
    $sql = "INSERT INTO stockcategory(categoryid, categorydescription) VALUES (".$param['product_id'].", ".$categorydescription.");"
    $sql_result = mysqli_query($conn_weberp, $sql);
  }
}





//ERP Stockmaster insert 문.
INSERT INTO stockmaster (stockid,description,longdescription,categoryid
                        ,units,mbflag,actualcost,lastcost,materialcost
                        ,labourcost,overheadcost,lowestlevel,discontinued
                        ,controlled,eoq,volume,grossweight,taxcatid,serialised
                        ,appendfile,perishable,decimalplaces)
VALUES ('PPS003','NickNicKGoodNick','NickNickGOodLongNick',1
  ,'each','B',0.0000,0.0000,0.0000
  ,0.0000,0.0000,0,0
  ,0,0,0.0000,0.0000,1,0
  ,'none',0,0);

INSERT INTO stockmaster (stockid, description, longdescription, categoryid, materialcost)
VALUES ($param['product_id'], $param['products_description'], $param['product_type']
        , $param['product_quantity'],$param['product_price']);

INSERT INTO locstock (loccode, stockid)
VALUES ('CA', $param['product_id']);



//원본데스네
INSERT INTO stockmaster ('.mb_substr($FieldNames,0,-2).') '.
      'VALUES ('.mb_substr($FieldValues,0,-2).')

INSERT INTO locstock (loccode,stockid)
    SELECT locations.loccode,'" . $StockItemDetails['stockid'] . "' FROM locations";
