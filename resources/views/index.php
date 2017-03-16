<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>AngryLadder API</title>
</head>
<body>

    <h1>AngryLadder API</h1>

    <p>Backend for AngryLadder service</p>

    <h2>Endpoints</h2>

    <ul>
        <li><a href="<?php echo url('/v1/matches'); ?>">/v1/matches</a></li>
        <li><a href="<?php echo url('/v1/matches/1'); ?>">/v1/games/{id}</a></li>
        <li><a href="<?php echo url('/v1/matches'); ?>">/v1/games (post)</a></li>
        <li><a href="<?php echo url('/v1/matches/1'); ?>">/v1/games/{id} (put)</a></li>


        <li><a href="<?php echo url('/v1/players/'); ?>">/v1/players</a></li>
        <li><a href="<?php echo url('/v1/players/1'); ?>">/v1/players/{id}</a></li>
        <li><a href="<?php echo url('/v1/players/1/stats'); ?>">/v1/players/{id}</a></li>
        <li><a href="<?php echo url('/v1/players'); ?>">/v1/players (post)</a></li>
        <li><a href="<?php echo url('/v1/players/1'); ?>">/v1/players/{id} (put)</a></li>

        <li><a href="<?php echo url('/v1/top'); ?>">/v1/top</a></li>
        <li><a href="<?php echo url('/v1/top/mostgames'); ?>">/v1/top/mostgames</a></li>

        <li><a href="<?php echo url('/v1/stats'); ?>">/v1/stats</a></li>
    </ul>

</body>
</html>

