<?php
function csv_to_array($filename='', $delimiter=',')
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 0, $delimiter)) !== FALSE)
        {
            if(!$header)
                $header = $row;
            else
                $data[] = array_combine($header, $row);
        }
        fclose($handle);
    }
    return $data;
}


function getFiles($folder){
$files1 = scandir(getcwd().$folder);
array_shift($files1);
array_shift($files1);
return $files1;
}

function remove_utf8_bom($text)
{
    $bom = pack('H*','EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);
    return $text;
}

function array_to_db_table(array $array, $prefix, $tableName, $connection){
	$query = 'CREATE TABLE '.$tableName.' (
 id int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY, ';
 foreach ($array as $key=>$value){
	 $key = str_replace (' ', '_',$key); 
	 $key = str_replace ('-', '_',$key); 
	 $key = str_replace (')', '',$key); 
	 $key = str_replace ('(', '',$key); 
	 $key = str_replace ('?', '',$key); 
	 $key = str_replace ('/', '_',$key); 
	 $key = str_replace ('\\', '_',$key); 
	 $key = str_replace ('`', '',$key); 
	 $key = str_replace ('°', '',$key); 
	 $key = strtolower($key);
	 $key = trim($key);
	 $key = remove_utf8_bom($key);
	
	 $query  .= $prefix.'_'.$key.' TEXT CHARACTER SET utf8, ';
 }
 $query = rtrim($query, ', ');
 $query .=')';
 $pdo = $connection->prepare($query);
 if($pdo->execute()){
	 echo 'DB Table created';
	  echo '</br>';
 }
 else{
echo 'Failed';
}
}

function insert_array($array, $table, $connection, $prefix){
	/* get the names of table columns, remove 'id' column from beginning*/
	$table_sql = 'DESCRIBE '.$table;
	$pdo = $connection->prepare($table_sql);
	$pdo->execute();
	$result = $pdo->fetchAll(PDO::FETCH_ASSOC);
	$table_columns = array();
	foreach ($result as $key=>$value){
		array_push($table_columns, $value['Field']);
	}
	if($table_columns[0] == 'id'){
		array_shift($table_columns);
	}
	$rows_counter = 1;
	/*structure obtained and assigned to $table_columns*/
	foreach ($array as $key=>$value){
		/*build a query with :values, ready for binding parameters*/
		$sql = 'INSERT INTO '.$table.' (';
		foreach ($table_columns as $id => $column_name){
			$sql .= $column_name.', ';
		}
		 $sql = rtrim($sql, ', ');
		 $sql.=') VALUES (';
	
		$sql_val_counter = 1;
		foreach ($value as $sub_key=>$sub_value){
			$sql .=':value'.$sql_val_counter.', '; 
			$sql_val_counter++;
		}
		$sql = rtrim($sql, ', ');
		$sql.=')';
	/*done building a general query*/
	
	
	$bind_counter = 1;
	$query = $connection->prepare($sql);
	foreach ($value as $sub_key2=>&$sub_value2){
		$temp = ':value'.$bind_counter;
		$query->bindParam($temp, $sub_value2, PDO::PARAM_STR);
		$bind_counter++;
	}
	if($query->execute()){
		echo 'row '.$rows_counter.' inserted to db';
		echo '<br>';
		$rows_counter++;
	}
	
	
	}
}
function wrapper($folderName, $db_conn){
	$folder = $folderName;
	$path = '/data/'.$folder.'/';
	$files = getFiles($path);
	foreach ($files as $key=>$value){
		 $plik = $path.$value;
		 $data = csv_to_array('data/'.$folder.'/'.$files[$key]);
	 }
	$prefix = $folder;
	$array = $data;
	$tableName = $folder;


	$array = $data[1]; //for array_to_db_table
	array_to_db_table($array, $prefix, $tableName, $db_conn);

	$array = $data;// for insert array
	insert_array($array, $tableName, $db_conn, $prefix);
}
?>