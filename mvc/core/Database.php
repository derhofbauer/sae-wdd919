<?php

namespace Core;

/**
 * Class Database
 *
 * @package Core
 * @todo: comment
 */
class Database
{
    private object $link;
    private object $lastResult;
    private object $data;

    /**
     * Database constructor.
     */
    public function __construct ()
    {
        $this->link = new \mysqli(
            Config::get('database.host'),
            Config::get('database.username'),
            Config::get('database.password'),
            Config::get('database.dbname'),
            Config::get('database.port'),
            Config::get('database.socket')
        );
    }

    /**
     * Anwendung:
     *  + $database->query('SELECT * FROM users WHERE id = ?', ['i:id' => $id]);
     *  + $database->query('SELECT * FROM users WHERE id = ? AND email = ?', ['i:id' => $id, 's:email' => $email]);
     *
     * @param string $query
     * @param array  $params
     */
    public function query (string $query, array $params = [])
    {
        if (empty($params)) {
            $this->lastResult = $this->link->query($query);
        } else {
            $stmt = $this->link->prepare($query);
            var_dump($stmt);

            $types = [];
            foreach ($params as $typeAndName => $value) {
                $types[] = explode(':', $typeAndName)[0];
            }
            $types = implode('', $types);

            $functionParams = [$types];
            foreach ($params as $param) {
                $_param = $param;
                $functionParams[] = &$_param;
                unset($_param);
            }

            call_user_func_array([$stmt, 'bind_param'], $functionParams);

            $stmt->execute();
            $this->lastResult = $stmt->get_result();
        }

        $this->data = $this->lastResult->fetch_all(MYSQLI_ASSOC);
        return $this->data;
    }

    /**
     * @return object
     */
    public function getLink (): object
    {
        return $this->link;
    }

    /**
     * @return object
     */
    public function getLastResult (): object
    {
        return $this->lastResult;
    }

    /**
     * @return mixed
     */
    public function getData ()
    {
        return $this->data;
    }

    /**
     * Database destructor.
     */
    public function __destruct ()
    {
        $this->link->close();
    }
}
