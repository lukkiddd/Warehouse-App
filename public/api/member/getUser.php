<?php
#-> Include config and class files.
include_once("../../includes/config.php");
include_once("../../includes/class_mysql.php");

#-> Get data from js and initialize
$data = file_get_contents("php://input");
$json = json_decode($data);
$staffID = $json->user->_id;

#-> Connect to the database
$db = new Database();
$db->connectdb(DB_NAME,DB_USER,DB_PASS);

#-> Query the data.
$query = $db->querydb("SELECT * FROM ".TB_STAFF." INNER JOIN ".TB_BRANCH." ON ".TB_STAFF.".branchID = ".TB_BRANCH.".branchID INNER JOIN ".TB_POSITION." ON ".TB_STAFF.".positionID = ".TB_POSITION.".positionID  WHERE staffID = '$staffID'");

#-> Preparing return data.
$arr = array();
if($query) {
	$arr["status"] = "success";
	if($result = $db->fetch($query)) {
		$arr["data"]["attributes"]["_id"] = $result["staffID"];
		$arr["data"]["attributes"]["name"] = $result["staffName"];
		$arr["data"]["attributes"]["email"] = $result["email"];
		$arr["data"]["relationships"]["position"]["_id"] = $result["positionID"];
		$arr["data"]["relationships"]["position"]["name"] = $result["positionName"];
		$arr["data"]["relationships"]["branch"]["_id"] = $result["branchID"];
		$arr["data"]["relationships"]["branch"]["name"] = $result["branchName"];
		$arr["data"]["relationships"]["branch"]["address"] = $result["branchAddress"];
	}
} else {
	$arr["status"] = "error";
	$arr["messages"] = "Cannot get the user.";

}


#-> Return json data.
echo json_encode($arr);

#-> Close database.
$db->closedb();

?>