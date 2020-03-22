<?php

   include 'db.php';

   $dbconn = new mysqli($dbserver, $dbuser, $dbpassword, $dbname);

   $dbconn->query("CREATE TABLE postcodes (Postcode VARCHAR(5) NOT NULL, Town VARCHAR(20), Xpos INT(11), Ypos INT(11), PRIMARY KEY (Postcode))");

   $fh = fopen('postcodes.csv', 'r');
   if ($fh)
   {

      $line = fgets($fh);

      while ( !feof($fh) )
      {

         $row = explode(',', $line);

         $dbconn->query("INSERT INTO postcodes VALUES('$row[0]', '$row[1]', '$row[2]', '$row[3]')");

         $line = fgets($fh);
    }

    fclose($fh);
}

?>
