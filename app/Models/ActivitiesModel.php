<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;
class ActivitiesModel extends Model
{

    protected $userTable;
    protected $table;
    protected $allowedFields = ['user_id', 'activity_type', 'section', 'content', 'created_at', 'updated_at'];

    public function __construct() {
        parent::__construct();
        $this->table = DbTables::$activitiesTable;
        foreach(DbTables::initTables() as $key) {
            if (property_exists($this, $key)) {
                $this->{$key} = DbTables::${$key};
            }
        }
    }

    /**
     * Get records from the activities table
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
                            ) FROM {$this->userTable} u WHERE u.id = a.user_id LIMIT 1) as user");

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
                    ) FROM {$this->userTable} u WHERE u.id = a.user_id LIMIT 1) as user");

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
    public function createRecord($data) {
        try {
            $this->db->table($this->table)->insert($data);
            return $this->db->insertID();
        } catch(DatabaseException $e) {
            return false;
        }
    }
    
}