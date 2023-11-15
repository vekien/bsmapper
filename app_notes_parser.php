<?php

require __DIR__ .'/vendor/autoload.php';

use App\Beatsaber\NoteParser;
use App\Beatsaber\Console;

Console::space();
Console::write("BS Mapper - Note Parsing");
Console::space();

/**
 * 
 * This is the big data sheet of .. data
 * 
 * It basically lists all maps that I've found have really good patterns
 * and then describes each pattern from beat X to Y as a "set of beats", these
 * are then pulled and stored separately so that they can be injected into other
 * songs. The generator will handle which patterns should go with which based on
 * the pattern structure you set out, trying to ensure that it has a nice flow.
 * 
 * Credit is assigned for every single pattern.
 * 
 */
$datasheet = [];
foreach (scandir("data") as $file) {
    if (is_file("data/{$file}") && preg_match('/^data_.*$/', $file)) {
        $datasheet[] = require __DIR__ . "/data/{$file}";
        Console::write("> Auto-include: {$file}");
    }
}

# Build logic
Console::space();
Console::write("Parsing Notes...");
Console::space();

// Create parser
$parser = new NoteParser();
$parser->reset();

// Parse first sheet
foreach($datasheet as $ds) {
    $parser->load($ds);
    $parser->save();
}





