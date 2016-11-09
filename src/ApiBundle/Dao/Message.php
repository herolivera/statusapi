<?php
namespace ApiBundle\Dao;

class Message
{
    protected $database;

    /**
     * Construct
     *
     * @param Doctrine\DBAL\Connection $databaseConn database connection
     *
     * @codeCoverageIgnore
     *
     * @return void
     */
    public function __construct($databaseConn)
    {
        $this->database = $databaseConn;
    }

    /**
     * Get one Message
     *
     * @param int $id Message ID
     *
     * @return array
     */
    public function get($id)
    {
        $sql = <<<EOQ
    SELECT id, email, text, strftime('%Y-%m-%dT%H:%M:%SZ',created_at) as created_at, status
      FROM message
     WHERE id = :id
EOQ;
        $bindings = [
            'id' => $id
        ];

        try {
            $stmt = $this->database->executeQuery($sql, $bindings);
            return $stmt->fetch();
        } catch (\Exception $e) {
            throw new \Exception('Get message failed, database error: '.$e->getMessage(), 400020);
        }
    }
    
    /**
     * Filter results
     *
     * @param int    $page  Page
     * @param int    $rows  Rows
     * @param string $query Query
     */
    public function getAll($page, $rows, $query)
    {
        $from = ($page-1) * $rows;
        $where = "";
        $bindings = [];
        
        //Filter
        if ($query) {
            $where .= "AND text like :text";
            $bindings['text'] = '%'.$query.'%';
        }
        
        $sql = <<<EOQ
    SELECT id, email, strftime('%Y-%m-%dT%H:%M:%SZ',created_at) as created_at, text as status
      FROM message
     WHERE status = 1 
       {$where}
      ORDER BY created_at DESC
      LIMIT {$from}, {$rows}
EOQ;
    

        try {
            $stmt = $this->database->executeQuery($sql, $bindings);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new \Exception('Get messages failed, database error: '.$e->getMessage(), 400024);
        }
    }
   

    /**
     * Create confirmation
     *
     * @param string $email  Email Address
     * @param string $text   Message status
     *
     * @throws \Exception
     * @return int
     */
    public function create($email, $text)
    {
        $sql = <<<EOQ
        INSERT INTO message
                    (email, text, created_at, status)
             VALUES (:email, :text, datetime('now'), :status)
EOQ;
        
        $binds = [
            'email'  => $email!=''?$email:'Anonimous',
            'text'   => $text,
            'status' => $email!=''?'0':'1' //if empty => true
        ];
        
        try {
            $this->database->executeQuery($sql, $binds);
            return $this->database->lastInsertId();
        } catch (\Exception $e) {
            throw new \Exception('Inserting message failed, database error: '.$e->getMessage(), 400021);
        }
    }
  

    /**
     * Delete a message
     *
     * @param int $id ID
     *
     * @return int
     */
    public function delete($id)
    {
        $sql = <<<EOQ
        DELETE FROM message         
         WHERE id = :id                    
EOQ;
        $bindings = [
            'id' => $id,
        ];

        try {
            return $this->database->executeUpdate($sql, $bindings);
        } catch (\Exception $e) {
            throw new \Exception('Deleting message failed, database error: '.$e->getMessage(), 400022);
        }
    }
    
    /**
     * Approve a message
     *
     * @param int $id ID
     *
     * @return int
     */
    public function approveCreate($id)
    {
        $sql = <<<EOQ
         UPDATE message
            SET status = 1
         WHERE id = :id                    
EOQ;
        $bindings = [
            'id' => $id,
        ];

        try {
            return $this->database->executeUpdate($sql, $bindings);
        } catch (\Exception $e) {
            throw new \Exception('Approving message failed, database error: '.$e->getMessage(), 400023);
        }
    }
}
