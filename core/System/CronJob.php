<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Maer\Config\Config;

class CronJob
{
    /**
     * @var null
     * @Description nome del package ad eseguire
     */
    private $jobs = null;

    /**
     * @var array
     * @Description Lista delle classi da lanciare
     */
    private $listJobs = [];

    /**
     * @var string[]
     * @Description Espressioni regolari per la validazione degli orari del Crontab
     */
    private $regex = [
        'hour' => '/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/',
        'minute' => '/^[0-5][0-9]$/',
        'days' => '/^[0-9,]+$/',
        'minute_reg' => '/^[0-6]{1}[0-9]{1}+$/',
    ];

    /**
     * @var bool
     * @Description varibile che valorizza la scrittura dei log in caso ndi errore
     */
    private $log;

    /**
     * @Description  Il parametro passato abilità la scrittura del cron al verificarsi di un errore nei parametri di
     *  ingresso nei metodi
     */
    public function __construct($log = false)
    {
        $this->log = (bool)$log;

    }

    /**
     * @Description Analizzo ed eseguo il package
     * @return void
     */
    public function run()
    {
        if (!empty($this->listJobs) && is_array($this->listJobs)) {
            foreach ($this->listJobs as $job) {
                call_user_func_array([$job, '__construct'], []);
            }

        }

    }


