<?php

namespace App\Models;

use Core\Config;
use Core\Database;
use Core\Models\BaseModel;

/**
 * Class Product
 *
 * @package App\Models
 */
class Product extends BaseModel
{

    /**
     * Wir definieren alle Spalten aus der Tabelle mit den richtigen Datentypen.
     */
    public int $id;
    public string $name;
    public string $description;
    public float $price;
    public int $stock;
    public string $images;

    /**
     * Die abstrakte Klasse BaseModel kann den Namen, der zu diesem Model gehörigen Tabelle, automatisch berechnen. Es
     * kann aber sein, dass die Tabelle anders heißt - für diesen Fall haben wir die Möglichkeit eingebaut, dass ein
     * alternative Tabellenname verwendet werden kann über eine Klassen-Konstante. Für das Product brauchen wir diese
     * Konstante nicht, daher ist sie auskommentiert.
     */
    // const TABLENAME = 'alternativeTable';

    /**
     * Damit wir von überall auf diesen Wert zugriff haben, definieren wir eine Klassenkonstante.
     */
    const IMAGES_DELIMITER = ';';

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
        $this->name = (string)$data['name'];
        $this->description = (string)$data['description'];
        $this->price = (float)$data['price'];
        $this->stock = (int)$data['stock'];
        $this->images = (string)$data['images'];
    }

    /**
     * $this->images ist ein String, weil in der Datenbank die Bild-Pfade in einem einzelnen Datenbank-Feld zusammen
     * gespeichert werden. Hier wird aus dem String ein Array und die vollständigen Pfade werden so zusammengebaut, dass
     * der Browser sie im HTML dann laden kann.
     *
     * @return array
     */
    public function getImages (): array
    {
        /**
         * Benötigte Pfade aus der Config laden.
         */
        $storagePath = Config::get('app.storage-path', 'storage/');
        $uploadPath = Config::get('app.upload-path', 'uploads/');

        /**
         * Sind überhaupt Bilder vorhanden für das aktuelle Produkt?
         */
        if (strlen($this->images) > 0) {
            /**
             * Kommt der Trenner in dem Bild-String vor und gibt es damit mehr als ein Bild?
             */
            if (strpos($this->images, self::IMAGES_DELIMITER) !== false) {
                /**
                 * Wenn ja, machen wir ein Array daraus.
                 */
                $images = explode(self::IMAGES_DELIMITER, $this->images);
            } else {
                /**
                 * Wenn nein, dann machen wir auch ein Array daraus, aber indem wir das eine Bild als einzelnes Element
                 * in ein Array speichern. Das hat den Sinn, dass diese Methode in jedem Fall, egal ob es Bilder gibt
                 * oder nicht, ein Array zurückgibt.
                 */
                $images = [$this->images];
            }

            /**
             * Die Bilder werden anschließend mit der array_map() Funktion zu korrekten, durch den Browser zugreifbaren
             * Pfaden umgebaut.
             *
             * Die array_map() Funktion führt dabei die angegebene anonyme Funktion auf jedes Element in dem Array aus.
             */
            $images = array_map(function ($image) use ($storagePath, $uploadPath) {
                /**
                 * Pfad zusammenbauen
                 */
                $imagePath = $storagePath . $uploadPath . $image;

                /**
                 * Doppelte Slashes mit einfachen ersetzen und zurückgeben. Das kann passieren, wenn die Pfad-Angaben in
                 * der Config ein führendes Slash haben.
                 */
                return str_replace('//', '/', $imagePath);
            }, $images);

            /**
             * Bilder-Array mit validen Pfaden zurückgeben.
             */
            return $images;
        }
        /**
         * Hat das Produkt keine Bilder, geben wir ein leeres Array zurück. Das machen wir, damit wir das Ergebnis dieser
         * Funktion einfach IMMER mit einer foreach-Schleife durchgehen können und nicht unterschiedliche Action brauchen.
         */
        return [];
    }

    /**
     * Preis des aktuellen Produkts immer gleich formatieren und zurückgeben.
     *
     * Wir rufen hier nur die statische Methode formatPrice() auf, weil wir irgendwann bemerkt haben, dass es auch
     * praktisch wäre, wenn wir beliebige Preise formatieren könnten. Daher haben wir diese statische Methode
     * entwickelt, um nicht mehrmals den selben Code zu haben - in unserem Fall wäre das mehrfach ein fast identer
     * Aufrud der number_format() Funktion gewesen.
     *
     * @return string
     */
    public function getPrice (): string
    {
        return self::formatPrice($this->price);
    }

    /**
     * s. $this->getPrice()
     *
     * Wir verwenden diese Methode beispielsweise im Cart-View, um alle dort angezeigten Preise einheitlich
     * darzustellen.
     *
     * @param float $price
     *
     * @return string
     */
    public static function formatPrice (float $price): string
    {
        return number_format($price, 2, ',', '.') . ' €';
    }

    /**
     * @param string $filename
     *
     * @todo: comment
     */
    public function removeImage (string $filename)
    {
        /**
         * [x] Dateinamen aus Bildern in der DB löschen: filename.jpg;something.png;file1.gif
         */
        $this->images = str_replace($filename, '', $this->images); // filename.jpg;;file1.gif
        $this->images = str_replace(self::IMAGES_DELIMITER . self::IMAGES_DELIMITER, self::IMAGES_DELIMITER, $this->images); // filename.jpg;file1.gif
        $this->images = trim($this->images, self::IMAGES_DELIMITER); // filename.jpg;file1.gif
    }

    /**
     * @todo: comment
     */
    public function save ()
    {
        /**
         * Datenbankverbindung herstellen.
         */
        $db = new Database();

        /**
         * Tabellennamen berechnen.
         */
        $tableName = self::getTableNameFromClassName();

        /**
         * Query ausführen.
         *
         * @todo: comment
         */
        return $db->query("UPDATE $tableName SET name = ?, description = ?, price = ?, stock = ?, images = ? WHERE id = ?", [
            's:name' => $this->name,
            's:description' => $this->description,
            'd:price' => $this->price,
            'i:stock' => $this->stock,
            's:images' => $this->images,
            'i:id' => $this->id
        ]);
    }

}
