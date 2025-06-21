<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Journals</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Manage Journals</h1>
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
                    <option value="insert">Insert New Journal</option>
                    <option value="search">Search Journal</option>
                    <option value="view">View All Journals</option>
                    <option value="update">Update Journal</option>
                    <option value="delete">Delete Journal</option>
                </select>
                <input type="submit" name="choose-action" value="Go">
            </form>
        </section>

        <?php
        if (isset($_POST['choose-action']) || isset($_GET['sortColumn'])) {
            $action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
            $sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'JournalID';
            $sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'ASC';

            if ($action == 'insert') {
                echo '
                <section id="insert">
                    <h2>Insert New Journal</h2>
                    <form method="post" action="">
                        <label>JournalID:</label>
                        <input type="number" name="JournalID" required><br>
                        <label>JournalName:</label>
                        <input type="text" name="JournalName" required><br>
                        <label>ISSN:</label>
                        <input type="text" name="ISSN"><br>
                        <label>Publisher:</label>
                        <input type="text" name="Publisher"><br>
                        <input type="submit" name="insert" value="Insert">
                    </form>
                </section>';
            } elseif ($action == 'search') {
                echo '
                <section id="search">
                    <h2>Search Journal</h2>
                    <form method="post" action="">
                        <label>JournalName:</label>
                        <input type="text" name="searchJournalName"><br>
                        <input type="submit" name="search" value="Search">
                    </form>';
            } elseif ($action == 'view') {
                echo '
                <section id="view">
                    <h2>All Journals</h2>';
                    
                    $sql = "SELECT * FROM Journal ORDER BY $sortColumn $sortOrder";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<table id='journalTable'>
                                <tr>
                                    <th><a href='?action=view&sortColumn=JournalID&sortOrder=" . ($sortColumn == 'JournalID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>JournalID</a></th>
                                    <th><a href='?action=view&sortColumn=JournalName&sortOrder=" . ($sortColumn == 'JournalName' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>JournalName</a></th>
                                    <th><a href='?action=view&sortColumn=ISSN&sortOrder=" . ($sortColumn == 'ISSN' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>ISSN</a></th>
                                    <th><a href='?action=view&sortColumn=Publisher&sortOrder=" . ($sortColumn == 'Publisher' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>Publisher</a></th>
                                    <th>Action</th>
                                </tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['JournalID']}</td>
                                <td>{$row['JournalName']}</td>
                                <td>{$row['ISSN']}</td>
                                <td>{$row['Publisher']}</td>
                                <td>
                                    <form method='post' action=''>
                                        <input type='hidden' name='deleteJournalID' value='{$row['JournalID']}'>
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
                    <h2>Update Journal</h2>
                    <form method="post" action="">
                        <label>JournalID:</label>
                        <input type="number" name="updateJournalID" required><br>
                        <label>New JournalName:</label>
                        <input type="text" name="newJournalName"><br>
                        <label>New ISSN:</label>
                        <input type="text" name="newISSN"><br>
                        <label>New Publisher:</label>
                        <input type="text" name="newPublisher"><br>
                        <input type="submit" name="update" value="Update">
                    </form>
                </section>';
            } elseif ($action == 'delete') {
                echo '
                <section id="delete">
                    <h2>Delete Journal</h2>
                    <form method="post" action="">
                        <label>JournalID:</label>
                        <input type="number" name="deleteJournalID" required><br>
                        <input type="submit" name="delete" value="Delete">
                    </form>';
            }
        }

        if (isset($_POST['insert'])) {
            $JournalID = $_POST['JournalID'];
            $JournalName = $_POST['JournalName'];
            $ISSN = $_POST['ISSN'];
            $Publisher = $_POST['Publisher'];

            $sql = "INSERT INTO Journal (JournalID, JournalName, ISSN, Publisher) VALUES ('$JournalID', '$JournalName', '$ISSN', '$Publisher')";
            if ($conn->query($sql) === TRUE) {
                echo "New journal added.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        if (isset($_POST['search'])) {
            $searchJournalName = $_POST['searchJournalName'];
            $sql = "SELECT * FROM Journal WHERE JournalName LIKE '%$searchJournalName%'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<section id='search-results'><h2>Search Results</h2><table><tr><th>JournalID</th><th>JournalName</th><th>ISSN</th><th>Publisher</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['JournalID']}</td>
                        <td>{$row['JournalName']}</td>
                        <td>{$row['ISSN']}</td>
                        <td>{$row['Publisher']}</td>
                    </tr>";
                }
                echo "</table></section>";
            } else {
                echo "<section id='search-results'><h2>Search Results</h2>No records found.</section>";
            }
        }

        if (isset($_POST['update'])) {
            $JournalID = $_POST['updateJournalID'];
            $newJournalName = $_POST['newJournalName'];
            $newISSN = $_POST['newISSN'];
            $newPublisher = $_POST['newPublisher'];
            $sql = "UPDATE Journal SET JournalName='$newJournalName', ISSN='$newISSN', Publisher='$newPublisher' WHERE JournalID='$JournalID'";
            if ($conn->query($sql) === TRUE) {
                echo "Journal updated.";
            } else {
                echo "Error updating: " . $conn->error;
            }
        }

        if (isset($_POST['delete'])) {
            $JournalID = $_POST['deleteJournalID'];
            $sql = "DELETE FROM Journal WHERE JournalID='$JournalID'";
            if ($conn->query($sql) === TRUE) {
                echo "Journal deleted.";
                header("Refresh:0");
            } else {
                echo "Error deleting: " . $conn->error;
            }
        }
        ?>

    </main>

    <footer>
        <p>&copy; 2024 Manage Journals. All rights reserved.</p>
    </footer>
</body>
</html>
