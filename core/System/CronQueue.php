<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/*
- register() : che registra la classe
- cron('* * * * *') : Esegui l'attività in base a una pianificazione cron personalizzata
- everyMinute : Esegui l'attività ogni minuto
- everyFiveMinutes : Eseguire l'attività ogni cinque minuti
- hourly(): Eseguire l'attività ogni ora
- minutes([5,10,5]) : Lo script viene eseguito ai minuti settati nell'array nell'arco di un ora. in questo caso al minuto 5, 10 e 45
- daily(10:00) : Esegui l'attività ogni giorno alle 13:00, dove le 13:00 può essere impostata: Default 0:00
- sundays(00:00) :  Esegue lo script ogni Domenica ad una determinata ora. Dove le 00:00 può essere impostata. Default 00:00
- mondays(00:00) : Esegue lo script ogni lunedì ad una determinata ora. Dove le 00:00 può essere impostata. Default 00:00
- tuesdays(00:00) : Esegue lo script ogni martedì ad una determinata ora. Dove le 00:00 può essere impostata. Default 00:00
- wednesdays(00:00) : Esegue lo script ogni mercoledì ad una determinata ora. Dove le 00:00 può essere impostata. Default 00:00
- thursdays(00:00) : Esegue lo script ogni giovedì ad una determinata ora. Dove le 00:00 può essere impostata. Default 00:00
- fridays(00:00) : Esegue lo script ogni venerdì ad una determinata ora. Dove le 00:00 può essere impostata. Default 00:00
- saturdays(00:00) : Esegue lo script ogni Sabato ad una determinata ora. Dove le 00:00 può essere impostata. Default 00:00
- monthly(00:00) : Lo script viene eseguito il primo giorno del mese ad un orario prestabilito. Default 00:00
- months([1,5,12,25]) : Lo script viene eseguito tutti i mesi a dei giorni prestabiliti, l'orario di default è 00:00
- always() : Lo script viene eseguito ad ogni chiamata cli
- exec: esegue tutte le classi registrate ed impostate per la data ed ora. questa classe esegue il metodo handle di delle classi inizializzate

Esempio:
    CronQueue::register(news className)->everyMinute();
    CronQueue::register(news className)->always();
    CronQueue::register(news className)->cron('* * * * *');
    CronQueue::register(news className)->minutes(5,10,5]);

    CronQueue::exec();
*/

class CronQueue
{
    /**
     * @var null
     */
    private static $instance = null;

    /**
     * @var array
     */
    private $jobs = [];

    /**
     * @var int
     */
    private $maxChildren;

    /**
     * @var int
     */
    private $minFreeMemory;

    /**
     * @param int $maxChildren
     * @param int $minFreeMemory
     */
    public function __construct(int $maxChildren = 100, int $minFreeMemory = 1048576)
    {
        $this->maxChildren = $maxChildren;
        $this->minFreeMemory = $minFreeMemory;
    }

    /**
     * @param int $maxChildren
     * @return CronQueue
     */
    public static function getInstance(int $maxChildren = 1000): CronQueue
    {
        if (self::$instance === null) {
            self::$instance = new CronQueue($maxChildren);
        }
        return self::$instance;
    }

    /**
     * @param $class
     * @return $this
     */
    public function register($class): CronQueue
    {
        $this->jobs[] = [
            'class' => $class,
            'schedule' => ''
        ];
        return $this;
    }

    /**
     * @param string $expression
     * @return $this
     */
    public function cron(string $expression): CronQueue
    {
        $this->jobs[count($this->jobs) - 1]['schedule'] = $expression;
        return $this;
    }

    /**
     * @return $this
     */
    public function everyMinute(): CronQueue
    {
        return $this->cron('* * * * *');
    }

    /**
     * @return $this
     */
    public function everyFiveMinutes(): CronQueue
    {
        return $this->cron('*/5 * * * *');
    }

    /**
     * @return $this
     */
    public function hourly(): CronQueue
    {
        return $this->cron('0 * * * *');
    }

    /**
     * @param array $minutes
     * @return $this
     */
    public function minutes(array $minutes): CronQueue
    {
        $expression = implode(',', $minutes) . ' * * * *';
        return $this->cron($expression);
    }

    /**
     * @param string $time
     * @return $this
     */
    public function daily(string $time = '00:00'): CronQueue
    {
        $hourMinute = explode(':', $time);
        $expression = $hourMinute[1] . ' ' . $hourMinute[0] . ' * * *';
        return $this->cron($expression);
    }

    /**
     * Programma il lavoro per essere eseguito a giorni specifici della settimana.
     *
     * @param string $time Il tempo in cui il lavoro deve essere eseguito (formato: 'HH:mm'). Default: '00:00'.
     * @param int|null $dayOfWeek Il giorno della settimana in cui il lavoro deve essere eseguito (0 = Domenica, 1 = Lunedì, ecc.). Default: null.
     * @return CronQueue L'istanza corrente della classe per consentire il method chaining.
     */
    public function daysOfWeek(string $time = '00:00', int $dayOfWeek = null): CronQueue
    {
        if ($dayOfWeek === null) {
            throw new \InvalidArgumentException("The dayOfWeek parameter is required.");
        }

        $hourMinute = explode(':', $time);
        $expression = $hourMinute[1] . ' ' . $hourMinute[0] . ' * * ' . $dayOfWeek;
        return $this->cron($expression);
    }

