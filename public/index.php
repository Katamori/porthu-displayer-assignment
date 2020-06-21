<head>
    <?php
        require realpath('..') . '/app/database.php';

        $sqlitePath = realpath("..") . "/database/" .DB_NAME. ".sqlite3";
        $dbHandler = new DatabaseHandler($sqlitePath);

        $channelList = $dbHandler->getChannels();
        $dayList = $dbHandler->getAvailableDays();

        $tableContent = [];

        if (isset($_GET["channel"]) && isset($_GET["day"])) {
            $tableContent = $dbHandler->getViewForChannel($_GET["channel"], $_GET["day"]);
        }
    ?>
</head>

<body>
    <!-- control form -->
    <?php if (empty($channelList) || empty($dayList)): ?>
        <div>
            <p>The database is devoid of any program to display.</p>
        </div>
    <?php else: ?>
        <form action="index.php" method="get">
        <label for="channels">Channel:</label>
        <select name="channel" id="channels">
            <?php foreach ($channelList as $channel): ?>
                <option 
                    <?php echo 'value="'.$channel['id'].'"' ?>  
                    <?php echo isset($_GET["channel"]) && $_GET["channel"] == $channel['id'] ? 'selected="selected"' : '' ?> 
                >
                    <?php echo $channel['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="days">Available days:</label>
        <select name="day" id="days">
            <?php foreach ($dayList as $day): ?>
                <option 
                    <?php echo 'value="'.$day.'"' ?> 
                    <?php echo isset($_GET["day"]) && $_GET["day"] === $day ? 'selected="selected"' : '' ?> 
                >
                    <?php echo $day ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="submit" value="Submit">
    </form>
    <?php endif; ?>

    <!-- display table -->
    <?php if (empty($tableContent)): ?>
        <p>Welcome to my port.hu viewer! Add data to your database or select a channel and a day to display!</p>
    <?php else: ?>
        <table width="100%">
            <tr>
                <th>Start time</th>
                <th>Title</th>
                <th>Short description</th>
                <th>Age restriction</th>
            </tr>
            <?php foreach ($tableContent as $row): ?>
                <tr>
                    <td><?php echo $row['time']; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['short_description']; ?></td>
                    <td><?php echo $row['restriction'];  ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
<?php $dbHandler->close(); ?>