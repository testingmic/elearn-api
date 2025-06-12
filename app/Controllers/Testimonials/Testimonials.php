<?php

namespace App\Controllers\Testimonials;

use App\Controllers\LoadController;
use App\Libraries\Routing;

class Testimonials extends LoadController {
    
    /**
     * List all categories
     * 
     * @return array
     */
    public function list() {

        // get the categories
        $testimonials = $this->testimonialsModel->getRecords();

        // return the categories
        return Routing::success($testimonials);

    }
    
    /**
     * View a category
     * 
     * @param int $id
     * @return array
     */
    public function view() {

        // get the category
        $testimonial = $this->testimonialsModel->getRecord($this->payload['testimonial_id']);

        if(empty($testimonial)) {
            return Routing::notFound('Testimonial not found', true);
        }

        // return the testimonial
        return Routing::success($testimonial);
    }

    /**
     * Create a category
     * 
     * @return array
     */
    public function create() {

        // set the created by
        $this->payload['created_by'] = $this->currentUser['user_id'];

        // create the category
        $testimonialId = $this->testimonialsModel->createRecord($this->payload);

        // log the count
        $this->analyticsObject->logCount('Testimonials');

        // set the testimonial id
        $this->payload['testimonial_id'] = $testimonialId;

        // get the category
        return Routing::created([
            'data' => 'Testimonial created successfully',
            'record' => $this->testimonialsModel->getRecord($testimonialId)
        ]);

    }

    /**
     * Update a category
     * 
     * @param int $id
     * @return array
     */
    public function update() {

        // get the category
        $testimonial = $this->testimonialsModel->getRecord($this->payload['testimonial_id']);
        
        if(empty($testimonial)) {
            return Routing::notFound('Testimonial not found', true);
        }

        // update the last date
        $this->submittedPayload['updated_at'] = date('Y-m-d H:i:s');

        // update the testimonial
        $this->testimonialsModel->updateRecord($this->payload['testimonial_id'], $this->submittedPayload);

        // return the testimonial
        return Routing::updated('Testimonial updated successfully', $this->testimonialsModel->getRecord($this->payload['testimonial_id']));
    }

    /**
     * Delete a category
     * 
     * @param int $id
     * @return array
     */
    public function delete() {

        // get the tag
        $testimonial = $this->testimonialsModel->getRecord($this->payload['testimonial_id']);
        
        if(empty($testimonial)) {
            return Routing::notFound('Testimonial not found', true);
        }

        // delete the Testimonial
        $this->testimonialsModel->deleteRecord($this->payload['testimonial_id']);

        // log the count
        $this->analyticsObject->logCount('Testimonials', 'decrement');

        // return the testimonial
        return Routing::deleted();
    }
}
?>