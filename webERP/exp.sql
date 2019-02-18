
// debtorsmaster
INSERT INTO debtorsmaster(debtorno,name,address1,address6,currcode,salestype,holdreason,paymentterms,discount,pymtdiscount,lastpaid,lastpaiddate,creditlimit,invaddrbranch,ediinvoices,ediorders,editransport,customerpoline,typeid,language_id)
VALUES ('9999', 'Nick Kang', '4091 Blackfin Ave', 'United States', 'USD', 1, 20, 0, 0, 0, NULL, 1000, 0, 0, 0, 'email', 0, 1,'en_US.utf8');

INSERT INTO debtormaster(debtorno, name, address1, address2, address3, address4, address6, currcode,salestype,holdreason,paymentterms,discount,pymtdiscount,lastpaid,lastpaiddate,creditlimit,invaddrbranch,ediinvoices,ediorders,editransport,customerpoline,typeid,language_id)
VALUES ($param['customers_id'], $param['customers_name'], $param['customers_street_address'], $param['customers_suburb'], $param['customers_postcode'], $param['customers_state'], 'United States', 'USD', 1, 20, 0, 0, 0, NULL, 1000, 0, 0, 0, 'email', 0, 1,'en_US.utf8');
// custbranch
INSERT INTO custbranch(branchcode,debtorno,brname,braddress1,braddress6,lat,lng,estdeliverydays,area,salesman,fwddate,contactname,defaultlocation,taxgroupid,defaultshipvia,deliverblind,disabletrans)
VALUES ('testcode','9999','Nick Kang','4071 Blackfin Ave','United States',0,0,0,'CA','1',0,'TEST_CONTACT','CA2',1,1,1,0);

// salesorder
SELECT orders_products_id, products_id, final_price, products_quantity
FROM orders_products
WHERE orders_id = 1;

INSERT INTO salesorders (orderno, debtorno, branchcode, deliverto, confirmeddate, deladd1, deladd2, deladd3, deladd4, deladd6, fromstkloc, shipvia, salesperson)
VALUES (3,'9999','testcode', 'MyeongSeon','2019-02-18','4091 Blackfin Ave','','','','United States', 'CA2',1,1)

INSERT INTO salesorderdetails








// debtorsmaster
INSERT INTO debtorsmaster(debtorno,name,address1,address6,currcode,salestype,holdreason,paymentterms,discount,pymtdiscount,lastpaid,lastpaiddate,creditlimit,invaddrbranch,ediinvoices,ediorders,editransport,customerpoline,typeid,language_id)
VALUES ('9999', 'Nick Kang', '4091 Blackfin Ave', 'United States', 'USD', 1, 20, 0, 0, 0, NULL, 1000, 0, 0, 0, 'email', 0, 1,'en_US.utf8');
