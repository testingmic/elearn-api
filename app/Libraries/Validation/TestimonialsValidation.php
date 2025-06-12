<?php

namespace App\Libraries\Validation;

class TestimonialsValidation {

    public $routes = [
        'list' => [
            'method' => 'GET',
            'payload' => [
                'limit' => 'permit_empty|integer',
                'offset' => 'permit_empty|integer',
            ]
        ],
        'create' => [
            'method' => 'POST',
            'authenticate' => true,
            'payload' => [
                'name' => 'required|string|max_length[255]',
                'email' => 'required|valid_email|max_length[255]',
                'title' => 'permit_empty|string|max_length[255]',
                'message' => 'required|string|max_length[1000]',
                'image' => 'permit_empty|string|max_length[255]',
                'rating' => 'integer|greater_than_equal_to[1]|less_than_equal_to[5]',
            ]
        ],
        'update' => [
            'method' => 'PUT',
            'authenticate' => true,
            'payload' => [
                'name' => 'permit_empty|string|max_length[255]',
                'email' => 'permit_empty|valid_email|max_length[255]',
                'title' => 'permit_empty|string|max_length[255]',
                'message' => 'permit_empty|string|max_length[1000]',
                'image' => 'permit_empty|string|max_length[255]',
                'rating' => 'integer|greater_than_equal_to[1]|less_than_equal_to[5]',
            ]
        ],
        'view:testimonial_id' => [
            'method' => 'GET',
            'payload' => [
                'testimonial_id' => 'required|integer',
            ]
        ],
        'delete:testimonial_id' => [
            'method' => 'DELETE',
            'authenticate' => true,
            'payload' => [
                'testimonial_id' => 'required|integer',
            ]
        ]
    ];

}

?>