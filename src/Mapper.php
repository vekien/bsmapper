<?php

namespace App\Beatsaber;

class Mapper
{
    private const ROOT = "D:\\SteamLibrary\\steamapps\\common\\Beat Saber\\Beat Saber_Data\\CustomWIPLevels\\";
    
    // Count the grid left to right = row/column
    public const GRID = [
        1,
        2,
        3,
        4,
        5,
        6,
        7,
        8,
        9,
        10,
        11,
        12
    ];

    public const COLOURS = [
        "red" => 0,
        "blue" => 1,
    ];

    // d = directions
    public const DIRECTIONS = [
        "↑" => 0, 
        "↓" => 1,   
        "←" => 2,   
        "→" => 3,   
        "↖" => 4,   
        "↗" => 5,   
        "↙" => 6,   
        "↘" => 7,   
        "•" => 8,   
    ];

    private array $parsedNotes = [];
    private string $songName;
    private string $fileName;
    private array $notes = [];
    private array $bombs = [];
    private array $walls = [];
    private array $bursts = [];
    private array $sliders = [];

    public float $beatCounter = 0;

    public function __construct($songName, $fileName, $beatCounter = 0)
    {
        $this->parsedNotes = json_decode(file_get_contents(NoteParser::FILENAME), true);

        // Setup
        $this->songName = $songName;
        $this->fileName = $fileName;
        $this->beatCounter = $beatCounter;

        Console::write("Mapper Setup for: {$songName} at {$fileName}");
        Console::space();
    }

    /**
     * Save the map to the beat saber .dat file
     */
    public function save()
    {
        Console::space();
        Console::write("Saving map...");

        // Save to beat saber map
        $filepath = self::ROOT . $this->fileName;
        $datjson = json_decode(file_get_contents($filepath), true);
        $datjson['colorNotes'] = $this->notes;
        $datjson['bombNotes'] = $this->bombs;
        $datjson['obstacles'] = $this->walls;
        $datjson['burstsliders'] = $this->bursts;
        $datjson['sliders'] = $this->sliders;
        $datjson = json_encode($datjson, JSON_PRETTY_PRINT);

        file_put_contents($filepath, $datjson);

        Console::write("✔  Updated: {$this->songName}");
    }

    /**
     * Saves the notes to a debug file, useful to.. well debug!
     */
    public function debug(): self
    {
        Console::space();
        Console::write("Dumping to debug file...");

        $debug = [
            'notes' => $this->notes,
            'bombs' => $this->bombs,
            'walls' => $this->walls
        ];

        file_put_contents(__DIR__ . "/../map_debug.json", json_encode($debug, JSON_PRETTY_PRINT));

        Console::write("✔  Debug ready: {$this->songName}");

        return $this;
    }

    /**
     * Add a series of notes, in this format:
     * [beat, grid_number, colour, direction]
     * 
     * It will go to the specified beat and then refresh beat counter after.
     * 
     * Info: https://bsmg.wiki/mapping/difficulty-format-v3.html#color-notes
     */
    public function addNotes(array $notes)
    {
        foreach ($notes as $note) {
            [$beat, $gridNumber, $colour, $direction] = $note;

            [$x, $y] = Utils::calculateGridCoordinates($gridNumber);

            $direction = self::DIRECTIONS[$direction];
            $colour = self::COLOURS[$colour];

            $this->notes[] = [
                "b" => $beat,
                "x" => $x,
                "y" => $y,
                "a" => 0,
                "c" => $colour,
                "d" => $direction
            ];
        }

        $this->refreshBeatCounter();
    }

    /**
     * Todo - convert to array similar to addNotes
     */
    public function addBombs($beat, $column, $row, $data)
    {
        $this->bombs[] = [
            "b" => $beat,
            "x" => $column,
            "y" => $row,
            "customData" => $data
        ];
    }

    /**
     * Todo - convert to array similar to addNotes
     */
    public function addWalls($beat, $column, $row, $depth, $width, $height)
    {
        $this->walls[] = [
            "b" => $beat,
            "x" => $column,
            "y" => $row,
            "d" => $depth,
            "w" => $width,
            "h" => $height
        ];
    }

    /**
     * Insert a pattern which uses the Beat Counter as the starting 
     * point and then at the end increases the Beat Counter.
     */
    public function addPattern($id, $pattern)
    {
        $pattern = $this->parsedNotes[$id]["patterns"][$pattern];

        foreach ($pattern['notes'] as $note) {
            $note['b'] = $note['b'] + $this->beatCounter;
            $this->notes[] = $note;
        }

        $this->refreshBeatCounter();
    }

    /**
     * Reduce notes back to a specific beat number, this can be useful if
     * the pattern is longer than desired and is okay to be cut.
     * 
     * It will reduce the note DOWN TO $beatNum
     * 
     * It will always floor so if you remove to 40 it wont remove 40.5 if $includeBeatNum == true
     * 
     * Can be enabled for Walls and Bombs too
     */
    public function reduceEntries($beatNum, $includeBeatNum, $includeBombs, $includeWalls)
    {
        foreach ($this->notes as $i => $row) {
            $rowBeat = floor($row['b']);

            if ($rowBeat < $beatNum) continue;
            if ($beatNum == floor($rowBeat) && !$includeBeatNum) continue;

            // remove
            unset($this->notes[$i]);
        }

        if ($includeBombs) {
            foreach ($this->bombs as $i => $row) {
                $rowBeat = floor($row['b']);
    
                if ($rowBeat < $beatNum) continue;
                if ($beatNum == floor($rowBeat) && !$includeBeatNum) continue;
    
                // remove
                unset($this->bombs[$i]);
            }
        }

        if ($includeWalls) {
            foreach ($this->walls as $i => $row) {
                $rowBeat = floor($row['b']);
    
                if ($rowBeat < $beatNum) continue;
                if ($beatNum == floor($rowBeat) && !$includeBeatNum) continue;
    
                // remove
                unset($this->walls[$i]);
            }
        }

        // refresh beat counter
        $this->refreshBeatCounter();
    }

    /**
     * Refresh the beat counter to the latest last note
     */
    public function refreshBeatCounter() {
        $this->beatCounter = end($this->notes)['b'] + 1;
        $this->beatCounter = Utils::roundToNearestQuarter($this->beatCounter);
    }

    /**
     * Increase the beat counter by a specific beat duration, useful
     * if you don't want any notes for a while. You can also reduce it if needed.
     */
    public function addBeatCounter($amount) {
        $this->beatCounter += $amount;
        $this->beatCounter = Utils::roundToNearestQuarter($this->beatCounter);
    }
}