<?php 
namespace App\Libraries\Validation;

class SupportValidation {

    public $routes = [
        'list' => [
            'method' => 'GET',
            'payload' => [
                "status" => "permit_empty|string|max_length[255]",
                "category_id" => "permit_empty|integer",
                "subcategory_id" => "permit_empty|integer",
                "search" => "permit_empty|string|max_length[255]",
                "sort" => "permit_empty|string|max_length[255]",
                "order" => "permit_empty|string|max_length[255]",
                "limit" => "permit_empty|integer",
                "offset" => "permit_empty|integer",
            ]
        ],
        'view:id' => [
            'method' => 'GET',
            'payload' => [
                "id" => "required",
            ]
        ],
        'create' => [
            'method' => 'POST',
            'authenticate' => true,
            'isAdmin' => true,
            'payload' => [
                "title" => "required|string|max_length[255]",
                "description" => "permit_empty|string|max_length[255]",
                "content" => "required|string",
                "image" => "permit_empty|string|max_length[255]",
                "status" => "permit_empty|string|max_length[255]",
                "category_id" => "permit_empty|integer",
                "subcategory_id" => "permit_empty|integer",
                "image" => "permit_empty|string",
                "thumbnail" => "permit_empty|string",
                "tags" => "permit_empty|string|max_length[255]",
                "writer" => "permit_empty|string|max_length[255]",
                "created_by" => "permit_empty|integer",
            ]
        ],
        'update:id' => [
            'method' => 'PUT',
            'authenticate' => true,
            'isAdmin' => true,
            'payload' => [
                "id" => "required|integer",
                "title" => "permit_empty|string|max_length[255]",
                "description" => "permit_empty|string|max_length[255]",
                "content" => "permit_empty|string",
                "image" => "permit_empty|string|max_length[255]",
                "status" => "permit_empty|string|max_length[255]",
                "category_id" => "permit_empty|integer",
                "subcategory_id" => "permit_empty|integer",
                "image" => "permit_empty|string",
                "thumbnail" => "permit_empty|string",
                "tags" => "permit_empty|string|max_length[255]",
                "writer" => "permit_empty|string|max_length[255]",
            ]
        ],
        'delete:id' => [
            'method' => 'DELETE',
            'authenticate' => true,
            'isAdmin' => true,
            'payload' => [
                "id" => "required",
            ]
        ],
        'listcategory' => [
            'method' => 'GET',
            'payload' => [
                "status" => "permit_empty|string|max_length[255]",
                "limit" => "permit_empty|integer",
                "offset" => "permit_empty|integer",
            ]
        ],
        'viewcategory:id' => [
            'method' => 'GET',
            'payload' => [
                "id" => "required",
            ]
        ],
        'createcategory' => [
            'method' => 'POST',
            'authenticate' => true,
            'isAdmin' => true,
            'payload' => [
                "name" => "required|string|max_length[255]",
                "description" => "permit_empty|string|max_length[255]",
                "image" => "permit_empty|string|max_length[255]",
                "status" => "permit_empty|string|max_length[255]",
                "parent_id" => "permit_empty|integer",
            ]
        ],
        'updatecategory:id' => [
            'method' => 'PUT',
            'authenticate' => true,
            'isAdmin' => true,
            'payload' => [
                "id" => "required|integer",
                "name" => "permit_empty|string|max_length[255]",
                "description" => "permit_empty|string|max_length[255]",
                "image" => "permit_empty|string|max_length[255]",
                "status" => "permit_empty|string|max_length[255]",
                "parent_id" => "permit_empty|integer",
            ]
        ],
        'deletecategory:id' => [
            'method' => 'DELETE',
            'authenticate' => true,
            'isAdmin' => true,
            'payload' => [
                "id" => "required|integer",
            ]
        ],
        'contact' => [
            'method' => 'POST',
            'payload' => [
                "name" => "required|string|max_length[255]",
                "subject" => "required|string|max_length[255]",
                "email" => "required|valid_email|max_length[255]",
                "message" => "required|string|max_length[1000]",
            ]
        ],
        'contacts' => [
            'method' => 'GET',
            'payload' => [
                "limit" => "permit_empty|integer",
                "offset" => "permit_empty|integer",
            ]
        ],
        'getcontact:id' => [
            'method' => 'GET',
            'payload' => [
                "id" => "required|integer",
            ]
        ],
        'replycontact:contact_id' => [
            'method' => 'POST',
            'authenticate' => true,
            'isAdmin' => true,
            'payload' => [
                "contact_id" => "required|integer",
                "message" => "required|string|max_length[1000]",
            ]
        ]
    ];

}