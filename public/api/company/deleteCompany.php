<?php
#-> Include config and class files.
include_once("../../includes/config.php");
include_once("../../includes/class_mysql.php");

#-> Get data from js and initialize
$data = file_get_contents("php://input");
$json = json_decode($data);

#-> Connect to the database
$db = new Database();
$db->connectdb(DB_NAME,DB_USER,DB_PASS);

//DELETE COMPANY TABLE 
$table = TB_COMPANY;
$where = "companyID=7";
$query = $db->delete($table,$where);

#-> Preparing the data for return.
$arr = array();
if($query) {
	$arr["status"] = "success";
	$query = $db->querydb("SELECT * FROM ".TB_COMPANY." INNER JOIN ".TB_COMPANYTYPE." ON ".TB_COMPANY.".companyTypeID = ".TB_COMPANYTYPE.".companyTypeID");
	$i = 0;
	while($result = $db->fetch($query)) {
		$arr["data"][$i]["attributes"]["id"] = $result["companyID"];
		$arr["data"][$i]["attributes"]["name"] = $result["companyName"];
		$arr["data"][$i]["attributes"]["address"] = $result["companyAddress"];
		$arr["data"][$i]["attributes"]["telephone"] = $result["companyTel"];
		
		$arr["data"][$i]["relationships"]["companyType"]["name"] = $result["companyTypeName"];
		$arr["data"][$i]["relationships"]["companyType"]["_id"] = $result["companyTypeID"];
		$i++;
	}
} else {
	$arr["status"] = "error";
	$arr["messages"] = "Failed to delete";
}

#-> Return the data.
echo json_encode($arr);
		 
#-> Close database.
$db->closedb();

?>