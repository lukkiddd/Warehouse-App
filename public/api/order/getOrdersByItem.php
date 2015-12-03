<?php
#-> Include config and class files.
include_once("../../includes/config.php");
include_once("../../includes/class_mysql.php");
date_default_timezone_set('Asia/Bangkok');

#-> Get data from js and initialize
$data = file_get_contents("php://input");
$json = json_decode($data);
$itemID = $json->item->attributes->_id;
$branchID = $json->branch->_id;

#-> Connect to the database
$db = new Database();
$db->connectdb(DB_NAME,DB_USER,DB_PASS);

$query = $db->querydb("SELECT * FROM ".TB_ORDER." INNER JOIN ".TB_ORDERITEM." ON ".TB_ORDER.".orderID = ".TB_ORDERITEM.".orderID INNER JOIN ".TB_BRANCH." b ON ".TB_ORDER.".branchID = b.branchID INNER JOIN ".TB_STATUS." ON ".TB_STATUS.".statusID = ".TB_ORDER.".statusID INNER JOIN ".TB_ORDERTYPE." ON ".TB_ORDERTYPE.".orderTypeID = ".TB_ORDER.".orderTypeID INNER JOIN ".TB_STAFF." ON ".TB_STAFF.".staffID = ".TB_ORDER.".staffID LEFT JOIN ".TB_COMPANY." ON ".TB_COMPANY.".companyID = ".TB_ORDER.".companyID LEFT JOIN ".TB_BRANCH." tobranch ON tobranch.branchID = ".TB_ORDER.".branchID WHERE itemID = $itemID and b.branchID = $branchID");
// var_dump("SELECT * FROM ".TB_ORDER." INNER JOIN ".TB_ORDERITEM." ON ".TB_ORDER.".orderID = ".TB_ORDERITEM.".orderID INNER JOIN ".TB_BRANCH." b ON ".TB_ORDER.".branchID = b.branchID INNER JOIN ".TB_STATUS." ON ".TB_STATUS.".statusID = ".TB_ORDER.".statusID INNER JOIN ".TB_ORDERTYPE." ON ".TB_ORDERTYPE.".orderTypeID = ".TB_ORDER.".orderTypeID INNER JOIN ".TB_STAFF." ON ".TB_STAFF.".staffID = ".TB_ORDER.".staffID LEFT JOIN ".TB_COMPANY." ON ".TB_COMPANY.".companyID = ".TB_ORDER.".companyID LEFT JOIN ".TB_BRANCH." tobranch tobranch.branchID = ".TB_ORDER.".branchID WHERE itemID = $itemID and b.branchID = $branchID");
if($query)
	{
		$arr["status"] = "success";
		$i = 0;
		while($result = $db->fetch($query)){
			// var_dump($result);
			$arr["data"][$i]["attributes"]["_id"] = $result["orderID"];
			$arr["data"][$i]["attributes"]["date"] = date('d M Y',strtotime($result["orderDate"]));
			$arr["data"][$i]["relationships"]["branch"]["_id"] = $result["b.branchID"];
			$arr["data"][$i]["relationships"]["branch"]["name"] = $result["b.branchName"];
			$arr["data"][$i]["relationships"]["branch"]["address"] = $result["b.branchAddress"];
			$arr["data"][$i]["relationships"]["branch"]["telephone"] = $result["b.branchTel"];
			$arr["data"][$i]["relationships"]["staff"]["_id"] = $result["staffID"];
			$arr["data"][$i]["relationships"]["staff"]["name"] = $result["staffName"];
			$arr["data"][$i]["relationships"]["staff"]["email"] = $result["staffEmail"];
			$arr["data"][$i]["relationships"]["staff"]["telephone"] = $result["staffTel"];
			$arr["data"][$i]["relationships"]["company"]["_id"] = $result["companyID"];
			$arr["data"][$i]["relationships"]["company"]["name"] = $result["companyName"];
			$arr["data"][$i]["relationships"]["status"]["attributes"]["_id"] = $result["statusID"];
			$arr["data"][$i]["relationships"]["status"]["attributes"]["name"] = $result["statusName"];
			$arr["data"][$i]["attributes"]["invoiceCode"] = $result["invoiceCode"];
			$arr["data"][$i]["attributes"]["deliverdDate"] = $result["deliverdDate"];
			$arr["data"][$i]["relationships"]["orderType"]["_id"] = $result["orderTypeID"];
			$arr["data"][$i]["relationships"]["orderType"]["name"] = $result["orderTypeName"];
			if(!$result["companyID"]) {
				$arr["data"][$i]["relationships"]["toBranch"]["_id"] = $result[33];
				$arr["data"][$i]["relationships"]["toBranch"]["name"] = $result[35];
			} else {
				$arr["data"][$i]["relationships"]["toBranch"]["_id"] = "";
				$arr["data"][$i]["relationships"]["toBranch"]["name"] = "";
			}
			$arr["data"][$i]["update"] = false;
			$i++;
		}
	} 
else {
		$arr["status"] = "error";
		$arr["messages"] = "failed to get order information";
	}
echo json_encode($arr,JSON_NUMERIC_CHECK);
#-> Close database.
$db->closedb();

?>