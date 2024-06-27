<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>


<div class='alert alert-info'>
    <button data-dismiss='alert' class='close' type='button'>&times;</button>
    <div>
        <h4>
            <span class='icon-info-sign' style='margin-right: 5px;'></span>Informativa - Atti e Documenti di carattere generale ai sensi della Delibera ANAC N. 264/2023
        </h4>
        <a class='text-muted' href='javascript:maggioriInfo("info-bdncp");' id='guide-button-info-bdncp'>Leggi contenuto</a>
        <div id='info-bdncp' style='display: none;'>
            <h5 style="border-bottom: 1px solid #CCC; font-weight: bold;" class="mt-2">Avviso finalizzato ad acquisire le manifestazioni di interesse degli operatori economici in ordine ai lavori di possibile completamento di opere incompiute nonché alla gestione delle stesse</h5>
            <ul>
                <li>
                    Riferimenti normativi
                    <ul>
                        <li>
                            ALLEGATO I.5 al d.lgs. 36/2023 - Elementi per la programmazione dei lavori e dei servizi. Schemi tipo (art. 4, co. 3)
                        </li>
                    </ul>
                </li>
                <li>
                    Contenuto dell'obbligo
                    <ul>
                        <li>
                            Avviso finalizzato ad acquisire le manifestazioni di interesse degli operatori economici in ordine ai lavori di possibile completamento di opere incompiute nonché alla gestione delle stesse - NB: Ove l'avviso è pubblicato nella apposita sezione del portale web del Ministero delle infrastrutture e dei trasporti, la pubblicazione in AT è assicurata mediante link al portale MIT
                        </li>
                    </ul>
                </li>
            </ul>

            <h5 style="margin-top: 1rem; margin-bottom: 0.5rem; border-bottom: 1px solid #CCC; font-weight: bold;">Comunicazione circa la mancata redazione del programma triennale</h5>
            <ul>
                <li>
                    Riferimenti normativi
                    <ul>
                        <li>
                            ALLEGATO I.5 al d.lgs. 36/2023 - Elementi per la programmazione dei lavori e dei servizi. Schemi tipo (art. 5, co. 8; art. 7, co. 4)
                        </li>
                    </ul>
                </li>
                <li>
                    Contenuto dell'obbligo
                    <ul>
                        <li>
                            Comunicazione circa la mancata redazione del programma triennale dei lavori pubblici, per assenza di lavori
                        </li>
                        <li>
                            Comunicazione circa la mancata redazione del programma triennale degli acquisti di forniture e servizi, per assenza di acquisti di forniture e servizi.
                        </li>
                    </ul>
                </li>
            </ul>

            <h5 style="margin-top: 1rem; margin-bottom: 0.5rem; border-bottom: 1px solid #CCC; font-weight: bold;">Atti recanti norme, criteri oggettivi per il funzionamento del sistema di qualificazione, l'eventuale aggiornamento periodico dello stesso e durata, criteri soggettivi (requisiti relativi alle capacità economiche, finanziarie, tecniche e professionali) per l'iscrizione al sistema</h5>
            <ul>
                <li>
                    Riferimenti normativi
                    <ul>
                        <li>
                            Art. 168, d.lgs. 36/2023 - Procedure di gara con sistemi di qualificazione
                        </li>
                    </ul>
                </li>
                <li>
                    Contenuto dell'obbligo
                    <ul>
                        <li>
                            Atti recanti norme, criteri oggettivi per il funzionamento del sistema di qualificazione, l'eventuale aggiornamento periodico dello stesso e durata, criteri soggettivi (requisiti relativi alle capacità economiche, finanziarie, tecniche e professionali) per l'iscrizione al sistema.
                        </li>
                    </ul>
                </li>
            </ul>

            <h5 style="margin-top: 1rem; margin-bottom: 0.5rem; border-bottom: 1px solid #CCC; font-weight: bold;">Atti eventualmente adottati recanti l'elencazione delle condotte che costituiscono gravi illeciti professionali agli effetti degli artt. 95, co. 1, lettera e) e 98 (cause di esclusione dalla gara per gravi illeciti professionali)</h5>
            <ul>
                <li>
                    Riferimenti normativi
                    <ul>
                        <li>
                            Art. 169, d.lgs. 36/2023 - Procedure di gara regolamentate - Settori speciali
                        </li>
                    </ul>
                </li>
                <li>
                    Contenuto dell'obbligo
                    <ul>
                        <li>
                            Obbligo applicabile alle imprese pubbliche e ai soggetti titolari di diritti speciali esclusivi
                            <br>
                            Atti eventualmente adottati recanti l'elencazione delle condotte che costituiscono gravi illeciti professionali agli effetti degli artt. 95, co. 1, lettera e) e 98 (cause di esclusione dalla gara per gravi illeciti professionali).
                        </li>
                    </ul>
                </li>
            </ul>

            <h5 style="margin-top: 1rem; margin-bottom: 0.5rem; border-bottom: 1px solid #CCC; font-weight: bold;">Elenco annuale dei progetti d'investimento pubblico finanziati</h5>
            <ul>
                <li>
                    Riferimenti normativi
                    <ul>
                        <li>
                            Art. 11, co. 2-quater, l. n. 3/2003, introdotto dall'art. 41, co. 1, d.l. n. 76/2020 - Dati e informazioni sui progetti di investimento pubblico
                        </li>
                    </ul>
                </li>
                <li>
                    Contenuto dell'obbligo
                    <ul>
                        <li>
                            Obbligo previsto per i soggetti titolari di progetti di investimento pubblico - Elenco annuale dei progetti finanziati, con indicazione del CUP, importo totale del finanziamento, le fonti finanziarie, la data di avvio del progetto e lo stato di attuazione finanziario e procedurale
                        </li>
                    </ul>
                </li>
            </ul>

        </div>
    </div>
</div>

<script>
    function maggioriInfo(elementId) {
        // console.log(elementId)
        let guide = $(`#${elementId}`);
        if (guide.is(':visible')) {
            $(`#guide-button-${elementId}`).text('Leggi contenuto');
        } else {
            $(`#guide-button-${elementId}`).text('Nascondi');
        }
        guide.slideToggle();
    }
</script>