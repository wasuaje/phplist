<?php
// General Idea taken from  lygie work
// CSV format head= mail;attr_id1;attr_id2;attr_id3;attr_id4;attr_idn
// CSV format body= "mail@mydomain";"attr_val1";attr_val2;att_val3;"attr_val4";attrvaln
//run it = php bulk_insert.php
//example of CVS:
//correo;22;10;11;30;31;32;9
//"xxxxdddfff@madmail.com";782;"name";"surname";"N";"S";"";"M"
//"asd3423asd@soft.net";783;"name";"surname";"S";"S";"";"M"
//"asdasd32@hardmail.net";784;"name";"surname";"S";"N";"";"F"
//"dadsa32323@gmail.com";785;"AAA";"XXXXXXXXXXX";"S";"S";"";"F"

//location of csvfile
$CSV="usuarios.csv";
//$database_host = "localhost";
$database_host = "10.3.0.130";
//what is the name of the database we are using
$database_name = "phplist";
// who do we log in as?
$database_user = "root";
// and what password do we use
$database_password = 'root';
//me conecto a la bbdd
$conexion = mysql_connect($database_host, $database_user, $database_password);
mysql_select_db($database_name,$conexion);

if ($conexion){
//	echo "todo bien";
	}
else{
//	echo "opps";	
	die(mysql_error());
	}
//abro el archivo csv
$Dateizeiger = fopen($CSV, "r");
$Zeilen=0;

while(($Daten = fgetcsv($Dateizeiger, 200000, ";")) !== FALSE)
	{
	$Zeilen++;
	$cantcampos = count($Daten);
	//salvamos un array con los id de atributo - need to keep the attrib ids somewhere 
	if ($Zeilen==1){
		for ($i=0;$i<=$cantcampos;$i++){			
			$acampos=$Daten;
		}
	} else {
		//verificamos que usuario existe! - check if user exists! to avoid too much inserts each time
		$valor=strtr($Daten[0],"'","");			
		$valor=mysql_escape_string($valor);
		$sql="SELECT id FROM phplist.phplist_user_user where email='$valor';";
		//echo $sql;
		$result=mysql_query($sql) or die($sql);
		if ($result) $num_rows = mysql_num_rows($result);
		if ($num_rows>0){ 
			$row=mysql_fetch_row($result);
			$Id=$row[0];
		}else{				//if it doesnt exist insert it
			$valor=strtr($Daten[0],"'","");			
			$valor=mysql_escape_string($valor);
			$sql="INSERT INTO phplist_user_user (email,confirmed,htmlemail) VALUES ('$valor',1,1);";
			mysql_query($sql) or die($sql);
			$Id= mysql_insert_id();
		}
		for ($i=1;$i<=$cantcampos-1;$i++){	//pass trough csv cols with attr_id
							//erase this atribbute if exists
		    $sql="DELETE FROM phplist_user_user_attribute WHERE attributeid=$acampos[$i] AND userid=$Id;";
	 	    mysql_query($sql)or die(mysql_error());
 		     $valor=mysql_escape_string($Daten[$i]);   	//insert the attribute
		     $sql="INSERT INTO phplist_user_user_attribute (attributeid, userid, value) VALUES ($acampos[$i],$Id,'$valor');";
		    mysql_query($sql)or die($sql);     	    		    
		   }	   
     }//if zeilen=1
} //while
fclose($Dateizeiger);

