<?php
//Tu już nie ładujemy konfiguracji - sam widok nie będzie już punktem wejścia do aplikacji.
//Wszystkie żądania idą do kontrolera, a kontroler wywołuje skrypt widoku.
?>
 <!DOCTYPE html>
<html>
<head>
    <title>Incident Response</title>
    <link rel="stylesheet" href="css/style.css">
</head>
    
<body>
    <h1> IncResp system </h1>
    
    <div>
        <a href="<?php print(_APP_ROOT); ?>/app/security/logout.php" class="button">Logout</a>
    </div>
    
    <table>
    <tr>
        <th>Title</th>
        <th>Date</th>
        <th>Team</th>
        <th>Solved</th>
    </tr>
    </table>

</body>
    
</html>