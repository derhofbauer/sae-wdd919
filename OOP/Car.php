<?php

class Car
{
    public string $marke;
    public string $autonummer;
    public int $numberDoors = 3;
    public string $owner = 'Arthur Dent';
    public int $speed = 0;

    /**
     * Car constructor.
     *
     * @param string $marke
     * @param string $autonummer
     * @param int    $numberDoors
     */
    public function __construct (string $marke, string $autonummer, int $numberDoors = 3)
    {
        $this->marke = $marke;
        $this->autonummer = $autonummer;
        $this->numberDoors = $numberDoors;
    }

    /**
     * @param string $owner
     */
    public function setOwner (string $owner) {
        $this->owner = $owner;
    }

    /**
     * @param int $speed
     */
    public function drive (int $speed = 50) {
        $this->speed = $speed;
    }

    public function break () {
        $this->speed = 0;
    }

    /**
     * @return string[]
     */
    public static function getAllSupportedModels (): array {
        return [
            'VW Polo',
            'VW Gold',
            'Audio XY',
            '...'
        ];
    }
}
