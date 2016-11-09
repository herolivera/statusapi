<?php
namespace ApiBundle\Dao;

class Schema
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
     * Create schema
     *
     * @param int $id Contact ID
     *
     * @return array
     */
    public function create()
    {
        $this->createMessage();
        $this->createConfirmation();
    }

    /**
     * Create confirmation Schema
     */
    protected function createMessage()
    {
        $sql = <<<EOQ
    CREATE TABLE IF NOT EXISTS message (
                'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                'email' VARCHAR(150),
                'text' VARCHAR(120),
                'created_at' DATETIME,
                'status' BOOLEAN 
    )                
EOQ;

        try {
            $this->database->executeQuery($sql);
        } catch (\Exception $e) {
            throw new \Exception('Create message schema error, database error: '.$e->getMessage(), 40080);
        }
    }

    /**
     * Create confirmation Schema
     */
    protected function createConfirmation()
    {
        $sql = <<<EOQ
    CREATE TABLE IF NOT EXISTS confirmation (
                'id' INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                'status_id' INTEGER,
                'code' VARCHAR(8),
                'action' VARCHAR (10)
    )              
EOQ;
        try {
            $this->database->executeQuery($sql);
        } catch (\Exception $e) {
            throw new \Exception('Create confirmation schema error, database error: '.$e->getMessage(), 40081);
        }
    }
}
