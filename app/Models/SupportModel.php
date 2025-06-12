<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class SupportModel extends Model
{
    protected $supportCategoryTable;
    protected $supportContactTable;
    protected $supportContactRepliesTable;
    protected $primaryKey = 'id';
    protected $userTable;
    protected $allowedFields = [
        'title', 'title_slug', 'description', 'thumbnail', 'image', 'tags', 'viewsCount', 
        'writer', 'sharesCount', 'status', 'category_id', 'created_by', 'content', 'icon'
    ];

    public function __construct() {
        parent::__construct();
        
        $this->table = DbTables::$supportTable;
        foreach(DbTables::initTables() as $key) {
            if (property_exists($this, $key)) {
                $this->{$key} = DbTables::${$key};
            }
        }
    }

    /**
     * This method gets the records
     * 
     * @param array $whereBind
     * @param int|null $limit
     * @param int $offset
     * @return array
     */
    public function getRecords($whereBind, $limit = null, $offset = 0) {
        try {

            $statuses = ['Active', 'Inactive'];
            if(!empty($whereBind['status'])) {
                $statuses = stringToArray($whereBind['status']);
            }

            return $this->db->table($this->table)
                            ->where($whereBind)
                            ->limit($limit, $offset)
                            ->get()
                            ->getResultArray();
        } catch (DatabaseException $e) {
            return [];
        }
    }

    /**
     * This method creates a new settings record
     * 
     * @param array $data
     * @return bool
     */
    public function createRecord($data) {
        try {
            $this->db->table($this->table)->insert($data);
            return $this->db->insertID();
        } catch (DatabaseException $e) {
            return false;
        }
    }

    /**
     * This method updates a settings record
     *
     * @param array $data
     * @param array|String $whereBind
     * @return bool
     */
    public function updateRecord(array $data, array|String $whereBind)
    {
        try {
            return $this->db->table($this->table)
                            ->where($whereBind)
                            ->update($data);
        } catch (DatabaseException $e) {
            return false;
        }
    }

    /**
     * This method finds a settings record by option
     *
     * @param $options
     * @param null $limit
     * @return array
     */
    public function findByCategory($categoryId, $limit = null): array
    {
        try {
            return $this->db->table($this->table)
                            ->where('category_id', $categoryId)
                            ->limit($limit)
                            ->get()
                            ->getResultArray();
        } catch (DatabaseException $e) {
            return [];
        }
    }
    
    /**
     * Delete the heatmaps groups
     *
     * @param array $bindWhere
     * @param string $status
     *
     * @return array
     */
    public function changeStatus($bindWhere, $status) {
        try {
            return $this->db->table($this->table)
                ->where($bindWhere)
                ->update(['status' => $status]);
        } catch (DatabaseException $e) {
            return [];
        }
    }

    /**
     * This method deletes a record
     * 
     * @param array $bindWhere
     * @return bool
     */
    public function deleteRecord($bindWhere) {
        try {
            return $this->db->table($this->table)
                ->where($bindWhere)
                ->delete();
        } catch (DatabaseException $e) {
            return false;
        }
    }

    /**
     * This method gets the categories
     * 
     * @return array
     */
    public function getCategory($whereBind, $limit = 1, $offset = 0) {
        try {

            // convert the where bind to an array if it is not an array
            $whereBind = !is_array($whereBind) ? ['id' => $whereBind] : $whereBind;

            $statuses = ['Active', 'Inactive'];
            if(!empty($whereBind['status'])) {
                $statuses = stringToArray($whereBind['status']);
            }

            // get the category
            return $this->db->table($this->supportCategoryTable)
                            ->where($whereBind)
                            ->whereIn('status', $statuses)
                            ->limit($limit, $offset)
                            ->get()
                            ->getRowArray();
        } catch (DatabaseException $e) {
            return [];
        }
    }

    /**
     * This method gets the categories
     * 
     * @return array
     */
    public function getCategories($whereBind = [], $limit = null, $offset = 0) {
        try {
            $statuses = ['Active', 'Inactive'];
            if(!empty($whereBind['status'])) {
                $statuses = stringToArray($whereBind['status']);
            }
            return $this->db->table($this->supportCategoryTable)
                            ->where($whereBind)
                            ->whereIn('status', $statuses)
                            ->limit($limit, $offset)
                            ->get()
                            ->getResultArray();
        } catch (DatabaseException $e) {
            return [];
        }
    }

    /**
     * This method creates a new category
     * 
     * @param array $data
     * @return bool
     */
    public function createCategory($data) {
        try {
            $this->db->table($this->supportCategoryTable)->insert($data);
            return $this->db->insertID();
        } catch (DatabaseException $e) {
            return false;
        }
    }

    /**
     * This method updates a category
     * 
     * @param array $data
     * @param array $bindWhere
     * @return bool
     */
    public function updateCategory($data, $bindWhere) {
        try {
            return $this->db->table($this->supportCategoryTable)
                            ->where($bindWhere)
                            ->update($data);
        } catch (DatabaseException $e) {
            return false;
        }
    }

    /**
     * This method deletes a category
     * 
     * @param array $bindWhere
     * @return bool
     */
    public function deleteCategory($whereBind) {
        try {
            // convert the where bind to an array if it is not an array
            $whereBind = !is_array($whereBind) ? ['id' => $whereBind] : $whereBind;

            // delete the category
            return $this->db->table($this->supportCategoryTable)
                            ->where($whereBind)
                            ->update(['status' => 'Deleted', 'updated_at' => date('Y-m-d H:i:s')]);
        } catch (DatabaseException $e) {
            return false;
        }
    }

    /**
     * This method creates a new contact
     * 
     * @param array $data
     * @return bool
     */
    public function createContact($data) {
        try {
            $this->db->table($this->supportContactTable)->insert($data);
            return $this->db->insertID();
        } catch (DatabaseException $e) {
            return false;
        }
    }

    /**
     * This method gets the contacts
     * 
     * @param array $whereBind
     * @param int|null $limit
     * @param int $offset
     * @return array
     */
    public function getContacts($whereBind = [], $limit = null, $offset = 0) {
        try {
            return $this->db->table($this->supportContactTable)
                            ->where($whereBind)
                            ->limit($limit, $offset)
                            ->orderBy('created_at', 'DESC')
                            ->get()
                            ->getResultArray();
        } catch (DatabaseException $e) {
            return [];
        }
    }

    /**
     * This method gets the contact
     * 
     * @param array $whereBind
     * @return array
     */
    public function getContact($whereBind) {
        try {
            return $this->db->table($this->supportContactTable)
                            ->where($whereBind)
                            ->get()
                            ->getRowArray();
        } catch (DatabaseException $e) {
            return [];
        }
    }

    /**
     * This method updates a contact
     * 
     * @param array $whereBind
     * @param array $data
     * @return bool
     */
    public function updateContact($whereBind, $data) {
        try {
            return $this->db->table($this->supportContactTable)
                            ->where($whereBind)
                            ->update($data);
        } catch (DatabaseException $e) {
            return false;
        }
    }

    /**
     * This method creates a new contact reply
     * 
     * @param array $data
     * @return bool
     */
    public function createContactReply($data) {
        try {
            $this->db->table($this->supportContactRepliesTable)->insert($data);
            return $this->db->insertID();
        } catch (DatabaseException $e) {
            return false;
        }
    }

    /**
     * This method gets the contact replies
     * 
     * @param array $whereBind
     * @return array
     */
    public function getContactReplies($whereBind) {
        try {
            return $this->db->table("{$this->supportContactRepliesTable} a")
                            ->select("*, (SELECT JSON_OBJECT(
                                'id', u.id, 'firstname', u.firstname, 'lastname', u.lastname, 'email', u.email, 'phone', u.phone, 'image', u.image
                            ) FROM {$this->userTable} u WHERE u.id = a.created_by LIMIT 1) as user")
                            ->where($whereBind)
                            ->orderBy('id', 'DESC')
                            ->get()
                            ->getResultArray();
        } catch (DatabaseException $e) {
            return [];
        }
    }
}
