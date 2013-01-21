<?php

include '../includes/MySQL.php';


/*CREATE TABLE IF NOT EXISTS example (
  id int(11) NOT NULL AUTO_INCREMENT,
  title varchar(255) DEFAULT NULL,
  value int(11) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;*/

MySQL::Config('localhost', 'root', '', 'test');

?>