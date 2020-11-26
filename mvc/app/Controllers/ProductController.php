<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Core\Config;
use Core\Database;
use Core\Session;
use Core\Validator;
use Core\View;

/**
 * Class ProductController
 *
 * @package App\Controllers
 */
class ProductController
{
    private array $_uploadedFiles = [];

    /**
     * @param int $id
     */
    public function show (int $id)
    {
        /**
         * Ein einzelnes Produkt anhand des Parameters in der URL über das Product Model aus der Datenbank abfragen.
         */
        $product = Product::find($id);

        /**
         * Kategorien zu dem Produkt abrufen
         */
        $categories = Category::findByProductId($product->id);

        /**
         * Produkt an View übergeben
         */
        View::render('product-single', [
            'product' => $product,
            'categories' => $categories
        ]);
    }

    /**
     * Product Bearbeitungsformular anzeigen.
     *
     * @param int $id
     */
    public function updateForm (int $id)
    {
        /**
         * Prüfen ob ein User eingeloggt ist und ob dieser eingeloggt User Admin ist. Wenn nicht, geben wir einen
         * Fehler 403 Forbidden zurück.
         */
        if (!User::isLoggedIn() || !User::getLoggedIn()->is_admin) {
            View::error403();
        }

        /**
         * Produkt, das bearbeitet werden soll, aus der Datenbank abfragen
         */
        $product = Product::find($id);

        /**
         * Kategorien abrufen, die dem Produkt zugewiesen sind
         */
        $productCategories = Category::findByProductId($product->id);
        /**
         * Alle Kategorien abrufen
         *
         * Wir machen das, damit wir im View prüfen können, ob eine Kategorie aus $allCategories auch in
         * $productCategories vorkommt - in diesem Fall werden wir die Checkbox schon vorauswählen, weil die
         * Zuweisung bereits besteht in der products_categories_mm Tabelle.
         */
        $allCategories = Category::all();

        /**
         * Produkt, das bearbeitet werden soll, an den View übergeben.
         */
        View::render('admin/product-update', [
            'product' => $product,
            'productCategories' => $productCategories,
            'allCategories' => $allCategories
        ]);
    }

