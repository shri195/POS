<?php

class License extends App {


    public static function add($data) {
    	global $database;
    	$lastid = $database->insert("licenses", [
    		"clientid" => $data['clientid'],
    		"statusid" => $data['statusid'],
    		"categoryid" => $data['categoryid'],
    		"supplierid" => $data['supplierid'],
    		"tag" => $data['tag'],
    		"name" => $data['name'],
    		"serial" => mc_encrypt($data['serial']),
    		"notes" => $data['notes'],
			"purchase_date" =>$data['purchase_date'],
			"expirory_date" =>$data['expirory_date'],
			"departmentId" => $data['departmentId'],
			"purchase_order" =>$data['purchase_order']
    	]);
    	if ($lastid == "0") { return "11"; } else { logSystem("License Added - ID: " . $lastid); return "10"; }
    }

	/**
	 * Import Licenses
	 */
	public static function importLicenses($data) {
		global $database;
        if ( isset($data['LicenseImport']) && $data['LicenseImport']['size'] > 0 ) {
            $file = fopen($data['LicenseImport']['tmp_name'],"r");
            $importArray=array(
				"name"=>"Name",
				"client"=>"Client",
                "category"=>"Category",
				"department"=>"Department",
				"tag"=>"License Tag",
				"serial"=>"Serial",
				"purchase_date"=>"Purchase Date",
				"expirory_date"=>"Expirory Date",
				"purchase_order"=>"Purchase Order",
				"status"=>"Status",
				"supplier"=>"Supplier",
				"notes"=>"Notes"
            );

            $row = 1;
            // Begin processing the rows:
            while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
                // Assign array keys to headers:
                if($row==1) {
                    $headerArray = $data;
                }
                if($row > 1) {
                    $rowArray = array();
                    foreach($importArray as $key=>$name) {
                        $index = array_search($name, $headerArray);
                        $rowArray[$key] = str_replace(",", "\,", str_replace("'", "\'", ($index === NULL)? '' : $data[$index]));
                    }

					$clientid = 0;
					$departmentid = 0;
                    $categoryid = 0;
                    $supplierid = 0;
                    $statusid = 0;

                    if(isset($rowArray['client'])) {
                        $clientid = $database->get("clients", "id", [ "name" => $rowArray['client'] ]);
                    }

                    if(isset($rowArray['department'])) {
                        $departmentid = $database->get("departments", "id", [ "name" => $rowArray['department'] ]);
                    }

                    if(isset($rowArray['category'])) {
                        $categoryid = $database->get("licensecategories", "id", [ "name" => $rowArray['category'] ]);
                        if($categoryid == "") $categoryid = $database->insert("licensecategories", [ "name" => $rowArray['category'], "color" => rand_color() ]);
                    }

                    if(isset($rowArray['supplier'])) {
                        $supplierid = $database->get("suppliers", "id", [ "name" => $rowArray['supplier'] ]);
                        if($supplierid == "") $supplierid = $database->insert("suppliers", [ "name" => $rowArray['supplier'] ]);
                    }

                    if(isset($rowArray['status'])) {
                        $statusid = $database->get("labels", "id", [ "name" => $rowArray['status'] ]);
                    }

					$database->insert("licenses", [
						"clientid" => $clientid,
						"statusid" => $statusid,
						"categoryid" => $categoryid,
						"supplierid" => $supplierid,
						"tag" => $rowArray['tag'],
						"name" => $rowArray['name'],
						"serial" => mc_encrypt($rowArray['serial']),
						"notes" => $rowArray['notes'],
						"purchase_date" =>$rowArray['purchase_date'],
						"expirory_date" =>$rowArray['expirory_date'],
						"departmentId" => $departmentid,
						"purchase_order" =>$rowArray['purchase_order']
					]);
                }
                $row++;
            }
        }		
	}

    public static function edit($data) {
    	global $database;
    	$database->update("licenses", [
    		"clientid" => $data['clientid'],
    		"statusid" => $data['statusid'],
    		"categoryid" => $data['categoryid'],
    		"supplierid" => $data['supplierid'],
    		"tag" => $data['tag'],
    		"name" => $data['name'],
    		"serial" => mc_encrypt($data['serial']),
    		"notes" => $data['notes'],
			"purchase_date" =>$data['purchase_date'],
			"expirory_date" =>$data['expirory_date'],
			"departmentId" => $data['departmentId'],
			"purchase_order" =>$data['purchase_order']
    	], [ "id" => $data['id'] ]);
    	logSystem("License Edited - ID: " . $data['id']);
    	return "20";
    }

    public static function delete($id) {
    	global $database;
        $database->delete("licenses", [ "id" => $id ]);
    	logSystem("License Deleted - ID: " . $id);
    	return "30";
    }

    public static function nextLicenseTag() {
    	global $database;
        $max = $database->max("licenses", "id");
    	return $max+1;
    }


    public static function assignAsset($data) { //assign license to asset
    	global $database;
    	$lastid = $database->insert("licenses_assets", [
    		"licenseid" => $data['licenseid'],
    		"assetid" => $data['assetid']
    	]);
    	if ($lastid == "0") { return "11"; } else { return "10"; }
    }

    public static function unassignAsset($id) { //unassign license to asset
    	global $database;
        $database->delete("licenses_assets", [ "id" => $id ]);
    	return "30";
    }

}


?>
