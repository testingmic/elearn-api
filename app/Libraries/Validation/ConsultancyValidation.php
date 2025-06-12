<?php

namespace App\Libraries\Validation;

class ConsultancyValidation {

    public $routes = [
        'list' => [
            'method' => 'GET',
            'partial_authenticate' => true,
            'payload' => [
                "status" => "permit_empty|string|max_length[255]",
            ]
        ],
        'contact' => [
            'method' => 'POST',
            'partial_authenticate' => true,
            'payload' => [
                "project_type" => "required|string|max_length[255]",
                "name" => "required|string|max_length[255]",
                "organization" => "permit_empty|string|max_length[255]",
                "email" => "required|valid_email|max_length[255]",
                "phone" => "required|string|max_length[255]",
                "project_title" => "required|string|max_length[255]",
                "message" => "required|string|max_length[1000]",
                "budget" => "required|string|max_length[255]",
                "timeline" => "required|string|max_length[255]",
                "privacy_policy" => "required|in_list[yes]",
            ]
        ],
        'getrequest:request_id' => [
            'method' => 'GET',
            'authenticate' => true,
            'payload' => [
                "request_id" => "required|integer",
            ]
        ],
        'replyrequest:request_id' => [
            'method' => 'POST',
            'authenticate' => true,
            'payload' => [
                "request_id" => "required|integer",
                "message" => "required|string|max_length[1000]",
            ]
        ]
    ];

}
?>