    /**
     * Produkt mit den Daten aus dem Bearbeitungsformular aktualisieren.
     *
     * @param int $id
     */
    public function update (int $id)
    {
        /**
         * Prüfen ob ein User eingeloggt ist und ob dieser eingeloggt User Admin ist. Wenn nicht, geben wir einen
         * Fehler 403 Forbidden zurück.
         */
        if (!User::isLoggedIn() || !User::getLoggedIn()->is_admin) {
            View::error403();
        }

        /**
         * Formulardaten validieren.
         */
        $validationErrors = $this->validateAndGetErrors();

        /**
         * [x] Product abrufen
         * [x] Geänderte Formulardaten ins Product schreiben
         * [x] Bilder ggf. löschen
         * [x] Datei-Upload verwalten
         * [x] Daten in die Datenbank speichern
         */

        /**
         * Produkt, das bearbeitet werden soll, aus der Datenbank laden.
         */
        $product = Product::find($id);

        /**
         * Eigenschaften des Produkts mit den Daten aus dem Formular aktualisieren.
         */
        $product->name = $_POST['name'];
        $product->description = $_POST['description'];
        $product->price = (float)$_POST['price'];
        $product->stock = (int)$_POST['stock'];

        /**
         * Sollen Bilder gelöscht werden?
         */
        if (isset($_POST['delete-image'])) {
            /**
             * Wenn ja, alle Bilder, die gelöscht werden sollen, durchgehen und die Verknüpfung zu dem Produkt aufheben.
             */
            foreach ($_POST['delete-image'] as $path => $on) {
                /**
                 * Die basename-Funktion extrahiert aus einem Dateipfad nur den Dateinamen, z.B.:
                 * basename('/var/www/html/index.php') --> 'index.php'
                 *
                 * s. https://www.php.net/manual/en/function.basename.php
                 */
                $filename = basename($path);

                /**
                 * Verknüpfung zwischen Bild und Produkt aufheben. Das Bild wird dabei nicht aus dem uploads-Ordner
                 * gelöscht.
                 */
                $product->removeImage($filename);
            }
        }

        /**
         * Hochgeladene, neue Bilder verarbeiten
         */
        $product = $this->validateFileUpload($product, $validationErrors);

        /**
         * Sind Validierungsfehler aufgetreten ...
         */
        if (!empty($validationErrors)) {
            /**
             * ... dann speichern wir sie in die Session und leiten zurück zum Bearbeitungsformular, wo die Fehler über
             * das errors.php Partial ausgegeben werden.
             */
            Session::set('errors', $validationErrors);

            /**
             * Hier löschen wir alle Dateien, die hochgeladen wurden wieder, weil sie nicht ins Product gespeichert wurden
             * und dadurch Dateien im Uploads Ordner wären, die nicht verwendet werden.
             */
            $this->discardUploadedFiles();

            /**
             * Redirect zurück zum Bearbeitungsformular.
             */
            header('Location: ' . BASE_URL . '/admin/products/' . $product->id . '/edit');
            exit;
        }

        /**
         * Geändertes Produkt in der Datenbank aktualisieren.
         */
        $product->save();

        /**
         * Category-Checkboxen verarbeiten
         */
        $this->handleCategories($product);

        /**
         * Wie oben, werden hier jetzt die Bilder, die gelöscht werden sollen, physisch aus dem uploads-Ordner gelöscht.
         * Das ganze muss in einem eigenen Schritt passieren, weil sonst Bilder gelöscht werden könnten, die in der
         * Datenbank noch referenziert sind - das darf nicht passieren. Lieber haben wir Bilder, die nicht mehr
         * referenziert sind und somit sinnlos gespeichert werden.
         */
        if (isset($_POST['delete-image'])) {
            foreach ($_POST['delete-image'] as $path => $on) {
                unlink(__DIR__ . "/../../$path");
            }
        }

        /**
         * Hat alles funktioniert und sind keine Fehler aufgetreten, leiten wir zurück zum Bearbeitungsformular. Hier
         * könnten wir auch auf die Produkt-Übersicht im Dashboard leiten oder irgendeine andere Route.
         */
        Session::set('success', ['Das Produkt wurde erfolgreich gespeichert.']);
        header('Location: ' . BASE_URL . '/admin/products/' . $product->id . '/edit');
        exit;
    }

    /**
     * Formular zur Erstellung eines neune Products ausgeben.
     */
    public function createForm ()
    {
        /**
         * Prüfen ob ein User eingeloggt ist und ob dieser eingeloggt User Admin ist. Wenn nicht, geben wir einen
         * Fehler 403 Forbidden zurück.
         */
        if (!User::isLoggedIn() || !User::getLoggedIn()->is_admin) {
            View::error403();
        }

        /**
         * Alle Kategorien aus der Datenbank auslesen
         */
        $categories = Category::all();

        /**
         * Produkt, das bearbeitet werden soll, an den View übergeben.
         */
        View::render('admin/product-create', [
            'categories' => $categories
        ]);
    }

