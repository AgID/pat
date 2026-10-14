<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ops!</title>
    <link href="/assets/frontend/v1/css/titillium_web.css?v=11" rel="stylesheet">
</head>
<style>
    * {
        margin:0;
        padding:0;
        box-sizing: border-box;
    }
    body {
        background:#06c;
        color:#fff;
        font-family: 'Titillium Web', sans-serif;
    }
    a {
        color:#fff;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
    h1 {
        font-size:10rem;
        line-height: 80%;
        margin-bottom: 2rem;
    }
    p {
        font-size:2rem;
        margin-bottom:2rem;
    }
    .testata {
        padding:1rem;
    }
    .container {
        display: flex;
        align-items: center;
        justify-content: center;
        width:100%;
        height:80vh;
    }
    .box {
        text-align: center;
        padding: 1rem;
    }
    .logo {
        width:10rem;
    }
    ul {
        list-style-type: none;
    }
</style>
<body>
<div class="container">
    <div class="box">
        <h1><?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

 echo (string) $text ?></h1>
        <p class="title"><?php echo (string) $heading ?></p>
        <p><?php echo (string) $message ?></p>
        
        <?php
       /* $tempUrl = "/";
        $tmpSplit = preg_split('/.html\?/' , $_SERVER['REQUEST_URI']);
        if(!empty($tmpSplit)){
            $tempUrl = baseUrl().substr($tmpSplit[0], 1).'.html';
        }*/
        ?>
        <a href="/" title="Torna indietro">
            &#8592; Torna alla home
        </a>
    </div>
</div>
</body>
</html>
