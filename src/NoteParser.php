<?php

namespace App\Beatsaber;

class NoteParser
{
    public const FILENAME = __DIR__ ."/../data/parsed_notes.json";
    public const ROOT = "D:\\SteamLibrary\\steamapps\\common\\Beat Saber\\Beat Saber_Data\\CustomLevels\\";

    private array $sheet = [];
    private array $data = [];
    private array $notes = [];
    private array $bombs = [];
    private array $walls = [];
    private array $bursts = [];
    private array $arcs = [];

    // todo - add  which are the multiple thin notes
    // todo - add  which are the arcs, a beat is listed for each arc.

    public function load($sheet) 
    {
        $this->sheet = $sheet;

        // Skip if no patterns
        if (empty($sheet['patterns'])) {
            return;
        }

        Console::write("Loading sheet for: {$sheet['name']}");

        // load data
        $this->data = json_decode(file_get_contents(self::ROOT . $sheet['file']), true);

        // Parse the map
        Console::write("- Parsing notes, bombs and walls...");

        $this->handleNotesParse();
        $this->handleBombsParse();
        $this->handleWallsParse();
        $this->handleBurstsParse();
        $this->handleArcsParse();

        Console::write("- Building beat patterns...");

        // Build patterns
        $this->buildPatterns();

        Console::write("- Complete, saved: {$sheet['id']}");
        Console::space();
    }

    public function save() 
    {
        $sheet = $this->sheet;
        $sheet['notes'] = $this->notes;
        $sheet['bombs'] = $this->bombs;
        $sheet['walls'] = $this->walls;
        $sheet['bursts'] = $this->bursts;
        $sheet['arcs'] = $this->arcs;

        $existing = json_decode(file_get_contents(self::FILENAME), true);
        $existing[$sheet['id']] = $sheet;

        file_put_contents(self::FILENAME, json_encode($existing, JSON_PRETTY_PRINT));
    }

    public function reset()
    {
        file_put_contents(self::FILENAME, "{}");
    }

    private function handleNotesParse() {
        if (isset($this->data['_notes'])) {
            foreach($this->data['_notes'] as $note) {
                $this->notes[] = [
                    "b" => $this->roundToNearestQuarter($note['_time']),
                    "c" => $note['_type'],
                    "y" => $note['_lineLayer'],
                    "x" => $note['_lineIndex'],
                    "d" => $note['_cutDirection']
                ];
            }
        }

        if (isset($this->data['colorNotes'])) {
            foreach($this->data['colorNotes'] as $note) {
                $note['b'] = $this->roundToNearestQuarter($note['b']);
                $this->notes[] = $note;
            }
        }
    }

    private function handleBombsParse() 
    {
        if (isset($this->data['bombNotes'])) {
            foreach($this->data['bombNotes'] as $bomb) {
                $this->bombs[] = $bomb;
            }
        }
    }

    private function handleWallsParse()
    {
        if (isset($this->data['obstacles'])) {
            foreach($this->data['obstacles'] as $wall) {
                $this->walls[] = $wall;
            }
        }
    }

    private function handleBurstsParse()
    {
        if (isset($this->data['burstsliders'])) {
            foreach($this->data['burstsliders'] as $burst) {
                $this->bursts[] = $burst;
            }
        }
    }

    private function handleArcsParse()
    {
        if (isset($this->data['sliders'])) {
            foreach($this->data['sliders'] as $arc) {
                $this->arcs[] = $arc;
            }
        }
    }

    private function buildPatterns()
    {
        foreach ($this->sheet["patterns"] as $beatName => $beatData) {
            [$start, $finish, $includeLastNote, $includeBombs, $includeWalls] = $beatData;

            $beatNotes = [];
            $beatBombs = [];
            $beatWalls = [];

            // Loop through notes and grab the ones for this beat
            foreach ($this->notes as $note) {
                if ($note['b'] > $finish) break;
                if ($note['b'] < $start) continue;

                // if this is the last note and we're not including it, skip.
                if ($note['b'] == $finish && !$includeLastNote) continue;

                // Reduce number down to just decimal
                $note['b'] = $note['b'] - $start;

                $beatNotes[] = $note;
            }

            // if we're including bombs
            if ($includeBombs) {
                foreach ($this->bombs as $bomb) {
                    if ($bomb['b'] > $finish) break;
                    if ($bomb['b'] < $start) continue;

                    // Reduce number down to just decimal
                    $bomb['b'] = $bomb['b'] - floor($bomb['b']);
    
                    $beatBombs[] = $bomb;
                }
            }

            // if we're including walls
            if ($includeWalls) {
                foreach ($this->walls as $wall) {
                    if ($wall['b'] > $finish) break;
                    if ($wall['b'] < $start) continue;

                    // Reduce number down to just decimal
                    $wall['b'] = $wall['b'] - floor($wall['b']);
    
                    $beatWalls[] = $wall;
                }
            }

            $this->sheet["patterns"][$beatName] = [
                'length' => $finish - $start,
                'notes'  => $beatNotes,
                'bombs'  => $beatBombs,
                'walls'  => $beatWalls,
            ];
        }
    }

    private function roundToNearestQuarter($number) 
    {
        $rounded = round($number * 4) / 4;
        return (float)number_format($rounded, 2);
    }
}