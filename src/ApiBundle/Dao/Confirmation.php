<?php
namespace ApiBundle\Dao;

class Confirmation
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
     * Get one Confirmation
     *
     * @param int $id Confirmation ID
     *
     * @return array
     */
    public function get($id)
    {
        $sql = <<<EOQ
    SELECT id, status_id, action, code
      FROM confirmation
     WHERE id = :id
EOQ;
        $bindings = [
            'id' => $id
        ];

        try {
            $stmt = $this->database->executeQuery($sql, $bindings);
            return $stmt->fetch();
        } catch (\Exception $e) {
            throw new \Exception('Get confirmation failed, database error: '.$e->getMessage(), 400030);
        }
    }
    
/**
     * Get one Confirmation
     *
     * @param string $code Confirmation Code
     *
     * @return array
     */
    public function getByCode($code)
    {
        $sql = <<<EOQ
    SELECT id, status_id, action, code
      FROM confirmation
     WHERE code = :code
EOQ;
        $bindings = [
            'code' => $code
        ];

        try {
            $stmt = $this->database->executeQuery($sql, $bindings);
            return $stmt->fetch();
        } catch (\Exception $e) {
            throw new \Exception('Get confirmation failed, database error: '.$e->getMessage(), 400030);
        }
    }

    
    /**
     * Create confirmation
     *
     * @param int    $messageId Message ID
     * @param string $action    Action
     *
     * @throws \Exception
     * @return string
     */
    public function create($messageId, $action)
    {
        $sql = <<<EOQ
        INSERT INTO confirmation
                    (status_id, code, action)
             VALUES (:status_id, :code, :action)
EOQ;
        $code = substr(md5(uniqid(mt_rand(), true)), 0, 8);
        
        $binds = [
            'status_id' => $messageId,
            'code'      => $code,
            'action'    => $action
        ];
        
        try {
            $this->database->executeQuery($sql, $binds);
            return $code;
        } catch (\Exception $e) {
            throw new \Exception('Inserting confirmation failed, database error: '.$e->getMessage(), 400031);
        }
    }

    /**
     * Delete a confirmation
     *
     * @param int $id ID
     *
     * @return int
     */
    public function delete($id)
    {
        $sql = <<<EOQ
        DELETE FROM confirmation          
         WHERE id = :id
                    
EOQ;
        $bindings = [
            'id' => $id
        ];

        try {
            return $this->database->executeUpdate($sql, $bindings);
        } catch (\Exception $e) {
            throw new \Exception('Deleting confirmation failed, database error: '.$e->getMessage(), 400032);
        }
    }
}
