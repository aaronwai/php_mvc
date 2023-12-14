<?php

namespace App\Controllers;


use Framework\Database;
use Framework\Validation;

class ListingController {
    protected $db;
    public function __construct() 
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    public function index()
    {
        $listings = $this->db->query('SELECT * FROM listings')->fetchAll();
        loadView('listings/index', [
            'listings' => $listings
        ]);
    }

    public function create() {
        loadView('listings/create');
    }

    public function show($params) {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];
        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();
        if (!$listing) {
            ErrorController::notFound('Listing not found !');
            return;
        }
        loadView('listings/show', [
            'listing' => $listing
        ]);
    }

    /**
     *  Store data in database
     * 
     * @return void
     */

    public function store() {
        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'benefits'];
        // return intersect of 2 array, $allowedFields has value only so flip the value to key then we can get the intersection  
        
        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));

        $newListingData['user_id'] = 1;
        // clean up input data
        $newListingData = array_map('sanitize', $newListingData);
        // prepare required fields
        $requiredFields = ['title', 'description', 'salary', 'email', 'city', 'state'];

        $errors = [];
        //  check empty and not string for required fields, then put error message 
        foreach ($requiredFields as $field) {
            if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }

        if (!empty($errors)) {
            // Reload view with errors, and keep the input passing back to the form
            loadView('listings/create', [
            'errors' => $errors,
            'listing' => $newListingData]);
        } else {
      // Submit data
        $fields = [];
        // add field names into fields array
        foreach($newListingData as $field => $value) {
            $fields[] = $field;
        }
        $fields = implode(', ', $fields); 

        $values = [];
        // add field names into fields array
        foreach($newListingData as $field => $value) {
            // convert empty strings to null
            if ($value === '') {
                $newListingData[$field] = null;
            }
            $values[] = ':' . $field;
        }
        $values = implode(', ', $values); 
        $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";
        $this->db->query($query, $newListingData);

        // add redirect to a given url 
        redirect('/listings');
        }
    }
}    