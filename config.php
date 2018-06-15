<?php
	//Podaj nazwę bazy danych
	$db_name = 'your_db_name';
//-----------------------------

	$create_db = "CREATE DATABASE IF NOT EXISTS $db_name
	 							DEFAULT CHARACTER SET utf8
								DEFAULT COLLATE utf8_polish_ci";


	return ['host' 			=> 'localhost',
					'user' 			=> 'your_username',
					'password' 	=> 'your_password',
					'db_name' 	=> $db_name];
?>
