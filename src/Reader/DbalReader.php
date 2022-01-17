<?php

namespace Import\Reader;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Statement;

/**
 * Reads data through the Doctrine DBAL
 */
class DbalReader implements CountableReader
{
    private Connection $connection;
    private ?array $data;
    private ?Statement $stmt;
    private string $sql;
    private array $params;
    private ?int $rowCount;
    private bool $rowCountCalculated = true;
    private string $key;

    /**
     * @param Connection $connection
     * @param string $sql
     * @param array      $params
     */
    public function __construct(Connection $connection, string $sql, array $params = [])
    {
        $this->connection = $connection;

        $this->setSql($sql, $params);
    }

    /**
     * Do calculate row count?
     *
     * @param boolean $calculate
     */
    public function setRowCountCalculated(bool $calculate = true)
    {
        $this->rowCountCalculated = (bool) $calculate;
    }

    /**
     * Is row count calculated?
     *
     * @return boolean
     */
    public function isRowCountCalculated(): bool
    {
        return $this->rowCountCalculated;
    }

    /**
     * Set Query string with Parameters
     */
    public function setSql(string $sql, array $params = [])
    {
        $this->sql = (string) $sql;

        $this->setSqlParameters($params);
    }

    /**
     * Set SQL parameters
     */
    public function setSqlParameters(array $params)
    {
        $this->params = $params;

        $this->stmt = null;
        $this->rowCount = null;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        if (is_null($this->data)) {
            $this->rewind();
        }

        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->key++;
        $this->data = $this->stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): bool
    {
        if (null === $this->data) {
            $this->rewind();
        }

        return (false !== $this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        if (null === $this->stmt) {
            $this->stmt = $this->prepare($this->sql, $this->params);
        }
        if (0 !== $this->key) {
            $this->stmt->execute();
            $this->data = $this->stmt->fetch(\PDO::FETCH_ASSOC);
            $this->key = 0;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        if (null === $this->rowCount) {
            if ($this->rowCountCalculated) {
                $this->doCalcRowCount();
            } else {
                if (null === $this->stmt) {
                    $this->rewind();
                }
                $this->rowCount = $this->stmt->rowCount();
            }
        }

        return $this->rowCount;
    }

    private function doCalcRowCount()
    {
        $statement = $this->prepare(sprintf('SELECT COUNT(*) FROM (%s) AS port_cnt', $this->sql), $this->params);
        $statement->execute();

        $this->rowCount = (int) $statement->fetchColumn(0);
    }

    /**
     * Prepare given statement
     * @throws Exception
     */
    private function prepare(string $sql, array $params): Statement
    {
        $statement = $this->connection->prepare($sql);
        foreach ($params as $key => $value) {
            $statement->bindValue($key, $value);
        }

        return $statement;
    }
}
