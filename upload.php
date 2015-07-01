<html>
<head>
	<title>Hello</title>
</head>
<div  class="menu">
    <form action="upload.php" method="post" enctype="multipart/form-data">
    Select CSV to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload CSV" name="submit">
</form>
	
</div>



<body>
<h1>Hello</h1>


<?php

class MyDB extends SQLite3
   {
      function __construct()
      {
         $this->open('egb230s.sdb');
      }
   
    function UpdateTimeZone($arg)
    {
   $arg = "615";
   		
   $sql =<<<EOF
      select tz.timezonecode from statearea sa, state st, timezone tz where sa.stateid = st.stateid and sa.areacode = $arg and tz.tzid = st.defaulttzid;
EOF;


   $ret = $this->query($sql);
         

        $row = array();

        $i = 0;

         while($res = $ret->fetchArray(SQLITE3_ASSOC)){

              $row[$i]['TIME ZONE 1'] = $res[0];
              $i++;

          }
       
    print_r($row);
	return $row;      
   }
   }
function csvToJson($filename, $separator = ",")
{
    //create the resulting array
    $result = array("records" => array());

    //check if the file handle is valid
    //echo $filename;
    if (($handle = fopen($filename, "r")) !== false)
    {
        
        //"Contact","Company","Business Email","Business Phone","Direct Phone","Time Zone","Fax","Web","Source"
        
        //check if the provided file has the right format
        if(($data = fgetcsv($handle, 4096, $separator)) == false || ($data[0] != "Contact" || $data[1] != "Company" || $data[2] != "Business Email"))
        {
            throw new InvalidImportFileFormatException(sprintf('The provided file (%s) has the wrong format!', $filename));
        }

              $db = new MyDB();	
		
   if(!$db){
      echo $db->lastErrorMsg();
   } else {
      echo "Opened database successfully\n";
   }
        //loop through your data
        while (($data = fgetcsv($handle, 4096, $separator)) !== false)
        {
            $TZ = $db->UpdateTimeZone($data[3]);
            echo json_encode($TZ);
            
            //store each line in the resulting array
            $result['records'][] = array("Contact" => $data[0], "Business Email" => $data[2], "Business Phone" => $data[3], "Time Zone" => $TZ ) ;
        }
          //close the filehandle
          $db->close();
        fclose($handle);
    }

    //return the json encoded result
    //echo json_encode($result);
    return;
}

$target_dir = "c:/wamp/uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
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
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file has been uploaded from". $_FILES["fileToUpload"]["name"] ."to". $target_file;
        csvToJson($target_file);
        return $target_file;
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
    
    $csv= file_get_contents($target_file);
$array = array_map("str_getcsv", explode("\n", $csv));

//$out = array_values($array);
//echo json_encode($out);

$json = json_encode($array);
//print_r($json);
//echo json_encode($array);
//print($json);

}

?>
</body>
</html>
