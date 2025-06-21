<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Papers</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Manage Papers</h1>
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
                    <option value="insert">Insert New Paper</option>
                    <option value="search">Search Paper</option>
                    <option value="view">View All Papers</option>
                    <option value="update">Update Paper</option>
                    <option value="delete">Delete Paper</option>
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
                    <h2>Insert New Paper</h2>
                    <form method="post" action="">
                        <label>PaperID:</label>
                        <input type="number" name="PaperID" required><br>
                        <label>Title:</label>
                        <input type="text" name="Title" required><br>
                        <label>PublicationDate:</label>
                        <input type="date" name="PublicationDate"><br>
                        <label>Abstract:</label>
                        <input type="text" name="Abstract"><br>
                        <input type="submit" name="insert" value="Insert">
                    </form>
                </section>';
            } elseif ($action == 'search') {
                echo '
                <section id="search">
                    <h2>Search Paper</h2>
                    <form method="post" action="">
                        <label>Title:</label>
                        <input type="text" name="searchTitle"><br>
                        <input type="submit" name="search" value="Search">
                    </form>';
            } elseif ($action == 'view') {
                echo '
                <section id="view">
                    <h2>All Papers</h2>';
                    
                    $sql = "SELECT * FROM Paper ORDER BY $sortColumn $sortOrder";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<table id='paperTable'>
                                <tr>
                                    <th><a href='?action=view&sortColumn=PaperID&sortOrder=" . ($sortColumn == 'PaperID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>PaperID</a></th>
                                    <th><a href='?action=view&sortColumn=Title&sortOrder=" . ($sortColumn == 'Title' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>Title</a></th>
                                    <th><a href='?action=view&sortColumn=PublicationDate&sortOrder=" . ($sortColumn == 'PublicationDate' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>PublicationDate</a></th>
                                    <th><a href='?action=view&sortColumn=Abstract&sortOrder=" . ($sortColumn == 'Abstract' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>Abstract</a></th>
                                    <th>Action</th>
                                </tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['PaperID']}</td>
                                <td>{$row['Title']}</td>
                                <td>{$row['PublicationDate']}</td>
                                <td>{$row['Abstract']}</td>
                                <td>
                                    <form method='post' action=''>
                                        <input type='hidden' name='deletePaperID' value='{$row['PaperID']}'>
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
                    <h2>Update Paper</h2>
                    <form method="post" action="">
                        <label>PaperID:</label>
                        <input type="number" name="updatePaperID" required><br>
                        <label>New Title:</label>
                        <input type="text" name="newTitle"><br>
                        <label>New PublicationDate:</label>
                        <input type="date" name="newPublicationDate"><br>
                        <label>New Abstract:</label>
                        <input type="text" name="newAbstract"><br>
                        <input type="submit" name="update" value="Update">
                    </form>
                </section>';
            } elseif ($action == 'delete') {
                echo '
                <section id="delete">
                    <h2>Delete Paper</h2>
                    <form method="post" action="">
                        <label>PaperID:</label>
                        <input type="number" name="deletePaperID" required><br>
                        <input type="submit" name="delete" value="Delete">
                    </form>';
            }
        }

        if (isset($_POST['insert'])) {
            $PaperID = $_POST['PaperID'];
            $Title = $_POST['Title'];
            $PublicationDate = $_POST['PublicationDate'];
            $Abstract = $_POST['Abstract'];

            $sql = "INSERT INTO Paper (PaperID, Title, PublicationDate, Abstract) VALUES ('$PaperID', '$Title', '$PublicationDate', '$Abstract')";
            if ($conn->query($sql) === TRUE) {
                echo "New paper added.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        if (isset($_POST['search'])) {
            $searchTitle = $_POST['searchTitle'];
            $sql = "SELECT * FROM Paper WHERE Title LIKE '%$searchTitle%'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<section id='search-results'><h2>Search Results</h2><table><tr><th>PaperID</th><th>Title</th><th>PublicationDate</th><th>Abstract</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['PaperID']}</td>
                        <td>{$row['Title']}</td>
                        <td>{$row['PublicationDate']}</td>
                        <td>{$row['Abstract']}</td>
                    </tr>";
                }
                echo "</table></section>";
            } else {
                echo "<section id='search-results'><h2>Search Results</h2>No records found.</section>";
            }
        }

        if (isset($_POST['update'])) {
            $PaperID = $_POST['updatePaperID'];
            $newTitle = $_POST['newTitle'];
            $newPublicationDate = $_POST['newPublicationDate'];
            $newAbstract = $_POST['newAbstract'];
            $sql = "UPDATE Paper SET Title='$newTitle', PublicationDate='$newPublicationDate', Abstract='$newAbstract' WHERE PaperID='$PaperID'";
            if ($conn->query($sql) === TRUE) {
                echo "Paper updated.";
            } else {
                echo "Error updating: " . $conn->error;
            }
        }

        if (isset($_POST['delete'])) {
            $PaperID = $_POST['deletePaperID'];
            $sql = "DELETE FROM Paper WHERE PaperID='$PaperID'";
            if ($conn->query($sql) === TRUE) {
                echo "Paper deleted.";
                header("Refresh:0");
            } else {
                echo "Error deleting: " . $conn->error;
            }
        }
        ?>

    </main>

    <footer>
        <p>&copy; 2024 Manage Papers. All rights reserved.</p>
    </footer>
</body>
</html>
