<?php
$connection1 = pg_connect("host=127.0.0.1 port=5432 dbname=reports user=reportuser password=") or die("pg_connect #1 returned error");
$connection2 = pg_connect("host=127.0.0.1 port=5432 dbname=reports user=admin password=") or die("pg_connect #2 returned error"); ?>