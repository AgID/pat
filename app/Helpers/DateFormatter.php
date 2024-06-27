<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Classe DateFormatter per formattare le date in diversi formati
 *
 * @usage
 * Esempio 1: formattazione di una data in formato data e ora completa
 * $dateString = '2022-03-24 15:30:00';
 * $formattedDate = DateFormatter::formatDate($dateString);
 * echo "Data formattata: " . $formattedDate . "\n";
 * Output: Data formattata: giovedì 24 marzo 2022 alle ore 15:30:00
 *
 * Esempio 2: formattazione di una data in formato solo data
 * $dateString = '2022-03-24 15:30:00';
 * $formattedDate = DateFormatter::formatDate($dateString, DateFormatter::DATE_FORMAT);
 * echo "Data formattata: " . $formattedDate . "\n";
 * Output: Data formattata: 24/03/2022
 *
 * Esempio 3: formattazione di una data in formato data senza anno
 * $dateString = '2022-03-24 15:30:00';
 * $formattedDate = DateFormatter::formatDate($dateString, DateFormatter::DATE_NO_YEAR_FORMAT);
 * echo "Data formattata: " . $formattedDate . "\n";
 * Output: Data formattata: 24/03
 *
 * Esempio 4: formattazione di una data in formato personalizzato
 * $dateString = '2022-03-24 15:30:00';
 * $formattedDate = DateFormatter::formatDate($dateString, DateFormatter::CUSTOM_FORMAT, ".", ":");
 * echo "Data formattata: " . $formattedDate . "\n";
 * Output: Data formattata: 24.03.2022 15:30
 *
 * Esempio 5: formattazione di una data senza specificare il formato
 * $dateString = '2022-03-24 15:30:00';
 * $formattedDate = DateFormatter::formatDate($dateString);
 * echo "Data formattata: " . $formattedDate . "\n";
 * Output: Data formattata: gioved� 24 marzo 2022 alle ore 15:30:00
 *
 **/
class DateFormatter
{
    /**
     * Costante per il formato data e ora completa
     */
    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * Costante per il formato solo data
     */
    const DATE_FORMAT = 'd/m/Y';

    /**
     * Costante per il formato data senza anno
     */
    const DATE_NO_YEAR_FORMAT = 'd/m';

    /**
     * Costante per il formato personalizzato della data
     */
    const CUSTOM_FORMAT = 'd/m/Y H-i';

    /**
     * Costante per il formato data sena ora
     */
    const DATE_FORMAT_NO_HOUR = 'Y-m-d';

    /**
     * Formatta una stringa di data in un formato personalizzato
     *
     * @param string $dateString La stringa di data da formattare
     * @param string $format Il formato di uscita della data (DATETIME_FORMAT, DATE_FORMAT, DATE_NO_YEAR_FORMAT, CUSTOM_FORMAT)
     * @param string $separatorDate Il separatore della data per il formato personalizzato
     * @param string $separatorTime Il separatore dell'ora per il formato personalizzato
     * @return string La stringa formattata in base al formato richiesto
     */
    public static function formatDate(string $dateString, string $format = self::DATETIME_FORMAT, string $separatorDate = "/", string $separatorTime = "-"): string
    {

        $dateTime = \DateTime::createFromFormat(self::DATETIME_FORMAT, $dateString);
        $dayOfWeek = self::getDayOfWeek($dateTime->format('w'));
        $day = $dateTime->format('j');
        $month = self::getMonthName($dateTime->format('n'));
        $year = $dateTime->format('Y');
        $time = $dateTime->format('H:i');

        switch ($format) {
            case self::DATE_FORMAT:
                return $day . $separatorDate . $month . $separatorDate . $year;
            case self::DATE_NO_YEAR_FORMAT:
                return $day . $separatorDate . $month;
            case self::CUSTOM_FORMAT:
                return $day . $separatorDate . $month . $separatorDate . $year . ' ' . $time;
            case self::DATE_FORMAT_NO_HOUR:
                return $dayOfWeek . ' ' . $day . ' ' . $month . ' ' . $year;
            default:
                return $dayOfWeek . ' ' . $day . ' ' . $month . ' ' . $year . ' alle ore ' . $time;
        }
    }

    /**
     * Restituisce il nome del giorno della settimana in base all'indice
     *
     * @param int $dayOfWeek L'indice del giorno della settimana (0 = domenica, 1 = luned�, ..., 6 = sabato)
     * @return string Il nome del giorno della settimana
     */
    private static function getDayOfWeek(int $dayOfWeek): string
    {
        $daysOfWeek = [
            'domenica',
            'luned&igrave;',
            'marted&igrave;',
            'mercoled&igrave;',
            'gioved&igrave;',
            'venerd&igrave;',
            'sabato'
        ];
        return $daysOfWeek[$dayOfWeek];
    }

    /**
     * Restituisce il nome del mese in base all'indice
     *
     * @param int $month L'indice del mese (1 = gennaio, 2 = febbraio, ..., 12 = dicembre)
     * @return string Il nome del mese
     */
    private static function getMonthName(int $month): string
    {
        $months = [
            'gennaio',
            'febbraio',
            'marzo',
            'aprile',
            'maggio',
            'giugno',
            'luglio',
            'agosto',
            'settembre',
            'ottobre',
            'novembre',
            'dicembre'
        ];

        return $months[$month - 1];
    }

}