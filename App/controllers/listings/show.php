<?php

$config = require basePath('config/db.php');
$db = new Database($config);
// we filter the query part to get id
$id = $_GET['id'] ?? '';
// inspect($id); check the query id is working

$params = [
    'id' => $id
];

$listing = $db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

// inspect($listing);
loadView('listings/show', [
    'listing' => $listing
] );
