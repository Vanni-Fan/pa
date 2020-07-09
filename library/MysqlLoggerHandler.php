<?php

use Monolog\Handler\AbstractProcessingHandler;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Db\Column;

class MysqlLoggerHandler extends AbstractProcessingHandler
{
    /**
     * @var DbAdapter
     */
    private $connection;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $databaseName;

    /**
     * @inheritDoc
     * @param array $dbConfig
     */
    public function __construct(array $dbConfig, $level = \Monolog\Logger::DEBUG, bool $bubble = true)
    {
        $this->connection = new DbAdapter([
            "host"     => $dbConfig['host'],
            "username" => $dbConfig['username'],
            "password" => $dbConfig['password'],
            "dbname"   => $dbConfig['dbname'],
        ]);
        $this->tableName = $dbConfig['table'];
        $this->databaseName = $dbConfig['dbname'];
        parent::__construct($level, $bubble);
    }

    /**
     * @param array $record
     */
    protected function write(array $record): void
    {
        if (!$this->connection->tableExists($this->tableName)) {
            $this->connection->createTable($this->tableName, $this->databaseName, [
                'columns' => [
                    new Column('id', ['type'=>Column::TYPE_INTEGER,'size'=>10,'primary'=>true,'autoIncrement'=>true,'notNull'=>true]),
                    new Column('channel', ['type'=>Column::TYPE_VARCHAR,'size'=>255,'notNull'=>true]),
                    new Column('level', ['type'=>Column::TYPE_VARCHAR,'size'=>20,'notNull'=>true]),
                    new Column('datetime', ['type'=>Column::TYPE_TIMESTAMP,'notNull'=>true]),
                    new Column('message', ['type'=>Column::TYPE_TEXT]),
                    new Column('context', ['type'=>Column::TYPE_TEXT]),
                    new Column('extra', ['type'=>Column::TYPE_TEXT]),
                    new Column('formatted', ['type'=>Column::TYPE_TEXT])
                ]
            ]);
        }
        $this->connection->insertAsDict($this->tableName, [
            'channel' => $record['channel'],
            'level' => $record['level_name'],
            'datetime' => $record['datetime']->format('Y-m-d H:i:s'),
            'message' => $record['message'],
            'context' => \GuzzleHttp\json_encode($record['context'], 256 | 128),
            'extra' => \GuzzleHttp\json_encode($record['extra'], 256 | 128),
            'formatted' => $record['formatted']
        ]);
    }
}