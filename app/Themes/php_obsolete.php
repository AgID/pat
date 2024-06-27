<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<html>
<head>
    <style>

        body {
            background-color: #212336;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .box {
            background-color: #ffffff;
            width: 80%;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 2px 2px 8px #212336;
            margin: 0 auto;
        }

        h1 {
            color: #212336;
            font-size: 20px;
            margin: 0;
            line-height:2rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
<div class="box">
    <h1>
        Per garantire il corretto funzionamento del software, si consiglia vivamente di aggiornare l'interprete PHP alla versione uguale o superiore a 8.0.0. La versione attualmente installata, {php_version}, potrebbe non essere sufficiente per soddisfare tutti i requisiti del software.
    </h1>
</div>
</body>
</html>