    /**
     * Daten aus dem Formular für ein neues Produkt entgegennehmen.
     */
    public function create ()
    {
        /**
         * Prüfen ob ein User eingeloggt ist und ob dieser eingeloggt User Admin ist. Wenn nicht, geben wir einen
         * Fehler 403 Forbidden zurück.
         */
        if (!User::isLoggedIn() || !User::getLoggedIn()->is_admin) {
            View::error403();
        }

        /**
         * Formulardaten validieren.
         */
        $validationErrors = $this->validateAndGetErrors();

        /**
         * Neues Produkt anlegen, damit wir die Daten aus dem Formular speichern können.
         */
        $product = new Product();

        /**
         * Eigenschaften des Produkts mit den Daten aus dem Formular aktualisieren.
         */
        $product->name = $_POST['name'];
        $product->description = $_POST['description'];
        $product->price = (float)$_POST['price'];
        $product->stock = (int)$_POST['stock'];

        /**
         * Hochgeladene, neue Bilder verarbeiten
         */
        $product = $this->validateFileUpload($product, $validationErrors);

        /**
         * Sind Validierungsfehler aufgetreten ...
         */
        if (!empty($validationErrors)) {
            /**
             * ... dann speichern wir sie in die Session und leiten zurück zum Bearbeitungsformular, wo die Fehler über
             * das errors.php Partial ausgegeben werden.
             */
            Session::set('errors', $validationErrors);

            /**
             * Hier löschen wir alle Dateien, die hochgeladen wurden wieder, weil sie nicht ins Product gespeichert wurden
             * und dadurch Dateien im Uploads Ordner wären, die nicht verwendet werden.
             */
            $this->discardUploadedFiles();

            /**
             * Redirect zurück zum Bearbeitungsformular.
             */
            header('Location: ' . BASE_URL . '/admin/products/create');
            exit;
        }

        /**
         * Neues Produkt in die Datenbank speichern.
         *
         * Die User::save() Methode gibt true zurück, wenn die Speicherung in die Datenbank funktioniert hat.
         */
        if ($product->save()) {
            /**
             * Category-Checkboxen verarbeiten
             */
            $this->handleCategories($product);

            /**
             * Hat alles funktioniert und sind keine Fehler aufgetreten, leiten wir zurück zum Bearbeitungsformular. Hier
             * könnten wir auch auf die Produkt-Übersicht im Dashboard leiten oder irgendeine andere Route.
             *
             * Um eine Erfolgsmeldung ausgeben zu können, verwenden wir die selbe Mechanik wie für die errors.
             */
            Session::set('success', ['Das Produkt wurde erfolgreich gespeichert.']);

            /**
             * Redirect zur Bearbeitungsseite.
             */
            header('Location: ' . BASE_URL . '/admin/products/' . $product->id . '/edit');
            exit;
        } else {
            /**
             * Fehlermeldung erstellen und in die Session speichern.
             */
            $validationErrors[] = 'Das Produkt konnte nicht gespeichert werden.';
            Session::set('errors', $validationErrors);

            /**
             * Hier löschen wir alle Dateien, die hochgeladen wurden wieder, weil sie nicht ins Product gespeichert wurden
             * und dadurch Dateien im Uploads Ordner wären, die nicht verwendet werden.
             */
            $this->discardUploadedFiles();

            /**
             * Redirect zurück zum Erstellungsformular.
             */
            header('Location: ' . BASE_URL . '/admin/products/create');
            exit;
        }

    }

    /**
     * Ein Produkt aus der Datenbank löschen.
     *
     * @param int $id
     */
    public function delete (int $id)
    {
        /**
         * Product, das gelöscht werden soll, aus der Datenbank abfragen.
         */
        $product = Product::find($id);

        /**
         * Produkt löschen.
         *
         * Dadurch wird das Produkt aus der Datenbank gelöscht, die Daten, die schon abgefragt wurden, bleiben in dem
         * Objekt $product aber erhalten.
         */
        $product->delete();

        /**
         * Alle Bilder des Products von der Festplatte löschen.
         */
        foreach ($product->getImages() as $filename) {
            unlink(__DIR__ . "/../../$filename");
        }

        /**
         * Redirect zur Produktübersicht.
         */
        header('Location: ' . BASE_URL . '/admin');
        exit;
    }

