<?php
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'project');
   define('DB_PASSWORD', 'Wer67ner!');
   define('DB_DATABASE', 'sio_project');
   $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);

   ini_set('log_errors', 1);
   ini_set('error_log', 'errors.log');
?>