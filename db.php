<?php

function load_notes() {
    $file = __DIR__ . "/notes/notes.json";
    if(!file_exists($file)){
        file_put_contents($file, json_encode([]));
    }
    $data = file_get_contents($file);
    $notes = json_decode($data, true);

    if(!is_array($notes)){
        return [];
    }
    return $notes;
}

function save_notes($notes) {
    $file = __DIR__ . "/notes/notes.json";
    $result = file_put_contents($file, json_encode($notes, JSON_PRETTY_PRINT));
    return $result !== false;
}
