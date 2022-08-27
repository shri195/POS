<?php

class Asset extends App {


    public static function add($data) {
    	global $database;

        $categoryid = 0;
        $manufacturerid = 0;
        $modelid = 0;
        $supplierid = 0;
        $locationid = 0;

        if(isset($data['category'])) {
            $categoryid = $database->get("assetcategories", "id", [ "name" => $data['category'] ]);
            if($categoryid == "") $categoryid = $database->insert("assetcategories", [ "name" => $data['category'], "color" => rand_color() ]);
        }

        if(isset($data['manufacturer'])) {
            $manufacturerid = $database->get("manufacturers", "id", [ "name" => $data['manufacturer'] ]);
            if($manufacturerid == "") $manufacturerid = $database->insert("manufacturers", [ "name" => $data['manufacturer'] ]);
        }

        if(isset($data['model'])) {
            $modelid = $database->get("models", "id", [ "name" => $data['model'] ]);
            if($modelid == "") $modelid = $database->insert("models", [ "name" => $data['model'] ]);
        }

        if(isset($data['supplier'])) {
            $supplierid = $database->get("suppliers", "id", [ "name" => $data['supplier'] ]);
            if($supplierid == "") $supplierid = $database->insert("suppliers", [ "name" => $data['supplier'] ]);
        }

        if(isset($data['location'])) {
            $locationid = $database->get("locations", "id", [ "AND" => [ "name" => $data['location'], "clientid" => $data['clientid'] ] ]);
            if($locationid == "") $locationid = $database->insert("locations", [ "name" => $data['location'], "clientid" => $data['clientid'] ]);
        }

    	$lastid = $database->insert("assets", [
    		"categoryid" => $categoryid,
            "maincategory"=>$data['maincategory'],
            "warrenty_expiry_date"=>$data['warrenty_expiry_date'],
    		"adminid" => $data['adminid'],
    		"clientid" => $data['clientid'],
    		// "userid" => $data['userid'],
            "manufacturerid" => $manufacturerid,
            "departmentId" => $data['departmentId'],
    		"modelid" => $modelid,
    		"supplierid" => $supplierid,
    		"statusid" => $data['statusid'],
    		"purchase_date" => $data['purchase_date'],
    		// "warranty_months" => $data['warranty_months'],
    		"tag" => $data['tag'],
    		"name" => $data['name'],
    		"serial" => $data['serial'],
    		"notes" => $data['notes'],
            "locationid" => $locationid,
            "purchase_order" => $data['purchase_order'],
            "value" => $data['value'],
            // "condition" => $data['condition'],
            // "removal_date" => $data['removal_date']
    	]);
    	if ($lastid == "0") { return "11"; } else { logSystem("Asset Added - ID: " . $lastid); return "10"; }
    	}


        function getArrayKey($param, $array) {
            if($param!="SKIP") {
                $k = array_search($param, $array);
                if ($k === false) {
                    //exit("key $param $k doesn't exist");
                    return NULL;
                }
                return $k;
            }
        }

