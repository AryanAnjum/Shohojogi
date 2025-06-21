<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Researcher Fields</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Manage Researcher Fields</h1>
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
                    <option value="insert">Link Researcher to Field</option>
                    <option value="search">Search Researcher Field Links</option>
                    <option value="view">View All Researcher Field Links</option>
                    <option value="update">Update Researcher Field Link</option>
                    <option value="delete">Unlink Researcher from Field</option>
                </select>
                <input type="submit" name="choose-action" value="Go">
            </form>
        </section>

        <?php
        if (isset($_POST['choose-action']) || isset($_GET['sortColumn'])) {
            $action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
            $sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'ResearcherID';
            $sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'ASC';

            if ($action == 'insert') {
                echo '
                <section id="insert">
                    <h2>Link Researcher to Field</h2>
                    <form method="post" action="">
                        <label>ResearcherID:</label>
                        <input type="number" name="ResearcherID" required><br>
                        <label>ResearchFieldID:</label>
                        <input type="number" name="ResearchFieldID" required><br>
                        <input type="submit" name="insert" value="Link">
                    </form>
                </section>';
            } elseif ($action == 'search') {
                echo '
                <section id="search">
                    <h2>Search Researcher Field Links</h2>
                    <form method="post" action="">
                        <label>ResearcherID:</label>
                        <input type="number" name="searchResearcherID"><br>
                        <label>ResearchFieldID:</label>
                        <input type="number" name="searchResearchFieldID"><br>
                        <input type="submit" name="search" value="Search">
                    </form>';
            } elseif ($action == 'view') {
                echo '
                <section id="view">
                    <h2>All Researcher Field Links</h2>';
                    
                    $sql = "SELECT * FROM ResearcherField ORDER BY $sortColumn $sortOrder";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<table id='researcherFieldTable'>
                                <tr>
                                    <th><a href='?action=view&sortColumn=ResearcherID&sortOrder=" . ($sortColumn == 'ResearcherID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>ResearcherID</a></th>
                                    <th><a href='?action=view&sortColumn=ResearchFieldID&sortOrder=" . ($sortColumn == 'ResearchFieldID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>ResearchFieldID</a></th>
                                    <th>Action</th>
                                </tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['ResearcherID']}</td>
                                <td>{$row['ResearchFieldID']}</td>
                                <td>
                                    <form method='post' action=''>
                                        <input type='hidden' name='deleteResearcherID' value='{$row['ResearcherID']}'>
                                        <input type='hidden' name='deleteResearchFieldID' value='{$row['ResearchFieldID']}'>
                                        <input type='submit' name='delete' value='Unlink'>
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
                    <h2>Update Researcher Field Link</h2>
                    <form method="post" action="">
                        <label>ResearcherID:</label>
                        <input type="number" name="updateResearcherID" required><br>
                        <label>New ResearchFieldID:</label>
                        <input type="number" name="newResearchFieldID"><br>
                        <input type="submit" name="update" value="Update">
                    </form>
                </section>';
            } elseif ($action == 'delete') {
                echo '
                <section id="delete">
                    <h2>Unlink Researcher from Field</h2>
                    <form method="post" action="">
                        <label>ResearcherID:</label>
                        <input type="number" name="deleteResearcherID" required><br>
                        <label>ResearchFieldID:</label>
                        <input type="number" name="deleteResearchFieldID" required><br>
                        <input type="submit" name="delete" value="Unlink">
                    </form>';
            }
        }

        if (isset($_POST['insert'])) {
            $ResearcherID = $_POST['ResearcherID'];
            $ResearchFieldID = $_POST['ResearchFieldID'];

            $sql = "INSERT INTO ResearcherField (ResearcherID, ResearchFieldID) VALUES ('$ResearcherID', '$ResearchFieldID')";
            if ($conn->query($sql) === TRUE) {
                echo "Researcher linked to field.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        if (isset($_POST['search'])) {
            $searchResearcherID = $_POST['searchResearcherID'];
            $searchResearchFieldID = $_POST['searchResearchFieldID'];
            $sql = "SELECT * FROM ResearcherField WHERE ResearcherID='$searchResearcherID' OR ResearchFieldID='$searchResearchFieldID'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<section id='search-results'><h2>Search Results</h2><table><tr><th>ResearcherID</th><th>ResearchFieldID</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['ResearcherID']}</td>
                        <td>{$row['ResearchFieldID']}</td>
                    </tr>";
                }
                echo "</table></section>";
            } else {
                echo "<section id='search-results'><h2>Search Results</h2>No records found.</section>";
            }
        }

        if (isset($_POST['update'])) {
            $ResearcherID = $_POST['updateResearcherID'];
            $newResearchFieldID = $_POST['newResearchFieldID'];
            $sql = "UPDATE ResearcherField SET ResearchFieldID='$newResearchFieldID' WHERE ResearcherID='$ResearcherID'";
            if ($conn->query($sql) === TRUE) {
                echo "Link updated.";
            } else {
                echo "Error updating: " . $conn->error;
            }
        }

        if (isset($_POST['delete'])) {
            $ResearcherID = $_POST['deleteResearcherID'];
            $ResearchFieldID = $_POST['deleteResearchFieldID'];
            $sql = "DELETE FROM ResearcherField WHERE ResearcherID='$ResearcherID' AND ResearchFieldID='$ResearchFieldID'";
            if ($conn->query($sql) === TRUE) {
                echo "Researcher unlinked from field.";
                header("Refresh:0");
            } else {
                echo "Error deleting: " . $conn->error;
            }
        }
        ?>

    </main>

    <footer>
        <p>&copy; 2024 Manage Researcher Fields. All rights reserved.</p>
    </footer>
</body>
</html>
