<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geen Toegang - VDL Groep</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <style>
        .forbidden-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f5f5f5;
        }
        .forbidden-box {
            text-align: center;
            background: white;
            padding: 60px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .forbidden-box h1 {
            font-size: 48px;
            color: #e74c3c;
            margin-bottom: 20px;
        }
        .forbidden-box p {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
        }
        .forbidden-box a {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .forbidden-box a:hover {
            background: #764ba2;
        }
    </style>
</head>
<body>
    <div class="forbidden-container">
        <div class="forbidden-box">
            <h1>❌ Geen toegang</h1>
            <p>Je hebt geen toestemming om deze pagina te bekijken.</p>
            <p>Je huidige rol: <strong id="currentRole"></strong></p>
            <a href="index.php">Terug naar homepagina</a>
        </div>
    </div>

    <script>
        document.getElementById('currentRole').textContent = localStorage.getItem('userRole') || 'onbekend';
    </script>
</body>
</html>
