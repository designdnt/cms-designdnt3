﻿<?php

namespace DntTest;

use DateTime;
use DntLibrary\Base\Dnt;

class WeekNumberTest
{

    public function run()
    {
        $date = new DateTime(Dnt::datetime());
        $week = $date->format("W");
        echo "Weeknummer: <b>$week</b>";
    }

}
