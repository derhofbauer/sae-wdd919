<?php

namespace App\Models;

use Core\Models\BaseUser;

class User extends BaseUser
{

    /**
     * Wir definieren alle Spalten aus der Tabelle mit den richtigen Datentypen.
     */
    public int $id;
    public string $email;
    public string $password;
    public string $username;
    public string $firstname;
    public string $lastname;
    public bool $is_admin;

    /**
     * Der Konstruktor befüllt das Objekt, sofern Daten übergeben worden sind.
     *
     * @param array $data
     */
    public function __construct (array $data = [])
    {
        if (!empty($data)) {
            $this->fill($data);
        }
    }

    /**
     * Diese Methode ermöglicht es uns, die Daten aus einem Datenbankergebnis in nur einer Zeile direkt in ein Objekt
     * zu füllen. Bei der Instanziierung kann über den Konstruktor auch diese Methode verwendet werden.
     *
     * @param array $data
     */
    public function fill (array $data)
    {
        $this->id = (int)$data['id'];
        $this->email = (string)$data['email'];
        $this->password = (string)$data['password'];
        $this->username = (string)$data['username'];
        $this->firstname = (string)$data['firstname'];
        $this->lastname = (string)$data['lastname'];
        $this->is_admin = (bool)$data['is_admin'];
    }
}
