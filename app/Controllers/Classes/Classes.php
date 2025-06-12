<?php

namespace App\Controllers\Classes;

use App\Controllers\LoadController;
use App\Libraries\Routing;

class Classes extends LoadController {

    private $updateRequest = false;

    /**
     * List classes
     * 
     * @return array
     */
    public function list() {

        // trigger models
        $this->triggerModel(['courses', 'enrollments']);

        // apply some checks
        $payload = [];

        if(!is_admin($this->currentUser)) {
            $payload['user_id'] = $this->currentUser['user_id'];
        }

        if(is_admin($this->currentUser) && !empty($this->payload['user_id'])) {
            $payload['user_id'] = $this->payload['user_id'];
        }

        if(is_student($this->currentUser)) {
            // get the list of students enrolled for the course
            $coursesList = $this->enrollmentsModel->getQueryList(['user_id' => $this->currentUser['user_id']], [], 'course_id');
            $payload['course_id'] = array_column($coursesList, 'course_id');
        }

        // get the list of classes
        $classes = $this->classesModel->getRecords($payload, $this->payload['offset'], $this->payload['limit']);

        return Routing::success(formatClassesResponse($classes));
    }

    /**
     * View a class
     * 
     * @return array
     */
    public function view() {

        // trigger models
        $this->triggerModel(['courses', 'enrollments']);

        // apply some checks
        $payload = [];

        if(!is_admin($this->currentUser)) {
            $payload['user_id'] = $this->currentUser['user_id'];
        }

        if(is_admin($this->currentUser) && !empty($this->payload['user_id'])) {
            $payload['user_id'] = $this->payload['user_id'];
        }

        // get the class record
        $classRecord = $this->classesModel->getRecord($this->payload['class_id'], $payload);

        $finalRecord = !empty($classRecord) ? formatClassesResponse([$classRecord], true) : [];


        // create a course object
        $courseObject = (new \App\Controllers\Courses\Courses());

        $this->payload['course_id'] = $finalRecord['course']['id'];
        $courseObject->payload = $this->payload;
        $courseObject->currentUser = $this->currentUser;

        unset($finalRecord['course']);
        $finalRecord['course_info'] = $courseObject->view()['data'];

        // return the class record
        return Routing::success($finalRecord);
    }

    /**
     * Create a new class
     * 
     * @return array
     */
    public function create() {

        $this->triggerModel(['courses', 'enrollments']);

        // get the record infomation
        $courseRecord = $this->coursesModel->getRecord($this->payload['course_id']);
        if(empty($courseRecord)) {
            return Routing::notFound();
        }

        if(strtotime($this->payload['class_date']) < strtotime(date('Y-m-d'))) {
            return Routing::error('Class date cannot be before the course start date');
        }

        // check if the user is authorized to create a class
        if(!is_admin_or_instructor($this->currentUser)) {
            return Routing::error('You are not authorized to schedule a class');
        }

        $typeKeys = array_keys(getClassTypes());
        $typeValues = array_values(getClassTypes());

        
        // check if there is a pending class for the same date and time
        $pendingClass = $this->classesModel->getRecords([
            'course_id' => $this->payload['course_id'],
            'class_date' => $this->payload['class_date'],
            'start_time' => $this->payload['start_time']
        ]);

        if(!empty($pendingClass)) {
            return Routing::error('There is already a class scheduled for the same date and time');
        }

        if(!in_array($this->payload['class_type'], $typeKeys) && !in_array($this->payload['class_type'], $typeValues)) {
            return Routing::error('Invalid class type; must be at be one of the keys or values in the array.', ['class_type' => getClassTypes()]);
        }

        // if the class type key if the name was found in the value
        if(!in_array($this->payload['class_type'], $typeKeys)) {
            $this->payload['class_type'] = array_search($this->payload['class_type'], $typeValues);
            $this->payload['class_type'] = $typeKeys[$this->payload['class_type']];
        }

        // convert the materials to json if its an array
        if(!empty($this->submittedPayload['materials'])) {
            $this->submittedPayload['materials'] = !empty($this->submittedPayload['materials']) ? (
                is_array($this->submittedPayload['materials']) ? 
                    json_encode($this->submittedPayload['materials']) : 
                    $this->submittedPayload['materials']
            ) : '';
        }

        // convert the students list to comma separated string if an array
        if(!empty($this->submittedPayload['students_list'])) {

            // convert the students list to comma separated string if an array
            $this->submittedPayload['students_list'] = !empty($this->submittedPayload['students_list']) ? (
                is_array($this->submittedPayload['students_list']) ? 
                    implode(',', $this->submittedPayload['students_list']) : 
                    $this->submittedPayload['students_list']
            ) : '';

            // get the list of students enrolled for the course
            $studentsList = $this->enrollmentsModel->getQueryList(['course_id' => $this->submittedPayload['course_id']], stringToArray(
                $this->submittedPayload['students_list']
            ), 'user_id');

            // check if the list of students are part of the list of students enrolled for the course
            if(count($studentsList) !== count(stringToArray($this->submittedPayload['students_list']))) {
                return Routing::error('One or more students are not enrolled for this course');
            }

        }

        // if the request is an update, return the payload
        if($this->updateRequest) {
            return $this->submittedPayload;
        }

        // set the user id and created by
        $this->submittedPayload['user_id'] = $this->currentUser['user_id'];
        $this->submittedPayload['created_by'] = $this->currentUser['user_id'];

        // create the class
        $classId = $this->classesModel->createRecord($this->submittedPayload);

        $this->payload['class_id'] = $classId;

        // get the class record
        return Routing::created([
            'data' => 'Class created successfully',
            'record' => $this->view()['data']
        ]);

    }

    public function update() {

    }

    public function delete() {

    }

}
?>