    /**
     * Wir haben die Validierung der Formulardaten für Erstellung und Bearbeitung eines Produkts in eine eigen Funktion
     * ausgelagert, weil beide Formulare mehr oder weniger ident validiert werden und wir daher den Code nicht zu
     * duplizieren brauchen.
     *
     * @return array
     */
    private function validateAndGetErrors ()
    {
        /**
         * Formulardaten validieren.
         */
        $validator = new Validator();
        $validator->validate($_POST['name'], 'Name', true, 'textnum');
        $validator->validate($_POST['stock'], 'Stock', true, 'int', 0);
        $validator->validate($_POST['price'], 'Price', true, 'float', 0);
        $validator->validate($_POST['description'], 'Description', false, 'textnum');

        /**
         * Validierungsfehler aus dem Validator holen und zurückgeben.
         */
        return $validator->getErrors();
    }

    /**
     * Nachdem die Formulare für Erstellung und Bearbeitung eines Produkts einen Datei-Upload haben und die Dateien in
     * beiden Fällen ident behandelt werden müssen, haben wir auch hier eine Funktion dafür erstellt, damit wir Code
     * Duplication vermeiden.
     *
     * @param Product $product
     * @param array   $validationErrors
     *
     * @return Product
     */
    private function validateFileUpload (Product $product, array &$validationErrors)
    {
        /**
         * Hochgeladenen, "neuen" Bilder verarbeiten
         *
         * $_FILES beinhaltet immer ein leeres Bild, wenn keine Datei hochgeladen wurde. Daher prüfen wir, ob das nullte
         * Bild leer ist. Wenn nicht, wurden Datein hochgeladen.
         */
        if (!empty($_FILES['images']['name'][0])) {

            /**
             * Alle hochgeladenen Bilder-Namen durchgehen.
             *
             * Hier benötigen wir den $index, weil die $_FILES Superglobal so strukturiert ist, dass die einzelnen
             * Stücke an Information zu einem Bild verteilt sind und nicht zusammen in einem einzelnen Array gebündelt.
             * Es gibt also in 'name' und 'type' usw. jeweils mehrere Werte, die über den $index zur selben Datei
             * zugeordnet werden können.
             */
            foreach ($_FILES['images']['name'] as $index => $originalFileName) {
                /**
                 * Werte der aktuellen Datei mithilfe des $index sammeln.
                 */
                $name = $originalFileName;
                $type = $_FILES['images']['type'][$index];
                $tmp_name = $_FILES['images']['tmp_name'][$index];
                $error = $_FILES['images']['error'][$index];
                $size = $_FILES['images']['size'][$index];

                /**
                 * [x] Handelt es sich um eine Bild?
                 * [x] Ist die Datei übermäßig groß (Dateigröße)? max. 10MB
                 * [x] Liegen die Dimensionen im Rahmen?
                 * [x] Wenn ja: speichern!
                 * [x] Wenn nein: Fehler!
                 */

                /**
                 * Limits für Dateigröße und Dimensionen (Breite & Höhe) für die hochgeladenen Bilder aus der Config
                 * auslesen.
                 */
                $uploadLimit = Config::get('app.upload-limit-filesize', 1024 * 1024 * 10);
                $imageSizeWidthLimit = Config::get('app.upload-limit-width', 1920);
                $imageSizeHeightLimit = Config::get('app.upload-limit-height', 1080);

                /**
                 * Auslesen der Dimensionen der aktuell durchlaufenen hochgeladenen Datei
                 */
                $uploadedImageSizes = getimagesize($tmp_name);
                $uploadedImageWidth = $uploadedImageSizes[0];
                $uploadedImageHeight = $uploadedImageSizes[1];

                /**
                 * Validieren der gerade durchlaufenen hochgeladenen Datei
                 */
                if ($error !== UPLOAD_ERR_OK) {
                    /**
                     * Ist der Status ungleich dem OK-Status, so ist ein Fehler aufgetreten und wir fügen eine Meldung
                     * zu den Validierungsfehlern aus dem Validator hinzu.
                     */
                    $validationErrors[] = 'Dateiupload ist fehlgeschlagen. Fehler' . $error;

                } elseif (strpos($type, 'image/') !== 0) {
                    /**
                     * Kommt 'image/' innerhalb des Mimetype, bspw. 'image/jpg' oder 'application/pdf', vor, so handelt
                     * es sich um eine Bild-Datei und wir nehmen sie an. Handelt es sich nicht um eine Bild-Datei,
                     * schreiben wir einen Fehler.
                     */
                    $validationErrors[] = 'Es sind nur Bilder erlaubt';

                } elseif ($size > $uploadLimit) {
                    /**
                     * Überschreitet die Größe der hochgeladenen Datei das Upload-Limit?
                     */
                    $validationErrors[] = 'Die Datei ist zu groß!';

                } elseif (
                    $uploadedImageWidth > $imageSizeWidthLimit
                    || $uploadedImageHeight > $imageSizeHeightLimit
                ) {
                    /**
                     * Überschreiten entweder die Breite, die Höhe oder beide Dimensionen die maximalen Dimensionen von
                     * Bildern, die wir in der Config definiert und oben ausgelesen haben?
                     */
                    $validationErrors[] = 'Die Dimensionen der Datei übeschreibten das Maximum';

                } else {
                    /**
                     * Wurden im Zuge der Validierung keine Fehler gefunden, holen wir uns die Pfade zum Uploads-Ordner
                     * aus der config.
                     */
                    $storagePath = Config::get('app.storage-path', 'storage/');
                    $uploadPath = Config::get('app.upload-path', 'uploads/');

                    /**
                     * Hier bauen wir aus $storagePath und $uploadPath einen Ordner-Pfad relativ zu dieser Datei
                     * (ProductController.php) zusammen. Diesen Ordner brauchen wir, damit wir die hochgeladene Datei
                     * aus dem temporären Ordner von PHP in den Ordner verschieben können.
                     */
                    $destinationFolder = __DIR__ . "/../../{$storagePath}{$uploadPath}";

                    /**
                     * Damit Dateien mit gleichen Namen sich nicht gegenseitig überschreiben hängen wir den aktuellen
                     * UNIX-Timestamp vorne dran.
                     */
                    $destinationFilename = time() . "_$name";

                    /**
                     * Jetzt bauen wir aus dem oben konstrukierten Ordner-Pfad und dem Dateinamen das finale Ziel, an
                     * das die hochgeladene Datei verschoben werden soll.
                     */
                    $destination = $destinationFolder . $destinationFilename;

                    /**
                     * PHP liefert eine Funktion, die genau dazu gedacht ist, hochgeladene Dateien vom temporären
                     * Verzeichnis von PHP an ihr Ziel zu verschieben. move_uploaded_file() gibt dabei true zurück, wenn
                     * der Vorgang erfolgreich war und false, wenn ein Fehler aufgetreten ist während des Verschiebens.
                     * Das kann bspw. passieren, wenn im Zielordner keine Schreibrechte vorhanden sind oder der Zielordner
                     * nicht existiert.
                     */
                    if (move_uploaded_file($tmp_name, $destination)) {
                        /**
                         * Wurde die Datei erfolgreich an ihr Ziel verschoben, verknüpfen wir sie mit dem Product, das
                         * bearbeitet wurde.
                         *
                         * Hier verwende ich absichtlich die basename()-Funktion um den Dateinamen wieder aus dem Pfad
                         * zu bekommen, weil ich an dieser Stelle 100%ig sicher sein kann, dass der Pfad, den wir in
                         * move_uploade_file() als $destination übergeben haben, valide ist und die hochgeladene Datei
                         * auch wirklich dort gespeichert ist. Ich könnte auch $destinationFilename verwenden, aber wenn
                         * wir möglicherweise noch eine Funktion bauen, die Leerzeichen in dem Pfad ersetzt oder etwas
                         * in der Art, dann macht es Sinn, hier den tatsächlich verwendeten Pfad heranzuziehen und den
                         * Dateinamen daraus zu berechnen.
                         */
                        $product->addImage(basename($destination));

                        /**
                         * Hier speichern wir den Pfad des aktuell verarbeiteten Bildes in eine Eigenschaft des
                         * Controllers, damit wir die in diesem Programmdurchlauf hochgeladenen Bilder auch direkt
                         * wieder löschen können, wenn die Validierung fehlschlagen sollte oder ein Fehler beim
                         * Speichern des Produkts in die DB auftritt.
                         */
                        $this->_uploadedFiles[] = $destination;
                    } else {
                        /**
                         * Ist ein Fehler beim verschieben der Datei aufgetreten, schreiben wir einen Fehler.
                         */
                        $validationErrors[] = 'Die hochgeladene Datei konnte nicht gespeichert werden.';
                    }
                }
            }
        }

        /**
         * Potentiell verändertes Produkt (neue Bilder) zurückgeben
         */
        return $product;
    }