    public static function importAssets($data) {
        global $database;
        if ( isset($data['assetsImport']) && $data['assetsImport']['size'] > 0 ) {
            $file = fopen($data['assetsImport']['tmp_name'],"r");
            $importArray=array(
                "category"=>"Category",
                "maincategory"=>"Maincategory",
                "warrenty_expiry_date"=>"Warrenty Expiry Date",
                "client"=>"Client",
                "admin"=>"Admin",
                "manufacturer"=>"Manufacturer",
                "department"=>"Department",
                "model"=>"Model",
                "supplier"=>"Supplier",
                "status"=>"Status",
                "purchase_date"=>"Purchase Date",
                "tag"=>"Tag",
                "name"=>"Name",
                "serial"=>"Serial",
                "notes"=>"Notes",
                "location"=>"Location",
                "purchase_order"=>"Purchase Order",
                "value"=>"Value"
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

                    $categoryid = 0;
                    $manufacturerid = 0;
                    $modelid = 0;
                    $supplierid = 0;
                    $locationid = 0;
                    $clientid = 0;

                    if(isset($rowArray['admin'])) {
                        $adminid = $database->get("people", "id", [ "name" => $rowArray['admin'] ]);
                    }

                    if(isset($rowArray['client'])) {
                        $clientid = $database->get("clients", "id", [ "name" => $rowArray['client'] ]);
                    }

                    if(isset($rowArray['department'])) {
                        $departmentid = $database->get("departments", "id", [ "name" => $rowArray['department'] ]);
                    }

                    if(isset($rowArray['category'])) {
                        $categoryid = $database->get("assetcategories", "id", [ "name" => $rowArray['category'] ]);
                        if($categoryid == "") $categoryid = $database->insert("assetcategories", [ "name" => $rowArray['category'], "color" => rand_color() ]);
                    }

                    if(isset($rowArray['manufacturer'])) {
                        $manufacturerid = $database->get("manufacturers", "id", [ "name" => $rowArray['manufacturer'] ]);
                        if($manufacturerid == "") $manufacturerid = $database->insert("manufacturers", [ "name" => $rowArray['manufacturer'] ]);
                    }

                    if(isset($rowArray['model'])) {
                        $modelid = $database->get("models", "id", [ "name" => $rowArray['model'] ]);
                        if($modelid == "") $modelid = $database->insert("models", [ "name" => $rowArray['model'] ]);
                    }

                    if(isset($rowArray['supplier'])) {
                        $supplierid = $database->get("suppliers", "id", [ "name" => $rowArray['supplier'] ]);
                        if($supplierid == "") $supplierid = $database->insert("suppliers", [ "name" => $rowArray['supplier'] ]);
                    }

                    if(isset($rowArray['location'])) {
                        $locationid = $database->get("locations", "id", [ "AND" => [ "name" => $rowArray['location'], "clientid" => $clientid ] ]);
                        if($locationid == "") $locationid = $database->insert("locations", [ "name" => $rowArray['location'], "clientid" => $clientid ]);
                    }

                    if(isset($rowArray['status'])) {
                        $statusid = $database->get("labels", "id", [ "name" => $rowArray['status'] ]);
                    }

                    $lastid = $database->insert("assets", [
                        "categoryid" => $categoryid,
                        "maincategory"=>$rowArray['maincategory'],
                        "warrenty_expiry_date"=>$rowArray['warrenty_expiry_date'],
                        "adminid" => $adminid,
                        "clientid" => $clientid,
                        "manufacturerid" => $manufacturerid,
                        "departmentId" => $departmentid,
                        "modelid" => $modelid,
                        "supplierid" => $supplierid,
                        "statusid" => $statusid,
                        "purchase_date" => $rowArray['purchase_date'],
                        "tag" => $rowArray['tag'],
                        "name" => $rowArray['name'],
                        "serial" => $rowArray['serial'],
                        "notes" => $rowArray['notes'],
                        "locationid" => $locationid,
                        "purchase_order" => $rowArray['purchase_order'],
                        "value" => $rowArray['value'],
                    ]);
                }
                $row++;
            }
        }
    }

    public static function edit($data) {
    	global $database;

        $categoryid = 0;
        $manufacturerid = 0;
        $modelid = 0;
        $supplierid = 0;
        $locationid = 0;

        if(isset($data['category'])) {
            $categoryid = $database->get("assetcategories", "id", [ "name" => $data['category'] ]);
            if($categoryid == "") $categoryid = $database->insert("assetcategories", [ "name" => $data['category'], "color" => rand_color() ]);
        }

        if(isset($data['manufacturer'])) {
            $manufacturerid = $database->get("manufacturers", "id", [ "name" => $data['manufacturer'] ]);
            if($manufacturerid == "") $manufacturerid = $database->insert("manufacturers", [ "name" => $data['manufacturer'] ]);
        }

        if(isset($data['model'])) {
            $modelid = $database->get("models", "id", [ "name" => $data['model'] ]);
            if($modelid == "") $modelid = $database->insert("models", [ "name" => $data['model'] ]);
        }

        if(isset($data['supplier'])) {
            $supplierid = $database->get("suppliers", "id", [ "name" => $data['supplier'] ]);
            if($supplierid == "") $supplierid = $database->insert("suppliers", [ "name" => $data['supplier'] ]);
        }

        if(isset($data['location'])) {
            $locationid = $database->get("locations", "id", [ "AND" => [ "name" => $data['location'], "clientid" => $data['clientid'] ] ]);
            if($locationid == "") $locationid = $database->insert("locations", [ "name" => $data['location'], "clientid" => $data['clientid'] ]);
        }
    	$database->update("assets", [
            "categoryid" => $categoryid,
            "maincategory"=>$data['maincategory'],
            "warrenty_expiry_date"=>$data['warrenty_expiry_date'],
    		"adminid" => $data['adminid'],
    		"clientid" => $data['clientid'],
    		// "userid" => $data['userid'],
            "manufacturerid" => $manufacturerid,
    		"modelid" => $modelid,
    		"supplierid" => $supplierid,
    		"statusid" => $data['statusid'],
    		"purchase_date" => $data['purchase_date'],
            "departmentId" => $data['departmentId'],
    		// "warranty_months" => $data['warranty_months'],
    		"tag" => $data['tag'],
    		"name" => $data['name'],
    		"serial" => $data['serial'],
    		"notes" => $data['notes'],
            "locationid" => $locationid,
            "purchase_order" => $data['purchase_order'],
            "value" => $data['value'],
            // "condition" => $data['condition'],
            // "removal_date" => $data['removal_date']
    	], [ "id" => $data['id'] ]);
    	logSystem("Asset Edited - ID: " . $data['id']);
    	return "20";
    	}

    public static function delete($id) {
    	global $database;
        $database->delete("assets", [ "id" => $id ]);
    	logSystem("Asset Deleted - ID: " . $id);
    	return "30";
    	}

    public static function nextAssetTag() {
    	global $database;
        $max = $database->max("assets", "id");
    	return $max+1;
    	}


    public static function assignLicense($data) { //assign license to asset
    	global $database;
    	$lastid = $database->insert("licenses_assets", [
    		"licenseid" => $data['licenseid'],
    		"assetid" => $data['assetid']
    	]);
    	if ($lastid == "0") { return "11"; } else { return "10"; }
    }

    public static function unassignLicense($id) { //unassign license to asset
    	global $database;
        $database->delete("licenses_assets", [ "id" => $id ]);
    	return "30";
    }
}


?>
