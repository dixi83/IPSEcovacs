<?php

/*
 * @addtogroup growatt
 * @{
 *
 * @package       Growatt
 * @file          module.php
 * @author        Martijn Diks
 * @copyright     2018 Martijn Diks
 * @license       
 * @version       0.1
 *
 */
require_once(__DIR__ . "/../libs/GrowattModule.php");  // diverse Klassen

class EV extends Ecovacs
{
    const PREFIX = 'EV';

    public static $Variables = [
		['Battery Power ', vtInteger, 'Battery.100', 4, 1 ,true],
	];
}


