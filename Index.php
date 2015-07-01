<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Hello</title>
</head>
<div  class="menu">
	
</div>



<body>
<h1>Hello</h1>
<?php


function csvToJson($filename, $separator = ",")
{
	
	$target_dir = "c:/wamp/uploads/";
$target_file = $target_dir . basename($filename);


$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    //$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    //if($check !== false) {
    //    echo "File is an image - " . $check["mime"] . ".";
    //    $uploadOk = 1;
    //} else {
    //    echo "File is not an image.";
    //    $uploadOk = 0;
    //}
}
// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "csv" ) {
    echo "Sorry, only CSV files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($filename, $target_file)) {
        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        return $target_file;
    } else
	{
		echo $filename;
		echo $target_file;
        echo "Sorry, there was an error uploading your file.";
    }
    
    $csv= file_get_contents($target_file);
$array = array_map("str_getcsv", explode("\n", $csv));
$json = json_encode($array);

}

    //create the resulting array
    $result = array("records" => array());

    //check if the file handle is valid
    echo $filename;
    if (($handle = fopen($filename, "r")) !== false)
    {
        
        //"Contact","Company","Business Email","Business Phone","Direct Phone","Time Zone","Fax","Web","Source"
        
        //check if the provided file has the right format
        if(($data = fgetcsv($handle, 4096, $separator)) == false || ($data[0] != "Contact" || $data[1] != "Company" || $data[2] != "Business Email"))
        {
            throw new InvalidImportFileFormatException(sprintf('The provided file (%s) has the wrong format!', $filename));
        }

        //loop through your data
        while (($data = fgetcsv($handle, 4096, $separator)) !== false)
        {
            //store each line in the resulting array
            $result['records'][] = array("Contact" => $data[0], "Business Email" => $data[2], "Business Phone" => $data[3], "Time Zone" => $data[5] ) ;
        }

        //close the filehandle
        fclose($handle);
    }

    //return the json encoded result
    echo json_encode($result);
    return;
}




function display_form($uid) {
$str = <<<EOT
<form action="upload.php" method="post" enctype="multipart/form-data">
    Select CSV to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload CSV" name="submit">
</form>

EOT;
return $str;
}




class MyDB extends SQLite3
   {
      function __construct()
      {
         $this->open('egb230s.sdb');
      }
   }

   
class MyRestClient 
{
	public function __constructor(){
		header('Content-Type: application/json');
		
	}

	/*Arg is a single phone number*/
	public function getTimeZoneForARecord($arg){
		$db = new MyDB();	
		
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      echo "Opened database successfully\n";
   }

   $sql =<<<EOF
      select tz.timezonecode from statearea sa, state st, timezone tz where sa.stateid = st.stateid and sa.areacode = $arg and tz.tzid = st.defaulttzid;
EOF;

   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
      echo json_encode($row);
   }
   echo "Operation done successfully\n";
   $db->close();
		echo json_encode(array('contacts' => array('contact' => array('phonenumber'=> 9884056307,'name' => 'Radhika'))));
	}
	
	/*arg is a CSV file*/
	public function getTimeZoneInCSV($arg){
		//echo display_form(1);
		csvToJson($arg)	;
		
	}

	public function getSomeRandomArgument($arg){
		echo json_encode(array('argument' => $arg));
	}

}

/*
	Reuest Handler

	could be a class that handle all the request.
*/
if ($_GET['r'] != null)
{
	$Req = $_GET['r'];
	$obj = new MyRestClient();
	
	if (isset($_GET["areaCode"]))
	{
		$Arg = $_GET["areaCode"];
		$obj->$Req($Arg);
	}
	else{		
		if (isset($_GET["updatescv"]))
		{
		$Arg = $_GET["updatescv"];
		echo 'Inside else';
		$obj->$Req($Arg);		
		
		}
		
	}
}else{
	echo 'Yes It Needs an argument';
}  

?>


</body>
</html>
