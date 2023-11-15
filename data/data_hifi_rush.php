<?php

return [
    "id"    => "data_hifi_rush",
    "name"  => "My Heart Feels No Pain (Chroma+) [Hi-Fi RUSH Soundtrack] ",
    "url"   => "",
    "file"  => "32222-My Heart Feels No Pain (Chroma+) [Hi-Fi RUSH Soundtrack] - The Glass Pyramids\\ExpertStandard.dat",

    // parsed stuff
    "notes" => [],
    "bombs" => [],
    "walls" => [],

    // beat patterns (start, finish, include last note? bombs? walls?)
    "patterns" => [
        "double_arcs_with_walls" => [
            17, 23, false, true, true,
        ],
        "long_arcs_with_walls" => [
            81, 87, true, true, true,
        ],
        "long_arc_right_hand" => [
            97, 108, true, true, true
        ],
        "long_arc_left_hand" => [
            113, 123, true, true, true
        ]
    ]
];