<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Utility;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Cli
{
    protected $registry = [];

    const COLOR_BLACK = '0;30';
    const COLOR_DARK_GRAY = '1;30';
    const COLOR_BLUE = '0;34';
    const COLOR_LIGHT_BLUE = '1;34';
    const COLOR_GREEN = '0;32';
    const COLOR_LIGHT_GREEN = '1;32';
    const COLOR_CYAN = '0;36';
    const COLOR_LIGHT_CYAN = '1;36';
    const COLOR_RED = '0;31';
    const COLOR_LIGHT_RED = '1;31';
    const COLOR_PURPLE = '0;35';
    const COLOR_LIGHT_PURPLE = '';
    const COLOR_BROWN = '1;35';
    const COLOR_YELLOW = '1;33';
    const COLOR_LIGHT_GRAY = '0;37';
    const COLOR_WHITE = '1;37';

    const BG_BLACK = '40';
    const BG_RED = '41';
    const BG_GREEN = '42';
    const BG_YELLO = '43';
    const BG_BLUE = '44';
    const BG_MAGEN = '45';
    const BG_CYAN = '46';
    const BG_LIGHT_GRAY = '47';

    protected $color = [
        'black' => '0;30',
        'dark_gray' => '1;30',
        'blue' => '0;34',
        'light_blue' => '1;34',
        'green' => '0;32',
        'light_green' => '1;32',
        'cyan' => '0;36',
        'light_cyan' => '1;36',
        'red' => '0;31',
        'light_red' => '1;31',
        'purple' => '0;35',
        'light_purple' => '1;35',
        'brown' => '0;33',
        'yellow' => '1;33',
        'light_gray' => '0;37',
        'white' => '1;37',
    ];

    protected $background = [
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'light_gray' => '47',
    ];

    public function __construct()
    {
        // register autoload delle classi
    }

    public function out($message)
    {
        fwrite(STDOUT, $message . PHP_EOL);
    }

    function outFormat(array $format = [], string $text = '')
    {
        $codes = [
            'bold' => 1,
            'italic' => 3,
            'underline' => 4,
            'strikethrough' => 9,
            'black' => 30,
            'red' => 31,
            'green' => 32,
            'yellow' => 33,
            'blue' => 34,
            'magenta' => 35,
            'cyan' => 36,
            'white' => 37,
            'blackbg' => 40,
            'redbg' => 41,
            'greenbg' => 42,
            'yellowbg' => 44,
            'bluebg' => 44,
            'magentabg' => 45,
            'cyanbg' => 46,
            'lightgreybg' => 47
        ];

        $formatMap = array_map(function ($v) use ($codes) {
            return @$codes[$v];
        }, $format);

        echo "\e[" . implode(';', $formatMap) . 'm' . $text . "\e[0m";
    }

    function outFormatLn(array $format = [], string $text = '')
    {
        $this->outFormat($format, $text);
        echo "\r\n";
    }

    public function newLine()
    {
        $this->out("\n");
    }

    public function registerCommand($name, $callable)
    {
        $this->registry[$name] = $callable;
    }

    public function getCommand($command)
    {
        return isset($this->registry[$command]) ? $this->registry[$command] : null;
    }

    public function input()
    {
        if ($this->readlineSupport()) {
            return readline();
        }

        return fgets(STDIN);
    }

    public function beep($num = 1)
    {
        echo str_repeat("\x07", $num);
    }

    private function readlineSupport()
    {
        return extension_loaded('readline');
    }

    private function isWin()
    {
        return 'win' === strtolower(substr(php_uname("s"), 0, 3));
    }

    public function notity($str, $type = 'i'){
        switch ($type) {
            case 'e': //error
                echo "\033[31m$str \033[0m\n";
                break;
            case 's': //success
                echo "\033[32m$str \033[0m\n";
                break;
            case 'w': //warning
                echo "\033[33m$str \033[0m\n";
                break;
            default : //info
                echo "\033[36m$str \033[0m\n";
        }
    }
}