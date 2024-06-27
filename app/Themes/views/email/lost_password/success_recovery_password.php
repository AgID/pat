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
                                                hai completato con successo la procedura di recupero della password.
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                            <div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:150%;text-align:left;color:#000000;">
                                                Se non sei stato tu a completare la procedura del recupero password,
                                                contatta l'amministratore di sistema.
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
