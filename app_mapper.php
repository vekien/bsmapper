<?php

require __DIR__ .'/vendor/autoload.php';

use App\Beatsaber\Mapper;
use App\Beatsaber\Console;

Console::space();
Console::write("BS Mapper - Building Songs!");
Console::space();

# ---------------------------------------------------------------
# Song: Ascension
# ---------------------------------------------------------------

$mapper = new Mapper("Ascension", "Ascension\\ExpertStandard.dat");

$mapper->addPattern("data_thewolf", "diagonal_slashes_1");
$mapper->addBeatCounter(-0.5);
$mapper->addPattern("data_thewolf", "diagonal_slashes_1");

# Beat 33, a side to side slam
$mapper->addNotes([
    [33, 5, "red", "←"],
    [33, 8, "blue", "→"],
]);

# Beat 34 a new pattern
$mapper->debug()->save();