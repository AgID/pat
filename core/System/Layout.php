<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

/** @noinspection ALL */

namespace System;

use Exception;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Class Template
 * @package Helpers
 */
class Layout
{
    /**
     * @var array
     */
    static array $execMethods = [
        'compileComment',
        'compileCodePhp',
        'compilePreserved',
        'compileBlock',
        'compilePHP',
        'compileYield',
        'compileOpeningStatements',
        'compileClosingStatements',
        'compileElse',
        'compileEscapedEchos',
        'compileXssEchos',
        'compileEscapedXssEchos',
        'compileTrimEchos',
        'compileUpperEchos',
        'compileLowerEchos',
        'compileUpperFirstEchos',
        'compileLengthEchos',
        'compileCountArrayEchos',
        'compileTitleEchos',
        'compileWordCountEchos',
        'compileNlTwoBrEchos',
        'compileJoinEchos',
        'compileRepeatEchos',
        'compileDateEchos',
        'compileCurrencySimpleEchos',
        'compileCurrencyEchos',
        'compileEchos',
        'compileUnless',
        'compileEndUnless',
        'compileEndCodePhp',
        'compileContinue',
        'compileBreak',
        'compileClosingSwitch',
        'compileOpeningSwitch',
        'compileSwitchCase'
        // 'compileWidget'
    ];
    /**
     * @var array
     */
    static array $blocks = [];

    /**
     * @var string
     */
    static string $cachePath = APP_PATH . 'Cache/template/';

    /**
     * @var bool
     */
    static bool $cacheEnabled = true;

    /**
     * @var
     */
    static $file;

    /**
     * @var string
     */
    static string $ext = '.blade.php';

    /**
     * @var string
     */
    static string $extCache = '.php';

    /**
     * @var string Suffisso del file che elenca i sorgenti da cui un compilato dipende.
     */
    static string $extDeps = '.dep';

    /**
     * @var ?string
     */
    static ?string $theme = null;

    /**
     * @var array
     */
    static array $sections = [];

    /**
     * @var array
     */
    static array $buffer = [];

    /**
     * @var int Rete di sicurezza, non piu' il meccanismo primario di invalidazione.
     *
     * Era 10 secondi: ogni template in uso veniva ricompilato - riletto, appiattito, passato per una
     * decina di preg_replace e riscritto su disco - una volta ogni dieci secondi, per sempre, anche
     * se nessuno aveva toccato niente. Adesso la cache si invalida sul filemtime dei sorgenti
     * (vedi isStale()), e questo TTL serve solo a coprire il caso in cui l'elenco delle dipendenze
     * fosse incompleto o corrotto: una ricompilazione al giorno, non ogni dieci secondi.
     */
    static int $timeCache = 86400;

    /**
     * @var array Sorgenti attraversati dall'ultima compilazione, usati per l'invalidazione.
     *
     * Popolato da includeFiles() mentre appiattisce l'albero di {% extends %} e {% include %},
     * azzerato da cache() prima di ogni compilazione. Le chiavi sono i percorsi assoluti.
     */
    static array $compileDeps = [];

