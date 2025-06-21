<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Authorship</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Manage Authorship</h1>
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
                    <option value="insert">Insert New Authorship</option>
                    <option value="search">Search Authorship</option>
                    <option value="view">View All Authorships</option>
                    <option value="update">Update Authorship</option>
                    <option value="delete">Delete Authorship</option>
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
                    <h2>Insert New Authorship</h2>
                    <form method="post" action="">
                        <label>PaperID:</label>
                        <input type="number" name="PaperID" required><br>
                        <label>ResearcherID:</label>
                        <input type="number" name="ResearcherID" required><br>
                        <label>ContributionType:</label>
                        <input type="text" name="ContributionType"><br>
                        <input type="submit" name="insert" value="Insert">
                    </form>
                </section>';
            } elseif ($action == 'search') {
                echo '
                <section id="search">
                    <h2>Search Authorship</h2>
                    <form method="post" action="">
                        <label>PaperID:</label>
                        <input type="number" name="searchPaperID"><br>
                        <input type="submit" name="search" value="Search">
                    </form>';
            } elseif ($action == 'view') {
                echo '
                <section id="view">
                    <h2>All Authorships</h2>';
                    
                    $sql = "SELECT * FROM Authorship ORDER BY $sortColumn $sortOrder";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<table id='authorshipTable'>
                                <tr>
                                    <th><a href='?action=view&sortColumn=PaperID&sortOrder=" . ($sortColumn == 'PaperID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>PaperID</a></th>
                                    <th><a href='?action=view&sortColumn=ResearcherID&sortOrder=" . ($sortColumn == 'ResearcherID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>ResearcherID</a></th>
                                    <th><a href='?action=view&sortColumn=ContributionType&sortOrder=" . ($sortColumn == 'ContributionType' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>ContributionType</a></th>
                                    <th>Action</th>
                                </tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['PaperID']}</td>
                                <td>{$row['ResearcherID']}</td>
                                <td>{$row['ContributionType']}</td>
                                <td>
                                    <form method='post' action=''>
                                        <input type='hidden' name='deletePaperID' value='{$row['PaperID']}'>
                                        <input type='hidden' name='deleteResearcherID' value='{$row['ResearcherID']}'>
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
                    <h2>Update Authorship</h2>
                    <form method="post" action="">
                        <label>PaperID:</label>
                        <input type="number" name="updatePaperID" required><br>
                        <label>ResearcherID:</label>
                        <input type="number" name="updateResearcherID" required><br>
                        <label>New ContributionType:</label>
                        <input type="text" name="newContributionType"><br>
                        <input type="submit" name="update" value="Update">
                    </form>
                </section>';
            } elseif ($action == 'delete') {
                echo '
                <section id="delete">
                    <h2>Delete Authorship</h2>
                    <form method="post" action="">
                        <label>PaperID:</label>
                        <input type="number" name="deletePaperID" required><br>
                        <label>ResearcherID:</label>
                        <input type="number" name="deleteResearcherID" required><br>
                        <input type="submit" name="delete" value="Delete">
                    </form>';
            }
        }

        if (isset($_POST['insert'])) {
            $PaperID = $_POST['PaperID'];
            $ResearcherID = $_POST['ResearcherID'];
            $ContributionType = $_POST['ContributionType'];

            // paperid check paper table a
            $checkPaperID = $conn->query("SELECT * FROM Paper WHERE PaperID = '$PaperID'");
            if ($checkPaperID->num_rows > 0) {
                $sql = "INSERT INTO Authorship (PaperID, ResearcherID, ContributionType) VALUES ('$PaperID', '$ResearcherID', '$ContributionType')";
                if ($conn->query($sql) === TRUE) {
                    echo "New authorship added.";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                echo "Error: PaperID does not exist.";
            }
        }

        if (isset($_POST['search'])) {
            $searchPaperID = $_POST['searchPaperID'];
            $sql = "SELECT * FROM Authorship WHERE PaperID = '$searchPaperID'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<section id='search-results'><h2>Search Results</h2><table><tr><th>PaperID</th><th>ResearcherID</th><th>ContributionType</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['PaperID']}</td>
                        <td>{$row['ResearcherID']}</td>
                        <td>{$row['ContributionType']}</td>
                    </tr>";
                }
                echo "</table></section>";
            } else {
                echo "<section id='search-results'><h2>Search Results</h2>No records found.</section>";
            }
        }

        if (isset($_POST['update'])) {
            $PaperID = $_POST['updatePaperID'];
            $ResearcherID = $_POST['updateResearcherID'];
            $newContributionType = $_POST['newContributionType'];
            $sql = "UPDATE Authorship SET ContributionType='$newContributionType' WHERE PaperID='$PaperID' AND ResearcherID='$ResearcherID'";
            if ($conn->query($sql) === TRUE) {
                echo "Authorship updated.";
            } else {
                echo "Error updating: " . $conn->error;
            }
        }

        if (isset($_POST['delete'])) {
            $PaperID = $_POST['deletePaperID'];
            $ResearcherID = $_POST['deleteResearcherID'];
            $sql = "DELETE FROM Authorship WHERE PaperID='$PaperID' AND ResearcherID='$ResearcherID'";
            if ($conn->query($sql) === TRUE) {
                echo "Authorship deleted.";
                header("Refresh:0");
            } else {
                echo "Error deleting: " . $conn->error;
            }
        }
        ?>

    </main>

    <footer>
        <p>&copy; 2024 Manage Authorship. All rights reserved.</p>
    </footer>
</body>
</html>
