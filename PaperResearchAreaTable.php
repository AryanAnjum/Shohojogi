<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Paper-Research Area Links</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Manage Paper-Research Area Links</h1>
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
                    <option value="insert">Insert New Link</option>
                    <option value="search">Search Link</option>
                    <option value="view">View All Links</option>
                    <option value="update">Update Link</option>
                    <option value="delete">Delete Link</option>
                </select>
                <input type="submit" name="choose-action" value="Go">
            </form>
        </section>

        <?php
        if (isset($_POST['choose-action']) || isset($_GET['sortColumn'])) {
            $action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
            $sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'PaperID';
            $sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'ASC';

            if ($action == 'insert') {
                echo '
                <section id="insert">
                    <h2>Insert New Paper-Research Area Link</h2>
                    <form method="post" action="">
                        <label>PaperID:</label>
                        <input type="number" name="PaperID" required><br>
                        <label>ResearchAreaID:</label>
                        <input type="number" name="ResearchAreaID" required><br>
                        <input type="submit" name="insert" value="Insert">
                    </form>
                </section>';
            } elseif ($action == 'search') {
                echo '
                <section id="search">
                    <h2>Search Link</h2>
                    <form method="post" action="">
                        <label>PaperID:</label>
                        <input type="number" name="searchPaperID"><br>
                        <input type="submit" name="search" value="Search">
                    </form>';
            } elseif ($action == 'view') {
                echo '
                <section id="view">
                    <h2>All Paper-Research Area Links</h2>';
                    
                    $sql = "SELECT * FROM PaperResearchArea ORDER BY $sortColumn $sortOrder";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<table id='linkTable'>
                                <tr>
                                    <th><a href='?action=view&sortColumn=PaperID&sortOrder=" . ($sortColumn == 'PaperID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>PaperID</a></th>
                                    <th><a href='?action=view&sortColumn=ResearchAreaID&sortOrder=" . ($sortColumn == 'ResearchAreaID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>ResearchAreaID</a></th>
                                    <th>Action</th>
                                </tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['PaperID']}</td>
                                <td>{$row['ResearchAreaID']}</td>
                                <td>
                                    <form method='post' action=''>
                                        <input type='hidden' name='deletePaperID' value='{$row['PaperID']}'>
                                        <input type='hidden' name='deleteResearchAreaID' value='{$row['ResearchAreaID']}'>
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
                    <h2>Update Paper-Research Area Link</h2>
                    <form method="post" action="">
                        <label>PaperID:</label>
                        <input type="number" name="updatePaperID" required><br>
                        <label>New ResearchAreaID:</label>
                        <input type="number" name="newResearchAreaID"><br>
                        <input type="submit" name="update" value="Update">
                    </form>
                </section>';
            } elseif ($action == 'delete') {
                echo '
                <section id="delete">
                    <h2>Delete Paper-Research Area Link</h2>
                    <form method="post" action="">
                        <label>PaperID:</label>
                        <input type="number" name="deletePaperID" required><br>
                        <label>ResearchAreaID:</label>
                        <input type="number" name="deleteResearchAreaID" required><br>
                        <input type="submit" name="delete" value="Delete">
                    </form>';
            }
        }

        if (isset($_POST['insert'])) {
            $PaperID = $_POST['PaperID'];
            $ResearchAreaID = $_POST['ResearchAreaID'];

            $sql = "INSERT INTO PaperResearchArea (PaperID, ResearchAreaID) VALUES ('$PaperID', '$ResearchAreaID')";
            if ($conn->query($sql) === TRUE) {
                echo "New link added.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        if (isset($_POST['search'])) {
            $searchPaperID = $_POST['searchPaperID'];
            $sql = "SELECT * FROM PaperResearchArea WHERE PaperID = '$searchPaperID'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<section id='search-results'><h2>Search Results</h2><table><tr><th>PaperID</th><th>ResearchAreaID</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['PaperID']}</td>
                        <td>{$row['ResearchAreaID']}</td>
                    </tr>";
                }
                echo "</table></section>";
            } else {
                echo "<section id='search-results'><h2>Search Results</h2>No records found.</section>";
            }
        }

        if (isset($_POST['update'])) {
            $PaperID = $_POST['updatePaperID'];
            $newResearchAreaID = $_POST['newResearchAreaID'];
            $sql = "UPDATE PaperResearchArea SET ResearchAreaID='$newResearchAreaID' WHERE PaperID='$PaperID'";
            if ($conn->query($sql) === TRUE) {
                echo "Link updated.";
            } else {
                echo "Error updating: " . $conn->error;
            }
        }

        if (isset($_POST['delete'])) {
            $PaperID = $_POST['deletePaperID'];
            $ResearchAreaID = $_POST['deleteResearchAreaID'];
            $sql = "DELETE FROM PaperResearchArea WHERE PaperID='$PaperID' AND ResearchAreaID='$ResearchAreaID'";
            if ($conn->query($sql) === TRUE) {
                echo "Link deleted.";
                header("Refresh:0");
            } else {
                echo "Error deleting: " . $conn->error;
            }
        }
        ?>

    </main>

    <footer>
        <p>&copy; 2024 Manage Paper-Research Area Links. All rights reserved.</p>
    </footer>
</body>
</html>
