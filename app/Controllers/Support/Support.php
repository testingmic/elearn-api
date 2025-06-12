<?php

namespace App\Controllers\Support;

use App\Controllers\LoadController;
use App\Libraries\Routing;

class Support extends LoadController {

    /**
     * Contact us
     * 
     * @return array
     */
    public function contacts() {

        // get the contacts
        $contacts = $this->supportModel->getContacts(['request_type' => 'contact'], $this->payload['limit'], $this->payload['offset']);

        // return the contacts
        return Routing::success(formatContacts($contacts));
    }

    /**
     * Contact us
     * 
     * @return array
     */
    public function contact() {

        // create the contact
        $contactId = $this->supportModel->createContact($this->submittedPayload);

        // return the contact
        return Routing::created([
            'data' => 'Contact created successfully',
            'record' => $this->getcontact($contactId)
        ]);
    }

    /**
     * Get a contact
     * 
     * @return array
     */
    public function getcontact($contactId = null) {

        if(empty($contactId)) {
            $contactId = $this->payload['id'];
        }

        // get the contact
        $contact = $this->supportModel->getContact(['id' => $contactId, 'request_type' => 'contact']);

        if(empty($contact)) {
            return Routing::notFound('Contact not found', true);
        }

        $contact = formatContacts([$contact])[0];

        $contact['replies'] = formatSupportReplies($this->supportModel->getContactReplies(['contact_id' => $contactId]));

        // return the contact
        return Routing::success($contact);
    }

    /**
     * Reply to a contact
     * 
     * @return array
     */
    public function replycontact() {

        // check if the contact ID is provided
        if(empty($this->payload['contact_id'])) {
            return Routing::error('Contact ID is required');
        }

        // get the contact
        $contact = $this->supportModel->getContact(['id' => $this->payload['contact_id'], 'request_type' => 'contact']);

        if(empty($contact)) {
            return Routing::notFound('Contact not found', true);
        }

        $payload = [
            'created_by' => $this->currentUser['user_id'],
            'contact_id' => $this->payload['contact_id'],
            'message' => $this->payload['message']
        ];

        // create the reply
        $this->supportModel->createContactReply($payload);

        // update the contact
        $this->supportModel->updateContact(['id' => $this->payload['contact_id']], [
            'repliesCount' => $contact['repliesCount'] + 1,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // return the reply
        return Routing::created([
            'data' => 'Reply created successfully',
            'record' => $this->getcontact($this->payload['contact_id'])['data']
        ]);
    }

    /**
     * List all categories
     * 
     * @return array
     */
    public function listcategory() {

        $payload = [];

        if(!empty($this->payload['status'])) {
            $payload['status'] = $this->payload['status'];
        }

        // get the categories
        $categories = $this->supportModel->getCategories($payload, $this->payload['limit'], $this->payload['offset']);

        // return the categories
        return Routing::success($categories);

    }
    
    /**
     * View a category
     * 
     * @param int $id
     * @return array
     */
    public function viewcategory() {

        $field = preg_match("/^[0-9]+$/", $this->payload['id']) ? 'id' : 'name_slug';
        $payload = [$field => $this->payload['id']];

        // get the category
        $category = $this->supportModel->getCategory($payload);

        if(empty($category)) {
            return Routing::notFound('Category not found', true);
        }

        // return the category
        return Routing::success($category);
    }

    /**
     * Create a category
     * 
     * @return array
     */
    public function createcategory() {

        // create the slug
        $create_slug = url_title($this->payload['name'], '-', true);

        // check if the slug already exists
        $check_slug = $this->supportModel->getCategory(['name_slug' => $create_slug]);
        if(!empty($check_slug)) {
            return Routing::error('Category already exists');
        }

        // set the created by
        if(!empty($this->currentUser['user_id'])) {
            $this->submittedPayload['created_by'] = $this->currentUser['user_id'];
        }

        // check if the parent category exists
        if(!empty($this->payload['parent_id'])) {
            $parentCategory = $this->supportModel->getCategory(['id' => $this->payload['parent_id']]);
            if(empty($parentCategory)) {
                return Routing::error('Parent category not found');
            }
        }

        // create the category
        $this->submittedPayload['name_slug'] = $create_slug;

        // create the category
        $categoryId = $this->supportModel->createCategory($this->submittedPayload);

        // log the count
        $this->analyticsObject->logCount('Support Categories');

        // get the category
        return Routing::created([
            'data' => 'Category created successfully',
            'record' => $this->supportModel->getCategory(['id' => $categoryId])
        ]);

    }

    /**
     * Update a category
     * 
     * @param int $id
     * @return array
     */
    public function updatecategory() {
        
        // get the category
        $category = $this->supportModel->getCategory($this->payload['id']);
        
        if(empty($category)) {
            return Routing::notFound('Category not found', true);
        }

        // check if the parent category exists
        if(!empty($this->payload['parent_id'])) {
            $parentCategory = $this->supportModel->getCategory($this->payload['parent_id']);
            if(empty($parentCategory)) {
                return Routing::error('Parent category not found');
            }
        }

        // update the last date
        $this->submittedPayload['updated_at'] = date('Y-m-d H:i:s');

        // update the category
        $this->supportModel->updateCategory($this->payload['id'], $this->submittedPayload);

        // return the category
        return Routing::updated('Category updated successfully', $this->supportModel->getCategory($this->payload['id']));
    }

    /**
     * Delete a category
     * 
     * @param int $id
     * @return array
     */
    public function deletecategory() {

        // get the category
        $category = $this->supportModel->getCategory($this->payload['id']);
        
        if(empty($category)) {
            return Routing::notFound('Category not found', true);
        }

        // delete the category
        $this->supportModel->deleteCategory($this->payload['id']);

        // log the count
        $this->analyticsObject->logCount('Support Categories', 'decrement');

        // return the category
        return Routing::deleted();
    }
}
?>