<!DOCTYPE html>
<html lang="nl">
    <head>
        <meta charset="UTF-8">
        <meta name=""viewport" content="width=device-width, initial-scale=1.0">
        <title>Camping De Groene Weide</title>

        <link rel="stylesheet" href="homestyle.css">
    </head>
    <body>
        <div class="container">
            <h1>Camping De Groene Weide</h1>
            <p>Welkom bij onze gezellige camping midden in de natuur.</p>
            <button class="btn" onclick="boeknu()"> <a href="{{ route('boeken') }}"></a>
                Boek nu

            </button>
            <div class="cancel">    
                <p>Wilt u uw boeking annuleren?</p>
                <a href="{{ route('annuleren') }}" onclick="annuleerboeking()">Klik hier</a>           
            </div>  
        </div>      
        <script src="annulerenscript.js"></script>
    </body>
</html>