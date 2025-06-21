<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Research Labs</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Manage Research Labs</h1>
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
                    <option value="insert">Insert New Lab</option>
                    <option value="search">Search Lab</option>
                    <option value="view">View All Labs</option>
                    <option value="update">Update Lab</option>
                    <option value="delete">Delete Lab</option>
                </select>
                <input type="submit" name="choose-action" value="Go">
            </form>
        </section>

        <?php
        if (isset($_POST['choose-action']) || isset($_GET['sortColumn'])) {
            $action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
            $sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'LabID';
            $sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'ASC';

            if ($action == 'insert') {
                echo '
                <section id="insert">
                    <h2>Insert New Lab</h2>
                    <form method="post" action="">
                        <label>LabID:</label>
                        <input type="number" name="LabID" required><br>
                        <label>Department:</label>
                        <input type="text" name="Department"><br>
                        <label>Description:</label>
                        <input type="text" name="Description"><br>
                        <input type="submit" name="insert" value="Insert">
                    </form>
                </section>';
            } elseif ($action == 'search') {
                echo '
                <section id="search">
                    <h2>Search Lab</h2>
                    <form method="post" action="">
                        <label>Department:</label>
                        <input type="text" name="search_department"><br>
                        <input type="submit" name="search" value="Search">
                    </form>';
            } elseif ($action == 'view') {
                echo '
                <section id="view">
                    <h2>All Labs</h2>';
                    
                    $sql = "SELECT * FROM ResearchLab ORDER BY $sortColumn $sortOrder";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<table id='labTable'>
                                <tr>
                                    <th><a href='?action=view&sortColumn=LabID&sortOrder=" . ($sortColumn == 'LabID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>LabID</a></th>
                                    <th><a href='?action=view&sortColumn=Department&sortOrder=" . ($sortColumn == 'Department' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>Department</a></th>
                                    <th><a href='?action=view&sortColumn=Description&sortOrder=" . ($sortColumn == 'Description' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>Description</a></th>
                                    <th>Action</th>
                                </tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['LabID']}</td>
                                <td>{$row['Department']}</td>
                                <td>{$row['Description']}</td>
                                <td>
                                    <form method='post' action=''>
                                        <input type='hidden' name='deleteLabID' value='{$row['LabID']}'>
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
                    <h2>Update Lab</h2>
                    <form method="post" action="">
                        <label>LabID:</label>
                        <input type="number" name="updateLabID" required><br>
                        <label>New Department:</label>
                        <input type="text" name="newDepartment"><br>
                        <label>New Description:</label>
                        <input type="text" name="newDescription"><br>
                        <input type="submit" name="update" value="Update">
                    </form>
                </section>';
            } elseif ($action == 'delete') {
                echo '
                <section id="delete">
                    <h2>Delete Lab</h2>
                    <form method="post" action="">
                        <label>LabID:</label>
                        <input type="number" name="deleteLabID" required><br>
                        <input type="submit" name="delete" value="Delete">
                    </form>';
            }
        }

        if (isset($_POST['insert'])) {
            $LabID = $_POST['LabID'];
            $Department = $_POST['Department'];
            $Description = $_POST['Description'];

            $sql = "INSERT INTO ResearchLab (LabID, Department, Description) VALUES ('$LabID', '$Department', '$Description')";
            if ($conn->query($sql) === TRUE) {
                echo "New lab added.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        if (isset($_POST['search'])) {
            $search_department = $_POST['search_department'];
            $sql = "SELECT * FROM ResearchLab WHERE Department LIKE '%$search_department%'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<section id='search-results'><h2>Search Results</h2><table><tr><th>LabID</th><th>Department</th><th>Description</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['LabID']}</td>
                        <td>{$row['Department']}</td>
                        <td>{$row['Description']}</td>
                    </tr>";
                }
                echo "</table></section>";
            } else {
                echo "<section id='search-results'><h2>Search Results</h2>No records found.</section>";
            }
        }

        if (isset($_POST['update'])) {
            $LabID = $_POST['updateLabID'];
            $newDepartment = $_POST['newDepartment'];
            $newDescription = $_POST['newDescription'];
            $sql = "UPDATE ResearchLab SET Department='$newDepartment', Description='$newDescription' WHERE LabID='$LabID'";
            if ($conn->query($sql) === TRUE) {
                echo "Lab updated.";
            } else {
                echo "Error updating: " . $conn->error;
            }
        }

        if (isset($_POST['delete'])) {
            $LabID = $_POST['deleteLabID'];
            $sql = "DELETE FROM ResearchLab WHERE LabID='$LabID'";
            if ($conn->query($sql) === TRUE) {
                echo "Lab deleted.";
                header("Refresh:0");
            } else {
                echo "Error deleting: " . $conn->error;
            }
        }
        ?>

    </main>

    <footer>
        <p>&copy; 2024 Manage Research Labs. All rights reserved.</p>
    </footer>
</body>
</html>
