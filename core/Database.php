<?php

namespace Core;

class Database
{

    private $connection;

    private $query;

    public function __construct()
    {
        switch (db_driver) {
            case 'mysql':
            default:
                $this->connection = new \mysqli(
                    db_host,
                    db_user,
                    db_pass,
                    db_database,
                    db_port
                );
                if ($this->connection->connect_errno) {
                    echo "Fallo al conectar a MySQL: (" . $this->connection->connect_errno . ") <br/>"
                        . $this->connection->connect_error;
                    die();
                } else {
//                    echo $this->connection->host_info . " <br/>";
                }
                break;
        }
        $this->connection->query("SET NAMES '".db_charset."'");
    }

    public function prepare($sql)
    {
        $this->query = $this->connection->prepare($sql);
    }

    public function execute()
    {
        $this->query->execute();
    }

    public function bindParams($params) {
        $string = '';
        foreach ($params as $key => $value) {
            $string .= 's';
        }
        $params = array_merge([$string], $params);
        call_user_func_array(array($this->query, 'bind_param'), $this->refValues($params));
        return $this->query;
    }

    public function close()
    {
        if ($this->connection) $this->connection->close();
    }

    public function get()
    {

        $recordset = $this->getAll();

        if (!is_null($recordset)) {
            return (count($recordset) > 0) ? $recordset[0] : null;
        }

        return null;

    }

    public function getAll() {

        $this->query->store_result();
        $num_row = $this->query->num_rows;
        $recordset = null;
        $row = [];
        $params = [];

        if ( $num_row > 0 ) {

            $meta = $this->query->result_metadata();
            while ($field = $meta->fetch_field())
            {
                $params[] = &$row[$field->name];
            }
            call_user_func_array(array($this->query, 'bind_result'), $params);

            while($this->query->fetch()){
                foreach($row as $key => $val)
                {
                    $c[$key] = $val;
                }
                $recordset[] = $c;
            }

        }

        return $recordset;

    }

    private function refValues($arr){
        if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
        {
            $refs = array();
            foreach($arr as $key => $value)
                $refs[$key] = &$arr[$key];
            return $refs;
        }
        return $arr;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    public function __destruct()
    {
        if (!is_null($this->query)) $this->query->close();
    }

}