    /**
     * @param string $time
     * @return $this
     */
    public function sundays(string $time = '00:00'): CronQueue
    {
        return $this->daysOfWeek($time, 0);
    }

    /**
     * @param string $time
     * @return $this
     */
    public function mondays(string $time = '00:00'): CronQueue
    {
        return $this->daysOfWeek($time, 1);
    }

    /**
     * @param string $time
     * @return $this
     */
    public function tuesdays(string $time = '00:00'): CronQueue
    {
        return $this->daysOfWeek($time, 2);
    }

    /**
     * @param string $time
     * @return $this
     */
    public function wednesdays(string $time = '00:00'): CronQueue
    {
        return $this->daysOfWeek($time, 3);
    }

    /**
     * @param string $time
     * @return $this
     */
    public function thursdays(string $time = '00:00'): CronQueue
    {
        return $this->daysOfWeek($time, 4);
    }

    /**
     * @param string $time
     * @return $this
     */
    public function fridays(string $time = '00:00'): CronQueue
    {
        return $this->daysOfWeek($time, 5);
    }

    /**
     * @param string $time
     * @return $this
     */
    public function saturdays(string $time = '00:00'): CronQueue
    {
        return $this->daysOfWeek($time, 6);
    }

    /**
     * @param string $time
     * @return $this
     */
    public function monthly(string $time = '00:00'): CronQueue
    {
        $hourMinute = explode(':', $time);
        $expression = $hourMinute[1] . ' ' . $hourMinute[0] . ' 1 * *';
        return $this->cron($expression);
    }

    /**
     * @param array $days
     * @param string $time
     * @return $this
     */
    public function months(array $days, string $time = '00:00'): CronQueue
    {
        $hourMinute = explode(':', $time);
        $expression = $hourMinute[1] . ' ' . $hourMinute[0] . ' ' . implode(',', $days) . ' * *';
        return $this->cron($expression);
    }

    /**
     * @return $this
     */
    public function always(): CronQueue
    {
        $this->jobs[count($this->jobs) - 1]['schedule'] = 'ALWAYS';
        return $this;
    }

    /**
     * @return void
     */
    public function exec()
    {
        if (strtoupper(substr(php_uname('s'), 0, 3)) === 'WIN') {
            // Se il sistema operativo è Windows, esegui le attività senza utilizzare code
            foreach ($this->jobs as $job) {
                if ($this->shouldRun($job['schedule'])) {
                    $job['class']->handle();
                }
            }
        } else {
            $childProcesses = [];

            foreach ($this->jobs as $job) {
                if ($this->shouldRun($job['schedule'])) {
                    $className = get_class($job['class']);

                    if (array_key_exists($className, $childProcesses)) {
                        // La classe è già stata aggiunta come processo figlio, quindi continua con il prossimo job
                        continue;
                    }

                    if (count($childProcesses) >= $this->maxChildren || $this->getFreeMemory() < $this->minFreeMemory) {
                        $pid = pcntl_wait($status);
                        unset($childProcesses[array_search($pid, $childProcesses)]);
                    }

                    $pid = pcntl_fork();

                    if ($pid == -1) {
                        throw new \RuntimeException('Impossibile eseguire il fork del processo.');
                    } elseif ($pid == 0) {
                        $job['class']->handle();
                        exit(0);
                    } else {
                        $childProcesses[$className] = $pid;
                    }
                }
            }

            foreach ($childProcesses as $pid) {
                pcntl_waitpid($pid, $status);
            }
        }

    }

    /**
     * Ottengo la quantità di memoria libera del server in bytes.
     *
     * @return int La quantità di memoria libera in bytes.
     */
    private function getFreeMemory(): int
    {
        if (exec("which free")) {
            $freeMemory = intval(shell_exec("free | awk 'FNR == 3 {print $4}'"));
        } else {
            $totalMemory = $this->convertToBytes(ini_get('memory_limit'));
            $usedMemory = memory_get_usage();

            $freeMemory = $totalMemory - $usedMemory;
        }

        return $freeMemory;
    }


    /**
     * @param string $totalMemory
     * @return int
     */
    private function convertToBytes(string $totalMemory) : int
    {
        $totalMemory = trim($totalMemory);
        $lastChar = strtolower($totalMemory[strlen($totalMemory) - 1]);
        $value = (int)$totalMemory;
        switch ($lastChar) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }
        return $value;
    }

    /**
     * Determina se un lavoro dovrebbe essere eseguito in base alla sua programmazione.
     *
     * @param string $schedule La programmazione del lavoro in formato cron o "ALWAYS".
     * @return bool True se il lavoro deve essere eseguito, altrimenti false.
     */
    private function shouldRun(string $schedule): bool
    {
        if ($schedule === 'ALWAYS') {
            return true;
        }

        try {
            return CronExpression::isDue($schedule);
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }
}