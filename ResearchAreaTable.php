<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Research Areas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Manage Research Areas</h1>
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
                    <option value="insert">Insert New Research Area</option>
                    <option value="search">Search Research Area</option>
                    <option value="view">View All Research Areas</option>
                    <option value="update">Update Research Area</option>
                    <option value="delete">Delete Research Area</option>
                </select>
                <input type="submit" name="choose-action" value="Go">
            </form>
        </section>

        <?php
        if (isset($_POST['choose-action']) || isset($_GET['sortColumn'])) {
            $action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
            $sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'ResearchAreaID';
            $sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'ASC';

            if ($action == 'insert') {
                echo '
                <section id="insert">
                    <h2>Insert New Research Area</h2>
                    <form method="post" action="">
                        <label>ResearchAreaID:</label>
                        <input type="number" name="ResearchAreaID" required><br>
                        <label>Name:</label>
                        <input type="text" name="Name" required><br>
                        <label>Description:</label>
                        <input type="text" name="Description"><br>
                        <label>ParentFieldID (optional):</label>
                        <input type="number" name="ParentFieldID"><br>
                        <input type="submit" name="insert" value="Insert">
                    </form>
                </section>';
            } elseif ($action == 'search') {
                echo '
                <section id="search">
                    <h2>Search Research Area</h2>
                    <form method="post" action="">
                        <label>Name:</label>
                        <input type="text" name="search_name"><br>
                        <input type="submit" name="search" value="Search">
                    </form>';
            } elseif ($action == 'view') {
                echo '
                <section id="view">
                    <h2>All Research Areas</h2>';
                    
                    $sql = "SELECT * FROM ResearchArea ORDER BY $sortColumn $sortOrder";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<table id='researchAreaTable'>
                                <tr>
                                    <th><a href='?action=view&sortColumn=ResearchAreaID&sortOrder=" . ($sortColumn == 'ResearchAreaID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>ResearchAreaID</a></th>
                                    <th><a href='?action=view&sortColumn=Name&sortOrder=" . ($sortColumn == 'Name' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>Name</a></th>
                                    <th><a href='?action=view&sortColumn=Description&sortOrder=" . ($sortColumn == 'Description' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>Description</a></th>
                                    <th><a href='?action=view&sortColumn=ParentFieldID&sortOrder=" . ($sortColumn == 'ParentFieldID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>ParentFieldID</a></th>
                                    <th>Action</th>
                                </tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['ResearchAreaID']}</td>
                                <td>{$row['Name']}</td>
                                <td>{$row['Description']}</td>
                                <td>{$row['ParentFieldID']}</td>
                                <td>
                                    <form method='post' action=''>
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
                    <h2>Update Research Area</h2>
                    <form method="post" action="">
                        <label>ResearchAreaID:</label>
                        <input type="number" name="updateResearchAreaID" required><br>
                        <label>New Name:</label>
                        <input type="text" name="newName"><br>
                        <label>New Description:</label>
                        <input type="text" name="newDescription"><br>
                        <label>New ParentFieldID (optional):</label>
                        <input type="number" name="newParentFieldID"><br>
                        <input type="submit" name="update" value="Update">
                    </form>
                </section>';
            } elseif ($action == 'delete') {
                echo '
                <section id="delete">
                    <h2>Delete Research Area</h2>
                    <form method="post" action="">
                        <label>ResearchAreaID:</label>
                        <input type="number" name="deleteResearchAreaID" required><br>
                        <input type="submit" name="delete" value="Delete">
                    </form>';
            }
        }

        if (isset($_POST['insert'])) {
            $ResearchAreaID = $_POST['ResearchAreaID'];
            $Name = $_POST['Name'];
            $Description = $_POST['Description'];
            $ParentFieldID = $_POST['ParentFieldID'];

            $sql = "INSERT INTO ResearchArea (ResearchAreaID, Name, Description, ParentFieldID) VALUES ('$ResearchAreaID', '$Name', '$Description', '$ParentFieldID')";
            if ($conn->query($sql) === TRUE) {
                echo "New research area added.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        if (isset($_POST['search'])) {
            $search_name = $_POST['search_name'];
            $sql = "SELECT * FROM ResearchArea WHERE Name LIKE '%$search_name%'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<section id='search-results'><h2>Search Results</h2><table><tr><th>ResearchAreaID</th><th>Name</th><th>Description</th><th>ParentFieldID</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['ResearchAreaID']}</td>
                        <td>{$row['Name']}</td>
                        <td>{$row['Description']}</td>
                        <td>{$row['ParentFieldID']}</td>
                    </tr>";
                }
                echo "</table></section>";
            } else {
                echo "<section id='search-results'><h2>Search Results</h2>No records found.</section>";
            }
        }

        if (isset($_POST['update'])) {
            $ResearchAreaID = $_POST['updateResearchAreaID'];
            $newName = $_POST['newName'];
            $newDescription = $_POST['newDescription'];
            $newParentFieldID = $_POST['newParentFieldID'];
            $sql = "UPDATE ResearchArea SET Name='$newName', Description='$newDescription', ParentFieldID='$newParentFieldID' WHERE ResearchAreaID='$ResearchAreaID'";
            if ($conn->query($sql) === TRUE) {
                echo "Research area updated.";
            } else {
                echo "Error updating: " . $conn->error;
            }
        }

        if (isset($_POST['delete'])) {
            $ResearchAreaID = $_POST['deleteResearchAreaID'];
            $sql = "DELETE FROM ResearchArea WHERE ResearchAreaID='$ResearchAreaID'";
            if ($conn->query($sql) === TRUE) {
                echo "Research area deleted.";
                header("Refresh:0");
            } else {
                echo "Error deleting: " . $conn->error;
            }
        }
        ?>

    </main>

    <footer>
        <p>&copy; 2024 Manage Research Areas. All rights reserved.</p>
    </footer>
</body>
</html>
