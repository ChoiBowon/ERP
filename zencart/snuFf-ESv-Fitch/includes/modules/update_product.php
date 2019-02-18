<?php

define('INSERT_STOCK', 1);
define('INSERT_ORDER', 2);
/**
 * @package admin
 * @copyright Copyright 2003-2019 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: DrByte 2019 Jan 04 Modified in v1.5.6a $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
if (isset($_GET['pID'])) {
  $products_id = zen_db_prepare_input($_GET['pID']);
}
if (isset($_POST['edit_x']) || isset($_POST['edit_y'])) {
  $action = 'new_product';
} elseif ((isset($_POST['products_model']) ? $_POST['products_model'] : '') . (isset($_POST['products_url']) ? implode('', $_POST['products_url']) : '') . (isset($_POST['products_name']) ? implode('', $_POST['products_name']) : '') . (isset($_POST['products_description']) ? implode('', $_POST['products_description']) : '') != '') {
  $products_date_available = zen_db_prepare_input($_POST['products_date_available']);
  $products_date_available = (date('Y-m-d') < $products_date_available) ? $products_date_available : 'null';

  // Data-cleaning to prevent data-type mismatch errors:
  $sql_data_array = array(
    'products_quantity' => convertToFloat($_POST['products_quantity']),
    'products_type' => (int)$_POST['product_type'],
    'products_model' => zen_db_prepare_input($_POST['products_model']),
    'products_price' => convertToFloat($_POST['products_price']),
    'products_date_available' => $products_date_available,
    'products_weight' => convertToFloat($_POST['products_weight']),
    'products_status' => (int)$_POST['products_status'],
    'products_virtual' => (int)$_POST['products_virtual'],
    'products_tax_class_id' => (int)$_POST['products_tax_class_id'],
    'manufacturers_id' => (int)$_POST['manufacturers_id'],
    'products_quantity_order_min' => convertToFloat($_POST['products_quantity_order_min']) == 0 ? 1 : convertToFloat($_POST['products_quantity_order_min']),
    'products_quantity_order_units' => convertToFloat($_POST['products_quantity_order_units']) == 0 ? 1 : convertToFloat($_POST['products_quantity_order_units']),
    'products_priced_by_attribute' => (int)$_POST['products_priced_by_attribute'],
    'product_is_free' => (int)$_POST['product_is_free'],
    'product_is_call' => (int)$_POST['product_is_call'],
    'products_quantity_mixed' => (int)$_POST['products_quantity_mixed'],
    'product_is_always_free_shipping' => (int)$_POST['product_is_always_free_shipping'],
    'products_qty_box_status' => (int)$_POST['products_qty_box_status'],
    'products_quantity_order_max' => convertToFloat($_POST['products_quantity_order_max']),
    'products_sort_order' => (int)$_POST['products_sort_order'],
    'products_discount_type' => (int)$_POST['products_discount_type'],
    'products_discount_type_from' => (int)$_POST['products_discount_type_from'],
    'products_price_sorter' => convertToFloat($_POST['products_price_sorter']),
  );

  $db_filename = zen_limit_image_filename($_POST['products_image'], TABLE_PRODUCTS, 'products_image');
  $sql_data_array['products_image'] = zen_db_prepare_input($db_filename);
  $new_image = 'true';

  // when set to none remove from database
  // is out dated for browsers use radio only
  if ($_POST['image_delete'] == 1) {
    $sql_data_array['products_image'] = '';
    $new_image = 'false';
  }

  if ($action == 'insert_product') {
    $sql_data_array['products_date_added'] = 'now()';
    $sql_data_array['master_categories_id'] = (int)$current_category_id;

    zen_db_perform(TABLE_PRODUCTS, $sql_data_array);
    $products_id = zen_db_insert_id();


    // reset products_price_sorter for searches etc.
    zen_update_products_price_sorter($products_id);

    $db->Execute("INSERT INTO " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id)
                  VALUES ('" . (int)$products_id . "', '" . (int)$current_category_id . "')");




    zen_record_admin_activity('New product ' . (int)$products_id . ' added via admin console.', 'info');

    ///////////////////////////////////////////////////////
    //// INSERT PRODUCT-TYPE-SPECIFIC *INSERTS* HERE //////
    ////    *END OF PRODUCT-TYPE-SPECIFIC INSERTS* ////////
    ///////////////////////////////////////////////////////



  } elseif ($action == 'update_product') {
    $sql_data_array['products_last_modified'] = 'now()';
    $sql_data_array['master_categories_id'] = (!empty($_POST['master_category']) && (int)$_POST['master_category'] > 0 ? (int)$_POST['master_category'] : (int)$_POST['master_categories_id']);

    zen_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = " . (int)$products_id);


    zen_record_admin_activity('Updated product ' . (int)$products_id . ' via admin console.', 'info');

    // reset products_price_sorter for searches etc.
    zen_update_products_price_sorter((int)$products_id);

    ///////////////////////////////////////////////////////
    //// INSERT PRODUCT-TYPE-SPECIFIC *UPDATES* HERE //////


    ////    *END OF PRODUCT-TYPE-SPECIFIC UPDATES* ////////
    ///////////////////////////////////////////////////////
  }

  $languages = zen_get_languages();
  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
    $language_id = $languages[$i]['id'];

    $sql_data_array = array(
      'products_name' => zen_db_prepare_input($_POST['products_name'][$language_id]),
      'products_description' => zen_db_prepare_input($_POST['products_description'][$language_id]),
      'products_url' => zen_db_prepare_input($_POST['products_url'][$language_id]));

    if ($action == 'insert_product') {
      $insert_sql_data = array(
        'products_id' => (int)$products_id,
        'language_id' => (int)$language_id);

      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

      zen_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);

      //cleverdevk
      $param_left = array('product_id','product_name','product_type','product_quantity','product_price');
      $param_right = array($products_id, zen_db_prepare_input($_POST['products_name'][$language_id]), $_POST['product_type'], convertToFloat($_POST['products_quantity']), convertToFloat($_POST['products_price']));
      $param = array_combine($param_left, $param_right);

      $conn_weberp = localdbconnect("weberp");

      insertToWeberp($param, $conn_weberp, INSERT_STOCK);
      //cleverdevk

    } elseif ($action == 'update_product') {
      zen_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', "products_id = " . (int)$products_id . " and language_id = " . (int)$language_id);
    }
  }

  zen_redirect(zen_href_link(FILENAME_CATEGORY_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $products_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . (isset($_POST['search']) ? '&search=' . $_POST['search'] : '')));
} else {
  $messageStack->add_session(ERROR_NO_DATA_TO_SAVE, 'error');
  zen_redirect(zen_href_link(FILENAME_CATEGORY_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $products_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') . (isset($_POST['search']) ? '&search=' . $_POST['search'] : '')));
}

/**
 * NOTE: THIS IS HERE FOR BACKWARD COMPATIBILITY. The function is properly declared in the functions files instead.
 * Convert value to a float -- mainly used for sanitizing and returning non-empty strings or nulls
 * @param int|float|string $input
 * @return float|int
 */
if (!function_exists('convertToFloat')) {

  function convertToFloat($input = 0) {
    if ($input === null) {
      return 0;
    }
    $val = preg_replace('/[^0-9,\.\-]/', '', $input);
    // do a non-strict compare here:
    if ($val == 0) {
      return 0;
    }

    return (float)$val;
  }

}


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
