<?php


namespace App\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\User;
use Core\Session;
use Core\Validator;
use Core\View;

/**
 * Class PostController
 *
 * @package App\Controllers
 * @todo    : comment
 */
class PostController
{

    /**
     * Bearbeitungsformular anzeigen.
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
         * Post, der bearbeitet werden soll, aus der Datenbank abfragen
         */
        $post = Post::find($id);

        /**
         * Alle Produkte aus der Datenbank abfragen
         */
        $allProducts = Product::all();

        /**
         * Produkte, die der Category zugewiesen sind, aus der der Datenbank abfragen
         */
        $postProducts = Product::findByPostId($post->id);

        /**
         * View laden und Variablen übergeben
         */
        View::render('admin/post-update', [
            'post' => $post,
            'allProducts' => $allProducts,
            'postProducts' => $postProducts
        ]);
    }

    /**
     * Daten aus Bearbeitungsformular entgegennehmen und verarbeiten.
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
         * Post, der bearbeitet werden soll, aus der Datenbank laden.
         */
        $post = Post::find($id);

        /**
         * Sind keine Validierungsfehler aufgetreten ...
         */
        if (!empty($validationErrors)) {
            /**
             * ... dann speichern wir sie in die Session und leiten zurück zum Bearbeitungsformular, wo die Fehler über
             * das errors.php Partial ausgegeben werden.
             */
            Session::set('errors', $validationErrors);

            /**
             * Redirect zurück zum Bearbeitungsformular.
             */
            header('Location: ' . BASE_URL . '/admin/posts/' . $post->id . '/edit');
            exit;
        }

        /**
         * Eigenschaften der Category mit den Daten aus dem Formular aktualisieren.
         */
        $post->title = $_POST['title'];
        $post->content = $_POST['content'];

        /**
         * Neues Produkt in die Datenbank speichern.
         *
         * Die User::save() Methode gibt true zurück, wenn die Speicherung in die Datenbank funktioniert hat.
         */
        if ($post->save()) {
            /**
             * Category-Checkboxen verarbeiten
             */
            $this->handleProducts($post);

            /**
             * Hat alles funktioniert und sind keine Fehler aufgetreten, leiten wir zurück zum Bearbeitungsformular.
             */
            Session::set('success', ['Der Post wurde erfolgreich gespeichert.']);
            header('Location: ' . BASE_URL . '/admin/posts/' . $post->id . '/edit');
            exit;
        } else {
            /**
             * Fehlermeldung erstellen und in die Session speichern.
             */
            $validationErrors[] = 'Der Post konnte nicht gespeichert werden.';
            Session::set('errors', $validationErrors);

            /**
             * Redirect zurück zum Erstellungsformular.
             */
            header('Location: ' . BASE_URL . '/admin/posts/' . $post->id . '/edit');
            exit;
        }
    }

    /**
     * Formular zur Erstellung eines neuen Posts ausgeben.
     *
     * @todo: comment
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
         * Alle Produkte aus der Datenbank abfragen
         */
        $allProducts = Product::all();


        /**
         * View laden
         */
        View::render('admin/post-create', [
            'products' => $allProducts
        ]);
    }

    /**
     * Daten aus Erstellungsformular entgegennehmen und verarbeiten.
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
         * Sind Validierungsfehler aufgetreten ...
         */
        if (!empty($validationErrors)) {
            /**
             * ... dann speichern wir sie in die Session und leiten zurück zum Bearbeitungsformular, wo die Fehler über
             * das errors.php Partial ausgegeben werden.
             */
            Session::set('errors', $validationErrors);

            /**
             * Redirect zurück zum Erstellungsformular.
             */
            header('Location: ' . BASE_URL . '/admin/posts/create');
            exit;
        }

        /**
         * Neue Kategorie anlegen, damit wir die Daten aus dem Formular speichern können.
         */
        $post = new Post();

        /**
         * Eigenschaften der Kategorie mit den Daten aus dem Formular befüllen.
         */
        $post->title = $_POST['title'];
        $post->content = $_POST['content'];
        $post->user_id = User::getLoggedIn()->id;

        /**
         * Neuen Post in die Datenbank speichern.
         *
         * Die User::save() Methode gibt true zurück, wenn die Speicherung in die Datenbank funktioniert hat und false,
         * wenn ein Fehler aufgetreten ist.
         */
        if (!$post->save()) {
            /**
             * Fehlermeldung erstellen und in die Session speichern.
             */
            $validationErrors[] = 'Der Post konnte nicht gespeichert werden.';
            Session::set('errors', $validationErrors);

            /**
             * Redirect zurück zum Erstellungsformular.
             */
            header('Location: ' . BASE_URL . '/admin/posts/create');
            exit;
        }

        /**
         * Hat alles funktioniert und sind keine Fehler aufgetreten, leiten wir zurück zum Bearbeitungsformular.
         *
         * Um eine Erfolgsmeldung ausgeben zu können, verwenden wir die selbe Mechanik wie für die errors.
         */
        Session::set('success', ['Der Post wurde erfolgreich gespeichert.']);

        /**
         * Redirect zur Bearbeitungsseite.
         */
        header('Location: ' . BASE_URL . '/admin/posts/' . $post->id . '/edit');
        exit;
    }

    /**
     * Wir haben die Validierung der Formulardaten für Erstellung und Bearbeitung einer Kategorie in eine eigen Funktion
     * ausgelagert, weil beide Formulare ident validiert werden und wir daher den Code nicht zu duplizieren brauchen.
     *
     * @return array
     */
    public function validateAndGetErrors (): array
    {
        /**
         * Formulardaten validieren.
         */
        $validator = new Validator();
        $validator->validate($_POST['title'], 'Title', true, 'textnum');
        $validator->validate($_POST['content'], 'Content', true, 'textnum');

        /**
         * Validierungsfehler aus dem Validator holen und zurückgeben.
         */
        return $validator->getErrors();
    }

    /**
     * Product-Checkboxen aus dem Post-Formular entgegennehmen und verarbeiten.
     *
     * @param Post $post
     */
    function handleProducts (Post $post)
    {
        /**
         * Alle Produkte zu dem $post aus der Datenbank abfragen
         */
        $postProducts = Product::findByPostId($post->id);
        /**
         * Indizes des Arrays, der aus dem Formular übergeben wird, als Werte eines neuen Arrays speichern, damit wir
         * leichter damit arbeiten können.
         */
        $newProductIds = array_keys($_POST['products']);
        /**
         * Array vorbereiten, in den wir die IDs der bereits verknüpften Products rein speichern, wenn wir durchgehen,
         * welche Verknüpfungen gelöst werden müssen.
         */
        $idsOfLinkedProducts = [];

        /**
         * Nun gehen wir alle Produkte durch, die mit der Kategorie verknüpft sind.
         */
        foreach ($postProducts as $product) {
            /**
             * Ist ein Produkt verknüpft, das auch im Formular angehakerlt ist, so soll die Verknüpfung bestehen
             * bleiben und wir speichern die ID des Produkts in unser vorbereitetes Array.
             */
            if (in_array($product->id, $newProductIds)) {
                $idsOfLinkedProducts[] = $product->id;
            } else {
                /**
                 * Ist ein Produkt verknüpft, das nicht im Formular angehakerlt ist, so soll die Verknüpfung gelöst
                 * werden.
                 */
                $product->detachFromPost($post->id);
            }
        }

        /**
         * Nun gehen wir alle angehakerlten Checkboxen durch.
         */
        foreach ($newProductIds as $productId) {
            /**
             * Wenn eine Produkt Checkbox angehakerlt wurde, die in $idsOfLinkedProducts vorhanden ist, so besteht
             * die Verbindung zwischen Category und Product bereits. Daher invertieren wir die Bedingung und prüfen, ob
             * die Checkbox noch nicht in dem vorbereiteten Array vorkommt - in diesem Fall muss eine neue Verknüpfung
             * zwischen Produkt und Kategorie angelegt werden.
             */
            if (!in_array($productId, $idsOfLinkedProducts)) {
                $product = Product::find($productId);
                $product->attachToPost($post->id);
            }
        }
    }

}
