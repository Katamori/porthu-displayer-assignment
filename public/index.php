<head>
    <?php
        require realpath('..') . '/app/database.php';

        $sqlitePath = realpath("..") . "/database/" .DB_NAME. ".sqlite3";
        $dbHandler = new DatabaseHandler($sqlitePath);

        $channelList = $dbHandler->getChannels();
        $dayList = $dbHandler->getAvailableDays();
    ?>
</head>

<body>
    <?php if (empty($channelList) && empty($dayList)): ?>
        <div>
            <p>The database is devoid of any program to display.</p>
        </div>
    <?php else: ?>
        <form action="index.php" method="get">
        <label for="channels">Channel:</label>
        <select name="channel" id="channels">
            <?php foreach ($channelList as $channel): ?>
                <option <?php echo 'value="'.$channel['id'].'"' ?> >
                    <?php echo $channel['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="days">Available days:</label>
        <select name="day" id="days">
            <?php foreach ($dayList as $day): ?>
                <option <?php echo 'value="'.$day.'"' ?> >
                    <?php echo $day ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="submit" value="Submit">
    </form>
    <?php endif; ?>
</body>
<?php $dbHandler->close(); ?>