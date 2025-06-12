<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;
class ClassesModel extends Model
{

    protected $userTable;
    protected $table;
    protected $classAttendeesTable;
    protected $coursesTable;
    protected $allowedFields = [
        'course_id', 'title', 'description', 'class_type', 'class_date', 'start_time', 'end_time', 
        'class_duration', 'class_link', 'class_password', 'materials', 'students_list',
        'user_id', 'status', 'created_at', 'updated_at', 'created_by', 'meeting_type', 
        'maximum_participants', 'is_recurring', 'recurring_interval', 'recurring_end_date',
        'notify_participants'
    ];

    public function __construct() {
        parent::__construct();
        $this->table = DbTables::$classesTable;
        foreach(DbTables::initTables() as $key) {
            if (property_exists($this, $key)) {
                $this->{$key} = DbTables::${$key};
            }
        }
    }

    /**
     * Get records from the classes table
     * 
     * @param array $data
     * @param array $order
     * @param int $offset
     * @param int $limit
     */
    public function getRecords($data = [], $offset = 0, $limit = 100) {

        try {
            $query = $this->db->table("{$this->table} a")
                            ->select("a.*, (SELECT JSON_OBJECT(
                                'id', u.id, 'firstname', u.firstname, 'lastname', u.lastname, 'email', u.email, 'phone', u.phone, 'image', u.image
                            ) FROM {$this->userTable} u WHERE u.id = a.user_id LIMIT 1) as user,
                            (SELECT JSON_OBJECT(
                                'id', c.id, 'title', c.title, 'slug',  c.title_slug, 'subtitle', c.subtitle, 
                                'course_duration', c.course_duration, 'description', c.description, 
                                'language', c.language, 'visibility', c.visibility, 'allow_discussion', c.allow_discussion
                            ) FROM {$this->coursesTable} c WHERE c.id = a.course_id LIMIT 1) as course");

            foreach($data as $key => $value) {
                if(is_array($value)) {
                    $query->whereIn("a.{$key}", $value);
                } else {
                    $query->where("a.{$key}", $value);
                }
            }

            return $query->orderBy('a.id', 'DESC')
                        ->limit($limit, $offset)
                        ->get()
                        ->getResultArray();
            
        } catch(DatabaseException $e) {
            return [];
        }

    }

     /**
     * Get records from the classes table
     * 
     * @param int $id
     * @param array $data
     */
    public function getRecord($id, $data = []) {

        try {
            $query = $this->db->table("{$this->table} a")
                    ->select("a.*, (SELECT JSON_OBJECT(
                        'id', u.id, 'firstname', u.firstname, 'lastname', u.lastname, 'email', u.email, 'phone', u.phone, 'image', u.image
                    ) FROM {$this->userTable} u WHERE u.id = a.user_id LIMIT 1) as user,
                    (SELECT JSON_OBJECT(
                        'id', c.id, 'title', c.title, 'slug',  c.title_slug, 'subtitle', c.subtitle, 
                        'course_duration', c.course_duration, 'description', c.description, 
                        'language', c.language, 'visibility', c.visibility, 'allow_discussion', c.allow_discussion
                    ) FROM {$this->coursesTable} c WHERE c.id = a.course_id LIMIT 1) as course");

            // append the class id to the query
            $query->where("a.id", $id);

            foreach($data as $key => $value) {
                if(is_array($value)) {
                    $query->whereIn("a.{$key}", $value);
                } else {
                    $query->where("a.{$key}", $value);
                }
            }

            return $query->orderBy('a.id', 'DESC')
                        ->get()
                        ->getRowArray();
            
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Create a new class
     * 
     * @param array $data
     */
    public function recordAttendance($data) {
        try {
            $this->db->table($this->classAttendeesTable)->insert($data);
            return $this->db->insertID();
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Remove attendance
     * 
     * @param array $data
     */
    public function removeAttendance($data) {
        try {
            $this->db->table($this->classAttendeesTable)->where($data)->delete();
            return true;
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Remove attendance
     * 
     * @param array $data
     * @param int $offset
     * @param int $limit
     */
    public function listAttendees($data, $offset = 0, $limit = 100) {
        try {
            return $this->db->table($this->classAttendeesTable)
                            ->select("a.*, (SELECT JSON_OBJECT(
                                'id', u.id, 'firstname', u.firstname, 'lastname', u.lastname, 'email', u.email, 'phone', u.phone, 'image', u.image
                            ) FROM {$this->userTable} u WHERE u.id = a.user_id LIMIT 1) as user")
                            ->where($data)
                            ->orderBy('created_at', 'DESC')
                            ->limit($limit, $offset)
                            ->get()
                            ->getResultArray();
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Create a new class
     * 
     * @param array $data
     */
    public function createRecord($data) {
        try {
            $this->db->table($this->table)->insert($data);
            return $this->db->insertID();
        } catch(DatabaseException $e) {
            print $e->getMessage();
            return false;
        }
    }

    /**
     * Update a class
     * 
     * @param int $id
     * @param array $data
     */
    public function updateRecord($id, $data) {
        try {
            $this->db->table($this->table)->where('id', $id)->update($data);
            return true;
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Delete a class
     * 
     * @param int $id
     */
    public function deleteRecord($id) {
        try {
            $this->db->table($this->table)->where('id', $id)->delete();
            return true;
        } catch(DatabaseException $e) {
            return false;
        }
    }
}