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

#-> UPDATE COMPANY TABLE 
$table = TB_COMPANY;
$data = array("companyName"=>"OCEAN Bank","companyAddress"=>"High Road NY 89024","companyTel"=>"022-688-51","companyTypeID"=>1);
$where = "companyID=1";
$query = $db->update($table,$data,$where);

#-> Preparing the data.
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
	$arr["messages"] = "Failed to update";
}

#-> Return json data.
echo json_encode($arr);

#-> Close database.
$db->closedb();

?>