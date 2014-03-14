<?php
$filePath = '/xxx/file.csv';
$csvReader = new CsvReader($filePath);

// Zaczytanie danych z pliku
$csvReader->discoverDelimiter();
$csvReader->setInputEncoding('UTF8');
$csvReader->detectEncodingFromFile();
$csvReader->readColumnNames();
$linesNo = $csvReader->getLineNo();

// Aby za każdym razem czytać plik od początku a nie gdzieś od środka
$csvReader->rewindFile();

// Pobranie kolejnych linii z pliku i zapis do LS-a
for ($lineNo = 0; $lineNo <= $linesNo; $lineNo++) { // W przypadku ignorowanie pierwszego wiersza - zaczynamy od 2giego
    // Jezeli jest potrzebne pominięcie pierwszej linni - wczytujemy ja ale pomijamy w imporcie
    if ($ignoreFirstRow == 1 && $lineNo == 0) {
        $csvReader->readLines();
    } else {
        $csvReader->readLines();
        $line = $csvReader->getLineData();

        // Czasem zdarzają się linie całkiem puste i takich nie chcemy.
        if (!empty($line)) {
            if (is_array($line)) {
                foreach ($line as $k => $v) {
                    $line[$k] = Utils::clearNonUtf8Characters($v);
                }
            } else if (is_string($line)) {
                $line = Utils::clearNonUtf8Characters($line);
            }

            // Zrób coś...
        }
    }
}
