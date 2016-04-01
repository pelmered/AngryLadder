<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>AngryLadder API</title>
</head>
<body>

    <h1>AngryLadder API</h1>

    <p>Backend for angryladder service</p>

    <h2>Endpoints</h2>

    <ul>
        <li><a href="<?php echo url('/v1/games'); ?>">/v1/games</a></li>
        <li><a href="<?php echo url('/v1/games/1'); ?>">/v1/games/{id}</a></li>
        <li><a href="<?php echo url('/v1/games'); ?>">/v1/games (post)</a></li>
        <li><a href="<?php echo url('/v1/games/1'); ?>">/v1/games/{id} (put)</a></li>


        <li><a href="<?php echo url('/v1/players/'); ?>">/v1/players</a></li>
        <li><a href="<?php echo url('/v1/players/1'); ?>">/v1/players/{id}</a></li>
        <li><a href="<?php echo url('/v1/players'); ?>">/v1/players (post)</a></li>
        <li><a href="<?php echo url('/v1/players/1'); ?>">/v1/players/{id} (put)</a></li>

        <li><a href="<?php echo url('/v1/players/top'); ?>">/v1/players/top</a></li>
        <li><a href="<?php echo url('/v1/players/top/mostgames'); ?>">/v1/players/top/mostgames</a></li>
    </ul>

</body>
</html>

