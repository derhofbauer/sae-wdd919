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
    public string $name = '';
    public string $description = '';
    public float $price = 0.0;
    public int $stock = 0;
    public string $images = '';
    /**
     * @todo: comment
     */
    public float $_rating = 0;

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
        return '€ ' . number_format($price, 2, ',', '.');
    }

    /**
     * Ein einzelnes Bild anhand des Dateinamens aus $this->images löschen.
     *
     * @param string $filename
     */
    public function removeImage (string $filename)
    {
        /**
         * [x] Dateinamen aus Bildern in der DB löschen: filename.jpg;something.png;file1.gif
         */

        /**
         * Dateinamen mit einem leeren String ersetzen.
         */
        $this->images = str_replace($filename, '', $this->images); // filename.jpg;;file1.gif

        /**
         * Die beiden Zeilen, die hier zuvor gestanden sind, haben wir in eine Funktion ausgelagert, damit wir sie auch
         * in der $this->addImage() Methode verwenden können ohne Code zu duplizieren.
         */
        $this->sanitizeImages();
    }

    /**
     * Ein einzelnes Bild anhand des Dateinamens in $this->images hinzufügen.
     *
     * @param string $filename
     */
    public function addImage (string $filename)
    {
        /**
         * Existiert das Bild noch nicht in $this->images ...
         */
        if (strpos($this->images, $filename) === false) {
            /**
             * ... so hängen wir an $this->images einen Trenner und den Dateinamen hinten dran. Das kann dazu führen,
             * dass Trenner an Stellen vorhanden sind, an denen keine sein sollen. War beispielsweise zuvor kein Bild
             * verknüpft, so würde jetzt ';neuesBild.jpg' in $this->images stehen und einen Fehler verursachen ...
             */
            $this->images .= (self::IMAGES_DELIMITER . $filename);
        }

        /**
         * ... daher verwenden wir hier auch die beiden Funktionen, die wir zuvor in $this->removeImage() programmiert
         * hatten, um $this->images zu korrigieren und bspw. doppelte Trenner zu entfernen.
         */
        $this->sanitizeImages();
    }

    /**
     * Diese Funktion haben wir angelegt, weil wir die beiden beinhalteten Zeilen sowohl in $this->removeImage() als
     * auch in $this->addImage() verwenden wollen. Best-Practice ist, Code-Duplikate so gut es geht zu vermeiden.
     */
    private function sanitizeImages ()
    {
        /**
         * Dadurch kann es passieren, dass zwei Trennzeichen direkt aufeinanderfolgen. Hier ersetzen wir also 2 direkt
         * aufeinanderfolgende Trennzeichen durch ein einzelnes Trennzeichen.
         */
        $this->images = str_replace(self::IMAGES_DELIMITER . self::IMAGES_DELIMITER, self::IMAGES_DELIMITER, $this->images); // filename.jpg;file1.gif

        /**
         * Es kann auch vorkommen, dass das erste oder letzte Bild gelöscht wird und damit ganz vorne oder ganz am Ende
         * ein Trennzeichen übrig bleibt. Hier schneiden wir vorn und hinten von dem String alle Trennzeichen weg.
         */
        $this->images = trim($this->images, self::IMAGES_DELIMITER); // filename.jpg;file1.gif
    }

    /**
     * Aktuelle Properties dieses Objekts wieder in die Datenbank zurückspeichern.
     */
    public function save ()
    {
        /**
         * Hier rufen wir die save() Methode der Elternklasse auf - in diesem Fall BaseModel. Würden wir das nicht tun,
         * dann würde Product::save() die BaseModel::save() Methode überschreiben, so erweitern wir die Methode quasi.
         */
        parent::save();

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
         * Hier ist es essenziell, dass die Werte in dem zweiten Funktionsparameter von $db->query() in der selben
         * Reihenfolge angegeben werden, wie sie im Query auftreten.
         *
         * Je nachdem, ob das aktuellen Objekt bereits eine ID hat oder nicht, speichern wir Änderungen oder eine neuen
         * Datensatz in die Datenbank. Dadurch können wir die save() Methode verwenden egal ob wir eine Änderung oder
         * ein neues Objekt speichern wollen.
         */
        if (!empty($this->id)) {
            return $db->query("UPDATE $tableName SET name = ?, description = ?, price = ?, stock = ?, images = ? WHERE id = ?", [
                's:name' => $this->name,
                's:description' => $this->description,
                'd:price' => $this->price,
                'i:stock' => $this->stock,
                's:images' => $this->images,
                'i:id' => $this->id
            ]);
        } else {
            $result = $db->query("INSERT INTO $tableName SET name = ?, description = ?, price = ?, stock = ?, images = ?", [
                's:name' => $this->name,
                's:description' => $this->description,
                'd:price' => $this->price,
                'i:stock' => $this->stock,
                's:images' => $this->images,
            ]);

            /**
             * Neu generierte ID abrufen. (vgl. auto_increment)
             */
            $newId = $db->getInsertId();

            /**
             * Handelt es sich um einen Integer und somit nicht um einen Fehler, aktualisieren wir das aktuelle Objekt.
             */
            if (is_int($newId)) {
                $this->id = $newId;
            }

            /**
             * Ergebnis zurück geben.
             */
            return $result;
        }
    }

    /**
     * Diese Methode ist eine Mischung zwischen der BaseModel::find() und der BaseModel::all() Methode, weil anhand
     * eines Wertes gesucht wird, aber mehr als ein Datensatz zurückkommen können.
     *
     * @param int $categoryId
     *
     * @return array
     */
    public static function findByCategoryId (int $categoryId): array
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
         * Hier können wir leider nicht alle benötigten Tabellen-Namen generieren, weil die Benennung der Mapping
         * Tabelle komplex ist. Technisch geht das natürlich schon, wir haben es aber nicht im Core gebaut.
         */
        $result = $db->query("
            SELECT $tableName.* FROM $tableName 
                JOIN products_categories_mm
                    ON products_categories_mm.product_id = $tableName.id
            WHERE products_categories_mm.category_id = ?
            ", ['i:category_id' => $categoryId]);

        /**
         * Ergebnis-Array vorbereiten.
         */
        $objects = [];

        /**
         * Ergebnisse des Datenbank-Queries durchgehen und jeweils ein neues Objekt erzeugen.
         */
        foreach ($result as $object) {
            /**
             * Auslesen, welche Klasse aufgerufen wurde und ein Objekt dieser Klasse erstellen und in den Ergebnis-Array
             * speichern.
             */
            $calledClass = get_called_class();
            $objects[] = new $calledClass($object);
        }

        /**
         * Ergebnisse zurückgeben.
         */
        return $objects;
    }

    /**
     * Produkte durchsuchen
     *
     * @param string $searchterm
     *
     * @return array
     */
    public static function search (string $searchterm): array
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
         * Hier führen wird eine Volltextsuche ausgeführt. MySQL unterstützt dabei nur eine sehr grundlegende Suche,
         * die für unsere Zwecke aber ausreichen ist. Im MATCH() Statement werden die Spalten angegeben, die durchsucht
         * werden sollen. Im AGAINST() Statement wird der Suchbegriff und der Suchmodus übergeben. Wichtig dabei ist,
         * dass die Spalten, die durchsucht werden sollen, in der Datenbank als kombinierter FULLTEXT-Index definiert
         * sind.
         */
        $result = $db->query("SELECT * FROM $tableName WHERE MATCH(name, description) AGAINST(? IN BOOLEAN MODE)", [
            's:term' => $searchterm
        ]);

        /**
         * Ergebnis-Array vorbereiten.
         */
        $objects = [];

        /**
         * Ergebnisse des Datenbank-Queries durchgehen und jeweils ein neues Objekt erzeugen.
         */
        foreach ($result as $object) {
            /**
             * Auslesen, welche Klasse aufgerufen wurde und ein Objekt dieser Klasse erstellen und in den Ergebnis-Array
             * speichern.
             */
            $calledClass = get_called_class();
            $objects[] = new $calledClass($object);
        }

        /**
         * Ergebnisse zurückgeben.
         */
        return $objects;
    }

    /**
     * Verknüpfung zwischen einem Produkt und einer Kategorie herstellen.
     *
     * @param int $categoryId
     *
     * @return array|bool|mixed
     */
    public function attachToCategory (int $categoryId)
    {
        /**
         * Datenbankverbindung herstellen.
         */
        $db = new Database();

        /**
         * Query ausführen.
         *
         * Hier können wir leider nicht alle benötigten Tabellen-Namen generieren, weil die Benennung der Mapping
         * Tabelle komplex ist. Technisch geht das natürlich schon, wir haben es aber nicht im Core gebaut.
         */
        $result = $db->query('INSERT INTO products_categories_mm SET product_id = ?, category_id = ?', [
            'i:product_id' => $this->id,
            'i:category_id' => $categoryId
        ]);

        /**
         * Rückgabewert des Queries zurückgeben.
         */
        return $result;
    }

    /**
     * Verknüpfung zwischen einem Produkt und einer Kategorie aufheben.
     *
     * @param int $categoryId
     *
     * @return mixed
     */
    public function detachFromCategory (int $categoryId)
    {
        /**
         * Datenbankverbindung herstellen.
         */
        $db = new Database();

        /**
         * Query ausführen.
         *
         * Hier können wir leider nicht alle benötigten Tabellen-Namen generieren, weil die Benennung der Mapping
         * Tabelle komplex ist. Technisch geht das natürlich schon, wir haben es aber nicht im Core gebaut.
         */
        $result = $db->query('DELETE FROM products_categories_mm WHERE product_id = ? AND category_id = ?', [
            'i:product_id' => $this->id,
            'i:category_id' => $categoryId
        ]);

        /**
         * Rückgabewert des Queries zurückgeben.
         */
        return $result;
    }

    /**
     * Verwandte Produkte anhang gemeinsamer Kategorien holen.
     *
     * Befindet sich das Ausgangsprodukt nicht in einer Kategorie, werden $limit Produkte aus der Menge [alle Produkte]
     * ausgewählt.
     *
     * @param int $limit
     *
     * @return array
     */
    public function getRelatedProducts (int $limit = 4): array
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
         * Kategorien zum aktuellen Produkt abfragen
         */
        $categories = Category::findByProductId($this->id);

        /**
         * Zählen, wie viele Kategorien diesem Produkt zugewiesen sind.
         *
         * Das brauchen wir, damit wir ein Prepared-Statement für den Datenbank-Query dynamisch generieren können.
         */
        $numberOfCategories = count($categories); // bspw. 2

        /**
         * Sind Kategorien zugewiesen ...
         */
        if ($numberOfCategories > 0) {
            /**
             * ... so bereiten wir eine Liste an Query Parametern vor ...
             */
            $inClauseParams = array_fill(0, $numberOfCategories, '?'); // ['?', '?']
            $inClause = implode(',', $inClauseParams); // ?,?

            /**
             * ... und danach die Liste an Werten, die auf die Parameter gelegt werden sollen.
             */
            $categoryIds = [];
            foreach ($categories as $category) {
                $categoryId = $category->id;

                /**
                 * Hier kommt für jede zugewiesen Kategorie ein neuer Wert in den Parameter Array.
                 */
                $categoryIds["i:category-$categoryId"] = $categoryId;
            } // ['i:category-1' => 1, 'i:category-4' => 4]

            $queryParams = $categoryIds;
            /**
             * Zum Schluss wird noch der letzte Query Parameter $limit hinzugefügt.
             */
            $queryParams['i:limit'] = $limit;

            /**
             * Query ausführen.
             *
             * Hier wird ein WHERE ... IN() Query ausgeführt. Nachdem die Anzahl der Werte in den IN()-Klammern dynamisch
             * ist, muss auch das Prepared Statement dynamisch generiert werden. Demzufolge gibt es aber auch
             * eine dynamische Anzahl an Werten für diese Parameter und auch $queryParams muss flexibel definiert werden.
             */
            $result = $db->query("SELECT DISTINCT $tableName.* FROM $tableName
            JOIN products_categories_mm ON products_categories_mm.product_id = products.id
                WHERE products_categories_mm.category_id IN ($inClause) LIMIT ?", $queryParams);
        } else {
            /**
             * Ist das Produkt keiner Kategorie zugewiesen, so laden wir einfach alle Produkte und nehmen davon nur $limit.
             */
            $result = $db->query("SELECT * FROM $tableName LIMIT ?", [
                'i:limit' => $limit
            ]);
        }

        /**
         * Ergebnis-Array vorbereiten.
         */
        $objects = [];

        /**
         * Ergebnisse des Datenbank-Queries durchgehen und jeweils ein neues Objekt erzeugen.
         */
        foreach ($result as $object) {
            /**
             * Auslesen, welche Klasse aufgerufen wurde und ein Objekt dieser Klasse erstellen und in den Ergebnis-Array
             * speichern.
             */
            $calledClass = get_called_class();
            $objects[] = new $calledClass($object);
        }

        /**
         * Ergebnisse zurückgeben.
         */
        return $objects;
    }

    /**
     * @return float|int
     * @todo: comment
     */
    public function getAverageRating ()
    {
        if ($this->_rating > 0) {
            return $this->_rating;
        }

        $ratings = Rating::findByProductId($this->id);

        if (!empty($ratings)) {
            $_ratings = [];

            foreach ($ratings as $rating) {
                $_ratings[] = $rating->rating;
            }

            $ratingSum = array_sum($_ratings);

            /**
             * SUM(<aller Werte>) / COUNT(<aller Werte>)
             */
            $this->_rating = round($ratingSum / count($_ratings), 1);
            return $this->_rating;
        }

        return null;
    }

}
