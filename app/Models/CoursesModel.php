<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class CoursesModel extends Model
{

    protected $table;
    protected $categoriesTable;
    protected $userTable;
    protected $contentTable;
    protected $instructorsTable;
    protected $reviewsTable;
    protected $enrollmentsTable;
    protected $tagsTable;
    protected $primaryKey = 'id';
    protected $allowedFields = [
        "title",
        "subtitle",
        "title_slug",
        "rating",
        "reviewCount",
        "enrollmentCount",
        "tags",
        "language",
        "visibility",
        "allow_discussion",
        "certification",
        "category_id",
        "subcategory_id",
        "course_type",
        "level",
        "totalDuration",
        "totalLessons",
        "originalPrice",
        "price",
        "viewsCount",
        "what_you_will_learn",
        "requirements",
        "features",
        "description",
        "course_duration",
        "description",
        "created_by",
        "status",
        "created_at",
        "updated_at"
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->table = DbTables::$coursesTable;
        foreach (DbTables::initTables() as $key) {
            if (property_exists($this, $key)) {
                $this->{$key} = DbTables::${$key};
            }
        }
    }

    /**
     * Get all courses
     * 
     * @param int $limit
     * @param int $offset
     * @param string $search
     * @param array $data
     * 
     * @return array
     */
    public function getRecords($limit = 10, $offset = 0, $search = null, $data = []) {
        try {
            // get query
            $query = $this->select("{$this->table}.*, 
                (SELECT JSON_OBJECT(
                    'id', u.id, 'firstname', u.firstname, 'lastname', u.lastname, 'email', u.email, 'phone', u.phone, 'image', u.image
                ) FROM {$this->userTable} u WHERE u.id = {$this->table}.created_by LIMIT 1) as created_by,
                c.name as category_name, c.name_slug as category_slug")
                ->join("{$this->categoriesTable} c", "c.id = {$this->table}.category_id", 'left');

            // search
            if (!empty($search)) {
                $query->like("{$this->table}.title", $search);
            }

            // search by course ids
            if (!empty($data['course_ids'])) {
                $query->whereIn("{$this->table}.id", $data['course_ids']);
            }

            // search by price ranges
            if (!empty($data['price_range']) && is_array($data['price_range'])) {
                $query->groupStart();
                $query->where('price >=', $data['price_range'][0]);
                $query->orWhere('price <=', $data['price_range'][1]);
                $query->groupEnd();
            }

            if(empty($data['status'])) {
                $query->where("{$this->table}.status != 'Deleted'");
            }

            // search by course type, category id and level
            foreach (['course_type', 'category_id', 'subcategory_id', 'level', 'rating', 'status', 'is_featured'] as $key) {
                if (!empty($data[$key])) {
                    if(is_array($data[$key])) {
                        $query->whereIn("{$this->table}.{$key}", $data[$key]);
                    } else {
                        $query->where("{$this->table}.{$key}", $data[$key]);
                    }
                }
            }

            $query->orderBy("{$this->table}.id", 'DESC');

            return $query->findAll($limit, $offset);

        } catch (DatabaseException $e) {
            return false;
        }
    }

    /**
     * Get a course record
     * 
     * @param int $id
     * 
     * @return array
     */
    public function getRecord($id) {
        try {
            // get column
            $column = preg_match("/^[0-9]+$/", $id) ? 'courses.id' : 'courses.title_slug';

            // get query
            $query = $this->select('courses.*, c.name as category_name, c.name_slug as category_slug, u.username as created_by_username')
                ->join("{$this->categoriesTable} c", 'c.id = courses.category_id', 'left')
                ->join("{$this->userTable} u", 'u.id = courses.created_by', 'left')
                ->where($column, $id);

            return $query->first();
        } catch (DatabaseException $e) {
            return false;
        }
    }

    /**
     * Get a course record by slug
     * 
     * @param string $slug
     * 
     * @return array
     */
    public function getRecordBySlug($slug) {
        return $this->where(['title_slug' => $slug, 'status !=' => 'Deleted'])->first();
    }

    /**
     * Create a course record
     * 
     * @param array $data
     * 
     * @return int
     */
    public function createRecord($data) {
        try {
            $this->insert($data);
            return $this->getInsertID();
        } catch (DatabaseException $e) {
            return false;
        }
    }

    /**
     * Update a course record
     * 
     * @param int $id
     * @param array $data
     * 
     * @return bool
     */
    public function updateRecord($id, $data) {
        try {
            return $this->update($id, $data);
        } catch (DatabaseException $e) {
            return false;
        }
    }

    /**
     * Update a course record using a query
     * 
     * @param array $data
     * @param array $where
     * 
     * @return bool
     */
    public function updateRecordQuery($data, $where) {
        try {
            return $this->db->table($this->table)->where($where)->update($data);
        } catch (DatabaseException $e) {
            return false;
        }
    }

    /**
     * Delete a course record
     * 
     * @param int $id
     * @param int $created_by
     * 
     * @return bool
     */
    public function deleteRecord($id, $created_by) {
        try {
            $this->where('id', $id)->update(['status' => 'Deleted']);
            $this->db->query("UPDATE {$this->userTable} SET coursesCount = coursesCount - 1 WHERE id = {$created_by}");
            return true;
        } catch (DatabaseException $e) {
            return false;
        }
    }

    /**
     * Create a course section
     * 
     * @param array $data
     * 
     * @return int
     */
    public function createSection($data) {
        try {
            $this->db->table($this->contentTable)->insert($data);
            return $this->db->insertID();
        } catch (DatabaseException $e) {
            return false;
        }
    }

    /**
     * Create a course section
     * 
     * @param array $data
     * 
     * @return int
     */
    public function getSections($data, $limit = 24, $offset = 0, $order = 'DESC') {
        try {
            return $this->db->table($this->contentTable)
                ->where($data)
                ->orderBy('id', $order)
                ->get($limit, $offset)
                ->getResultArray();
        } catch (DatabaseException $e) {
            return false;
        }
    }

    /**
     * Create a course section
     * 
     * @param array $data
     * 
     * @return int
     */
    public function deleteSection($data) {
        try {
            return $this->db->table($this->contentTable)->where($data)->delete();
        } catch (DatabaseException $e) {
            return false;
        }
    }
}
