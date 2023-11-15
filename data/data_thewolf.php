<?php

return [
    "id"    => "data_thewolf",
    "name"  => "The Wolf - Siames",
    "url"   => "https://beatsaver.com/maps/33a6b",
    "file"  => "33a6b-The Wolf - SIAMÉS\\ExpertStandard.dat",

    // parsed stuff
    "notes" => [],
    "bombs" => [],
    "walls" => [],

    // beat patterns (start, finish, include last note? bombs? walls?)
    "patterns" => [
        # Can loop really well
        "bomb_bouncing_short_1" => [
            3, 5, false, true, true
        ],

        # This can't loop well without a spacer pattern
        "diagonal_slashes_1" => [
            26, 41, true, true, true
        ],

        "bomb_huge_sweeps" => [
            79.5, 93.5, true, true, true
        ]
    ]
];