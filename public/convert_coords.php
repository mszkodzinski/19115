<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 3/15/14
 * Time: 7:23 PM
 */

include_once('../application/autoloader.php');
$db  = Medoo_Medoo::getInstance();
$path = '../data/file/';


//var_dump($db->selectDict('district'));
$query = $db->select("notification", array("longtitude_2000", "lattitude_2000"), array("lattitude_2000[!]" => "", "longtitude_2000[!]" => "", "ORDER" => "id ASC"));
//$sql = "SELECT longtitude_2000, lattitude_2000 FROM  `notification` where lattitude_2000 !='' &&  longtitude_2000 !=''  order by id asc ";
//echo $sql;
//$query = $db->getDBObject()->query($sql);
$file = $path.'coords.csv';
$fp = fopen($file, 'w');
//var_dump($fp);
foreach($query as $coords) {
    //var_dump($coords);
    fputcsv($fp,array_values($coords),"\t" );

}

fclose($fp);

$file_out = $path.'coords_converted.csv';
$file_joined = $path.'coords_joined.csv';


exec("proj +proj=tmerc +lat_0=0 +lon_0=21 +k=0.999923 +x_0=7500000 +y_0=0 +ellps=GRS80 +units=m +no_defs -f %12.6f -I < $file > $file_out");
exec("paste -d'\\t' $file $file_out > $file_joined");




// Aby za każdym razem czytać plik od początku a nie gdzieś od środka

if (($handle = fopen($file_joined, 'r')) !== FALSE)
{
    while (($row = fgetcsv($handle, 1000, "\t")) !== FALSE)
    {
        //print_r($row);
        $res = $db->update("notification", array("lattitude" => trim($row[3]), "longtitude" => trim($row[2])), array("lattitude_2000" => trim($row[1]), "longtitude_2000" => trim($row[0])));
//        $res = $db->updateRow('notification',
//            array('lattitude'=>trim($row[3]),'longtitude'=>trim($row[2])),
//            array('lattitude_2000'=>trim($row[1]),'longtitude_2000'=>trim($row[0]))
//        );
        //var_dump($res);
    }

    fclose($handle);
}