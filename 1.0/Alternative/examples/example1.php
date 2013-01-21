<?php

include 'config.php';

MySQL::Create('INSERT INTO example(title, value) VALUES (?title, ?value)')
		->Parameter('title', 'some other title')
		->Parameter('value', 124)
		->NonQuery();

print MySQL::LastId();

?>