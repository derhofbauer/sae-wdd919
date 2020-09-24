# PHP Basics - WDD919

Dieses README enhält einige Infos zu dieser Repository, nützliche Links und andere Infos, die sich in den letzten Unterrichten als hilfreich erwiesen haben. Ich werde versuchen die Datei aktuell zu halten und mit weiteren Infos zu füllen.

## Text Editor

Ihr braucht für den Unterricht einen Text-Editor. Im Prinzip könnt ihr jeden beliebigen Plaintext Editor verwenden. Ich bin persönlich bin ein großer Fan von PhpStorm. Als SAE Studenten kriegt ihr das gesamte Programm Angebot vom Hersteller JetBrains, also auch PhpStorm kostenlos als Student Version. Nähere Infos auf der Hersteller Website: https://www.jetbrains.com/community/education/#students

## `docker-compose`

Wer gerne Docker/Docker Compose nutzen möchte, findet ein entsprechendes File im Repository. Ihr könnte aber genauso gut auch den XAMPP/MAMP/whatever verwenden.

### Web
+ Apache: [localhost:8080](localhost:8080)
+ PhpMyAdmin: [localhost:8081](localhost:8081)

### Database
+ MariaDB: [localhost:3306](localhost:3306)

### Composer

Das MVC verwendet Composer. Wir haben die Bibliothek MPDF mittels Composer installiert. Um alle Abhängigkeiten nach dem Pull zu installieren führe im Terminal im Ordner `mvc` den Befehl `php composer.phar install` aus. Danach sollte ein Ordner angelegt werden mit dem Namen `vendor` und mehreren Inhalten. Bis dahin funktioniert das MVC möglicherweise nicht mehr.

## Nützliche Informationen & Links

+ PHP Docs: https://www.php.net/
+ MySQL Docs: https://dev.mysql.com/doc/
+ GIT Flow: https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow
+ https://learngitbranching.js.org/
+ Regular Expressions:
  + https://regex101.com/
  + https://regexr.com/
+ https://regexcrossword.com
+ Command Line Basics: https://levelup.gitconnected.com/console-commands-that-you-should-know-how-to-use-f2b24a455394
+ https://www.gitignore.io/

### Cheatsheets

+ Markdown: https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet
+ GIT: https://www.atlassian.com/git/tutorials/atlassian-git-cheatsheet
+ https://devhints.io/
+ http://cheat.sh/

### Alternative relationale Datenbanksysteme

+ PostgreSQL (bspw. Volltextsuche): https://www.postgresql.org/

### Snippets

```php

/**
 * $link holds the DB connection object created by mysqli_connect()
 */
$result = mysqli_query($link, "SELECT ...");

while ($row = mysqli_fetch_assoc($result)) {
    // do something with the current $row
}
```

```php
/**
 * Die extract Funktion macht im Hintergrund folgendes:
 *
 * $id = $row['id'];
 * $title = $row['title'];
 * usw.
 */
extract($row);
```

### Misc

+ Manche Verzeichnisse in diesem Repository werden ein `.gitkeep` file beinhalten und sonst nichts. Das liegt daran, dass GIT leere Verzeichnisse nicht versioniert, ich möchte euch aber unter Umständen schon ein paar Ordner vorbereiten, in die später einmal etwas rein kommen wird.


### Virtual Hosts / vhosts

Normalerweise sind Dateien im Apache Webserver nur über den Pfad erreichbar:

```
localhost/datei.php --> /var/www/html/datei.php
domain.tld/datei.php --> /var/www/html/datei.php

localhost/php/index.php --> /var/www/html/php/index.php
localhost/mvc/index.php --> /var/www/html/mvc/index.php
localhost/project3/index.php --> /var/www/html/project3/index.php
```

Sollen Dateien über eine Subdomain erreichbar sein, müssen virtuelle Hosts konfiguriert werden:

```
php.localhost/index.php --> /var/www/html/php/index.php
mvc.localhost/index.php --> /var/www/html/mvc/index.php
project3.localhost/index.php --> /var/www/html/project3/index.php

localhost/php_workspace/Aufgabe/1/index.php --> /Application/MAMP/htdocs/php_workspace/Aufgabe/1/index.php
php_workspace.localhost/Aufgabe/1/index.php --> /Application/MAMP/htdocs/php_workspace/Aufgabe/1/index.php
```
