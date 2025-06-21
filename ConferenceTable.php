<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Conferences</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Manage Conferences</h1>
    </header>

    <nav>
        <ul>
            <li><a href="#choose-action">Choose Action</a></li>
        </ul>
    </nav>

    <main>
        <section id="choose-action">
            <h2>What would you like to do?</h2>
            <form method="post" action="">
                <label for="action">Choose an action:</label>
                <select name="action" id="action">
                    <option value="insert">Insert New Conference</option>
                    <option value="search">Search Conference</option>
                    <option value="view">View All Conferences</option>
                    <option value="update">Update Conference</option>
                    <option value="delete">Delete Conference</option>
                </select>
                <input type="submit" name="choose-action" value="Go">
            </form>
        </section>

        <?php
        if (isset($_POST['choose-action']) || isset($_GET['sortColumn'])) {
            $action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
            $sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'ConferenceID';
            $sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'ASC';

            if ($action == 'insert') {
                echo '
                <section id="insert">
                    <h2>Insert New Conference</h2>
                    <form method="post" action="">
                        <label>ConferenceID:</label>
                        <input type="number" name="ConferenceID" required><br>
                        <label>ConferenceName:</label>
                        <input type="text" name="ConferenceName" required><br>
                        <label>ConferenceDate:</label>
                        <input type="date" name="ConferenceDate"><br>
                        <label>Location:</label>
                        <input type="text" name="Location"><br>
                        <input type="submit" name="insert" value="Insert">
                    </form>
                </section>';
            } elseif ($action == 'search') {
                echo '
                <section id="search">
                    <h2>Search Conference</h2>
                    <form method="post" action="">
                        <label>ConferenceID:</label>
                        <input type="number" name="searchConferenceID"><br>
                        <input type="submit" name="search" value="Search">
                    </form>';
            } elseif ($action == 'view') {
                echo '
                <section id="view">
                    <h2>All Conferences</h2>';
                    
                    $sql = "SELECT * FROM Conference ORDER BY $sortColumn $sortOrder";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<table id='conferenceTable'>
                                <tr>
                                    <th><a href='?action=view&sortColumn=ConferenceID&sortOrder=" . ($sortColumn == 'ConferenceID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>ConferenceID</a></th>
                                    <th><a href='?action=view&sortColumn=ConferenceName&sortOrder=" . ($sortColumn == 'ConferenceName' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>ConferenceName</a></th>
                                    <th><a href='?action=view&sortColumn=ConferenceDate&sortOrder=" . ($sortColumn == 'ConferenceDate' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>ConferenceDate</a></th>
                                    <th><a href='?action=view&sortColumn=Location&sortOrder=" . ($sortColumn == 'Location' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>Location</a></th>
                                    <th>Action</th>
                                </tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['ConferenceID']}</td>
                                <td>{$row['ConferenceName']}</td>
                                <td>{$row['ConferenceDate']}</td>
                                <td>{$row['Location']}</td>
                                <td>
                                    <form method='post' action=''>
                                        <input type='hidden' name='deleteConferenceID' value='{$row['ConferenceID']}'>
                                        <input type='submit' name='delete' value='Delete'>
                                    </form>
                                </td>
                            </tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "No records found.";
                    }

                echo '</section>';
            } elseif ($action == 'update') {
                echo '
                <section id="update">
                    <h2>Update Conference</h2>
                    <form method="post" action="">
                        <label>ConferenceID:</label>
                        <input type="number" name="updateConferenceID" required><br>
                        <label>New ConferenceName:</label>
                        <input type="text" name="newConferenceName"><br>
                        <label>New ConferenceDate:</label>
                        <input type="date" name="newConferenceDate"><br>
                        <label>New Location:</label>
                        <input type="text" name="newLocation"><br>
                        <input type="submit" name="update" value="Update">
                    </form>
                </section>';
            } elseif ($action == 'delete') {
                echo '
                <section id="delete">
                    <h2>Delete Conference</h2>
                    <form method="post" action="">
                        <label>ConferenceID:</label>
                        <input type="number" name="deleteConferenceID" required><br>
                        <input type="submit" name="delete" value="Delete">
                    </form>';
            }
        }

        if (isset($_POST['insert'])) {
            $ConferenceID = $_POST['ConferenceID'];
            $ConferenceName = $_POST['ConferenceName'];
            $ConferenceDate = $_POST['ConferenceDate'];
            $Location = $_POST['Location'];

            $sql = "INSERT INTO Conference (ConferenceID, ConferenceName, ConferenceDate, Location) VALUES ('$ConferenceID', '$ConferenceName', '$ConferenceDate', '$Location')";
            if ($conn->query($sql) === TRUE) {
                echo "New conference added.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        if (isset($_POST['search'])) {
            $searchConferenceID = $_POST['searchConferenceID'];
            $sql = "SELECT * FROM Conference WHERE ConferenceID = '$searchConferenceID'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<section id='search-results'><h2>Search Results</h2><table><tr><th>ConferenceID</th><th>ConferenceName</th><th>ConferenceDate</th><th>Location</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['ConferenceID']}</td>
                        <td>{$row['ConferenceName']}</td>
                        <td>{$row['ConferenceDate']}</td>
                        <td>{$row['Location']}</td>
                    </tr>";
                }
                echo "</table></section>";
            } else {
                echo "<section id='search-results'><h2>Search Results</h2>No records found.</section>";
            }
        }

        if (isset($_POST['update'])) {
            $ConferenceID = $_POST['updateConferenceID'];
            $newConferenceName = $_POST['newConferenceName'];
            $newConferenceDate = $_POST['newConferenceDate'];
            $newLocation = $_POST['newLocation'];
            $sql = "UPDATE Conference SET ConferenceName='$newConferenceName', ConferenceDate='$newConferenceDate', Location='$newLocation' WHERE ConferenceID='$ConferenceID'";
            if ($conn->query($sql) === TRUE) {
                echo "Conference updated.";
            } else {
                echo "Error updating: " . $conn->error;
            }
        }

        if (isset($_POST['delete'])) {
            $ConferenceID = $_POST['deleteConferenceID'];
            $sql = "DELETE FROM Conference WHERE ConferenceID='$ConferenceID'";
            if ($conn->query($sql) === TRUE) {
                echo "Conference deleted.";
                header("Refresh:0");
            } else {
                echo "Error deleting: " . $conn->error;
            }
        }
        ?>

    </main>

    <footer>
        <p>&copy; 2024 Manage Conferences. All rights reserved.</p>
    </footer>
</body>
</html>
