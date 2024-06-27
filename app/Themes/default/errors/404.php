<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404</title>
    <link href="/assets/frontend/v1/css/titillium_web.css?v=11" rel="stylesheet">
    <link href="/assets/admin/css/custom.css" rel="stylesheet">
</head>
<style>
    * {
        margin:0;
        padding:0;
        box-sizing: border-box;
    }
    body {
        background: var(--colore1);
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
    }
    .logo {
        width:10rem;
    }
</style>
<body>
<div class="container">
    <div class="box">
        <h1>404</h1>
        <p class="title"><?php echo $heading ?></p>
        <p><?php echo $message ?></p>
        <a href="/" alt="Torna indietro">&#8592; Homepage</a>
    </div>
</div>
</body>
</html>
