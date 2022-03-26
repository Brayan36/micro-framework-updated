<?php

namespace Core;

use Core\InterfaceModel;
use Core\Database;

abstract class BaseModel implements InterfaceModel
{

    protected $db;
    protected $table;
    protected $fillable = [ "*" ];
    private static $this;
    private $select;
    private $limit;
    private $where;
    private $whereParams;
    private $sql;

    public function __construct()
    {
        $this->db = new Database();
        $this->select = null;
        $this->limit = null;
        $this->where = null;
        $this->sql = '';
        self::$this = $this;
    }

    /**
     * @param $data
     * @return array|null
     */
    public static function getAll($data = null): ?array
    {
        $model = self::$this;

        $select = $model->select ?? $model->fillable;
        $model->sql = "SELECT " . implode(',', $select) . " FROM " . $model->table;
        if (!is_null($model->where)) $model->sql .= $model->where;
        if (!is_null($model->limit)) $model->sql .= $model->limit;

        $model->db->prepare($model->sql);
        if (!is_null($model->whereParams)) $model->db->bindParams($model->whereParams);

        $model->db->execute();
        $model->clean();
        return $model->db->getAll();
    }

    /**
     * @param $id
     * @return array|null
     */
    public static function get($id): ?array
    {
        $model = self::$this;

        $select = $model->select ?? $model->fillable;
        $model->sql = "SELECT " . implode(',', $select) . " FROM " . $model->table
        . " WHERE id = ? " ;
        $model->db->prepare($model->sql);
        $model->db->bindParams([$id]);

        $model->db->execute();
        $model->clean();
        return $model->db->get();
    }

    /**
     * @param $data
     * @return array
     */
    public static function create($data): array
    {
        $model = self::$this;
        $fields = [];
        $params = [];

        foreach ( $data as $key => $datum ) {
            $fields[] = $key;
            $params[] = '?';
        }

        $model->sql = "INSERT INTO " . $model->table . " (" . implode( ',', $fields ) . " ) VALUES ( ". implode(',', $params ) ." )";

        $model->db->prepare($model->sql);
        $model->db->bindParams($data);

        $model->db->execute();
        $model->clean();

        $query = $model->db->getQuery();

        if ( $query == false ) {
            return [
                'status' => 'error',
                'msj' => $query->error
            ];
        }

        $response = $model->get( $query->insert_id );
        return [
            'status' => 'success',
            'data' => $response,
            'msj' => 'Registro Exitoso'
        ];
    }

    /**
     * @param $data
     * @param $id
     * @return array
     */
    public static function update($data, $id): array
    {
        $model = self::$this;
        $setFields = '';
        $data = array_filter($data, fn($key) => $key != 'id', ARRAY_FILTER_USE_KEY);
        foreach ($data as $key => $value) {
            if ($setFields != '') $setFields .= ',';
            $setFields .= "$key = ?";
        }
        $model->sql = "UPDATE " . $model->table . " SET $setFields ";

        if (!is_null($id)) $model->where(['id', '=', $id]);
        if (!is_null($model->where)) $model->sql .= $model->where;
        if (!is_null($model->whereParams)) $data = array_merge($data, $model->whereParams);

        $model->db->prepare($model->sql);
        $model->db->bindParams($data);
        $model->db->execute();
        $model->clean();
        $query = $model->db->getQuery();

        if ( $query == false ) {
            return [
                'status' => 'error',
                'msj' => $query->error
            ];
        }

        $response = $model->get( $data['id'] );
        return [
            'status' => 'success',
            'data' => $response,
            'msj' => 'Actualizacion Exitosa'
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public static function delete($id): array
    {
        $model = self::$this;
        $response = $model->get( $id );

        $model->sql = "DELETE FROM " . $model->table . " ";

        $data = [$id];
        if (!is_null($id)) $model->where(['id', '=', $id]);
        if (!is_null($model->where)) $model->sql .= $model->where;
        if (!is_null($model->whereParams)) $data = $model->whereParams;

        $model->db->prepare($model->sql);
        $model->db->bindParams($data);
        $model->db->execute();
        $model->clean();
        $query = $model->db->getQuery();

        if ( $query == false ) {
            return [
                'status' => 'error',
                'msj' => $query->error
            ];
        }

        return [
            'status' => 'success',
            'data' => $response,
            'msj' => 'Registro Eliminado'
        ];
    }

    public static function limit( int $number ): BaseModel
    {
        self::$this->limit = ' LIMIT ' . $number;
        return self::$this;
    }

    public static function where(array $where): BaseModel
    {
        return self::$this->condition($where, 'AND');
    }

    public static function orWhere(array $where): BaseModel
    {
        return self::$this->condition($where, 'OR');
    }

    public static function whereIn(array $where): BaseModel
    {
        $model = self::$this;
        $model->whereParams = array_merge($model->whereParams ?? [], end($where));
        $index = count($where) - 1;
        $where[$index] = '(' . implode(',', array_map(function ($value) {
            return '?';
        }, $where[$index])) . ')';

        $where = [
            $where[0],
            'IN',
            end($where)
        ];

        if (!is_null($model->where)) $model->where .= " AND "
            . $model->table . '.' . implode(' ', $where);
        if (is_null($model->where)) $model->where .= " WHERE "
            . $model->table . '.' . implode(' ', $where);
        return $model;
    }

    public static function select(array $select): BaseModel
    {
        $model = self::$this;
        $model->select = $select;
        return $model;
    }

    private function condition($where, $type): BaseModel
    {
        $model = self::$this;
        $model->whereParams[] = end($where);
        $where[count($where) - 1] = '?';
        if (!is_null($model->where)) $model->where .= " $type "
            . $model->table . '.' . implode(' ', $where);
        if (is_null($model->where)) $model->where .= " WHERE "
            . $model->table . '.' . implode(' ', $where);
        return $model;
    }

    private function clean()
    {
        $this->select = null;
        $this->where = null;
        $this->limit = null;
        $this->whereParams = null;
    }

    public function getSql(): string
    {
        return $this->sql;
    }

    public function __destruct()
    {
        $this->clean();
        $this->sql = '';
        $this->db->close();
    }
}