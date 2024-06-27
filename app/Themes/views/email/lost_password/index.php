<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

?>

<?php echo $header ?>
<table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;"
       width="600" bgcolor="#ffffff">
    <tr>
        <td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
            <div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:600px;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation"
                       style="background:#ffffff;background-color:#ffffff;width:100%;">
                    <tbody>
                    <tr>
                        <td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;">
                            <!--[if mso | IE]>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="" style="vertical-align:top;width:600px;"><![endif]-->
                            <div class="mj-column-per-100 mj-outlook-group-fix"
                                 style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                                <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                       style="vertical-align:top;" width="100%">
                                    <tbody>
                                    <tr>
                                        <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                            <div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:150%;text-align:left;color:#000000;">
                                                Salve <strong><?php echo $email; ?></strong> <br/>
                                                Nel giorno <?php echo date('d-m-Y') ?> alle
                                                ore <?php echo date('H:i') ?>,
                                                hai richiesto una procedura per il recupero della password.
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                            <div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:150%;text-align:left;color:#000000;">
                                                Tale procedura ha una validit&agrave; di 24 ore, a partire dalla data di
                                                richiesta di recupero password.<br/>
                                                Alla scadenza sar&agrave; necessario avviare una nuova procedura di
                                                recupero.
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                            <div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:150%;text-align:left;color:#000000;">
                                                Purtroppo il sistema non &egrave; in grado di recuperare la password
                                                poich&egrave;
                                                quest'ultima viene crittografata in modo irreversibile.<br/>
                                                <strong>Tuttavia puoi generare una nuova password cliccando sul link
                                                    sottostante:</strong>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="left" vertical-align="middle"
                                            style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                            <table border="0" cellpadding="0" cellspacing="0" role="presentation"
                                                   style="border-collapse:separate;line-height:100%;">
                                                <tbody>
                                                <tr>
                                                    <td align="center" bgcolor="#7fc026" role="presentation"
                                                        style="border:none;border-radius:3px;cursor:auto;mso-padding-alt:10px 25px;background:#7fc026;"
                                                        valign="middle">
                                                        <a href="<?php echo $link; ?>"
                                                           style="display:inline-block;background:#7fc026;color:#ffffff;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;font-weight:normal;line-height:120%;margin:0;text-decoration:none;text-transform:none;padding:10px 25px;mso-padding-alt:0px;border-radius:3px;"
                                                           target="_blank"> CLICCA QUI </a>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                            <div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:150%;text-align:left;color:#000000;">
                                                Se non riesci a cliccare sul link indicato sopra, copia ed incolla sul
                                                tuo browser
                                                il testo sottostante per avviare la procedura del recupero della
                                                password:
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                            <div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:150%;text-align:left;color:#000000;">
                                                <i>
                                                    <?php echo $link; ?>
                                                </i>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                            <div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:150%;text-align:left;color:#000000;">
                                                Ignora questa e-mail se non sei stato tu a richiedere la procedura di
                                                recupero password.
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--[if mso | IE]></td></tr></table><![endif]-->
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <!--[if mso | IE]></td></tr></table>


<?php echo $footer ?>
