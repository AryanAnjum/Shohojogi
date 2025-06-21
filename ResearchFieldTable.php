<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Research Fields</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Manage Research Fields</h1>
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
                    <option value="insert">Insert New Research Field</option>
                    <option value="search">Search Research Field</option>
                    <option value="view">View All Research Fields</option>
                    <option value="update">Update Research Field</option>
                    <option value="delete">Delete Research Field</option>
                </select>
                <input type="submit" name="choose-action" value="Go">
            </form>
        </section>

        <?php
        if (isset($_POST['choose-action']) || isset($_GET['sortColumn'])) {
            $action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
            $sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'ResearchFieldID';
            $sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'ASC';

            if ($action == 'insert') {
                echo '
                <section id="insert">
                    <h2>Insert New Research Field</h2>
                    <form method="post" action="">
                        <label>ResearchFieldID:</label>
                        <input type="number" name="ResearchFieldID" required><br>
                        <label>FieldName:</label>
                        <input type="text" name="FieldName" required><br>
                        <input type="submit" name="insert" value="Insert">
                    </form>
                </section>';
            } elseif ($action == 'search') {
                echo '
                <section id="search">
                    <h2>Search Research Field</h2>
                    <form method="post" action="">
                        <label>Field Name:</label>
                        <input type="text" name="search_field_name"><br>
                        <input type="submit" name="search" value="Search">
                    </form>';
            } elseif ($action == 'view') {
                echo '
                <section id="view">
                    <h2>All Research Fields</h2>';
                    
                    $sql = "SELECT * FROM ResearchField ORDER BY $sortColumn $sortOrder";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<table id='researchFieldTable'>
                                <tr>
                                    <th><a href='?action=view&sortColumn=ResearchFieldID&sortOrder=" . ($sortColumn == 'ResearchFieldID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>ResearchFieldID</a></th>
                                    <th><a href='?action=view&sortColumn=FieldName&sortOrder=" . ($sortColumn == 'FieldName' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>FieldName</a></th>
                                    <th>Action</th>
                                </tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['ResearchFieldID']}</td>
                                <td>{$row['FieldName']}</td>
                                <td>
                                    <form method='post' action=''>
                                        <input type='hidden' name='deleteResearchFieldID' value='{$row['ResearchFieldID']}'>
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
                    <h2>Update Research Field</h2>
                    <form method="post" action="">
                        <label>ResearchFieldID:</label>
                        <input type="number" name="updateResearchFieldID" required><br>
                        <label>New FieldName:</label>
                        <input type="text" name="newFieldName"><br>
                        <input type="submit" name="update" value="Update">
                    </form>
                </section>';
            } elseif ($action == 'delete') {
                echo '
                <section id="delete">
                    <h2>Delete Research Field</h2>
                    <form method="post" action="">
                        <label>ResearchFieldID:</label>
                        <input type="number" name="deleteResearchFieldID" required><br>
                        <input type="submit" name="delete" value="Delete">
                    </form>';
            }
        }

        if (isset($_POST['insert'])) {
            $ResearchFieldID = $_POST['ResearchFieldID'];
            $FieldName = $_POST['FieldName'];

            $sql = "INSERT INTO ResearchField (ResearchFieldID, FieldName) VALUES ('$ResearchFieldID', '$FieldName')";
            if ($conn->query($sql) === TRUE) {
                echo "New research field added.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        if (isset($_POST['search'])) {
            $search_field_name = $_POST['search_field_name'];
            $sql = "SELECT * FROM ResearchField WHERE FieldName LIKE '%$search_field_name%'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<section id='search-results'><h2>Search Results</h2><table><tr><th>ResearchFieldID</th><th>FieldName</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['ResearchFieldID']}</td>
                        <td>{$row['FieldName']}</td>
                    </tr>";
                }
                echo "</table></section>";
            } else {
                echo "<section id='search-results'><h2>Search Results</h2>No records found.</section>";
            }
        }

        if (isset($_POST['update'])) {
            $ResearchFieldID = $_POST['updateResearchFieldID'];
            $newFieldName = $_POST['newFieldName'];
            $sql = "UPDATE ResearchField SET FieldName='$newFieldName' WHERE ResearchFieldID='$ResearchFieldID'";
            if ($conn->query($sql) === TRUE) {
                echo "Research field updated.";
            } else {
                echo "Error updating: " . $conn->error;
            }
        }

        if (isset($_POST['delete'])) {
            $ResearchFieldID = $_POST['deleteResearchFieldID'];
            $sql = "DELETE FROM ResearchField WHERE ResearchFieldID='$ResearchFieldID'";
            if ($conn->query($sql) === TRUE) {
                echo "Research field deleted.";
                header("Refresh:0");
            } else {
                echo "Error deleting: " . $conn->error;
            }
        }
        ?>

    </main>

    <footer>
        <p>&copy; 2024 Manage Research Fields. All rights reserved.</p>
    </footer>
</body>
</html>