    /**
     * @param $hourMinute
     * @return void
     * @Description Esegue lo script ogni Domenica ad una determinata ora. Default 00:00
     */
    public function sundays($hourMinute = '00:00', $package = false)
    {
        if (!$this->validate($hourMinute, $this->regex['hour'])) {
            $error = sprintf('sundays "%s" is not valid', (string)$hourMinute);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if (!$this->validatePackage($package)) {
            $error = sprintf('class "%s" is not valid', (string)$package);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if ((string)$this->getCurrentDay() === (string)'Sunday' && (string)$this->getCurrentHourAndMinute() === (string)$hourMinute) {
            $this->listJobs[] = $package;
        }

        return $this;
    }

    /**
     * @param $hourMinute
     * @return void
     * @Description Esegue lo script ogni lunedì ad una determinata ora. Default 00:00
     */
    public function mondays($hourMinute = '00:00', $package = false)
    {
        if (!$this->validate($hourMinute, $this->regex['hour'])) {
            $error = sprintf('mondays "%s" is not valid', (string)$hourMinute);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if (!$this->validatePackage($package)) {
            $error = sprintf('class "%s" is not valid', (string)$package);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if ((string)$this->getCurrentDay() === (string)'Monday' && (string)$this->getCurrentHourAndMinute() === (string)$hourMinute) {
            $this->listJobs[] = $package;
        }

        return $this;
    }

    /**
     * @param $hourMinute
     * @return void
     * @Description Esegue lo script ogni martedì ad una determinata ora. Default 00:00
     */
    public function tuesdays($hourMinute = '00:00', $package = false)
    {
        if (!$this->validate($hourMinute, $this->regex['hour'])) {
            $error = sprintf('tuesdays "%s" is not valid', (string)$hourMinute);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if (!$this->validatePackage($package)) {
            $error = sprintf('class "%s" is not valid', (string)$package);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if ((string)$this->getCurrentDay() === (string)'Tuesday' && (string)$this->getCurrentHourAndMinute() === (string)$hourMinute) {
            $this->listJobs[] = $package;
        }

        return $this;
    }

    /**
     * @param $hourMinute
     * @return void
     * @Description Esegue lo script ogni mercoledì ad una determinata ora. Default 00:00
     */
    public function wednesdays($hourMinute = '00:00', $package = false)
    {
        if (!$this->validate($hourMinute, $this->regex['hour'])) {
            $error = sprintf('wednesdays "%s" is not valid', (string)$hourMinute);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if (!$this->validatePackage($package)) {
            $error = sprintf('class "%s" is not valid', (string)$package);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if ((string)$this->getCurrentDay() === (string)'Wednesday' && (string)$this->getCurrentHourAndMinute() === (string)$hourMinute) {
            $this->listJobs[] = $package;
        }

        return $this;
    }

    /**
     * @param $hourMinute
     * @return void
     * @Description Esegue lo script ogni giovedì ad una determinata ora. Default 00:00
     */
    public function thursdays($hourMinute = '00:00', $package = false)
    {
        if (!$this->validate($hourMinute, $this->regex['hour'])) {
            $error = sprintf('thursdays "%s" is not valid', (string)$hourMinute);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if (!$this->validatePackage($package)) {
            $error = sprintf('class "%s" is not valid', (string)$package);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if ((string)$this->getCurrentDay() === (string)'Thursday' && (string)$this->getCurrentHourAndMinute() === (string)$hourMinute) {
            $this->listJobs[] = $package;
        }

        return $this;
    }


    /**
     * @param $hourMinute
     * @return void
     * @Description Esegue lo script ogni venerdì ad una determinata ora. Default 00:00
     */
    public function fridays($hourMinute = '00:00', $package = false)
    {
        if (!$this->validate($hourMinute, $this->regex['hour'])) {
            $error = sprintf('fridays "%s" is not valid', (string)$hourMinute);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if (!$this->validatePackage($package)) {
            $error = sprintf('class "%s" is not valid', (string)$package);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if ((string)$this->getCurrentDay() === (string)'Friday' && (string)$this->getCurrentHourAndMinute() === (string)$hourMinute) {
            $this->listJobs[] = $package;
        }

        return $this;
    }

    /**
     * @param $hourMinute
     * @param $package
     * @return $this
     * @Description Esegue lo script ogni Sabato ad una determinata ora. Default 00:00
     */
    public function saturdays($hourMinute = '00:00', $package = false)
    {
        if (empty($hourMinute) && strlen($hourMinute) != 7 && $this->validate($hourMinute, $this->regex['hour'])) {
            $error = sprintf('saturdays "%s" is not valid', (string)$hourMinute);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if (!$this->validatePackage($package)) {
            $error = sprintf('class "%s" is not valid', (string)$package);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if ((string)$this->getCurrentDay() === (string)'Saturday' && (string)$this->getCurrentHourAndMinute() === (string)$hourMinute) {
            $this->listJobs[] = $package;
        }

        return $this;
    }

    /**
     * @param $hourMinute
     * @param $package
     * @return $this
     * @Description Esegue lo script tutti i giorni ad una determinata ora. Default 00:00
     */
    public function daily($hourMinute = '00:00', $package = false)
    {
        if (!$this->validate($hourMinute, $this->regex['hour'])) {
            $error = sprintf('daily "%s" is not valid', (string)$hourMinute);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if (!$this->validatePackage($package)) {
            $error = sprintf('class "%s" is not valid', (string)$package);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if ((string)$this->getCurrentHourAndMinute() === (string)$hourMinute) {
            $this->listJobs[] = $package;
        }

        return $this;
    }

    /**
     * @return void
     * @Description Esegue lo script all'inizio di ogni ora e al minuto specificato.
     */
    public function hourly($minute = '00', $package = false)
    {
        if (!$this->validate($minute, $this->regex['minute'])) {
            $error = sprintf('hourly "%s" is not valid', (string)$minute);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if (!$this->validatePackage($package)) {
            $error = sprintf('class "%s" is not valid', (string)$package);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if ((string)$this->getCurrentMinute() === (string)$minute) {
            $this->listJobs[] = $package;
        }

        return $this;
    }

    /**
     * @param $hourMinute
     * @param $package
     * @return $this
     * @Description Lo script viene eseguito il primo giorno del mese ad un orario prestabilito. Default 00:00
     */
    public function monthly($hourMinute = '00:00', $package = false)
    {
        if (!$this->validate($hourMinute, $this->regex['hour'])) {
            $error = sprintf('monthly "%s" is not valid', (string)$hourMinute);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if (!$this->validatePackage($package)) {
            $error = sprintf('class "%s" is not valid', (string)$package);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if ((string)$this->getCurretMonth() === (string)'01' && (string)$this->getCurrentHourAndMinute() === (string)$hourMinute) {
            $this->listJobs[] = $package;
        }

        return $this;
    }

    /**
     * @param $days
     * @param $package
     * @return $this
     * @Description Lo script viene eseguito tutti i mesi e con dei giorni prestabiliti
     */
    public function months($months = null, $package = false)
    {
        if (!$this->validateDayNumbers($months, $this->regex['days'])) {
            $error = sprintf('months "%s" is not valid', (string)implode(',', $days));
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if (!$this->validatePackage($package)) {
            $error = sprintf('class "%s" is not valid', (string)$package);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if (in_array($this->getCurrentNumberDay(), $days) && (string)$this->getCurrentMinute() === '00') {
            $this->listJobs[] = $package;
        }

        return $this;
    }

    /**
     * @param $days
     * @param $package
     * @return $this
     * @Description Lo script viene eseguito tutti i giorni con degli orari prestabiliti
     */
    public function days($days = [], $package = false)
    {
        if (!$this->validateDayNumbers($days, $this->regex['days'])) {
            $error = sprintf('days "%s" is not valid', (string)implode(',', $days));
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if (!$this->validatePackage($package)) {
            $error = sprintf('class "%s" is not valid', (string)$package);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if (in_array($this->getCurrentNumberDay(), $days) && (string)$this->getCurrentMinute() === '00') {
            $this->listJobs[] = $package;
        }

        return $this;
    }

    /**
     * @param $minutes
     * @param $package
     * @return $this
     * @Description Lo script viene eseguito ai minuti settati nellarco di un ora.
     */
    public function minutes($minutes = [], $package = false)
    {
        if (!$this->validateMinutes($minutes, $this->regex['minute_reg'])) {
            $error = sprintf('minutes "%s" is not valid', (string)implode(',', $minutes));
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if (!$this->validatePackage($package)) {
            $error = sprintf('class "%s" is not valid', (string)$package);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        if (in_array($this->getCurrentMinute(), $minutes)) {
            $this->listJobs[] = $package;
        }

        return $this;
    }

    /**
     * @return void
     * @Description Setta ogni minuto la chiamata al package
     */
    public function everyMinute($package = null)
    {
        if (!$this->validatePackage($package)) {
            $error = sprintf('class "%s" is not valid', (string)$package);
            $this->writeLog('Cron Job - ' . $error);
            throw new \InvalidArgumentException($error);
        }

        // Per evitare che lo script venga chiamato ogni secondo se il Crontab settato per ogni secondo.
        $minutes = [];
        for ($i = 0; $i <= 59; $i++) {
            $minute = strlen($i) == 1 ? (int)'0' . $i : $i;
            $minutes[] = $minute . ':00';
        }

        if (in_array($this->getCurrentMinuteAndSeconds(), $minutes)) {
            $this->listJobs[] = $package;
        }

        return $this;
    }

    /**
     * @return array
     * @Description Ritorna vari tipi di date
     */
    public static function getFullDate()
    {
        return [
            date('Y-m-d H:i:s', CRONTAB_NOW),
            date('Y', CRONTAB_NOW),
            date('l', CRONTAB_NOW),
            date('H:i', CRONTAB_NOW),
            date('d', CRONTAB_NOW),
            date('m', CRONTAB_NOW),
        ];
    }

    /**
     * @param $hourMinute
     * @param $regex
     * @return bool
     * @Description Validatore tramire Regex
     */
    private function validate($hourMinute, $regex)
    {
        return (bool)preg_match($regex, strtolower($hourMinute));
    }

    /**
     * @param $days
     * @param $regex
     * @return bool
     * @Description Validatore tramire Regex
     */
    public function validateDayNumbers($days, $regex)
    {
        if (!empty($days) && is_array($days)) {

            $results = filter_var($days, FILTER_VALIDATE_INT, [
                'flags' => FILTER_REQUIRE_ARRAY,
                'options' => [
                    'min_range' => 1
                ]
            ]);

            foreach ($results as $result) {
                if ($result === false) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param $hours
     * @param $regex
     * @return bool
     * @Description Validatore tramire Regex delle ore
     */
    public function validateHours($hours, $regex)
    {
        if (is_array($hours)) {
            foreach ($hours as $hour) {
                if (!empty($hour) && (bool)preg_match($regex, (string)$hour)) {
                    return false;
                }
            }
        } else {
            if (!empty($hour) && (bool)preg_match($regex, (string)$hour)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $minutes
     * @param $regex
     * @return bool
     * @Description Validatore tramire Regex delle ore
     */
    public function validateMinutes($minutes, $regex)
    {
        if (is_array($minutes)) {
            foreach ($minutes as $minute) {
                if (empty($minute) && ((bool)preg_match($regex, $minute) !== true) && ((int)$minute > 59)) {
                    echo "Ok";
                    return false;
                }
            }
        } else {
            if (empty($minute) && ((bool)preg_match($regex, $minute) !== true) && ((int)$minute > 59)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $package
     * @return bool
     * @Description Verifica se il parametro di ingresso è un oggetto
     */
    private function validatePackage($package = null)
    {
        return (bool)is_object($package);
    }

    /**
     * @return string
     * @Description Ritorna la string del giorno corrente in inglese
     */
    public function getCurrentDay()
    {
        return ucfirst(date('l', CRONTAB_NOW));
    }

    /**
     * @return string
     * @Description Ritorna l'ora ed il minuto corrente
     */
    public function getCurrentHourAndMinute()
    {
        return date('H:i', CRONTAB_NOW);
    }

    /**
     * @return string
     * @Description Ritorna il minuto corrente
     */
    public function getCurrentMinute()
    {
        return date('i', CRONTAB_NOW);
    }

    /**
     * @return string
     * @Description Ritorna il minuto corrente
     */
    public function getCurrentMinuteAndSeconds()
    {
        return date('i:s', CRONTAB_NOW);
    }

    /**
     * @return string
     * @Description Ritorna il mese corrente
     */
    public function getCurretMonth()
    {
        return date('m', CRONTAB_NOW);
    }

    /**
     * @return void
     */
    public function getCurrentNumberDay()
    {
        return date('d', CRONTAB_NOW);
    }

    /**
     * @param $notify
     * @param $log
     * @return void
     * @throws \Exception
     */
    protected function writeLog($notify, $log)
    {
        $config = new Config();
        $config->load(APP_PATH . 'Config/app.php');

        if (is_array($log) || is_object($log)) {
            $log = json_encode($log);
        }

        $data = date('Y-m-d');
        $hours = date('H:i:s');
        $pathFile = APP_PATH . $config->get('path_log') . DIRECTORY_SEPARATOR . '[' . $notify . '] - ' . $data . '.php';
        $log = '[CRON JOB  - ' . $data . ' ' . $hours . ']' . ': ' . $log;
        file_put_contents($pathFile, $log . PHP_EOL, FILE_APPEND);
    }
}