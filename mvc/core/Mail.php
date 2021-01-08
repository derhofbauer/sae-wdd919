<?php

namespace Core;

/**
 * Class Mail
 *
 * @package Core
 */
class Mail
{

    /**
     * Eigenschaften eines einfachen Mails vorbereiten.
     *
     * + $to, $cc und $bcc sind arrays, weil es jeweils mehrere Empfänger in diesen Feldern geben kann.
     * + $replyTo, $from und $mailer sind wie alle anderen Informationen außer $message eigentlich auch nur
     * Informationen, die im E-Mail Header übertragen werden. Sie sind aber so gängig, dass wir sie als eigene
     * Eigenschaften behandeln und daraus dann die E-Mail Header generieren.
     */
    public array $to = [];
    public array $cc = [];
    public array $bcc = [];
    public string $subject;
    public string $message;
    public string $replyTo;
    public string $from;
    public string $mailer;
    public array $headers = [];
    public array $error;

    /**
     * Mail constructor.
     *
     * Hier bieten wir durch die optionalen Funktionsparameter die Möglichkeit, die verpflichtend benötigten
     * Informationen für eine E-Mail direkt bei der Erstellung des neuen Objects zu übergeben. Dadurch könnte in nur 2
     * Zeilen ein Mail erstellt und abgeschickt werden.
     *
     * @param string|null $to
     * @param string|null $subject
     * @param string|null $message
     */
    public function __construct (string $to = null, string $subject = null, string $message = null)
    {
        /**
         * Mailer setzen.
         *
         * Diese Information kann später überschrieben werden, wenn das benötigt wird.
         */
        $this->mailer = 'PHP/' . phpversion();

        /**
         * $to, $subject und $message setzen, wenn Werte übergeben wurden.
         */
        if (!empty($to)) {
            $this->to[] = $to;
        }

        if (!empty($subject)) {
            $this->subject = $subject;
        }

        if (!empty($message)) {
            $this->message = $message;
        }
    }

    /**
     * Einen Empfänger hinzufügen.
     *
     * Laut Spezifikation muss kein Name angegeben werden, es reicht auch eine E-Mail Adresse.
     *
     * @param string      $email
     * @param string|null $name
     */
    public function addTo (string $email, ?string $name)
    {
        $this->to[] = $this->prepareNameAndEmail($email, $name);
    }

    /**
     * s. self::addTo()
     *
     * @param string      $email
     * @param string|null $name
     */
    public function addCc (string $email, ?string $name)
    {
        $this->cc[] = $this->prepareNameAndEmail($email, $name);
    }

    /**
     * s. self::addTo()
     *
     * @param string      $email
     * @param string|null $name
     */
    public function addBcc (string $email, ?string $name)
    {
        $this->bcc[] = $this->prepareNameAndEmail($email, $name);
    }

    /**
     * Hilfsfunktion um einfach einen neuen Header für die E-Mail hinzuzufügen.
     *
     * Wir müssen nicht prüfen, ob ein Wert schon gesetzt ist oder nicht, da wir nicht in $this->headers pushen,
     * sondern mit einem eindeutigen Array-Key arbeiten.
     *
     * @param string $name
     * @param string $value
     */
    public function addHeader (string $name, string $value)
    {
        $this->headers[$name] = $value;
    }

    /**
     * Hilfsfunktion um einfach den/die Absender*in anzugeben.
     *
     * s. self::addTo()
     *
     * @param string      $from
     * @param string|null $name
     */
    public function setFrom (string $from, string $name = null)
    {
        $sender = $this->prepareNameAndEmail($from, $name);
        $this->addHeader('From', $sender);
    }

    /**
     * Sendet die erstellte E-Mail ab.
     *
     * @return bool
     */
    public function send (): bool
    {
        /**
         * Empfänger*innen vorbereiten.
         */
        $recipients = $this->prepareRecipients();
        /**
         * E-Mail Header vorbereiten.
         */
        $this->prepareHeaders();

        /**
         * Mail mit der PHP mail() Funktion abschicken.
         *
         * Der Grund, wieso wir eine eigene Mail Klasse geschrieben haben, liegt genau hier. Wenn aus irgendeinem Grund eine andere Versandart verwendet werden soll (bspw. SMTP oder irgendein gehostet Service, das über eine API angesprochen wird), dann können wir hier die mail() Funktion einfach austauschen - überall, wo wir die Mail Klasse verwenden, brauchen wir aber keinerlei Änderung machen.
         */
        $success = mail($recipients, $this->subject, $this->message, $this->headers);

        /**
         * Wenn ein Fehler aufgetreten ist, holen wir uns diesen in die $error Property.
         */
        if (!$success) {
            $this->error = error_get_last();
        }

        return $success;
    }

    /**
     * Hilfsfunktion, mit der die Arrays der Properties $to, $cc und $bcc in einen kommasepatierten String umformatiert
     * werden, damit sie in E-Mails verwendet werden können.
     *
     * @return string
     */
    private function prepareRecipients (): string
    {
        /**
         * Gibt es $cc-Adressen?
         */
        if (!empty($this->cc)) {
            /**
             * Wenn ja, fügen wir sie mit einem ", " zu einem String zusammen und speichern das Ergebnis in einen Header.
             */
            $this->addHeader('Cc', implode(', ', $this->cc));
        }
        if (!empty($this->bcc)) {
            $this->addHeader('Bcc', implode(', ', $this->bcc));
        }

        /**
         * Die Empfänger*innen werden ebenso zu einem String zusammengefügt. Allerdings braucht die Mail-Funktion diesen
         * Wert als Funktionsparameter, daher fügen wir ihn nicht als Header hinzu, sondern geben ihn zurück.
         */
        return implode(', ', $this->to);
    }

    /**
     * Hilfsfunktion, um die Standard-Header zu erstellen.
     */
    private function prepareHeaders ()
    {
        $this->addHeader('X-Mailer', $this->mailer);

        if (!empty($this->replyTo)) {
            $this->addHeader('Reply-To', $this->replyTo);
        }
    }

    /**
     * Hilfsfunktion, damit wir die selbe Logik zur Erstellung von Emofänger*innen- oder Absender*innen-Adressen in
     * mehreren Methoden verwenden können.
     *
     * @param string      $email
     * @param string|null $name
     *
     * @return string
     */
    private function prepareNameAndEmail (string $email, ?string $name): string
    {
        /**
         * Wurde ein $name übergeben, verwenden wir diesen, andernfalls nur die E-Mail Adresse, die verpflichtend übergeben werden muss.
         */
        if (!is_null($name)) {
            return "$name <$email>";
        }
        return $email;
    }

}