    /**
     * Dateien, die im aktuellen Programmdurchlauf hochgeladen wurden, wieder löschen.
     *
     * Diese Funktion wird verwendet, wenn ein Fehler bei der Validierung oder Speicherung eines Produkts in die DB
     * aufgetreten sind und die Dateien somit nicht erhalten bleiben sollten, weil sie nicht in der Datenbank vermerkt
     * werden konnten.
     */
    private function discardUploadedFiles ()
    {
        foreach ($this->_uploadedFiles as $uploadedFile) {
            unlink($uploadedFile);
        }
    }

    /**
     * Category-Checkboxen aus dem Produkt-Formular entgegennehmen und verarbeiten.
     *
     * @param Product $product
     */
    private function handleCategories (Product $product)
    {
        /**
         * Alle Kategorien zu dem $product aus der Datenbank abfragen
         */
        $productCategories = Category::findByProductId($product->id);
        /**
         * Indizes des Arrays, der aus dem Formular übergeben wird, als Werte eines neuen Arrays speichern, damit wir
         * leichter damit arbeiten können.
         */
        $newCategoryIds = array_keys($_POST['categories']);
        /**
         * Array vorbereiten, in den wir die IDs der bereits verknüpften Categories rein speichern, wenn wir durchgehen,
         * welche Verknüpfungen gelöst werden müssen.
         */
        $idsOfLinkedCategories = [];

        /**
         * Nun gehen wir alle Kategorien durch, die mit dem Produkt verknüpft sind.
         */
        foreach ($productCategories as $category) {
            /**
             * Ist eine Category verknüpft, die auch im Formular angehakerlt ist, so soll die Verknüpfung bestehen
             * bleiben und wir speichern die ID der Category in unser vorbereitetes Array.
             */
            if (in_array($category->id, $newCategoryIds)) {
                $idsOfLinkedCategories[] = $category->id;
            } else {
                /**
                 * Ist eine Category verknüpft, die nicht im Formular angehakerlt ist, so soll die Verknüpfung gelöst
                 * werden.
                 */
                $product->detachFromCategory($category->id);
            }
        }

        /**
         * Nun gehen wir alle angehakerlten Checkboxen durch.
         */
        foreach ($newCategoryIds as $categoryId) {
            /**
             * Wenn eine Category Checkbox angehakerlt wurde, die in $idsOfLinkedCategories vorhanden ist, so besteht
             * die Verbindung zwischen Category und Product bereits. Daher invertieren wir die Bedingung und prüfen, ob
             * die Checkbox noch nicht in dem vorbereiteten Array vorkommt - in diesem Fall muss eine neue Verknüpfung
             * zwischen Produkt und Kategorie angelegt werden.
             */
            if (!in_array($categoryId, $idsOfLinkedCategories)) {
                $product->attachToCategory($categoryId);
            }
        }
    }
}
