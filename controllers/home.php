<?php

$config = require basePath('config/db.php');
$db = new Database($config);

$listings = $db->query('SELECT * FROM listings LIMIT 6')->fetchAll();

// dump the database data, if dump occur then db connectrion is working
// inspect($listings);

loadView('home', [
    'listings' => $listings
]);
