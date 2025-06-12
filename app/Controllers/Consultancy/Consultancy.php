<?php
namespace App\Controllers\Consultancy;

use App\Controllers\LoadController;
use App\Libraries\Routing;

class Consultancy extends LoadController {

    /**
     * List consultancy requests
     * 
     * @return array
     */
    public function list() {
        // trigger models
        $this->triggerModel(['support']);

        $payload = ['request_type' => 'consultancy'];

        // if the user is not an admin, then filter the requests by the user id
        if(!is_admin($this->currentUser)) {
            $payload['created_by'] = $this->currentUser['user_id'];
        }
        
        // get the consultancy requests
        $consultancy = $this->supportModel->getContacts($payload);

        // return the consultancy requests
        return Routing::success(consultancyRequests($consultancy));
    }

    /**
     * Request consultancy
     * 
     * @return array
     */
    public function contact() {

        // trigger models
        $this->triggerModel(['support']);

        $consultancy = getConsultancyTypes();

        // validate the payload
        foreach(['project_type', 'budget', 'timeline'] as $key) {
            if(!in_array($this->submittedPayload[$key], array_keys($consultancy[$key]))) {
                return Routing::error('Invalid ' . $key . ' selected: ' . implode(', ', array_keys($consultancy[$key])));
            }
        }

        // set the request type
        $this->submittedPayload['request_type'] = 'consultancy';

        // find the user by email address
        if(empty($this->currentUser['user_id'])) {
            $userId = $this->usersModel->findByEmail($this->payload['email']);
            if(!empty($userId)) {
                $this->submittedPayload['created_by'] = $userId['id'];
            }
        } else {
            $this->submittedPayload['created_by'] = $this->currentUser['user_id'];
        }

        // create the consultancy request
        $this->supportModel->createContact($this->submittedPayload);

        // log the count
        $this->analyticsObject->logCount('Consultancy Requests');

        // return the success message
        return Routing::success('Consultancy request submitted successfully');

    }

    /**
     * Get a request
     * 
     * @return array
     */
    public function getrequest($requestId = null) {

        // trigger models
        $this->triggerModel(['support']);

        if(empty($requestId)) {
            $requestId = $this->payload['request_id'];
        }

        // set the payload
        $payload = ['id' => $requestId, 'request_type' => 'consultancy'];

        // if the user is not an admin, then filter the requests by the user id
        if(!is_admin($this->currentUser)) {
            $payload['created_by'] = $this->currentUser['user_id'];
        }

        // get the request
        $contact = $this->supportModel->getContact($payload);

        if(empty($contact)) {
            return Routing::notFound('Request not found', true);
        }

        $contact = consultancyRequests([$contact])[0];

        $contact['replies'] = formatSupportReplies($this->supportModel->getContactReplies(['contact_id' => $requestId]));

        // return the request
        return Routing::success($contact);
    }

    /**
     * Reply to a contact
     * 
     * @return array
     */
    public function replyrequest() {

        // trigger models
        $this->triggerModel(['support']);

        // check if the Request ID is provided
        if(empty($this->payload['request_id'])) {
            return Routing::error('Request ID is required');
        }

        // get the Request
        $contact = $this->supportModel->getContact(['id' => $this->payload['request_id'], 'request_type' => 'consultancy']);

        if(empty($contact)) {
            return Routing::notFound('Request not found', true);
        }

        $payload = [
            'created_by' => $this->currentUser['user_id'],
            'contact_id' => $this->payload['request_id'],
            'message' => $this->payload['message']
        ];

        // create the reply
        $this->supportModel->createContactReply($payload);

        // update the contact
        $this->supportModel->updateContact(['id' => $this->payload['request_id']], [
            'repliesCount' => $contact['repliesCount'] + 1,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // return the reply
        return Routing::created([
            'data' => 'Reply created successfully',
            'record' => $this->getrequest($this->payload['request_id'])['data']
        ]);
    }

}
?>