    /**
     * @var array
     */
    static array $dirs = [];

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return null|string|string[]
     */
    public static function compileUpperEchos(array|string $code): array|string|null
    {
        return preg_replace('/\{\{upper:\s*(.+?)\s*}}/', "<?php echo mb_strtoupper($1, CHARSET) ?>", $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return null|string|string[]
     */
    public static function compileLowerEchos(array|string $code): array|string|null
    {
        return preg_replace('/\{\{lower:\s*(.+?)\s*}}/', "<?php echo mb_strtolower($1, CHARSET) ?>", $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return null|string|string[]
     */
    public static function compileUpperFirstEchos(array|string $code): array|string|null
    {
        return preg_replace('/\{\{upper_first:\s*(.+?)\s*}}/', "<?php echo ucfirst($1) ?>", $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return null|string|string[]
     */
    public static function compileLengthEchos(array|string $code): array|string|null
    {
        return preg_replace('/\{\{length:\s*(.+?)\s*}}/', "<?php echo strlen($1) ?>", $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    public static function compileCountArrayEchos(array|string $code): array|string|null
    {
        return preg_replace('/\{\{count:\s*(.+?)\s*}}/', "<?php echo count($1) ?>", $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    public static function compileTitleEchos(array|string $code): array|string|null
    {
        return preg_replace('/\{\{title:\s*(.+?)\s*}}/', "<?php echo mb_convert_case($1, MB_CASE_TITLE, CHARSET) ?>", $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    public static function compileWordCountEchos(array|string $code): array|string|null
    {
        return preg_replace('/\{\{count_words:\s*(.+?)\s*}}/', "<?php echo str_word_count($1) ?>", $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    public static function compileNlTwoBrEchos(array|string $code): array|string|null
    {
        return preg_replace('/\{\{nl2br:\s*(.+?)\s*}}/', "<?php echo nl2br($1) ?>", $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    public static function compileJoinEchos(array|string $code): array|string|null
    {
        return preg_replace('/\{\{join(.*?):\s*(.*?)\s*}}/', "<?php echo implode($1, $2) ?>", $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    public static function compileRepeatEchos(array|string $code): array|string|null
    {
        return preg_replace('/\{\{repeat(.*?):\s*(.*?)\s*}}/', "<?php echo str_repeat($2,$1) ?>", $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    private static function compileCurrencyEchos(array|string $code): array|string|null
    {
        return preg_replace('/\{\{currency(.*?):\s*(.*?)\s*}}/', "<?php echo self::buildCurrencyEchos($1,$2); ?>", $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    private static function compileCurrencySimpleEchos(array|string $code): array|string|null
    {
        return preg_replace('/\{\{currency:\s*(.+?)\s*}}/', "<?php echo self::buildCurrencyEchos($1); ?>", $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|string[]|null
     */
    private static function compileDateEchos(array|string $code): array|string|null
    {
        return preg_replace('/\{\{date(.*?):\s*(.*?)\s*}}/', "<?php echo self::buildDateEchos($1,$2); ?>", $code);
    }

    /**
     * @return string
     */
    private static function buildDateEchos(): string
    {
        $data = '';
        $args = func_get_args();
        $format = 'unix';
        $formatDate = 'Y-m-d H:i:s';
        $accept = ['unix', 'date'];

        if (!empty($args) && is_array($args) && (int)count($args) === 2) {

            $parts = explode('|', $args[0]);

            if (!empty($parts)) {

                if (!empty($parts[1]) && in_array($parts[1], $accept)) {
                    $format = $parts[1];
                }

                if (!empty($parts[0])) {
                    $formatDate = str_replace('.', ':', $parts[0]);
                }

                $time = ($format !== 'unix') ? strtotime($args[1]) : $args[1];
                $data = date($formatDate, $time);
            }
        }

        return $data;
    }

    /**
     * @return array|string|null
     */
    private static function buildCurrencyEchos(): array|string|null
    {
        $args = func_get_args();
        $data = null;
        $value = null;
        $decimals = 2;
        $decimalSeparator = ".";
        $thousandsSeparator = ",";
        $symbol = ',';
        $escapingXss = null;

        if (is_array($args) && count($args) === 2) {

            $directive = trim($args[0]);
            $value = trim((string)$args[1]);

            if (strlen($value) === 0) {
                return $data;
            }

            $pattern = '/([a-zA-Z0-9_]*=[0-9a-zA-Z-\w,.@#%&-_\'\"`(){}<>\[\];^~*+=\/]*)/';
            preg_match_all($pattern, $directive, $match, PREG_SET_ORDER);

            if (is_array($match) && count($match)) {
                foreach ($match as $m) {
                    if (!empty($m[0])) {

                        $setting = explode('=', $m[0]);

                        if (is_array($setting) && count($setting) === 2) {
                            if ($setting[0] === 'decimals' && ctype_digit($setting[1])) {
                                $decimals = (int)trim($setting[1]);
                            }

                            if ($setting[0] === 'decimal_separator' && in_array($setting[1], [',', '.'])) {
                                $decimalSeparator = trim($setting[1]);
                            }

                            if ($setting[0] === 'thousands_separator' && in_array($setting[1], [',', '.'])) {
                                $thousandsSeparator = trim($setting[1]);
                            }

                            if ($setting[0] === 'filter' && in_array($setting[1], ['escape', 'xss', 'escape_xss'])) {
                                $escapingXss = $setting[1];
                            }
                        }
                    }
                }
            }
        } else if (is_array($args) && count($args) === 1) {
            $value = (string)trim($args[0]);
        }

        if ($value !== null) {
            $getComma = (int)strpos($value, ',', 1);
            $getPoint = (int)strpos($value, '.', 1);

            if ($getComma >= 1 && $getPoint >= 1) {

                if ($getPoint < $getComma) {

                    $value = preg_replace('/\./u', '', $value);
                    $value = preg_replace('/,/u', '.', $value);
                } else {

                    $value = preg_replace('/,/u', '', $value);
                    $value = preg_replace('/\./u', '.', $value);
                }
            } else if ($getComma >= 1 && $getPoint == 0) {
                $symbol = ',';
            } else if ($getPoint >= 1 && $getComma == 0 && !floatval($value)) {
                $symbol = '.';
            }

            $countSymbol = (int)strpos($value, $symbol);
            $found = 0;

            if ($countSymbol >= 1 && $symbol === ',') {
                for ($i = 0; $i < strlen((string)$value); $i++) {
                    $tmpString = '';
                    if ($value[$i] == $symbol) {

                        if ((int)$found === (int)$countSymbol) {
                            $tmpString = '.';
                        }
                    } else {
                        $found++;
                        $tmpString = $value[$i];
                    }
                    $data .= $tmpString;
                }
            } else {
                $data = number_format($value, $decimals, $decimalSeparator, $thousandsSeparator);
            }
        }

        // Filtri sicurezza
        if ($escapingXss !== null) {
            if ($escapingXss === 'xss') {
                $data = escapeXss($data, true, false);
            } elseif ($escapingXss === 'escape') {
                $data = htmlEscape($data);
            } elseif ($escapingXss === 'escape_xss') {
                $data = escapeXss($data);
            }

            $data = convertEncodeQuotes($data);
        }

        return $data;
    }

    /**
     * @param $file
     * @param array $data
     * @param null  $theme
     * @param bool  $overwrite
     * @param array $dirs
     * @return void
     * @throws Exception
     */
    public static function view($file, array $data = [], $theme = null, bool $overwrite = false, array $dirs = []): void
    {
        helper('html');

        if ($overwrite) {

            self::$theme = $theme;
            self::$dirs = $dirs;
        } else {

            if ($theme !== null) {

                self::$theme = $theme;
            } else {

                self::$theme = config('theme', null, 'app');
            }

            self::$theme = APP_PATH . 'Themes' . DIRECTORY_SEPARATOR . self::$theme . DIRECTORY_SEPARATOR;
        }

        $cachedFile = self::cache($file);
        extract($data, EXTR_SKIP);
        require($cachedFile);
    }

    /**
     * Dice se un template va ricompilato, confrontando la data dei sorgenti con quella del compilato.
     *
     * Sostituisce l'invalidazione a TTL: prima bastava che il compilato avesse piu' di
     * self::$timeCache secondi - dieci - perche' venisse rifatto, indipendentemente dal fatto che
     * qualcuno avesse toccato un sorgente.
     *
     * Non basta guardare il template richiesto: compileCode() lavora sull'albero gia' appiattito da
     * includeFiles(), quindi il compilato dipende anche da ogni {% extends %} e {% include %},
     * ricorsivamente. I loro percorsi sono nel file affiancato scritto al momento della
     * compilazione; se manca - compilato da una versione precedente, o cancellato - si ricompila.
     *
     * @param string $cachedFile Percorso del compilato
     * @param string $depFile Percorso dell'elenco dei sorgenti
     * @return bool
     */
    private static function isStale(string $cachedFile, string $depFile): bool
    {
        if (!file_exists($cachedFile) || !file_exists($depFile)) {
            return true;
        }

        $compiledAt = filemtime($cachedFile);

        // Rete di sicurezza: copre un elenco delle dipendenze incompleto o non piu' veritiero.
        if (($compiledAt + self::$timeCache) <= time()) {
            return true;
        }

        $sources = @file($depFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($sources === false) {
            return true;
        }

        foreach ($sources as $source) {

            // Un sorgente sparito e' un cambiamento come un altro: si ricompila, cosi' l'errore
            // viene a galla subito invece di restare nascosto dietro un compilato vecchio.
            // Il confronto e' ">=" e non ">" per via della granularita' al secondo di filemtime:
            // un sorgente salvato nello stesso secondo in cui il template veniva compilato avrebbe
            // la stessa data del compilato e con ">" passerebbe inosservato fino alla scadenza del
            // TTL, cioe' un giorno. Con ">=" quel salvataggio provoca qualche ricompilazione in
            // piu' dentro quel secondo, e si ferma da sola non appena il secondo avanza.
            if (!file_exists($source) || filemtime($source) >= $compiledAt) {
                return true;
            }
        }

        return false;
    }

    /**
     * Scrive un file di cache in modo atomico: file temporaneo con nome irripetibile, poi rename.
     *
     * Estratta perche' ora i file scritti per ogni compilazione sono due, il compilato e l'elenco
     * delle sue dipendenze. Il rename e' atomico sullo stesso filesystem, quindi un processo che
     * legge mentre un altro compila vede sempre una versione intera, mai un file a meta'.
     *
     * @param string $path
     * @param string $content
     * @return void
     */
    private static function writeAtomic(string $path, string $content): void
    {
        $tmpFile = $path . '.' . getmypid() . '.' . uniqid('', true) . '.tmp';
        $result = @file_put_contents($tmpFile, $content, LOCK_EX);

        if ($result === false) {
            error_log("Impossibile scrivere il file di cache temporaneo: " . $tmpFile);

            return;
        }

        if (!@rename($tmpFile, $path)) {
            @unlink($tmpFile);
            error_log("Impossibile rinominare il file di cache: " . $path);
        }
    }

    /**
     * @param $file
     * @return string
     * @throws Exception
     */
    public static function cache($file): string
    {

        // if (!file_exists(self::$cachePath)) {
        //     mkdir(self::$cachePath, 0744);
        // }

        if (!file_exists(self::$cachePath)) {
            if (!@mkdir(self::$cachePath, 0744, true)) {
                error_log("Impossibile creare la directory di cache: " . self::$cachePath);
                // throw new Exception("Impossibile creare la directory di cache");
            }
        }

        // Verifica se la directory è scrivibile
        if (!is_writable(self::$cachePath)) {
            error_log("La directory di cache non è scrivibile: " . self::$cachePath);
            // throw new Exception("La directory di cache non è scrivibile");
        }

        // Il nome del file non basta a identificare il template: lo stesso nome relativo esiste in
        // piu' temi (layout/master, layout/partials/header e footer stanno sia in Themes/admin sia
        // in Themes/frontend/v1). Finche' la cache scadeva ogni 10 secondi la collisione si
        // auto-guariva - il richiedente successivo ricompilava dal proprio tema - ma con
        // l'invalidazione sul filemtime resterebbe appiccicata finche' non cambia il sorgente.
        // Tema e directory di ricerca entrano quindi nella chiave.
        $cacheKey = substr(md5(self::$theme . '|' . implode(',', self::$dirs)), 0, 8);

        $cachedFile = self::$cachePath
            . str_replace(['/', self::$ext], ['_', ''], $file)
            . '.' . $cacheKey
            . self::$extCache;

        $depFile = $cachedFile . self::$extDeps;

        // In sviluppo si ricompila sempre, come prima. Con la cache disattivata pure: la condizione
        // precedente ("!$cacheEnabled && !file_exists") faceva l'opposto, se il file c'era gia' lo
        // riusava. Inerte - $cacheEnabled e' true e non viene mai riassegnato - ma sbagliata.
        $mustCompile = (!self::$cacheEnabled || ENVIRONMENT == 'development')
            ? true
            : self::isStale($cachedFile, $depFile);

        if ($mustCompile) {

            self::$compileDeps = [];

            $code = self::includeFiles($file);
            $code = self::compileCode($code);
            $content = '<?php class_exists(\'' . __CLASS__ . '\') or exit; ?>' . $code;

            // Prima le dipendenze, poi il compilato: se il secondo write fallisce restiamo con un
            // elenco piu' recente del compilato, che al giro dopo provoca una ricompilazione. Il
            // contrario lascerebbe un compilato senza elenco, che isStale() tratta comunque come da
            // rifare - ma passando per un giro di I/O in piu'.
            self::writeAtomic($depFile, implode(PHP_EOL, array_keys(self::$compileDeps)));
            self::writeAtomic($cachedFile, $content);

            // Elimino gli spazi bianchi.
            // $code = preg_replace('/\s\s+/', ' ', $code);
        }


        return $cachedFile;
    }

    /**
     * Cancella la cache prodotta dal template engine
     * @return void
     */
    public static function clearCache(): void
    {
        foreach (glob(self::$cachePath . '*') as $file) {
            unlink($file);
        }
    }

    /**
     * @param $code
     * @return string|string[]|null
     * @throws Exception
     */
    protected static function compileCode($code): array|string|null
    {

        foreach (self::$execMethods as $method) {

            // $code = trim(preg_replace('/\s\s+/', ' ', $code));
            $code = self::$method($code);
        }

        $code = preg_replace(['/<@{/', '/}@>/'], ['{{', '}}'], $code);

        return self::trimCompiledEdges((string)$code);
    }

    protected static function trimCompiledEdges(string $code): string
    {
        // Con PREG_SPLIT_DELIM_CAPTURE le posizioni pari sono testo letterale
        // e quelle dispari i tag PHP catturati dal delimitatore.
        $parts = preg_split('/(<\?php.*?\?>)/s', $code, -1, PREG_SPLIT_DELIM_CAPTURE);

        if ($parts === false) {
            return $code;
        }

        $lastIndex = count($parts) - 1;
        $lastLiteral = $lastIndex - ($lastIndex % 2);

        for ($i = 0; $i <= $lastLiteral; $i += 2) {

            $parts[$i] = ltrim($parts[$i]);

            if ($parts[$i] !== '') {
                break;
            }
        }

        // Coda: stessa logica a partire dall'ultima parte letterale.
        for ($i = $lastLiteral; $i >= 0; $i -= 2) {

            $parts[$i] = rtrim($parts[$i]);

            if ($parts[$i] !== '') {
                break;
            }
        }

        return implode('', $parts);
    }

    /**
     * @param $file
     * @return string|string[]|null
     */
    protected static function includeFiles($file): array|string|null
    {

        $filePath = self::$theme . $file . self::$ext;
        $code = null;
    
        if (!file_exists($filePath) && self::$dirs !== null) {

            foreach (self::$dirs as $dir) {

                $filePath = APP_PATH . trim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file . self::$ext;

                if (file_exists($filePath)) {
                    $code = @file_get_contents($filePath);
                    self::$compileDeps[$filePath] = true;
                }
            }
        } else {
            $code = @file_get_contents($filePath);
            self::$compileDeps[$filePath] = true;
        }

        preg_match_all('/{% ?(extends|include) ?\'?(.*?)\'? ?%}/i', (string)$code, $matches, PREG_SET_ORDER);

        foreach ($matches as $value) {

            $code = str_replace($value[0], self::includeFiles($value[2]), (string)$code);
        }

        $code = preg_replace('/{% ?(extends|include) ?\'?(.*?)\'? ?%}/i', '', (string)$code);

        return $code;
    }

    /**
     * @param $code
     * @return null|string|string[]
     */
    protected static function compilePHP($code): array|string|null
    {
        return preg_replace('~\{%\s*(.+?)\s*%}~is', '<?php $1 ?>', $code);
    }

    /**
     * @param $code
     * @return array|string|null
     */
    protected static function compilePreserved($code): array|string|null
    {
        $pattern = '/@(\{\{(.+?)}})/';

        return preg_replace($pattern, '<@{$2}@>', $code);
    }

    /**
     * @param string $var Descrizione parametro
     * @return string
     */
    protected static function untouch(string $var): string
    {
        return '\{\{' . $var . '\}\}';
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return null|string|string[]
     */
    protected static function compileEchos(array|string $code): array|string|null
    {
        return preg_replace('~\{{\s*(.+?)\s*}}~is', '<?php echo $1 ?>', $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return null|string|string[]
     */
    protected static function compileEscapedEchos(array|string $code): array|string|null
    {
        return preg_replace('/\{\{e:\s*(.+?)\s*}}/', "<?php echo htmlEscape(convertEncodeQuotes($1)) ?>", $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return null|string|string[]
     */
    protected static function compileXssEchos(array|string $code): array|string|null
    {
        return preg_replace('/\{\{xss:\s*(.+?)\s*}}/', "<?php echo escapeXss(convertEncodeQuotes($1),true,false) ?>", $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return null|string|string[]
     */
    public static function compileEscapedXssEchos(array|string $code): array|string|null
    {
        return preg_replace('/\{\{escape_xss:\s*(.+?)\s*}}/', "<?php echo escapeXss(convertEncodeQuotes($1)) ?>", $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return null|string|string[]
     */
    public static function compileTrimEchos(array|string $code): array|string|null
    {
        return preg_replace('/\{\{trim:\s*(.+?)\s*}}/', "<?php echo trim($1) ?>", $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string
     */
    protected static function compileBlock(array|string $code): array|string
    {
        preg_match_all('/{% ?block ?(.*?) ?%}(.*?){% ?endblock ?%}/is', $code, $matches, PREG_SET_ORDER);
        foreach ($matches as $value) {
            if (!array_key_exists($value[1], self::$blocks)) self::$blocks[$value[1]] = '';
            if (strpos($value[2], '@parent') === false) {
                self::$blocks[$value[1]] = $value[2];
            } else {
                self::$blocks[$value[1]] = str_replace('@parent', self::$blocks[$value[1]], $value[2]);
            }

            $code = str_replace($value[0], '', $code);
        }

        return $code;
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    protected static function compileYield(array|string $code): array|string|null
    {

        foreach (self::$blocks as $block => $value) {
            $code = preg_replace('/@yield\(' . $block . '\)/', $value, $code);
        }
        // $code = preg_replace('/(\s*)@(yield)(\s*)/i', '', $code);
        return preg_replace('/(\s*)@yield\((.*?)\)(\s*)/i', '', $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    protected static function compileComment(array|string $code): array|string|null
    {
        $returnPattern = '/\{\{--((?:(?!\{\{--)[\s\S])*?)--}}/';
        $code = preg_replace($returnPattern, "<?php /* $1 */ ?>", $code);
        $pattern = '/\{\{--(.+?)(--}})?\n/';
        $code = preg_replace($pattern, "<?php // $1 ?>", $code);

        return $code;
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    protected static function compileOpeningStatements(array|string $code): array|string|null
    {
        $pattern = '/(\s*)@(if|elseif|foreach|for|while)(\s*\(.*\))/';
        return preg_replace($pattern, '$1<?php $2$3: ?>', $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    protected static function compileOpeningSwitch(array|string $code): array|string|null
    {
        $pattern = '/(\s*)@(switch)(\s*.*\))/';
        return preg_replace($pattern, '$1<?php $2$3{ ?>', $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    protected static function compileElse(array|string $code): array|string|null
    {
        $pattern = '/(\s*)@(else)(\s*)/';
        return preg_replace($pattern, '$1<?php $2: ?>$3', $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    protected static function compileClosingStatements(array|string $code): array|string|null
    {
        $pattern = '/(\s*)@(endif|endforeach|endfor|endwhile)(\s*)/';
        return preg_replace($pattern, '$1<?php $2; ?>$3', $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    protected static function compileClosingSwitch(array|string $code): array|string|null
    {
        $pattern = '/(\s*)@(endswitch)(\s*)/';
        return preg_replace($pattern, '$1<?php } ?>$3', $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    protected static function compileSwitchCase(array|string $code): array|string|null
    {
        $pattern = '/(\s*)@(case)(\s.*\')/';
        return preg_replace($pattern, '$1<?php $2$3: ?>', $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    protected static function compileUnless(array|string $code): array|string|null
    {
        $pattern = '/(\s*)@unless(\s*\(.*\))/';
        return preg_replace($pattern, '$1<?php if ( ! ($2)): ?>', $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    protected static function compileEndUnless(array|string $code): array|string|null
    {
        $pattern = '/(\s*)@(endunless|endisset|endempty)/';
        return preg_replace($pattern, '<?php endif; ?>', $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    protected static function compileCodePhp(array|string $code): array|string|null
    {
        return preg_replace('/@php/', '<?php', $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    protected static function compileEndCodePhp(array|string $code): array|string|null
    {
        return preg_replace('/@endphp/', '?>', $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    protected static function compileContinue(array|string $code): array|string|null
    {
        $pattern = '/(\s*)@(continue)(\s*)/';

        return preg_replace($pattern, '$1<?php $2; ?>$3', $code);
    }

    /**
     * @param array|string $code Elemento su cui fare il replace
     * @return array|string|null
     */
    protected static function compileBreak(array|string $code): array|string|null
    {
        $pattern = '/(\s*)@(break)(\s*)/';
        return preg_replace($pattern, '$1<?php $2; ?>$3', $code);
    }
}
