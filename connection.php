<?php

    include "credentials.php";
    
    //Make a database connection
    $connection = new mysqli('localhost', $user, $pw, $db);

    //Select all records from our table
    $record = $connection->prepare("select * from SCP");
    $record->execute();
    $result = $record->get_result();

?>
