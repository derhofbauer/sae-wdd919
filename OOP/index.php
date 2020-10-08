<?php

include_once 'Car.php';

//$polo = new Car('VW', 'W-1234X', 3);
//$polo->setOwner('Alex');

//$polo->drive(50);
//var_dump($polo->speed);
//$polo->break();
//var_dump($polo->speed);

$dropdownValues = Car::getAllSupportedModels();
var_dump($dropdownValues);
