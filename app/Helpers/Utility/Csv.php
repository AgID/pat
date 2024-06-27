<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Utility;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Csv
{
    /**
     * @var string
     */
    public string $terminator = "\n";

    /**
     * @var string
     */
    public mixed $separator = ",";

    /**
     * @var string
     */
    public mixed $enclosed = '"';

    /**
     * @var string
     */
    public mixed $escaped = "\\";

    /**
     * @var string
     */
    public string $mimeType = "text/csv";

    /**
     * @var string
     */
    private string $filename;

    /**
     * @var array
     */
    protected array $headerColumns = [];

    /**
     * @var array
     */
    protected array $rows = [];

    /**
     * @param string $filename  Nome del file CSV
     * @param string $separator Separatore da utilizzare
     * @param string $enclosed  Carattere di enclose
     * @param string $escaped   Carattere di escape
     */
    public function __construct(string $filename = "file", string $separator = ',', string $enclosed = '"', string $escaped = "\\")
    {
        $this->filename = $filename . '.csv';
        $this->separator = $separator;
        $this->enclosed = $enclosed;
        $this->escaped = $escaped;
    }

    /**
     * @description Restituisce il nome del file
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @description Funzione che genera l'header della tabella
     * @param bool|array $columns Variabile di controllo
     * @return array|void
     */
    public function headerColumns(bool|array $columns = false)
    {
        if ($columns) {
            if ($this->colCheckMismatch($columns)) {
                echo 'Unable to add header columns - row column mismatch!';
            } else {
                if (is_array($columns)) {
                    foreach ($columns as $column) {
                        $this->headerColumns[] = $column;
                    }
                } else {
                    $this->headerColumns[0] = $columns;
                }
            }
        } else {
            return $this->headerColumns;
        }
    }

    /**
     * @description Funzione che aggiunge una riga al file CSV
     * @param mixed $row Riga da aggiungere
     * @return void
     */
    public function addRow(mixed $row): void
    {
        if ($this->colCheckMismatch($row)) {
            echo 'Unable to insert row into CSV - header column mismatch!';
        } else {
            if (is_array($row)) {
                $this->rows[] = $row;
            } else {
                $this->rows[][0] = $row;
            }
        }

    }

    /**
     * @description Funzione che controlla se c'è un Mismatch sul numero di colonne della riga e dell'header
     * @param array $row Riga su cui effettuare il controllo
     * @return bool
     */
    private function colCheckMismatch(array $row): bool
    {
        if ($this->headerColumns) {
            if (count($this->headerColumns) != count($row)) return true;
        } elseif ($this->rows) {
            if (count($this->rows[0]) != count($row)) return true;
        }

        return false;
    }

    /**
     * @description Funzione che fa l'export del CSV generato
     * @param bool $toString Controllo
     * @return string|void
     */
    public function export(bool $toString = false)
    {
        $schemaInsert = '';
        $out = '';

        if ($this->headerColumns) {
            foreach ($this->headerColumns as $columnNumber => $column) {
                $l = $this->enclosed . str_replace($this->enclosed, $this->escaped . $this->enclosed,
                        stripslashes($column)) . $this->enclosed;
                $schemaInsert .= $l;
                $schemaInsert .= $this->separator;
            }

            $out .= trim(substr($schemaInsert, 0, -1));
            $out .= $this->terminator;
        }

        if ($this->rows) {
            foreach ($this->rows as $row) {
                foreach ($row as $column => $value) {

                    $schemaInsert = '';

                    if (isset($value)) {

                        if ($this->enclosed == '') {
                            $schemaInsert .= $value;
                        } else {
                            $schemaInsert .= $this->enclosed .
                                str_replace($this->enclosed, $this->escaped . $this->enclosed, $value) .
                                $this->enclosed;
                        }
                    } else {
                        $schemaInsert .= '';
                    }

                    if ($column < count($row) - 1) {
                        $schemaInsert .= $this->separator;
                    }

                    $out .= $schemaInsert;

                }

                $out .= $this->terminator;

            }

        }


        if (!$toString) {
            header('Content-Type: text/html; charset=' . CHARSET);
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Content-Length: " . strlen($out));
            header("Content-type: " . $this->mimeType);
            header("Content-Disposition: attachment; filename=" . $this->filename);
            echo $out;
        } else {
            return $out;
        }
    }

    /**
     * @description Funzione per leggere un file CSV
     * @param string $file    File CSV da leggere
     * @param bool   $headers Controllo sull'header
     * @return false|void
     */
    public function readCSV(string $file, bool $headers = false)
    {
        if (version_compare(phpversion(), '5.3.0', '<=')) {
            $this->readCSVOldPHP($file, $headers);
            return false;
        }

        $row = 0;

        if (($handle = fopen($file, "r")) !== false) {

            while (($data = fgetcsv($handle, 0, $this->separator, $this->enclosed, $this->escaped)) !== false) {

                $num = count($data);

                if ($row == 0) {
                    $firstRowColumns = $num;

                    if ($headers) {
                        $headerRow = array();
                        for ($c = 0; $c < $num; $c++) {
                            $headerRow[$c] = $data[$c];
                        }
                        $this->headerColumns($headerRow);
                    } else {
                        for ($c = 0; $c < $num; $c++) {
                            $this->rows[$row][$c] = $data[$c];
                        }
                    }

                } else {
                    if ($num != $firstRowColumns) {
                        echo 'The number of columns in row ' . $row . ' does not match the number of columns in row 0';
                        fclose($handle);
                        return false;
                    }

                    if ($headers) {

                        for ($c = 0; $c < $num; $c++) {
                            $this->rows[$row - 1][$c] = $data[$c];
                        }
                    } else {

                        for ($c = 0; $c < $num; $c++) {
                            $this->rows[$row][$c] = $data[$c];
                        }
                    }
                }

                $row++;

            }
            fclose($handle);
        }

    }

    /**
     * @description Funzione per leggere un file CSV con php vecchio
     * @param string $file    Nome del file CSV da leggere
     * @param bool   $headers Controllo sull'header
     * @return bool|null
     */
    public function readCSVOldPHP(string $file, bool $headers = false)
    {
        $row = 0;
        if (($handle = fopen($file, "r")) !== false) {

            while (($data = fgetcsv($handle, 0, $this->separator, $this->enclosed)) !== false) {

                $num = count($data);

                if ($row == 0) {
                    $firstRowColumns = $num;

                    if ($headers) {
                        $headerRow = array();
                        for ($c = 0; $c < $num; $c++) {
                            $headerRow[$c] = $data[$c];
                        }
                        $this->headerColumns($headerRow);
                    } else {
                        for ($c = 0; $c < $num; $c++) {
                            $this->rows[$row][$c] = $data[$c];
                        }
                    }

                } else {
                    if ($num != $firstRowColumns) {
                        echo 'The number of columns in row ' . $row . ' does not match the number of columns in row 0';
                        fclose($handle);
                        return false;
                    }

                    if ($headers) {
                        //loop through columns
                        for ($c = 0; $c < $num; $c++) {
                            $this->rows[$row - 1][$c] = $data[$c];
                        }
                    } else {
                        //loop through columns
                        for ($c = 0; $c < $num; $c++) {
                            $this->rows[$row][$c] = $data[$c];
                        }
                    }
                }

                $row++;

            }
            fclose($handle);
        }
    }

    /**
     * @description Restituisce la riga specificata
     * @param mixed $row Riga che si vuole ottenere
     * @return mixed
     */
    public function getRow(mixed $row): mixed
    {
        return $this->rows[$row];
    }

    /**
     * @description Funzione che ritorna l'elemento nella posizione data dalla riga e dalla colonna specificate
     * @param mixed $row Riga dove cercare l'elemento
     * @param mixed $col Colonna dove cercare l'elemento
     * @return mixed
     */
    public function getRowCol(mixed $row, mixed $col): mixed
    {
        return $this->rows[$row][$col];
    }

    /**
     * @description Restituisce l'indice dell'header specificato
     * @param mixed $header Header di cui si vuole l'indice
     * @return false|int|string
     */
    public function getHeaderIndex(mixed $header): bool|int|string
    {

        if ($this->headerColumns) {
            return array_search($header, $this->headerColumns);
        }

        return false;
    }

    /**
     * @param $col
     * @param $data
     * @return array|false
     */
    public function getRowIndex($col, $data): bool|array
    {
        if ($col && $this->rows) {

            $matchingRows = array();
            foreach ($this->rows as $row => $rowData) {
                foreach ($rowData as $column => $value) {
                    if ($value == $data) $matchingRows[] = $row;
                }
            }

            return $matchingRows;
        }

        return false;
    }

    /**
     * @description Restituisce il numero delle righe
     * @return int
     */
    public function totalRows(): int
    {
        return count($this->rows);
    }

    /**
     * @description Restituisce il numero delle colonne
     * @return int
     */
    public function totalCols(): int
    {
        if ($this->headerColumns) {
            return count($this->headerColumns);
        } elseif ($this->rows) {
            return count($this->rows[0]);
        }
        return 0;
    }
}
