<?php

namespace App\Controllers;


use Framework\Database;
use Framework\Validation;
use Framework\Session;
use Framework\Authorization;

class ListingController {
    protected $db;
    public function __construct() 
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    public function index()
    {
        $listings = $this->db->query('SELECT * FROM listings ORDER BY created_at DESC')->fetchAll();
        loadView('listings/index', [
            'listings' => $listings
        ]);
    }

    public function create() {
        loadView('listings/create');
    }

    /**
     *  show a single listing
     * 
     * @param array $params
     * @return void
     */
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

        $newListingData['user_id'] = Session::get('user')['id'];
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
        Session::setFlashMessage('success_message','Listing created successfully !');
        // add redirect to a given url 
        redirect('/listings');
        }
    }

    /**
     * Delete a listing
     * 
     * @param array $params
     * @return void
     */
    public function destroy($params){
        // check the id exist then delete
        $id = $params['id'];
        $params = ['id' => $id];
        $listing = $this->db->query('SELECT * FROM listings WHERE id= :id', $params)->fetch();
        if (! $listing) {
            ErrorController::notFound('Listing not found !');
            return;
        }
        // Authorization
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message','You are not authorized to delete this listing');
            return redirect("/listings/" . $listing->id);
        }
        $this->db->query('DELETE FROM listings WHERE id = :id', $params);

        //  add flash message
        Session::setFlashMessage('success_message','Listing successfully deleted !');
        redirect('/listings');
    }

    /**
     * show the listing in the edit form
     * 
     * @param array $params
     * @return void
     */
    public function edit($params) {

        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];
        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

          // Authorization
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message','You are not authorized to update this listing');
            return redirect("/listings/" . $listing->id);
        } 
        
        if (!$listing) {
            ErrorController::notFound('Listing not found !');
            return;
        }
        loadView('listings/edit', [
            'listing' => $listing
        ]);
    }

    /**
     * Update a listing
     * 
     * @param array $param
     * @return void
     */
    public function update($params) {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];
        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();
        if (!$listing) {
            ErrorController::notFound('Listing not found !');
            return;
        }
        // Authorization
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message','You are not authorized to update this listing');
            return redirect("/listings/" . $listing->id);
        } 
        
        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'benefits'];
        $updateValues = [];
        $updateValues = array_intersect_key($_POST, array_flip($allowedFields));
        $updateValues = array_map('sanitize', $updateValues);
        $requiredFields = ['title', 'description', 'salary', 'email', 'city', 'state'];
        $errors = [];
        //  check empty and not string for required fields, then put error message 
        foreach ($requiredFields as $field) {
            if (empty($updateValues[$field]) || !Validation::string($updateValues[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }
        if (!empty($errors)) {
            // Reload view with errors, and keep the input passing back to the form
            loadView('listings/edit', [
            'errors' => $errors,
            'listing' => $listing]);
            exit;
        } else {
            $updateFields = [];
        
        foreach(array_keys($updateValues) as $field) {
            $updateFields[] = "{$field} = :{$field}";
        }
        $updateFields = implode(', ', $updateFields);

        $updateQuery = "UPDATE listings SET $updateFields WHERE id = :id";
        $updateValues['id'] = $id;
        $this->db->query($updateQuery, $updateValues);
        Session::setFlashMessage('success_message','Listing Updated');
        redirect('/listings/' . $id);
        }
    }
}    