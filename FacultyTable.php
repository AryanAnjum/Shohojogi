<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Faculty</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Manage Faculty</h1>
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
                    <option value="insert">Insert New Faculty</option>
                    <option value="search">Search Faculty</option>
                    <option value="view">View All Faculty</option>
                    <option value="update">Update Faculty</option>
                    <option value="delete">Delete Faculty</option>
                </select>
                <input type="submit" name="choose-action" value="Go">
            </form>
        </section>

        <?php
        if (isset($_POST['choose-action']) || isset($_GET['sortColumn'])) {
            $action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
            $sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'FacultyID';
            $sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'ASC';

            if ($action == 'insert') {
                echo '
                <section id="insert">
                    <h2>Insert New Faculty</h2>
                    <form method="post" action="">
                        <label>FacultyID:</label>
                        <input type="number" name="FacultyID" required><br>
                        <label>Name:</label>
                        <input type="text" name="Name" required><br>
                        <label>Department:</label>
                        <input type="text" name="Department"><br>
                        <label>Email:</label>
                        <input type="email" name="Email" required><br>
                        <input type="submit" name="insert" value="Insert">
                    </form>
                </section>';
            } elseif ($action == 'search') {
                echo '
                <section id="search">
                    <h2>Search Faculty</h2>
                    <form method="post" action="">
                        <label>Name:</label>
                        <input type="text" name="searchName"><br>
                        <input type="submit" name="search" value="Search">
                    </form>';
            } elseif ($action == 'view') {
                echo '
                <section id="view">
                    <h2>All Faculty</h2>';
                    
                    $sql = "SELECT * FROM Faculty ORDER BY $sortColumn $sortOrder";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<table id='facultyTable'>
                                <tr>
                                    <th><a href='?action=view&sortColumn=FacultyID&sortOrder=" . ($sortColumn == 'FacultyID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>FacultyID</a></th>
                                    <th><a href='?action=view&sortColumn=Name&sortOrder=" . ($sortColumn == 'Name' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>Name</a></th>
                                    <th><a href='?action=view&sortColumn=Department&sortOrder=" . ($sortColumn == 'Department' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>Department</a></th>
                                    <th><a href='?action=view&sortColumn=Email&sortOrder=" . ($sortColumn == 'Email' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>Email</a></th>
                                    <th>Action</th>
                                </tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['FacultyID']}</td>
                                <td>{$row['Name']}</td>
                                <td>{$row['Department']}</td>
                                <td>{$row['Email']}</td>
                                <td>
                                    <form method='post' action=''>
                                        <input type='hidden' name='deleteFacultyID' value='{$row['FacultyID']}'>
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
                    <h2>Update Faculty</h2>
                    <form method="post" action="">
                        <label>FacultyID:</label>
                        <input type="number" name="updateFacultyID" required><br>
                        <label>New Name:</label>
                        <input type="text" name="newName"><br>
                        <label>New Department:</label>
                        <input type="text" name="newDepartment"><br>
                        <label>New Email:</label>
                        <input type="email" name="newEmail"><br>
                        <input type="submit" name="update" value="Update">
                    </form>
                </section>';
            } elseif ($action == 'delete') {
                echo '
                <section id="delete">
                    <h2>Delete Faculty</h2>
                    <form method="post" action="">
                        <label>FacultyID:</label>
                        <input type="number" name="deleteFacultyID" required><br>
                        <input type="submit" name="delete" value="Delete">
                    </form>';
            }
        }

        if (isset($_POST['insert'])) {
            $FacultyID = $_POST['FacultyID'];
            $Name = $_POST['Name'];
            $Department = $_POST['Department'];
            $Email = $_POST['Email'];

            $sql = "INSERT INTO Faculty (FacultyID, Name, Department, Email) VALUES ('$FacultyID', '$Name', '$Department', '$Email')";
            if ($conn->query($sql) === TRUE) {
                echo "New faculty added.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        if (isset($_POST['search'])) {
            $searchName = $_POST['searchName'];
            $sql = "SELECT * FROM Faculty WHERE Name LIKE '%$searchName%'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<section id='search-results'><h2>Search Results</h2><table><tr><th>FacultyID</th><th>Name</th><th>Department</th><th>Email</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['FacultyID']}</td>
                        <td>{$row['Name']}</td>
                        <td>{$row['Department']}</td>
                        <td>{$row['Email']}</td>
                    </tr>";
                }
                echo "</table></section>";
            } else {
                echo "<section id='search-results'><h2>Search Results</h2>No records found.</section>";
            }
        }

        if (isset($_POST['update'])) {
            $FacultyID = $_POST['updateFacultyID'];
            $newName = $_POST['newName'];
            $newDepartment = $_POST['newDepartment'];
            $newEmail = $_POST['newEmail'];
            $sql = "UPDATE Faculty SET Name='$newName', Department='$newDepartment', Email='$newEmail' WHERE FacultyID='$FacultyID'";
            if ($conn->query($sql) === TRUE) {
                echo "Faculty updated.";
            } else {
                echo "Error updating: " . $conn->error;
            }
        }

        if (isset($_POST['delete'])) {
            $FacultyID = $_POST['deleteFacultyID'];
            $sql = "DELETE FROM Faculty WHERE FacultyID='$FacultyID'";
            if ($conn->query($sql) === TRUE) {
                echo "Faculty deleted.";
                header("Refresh:0");
            } else {
                echo "Error deleting: " . $conn->error;
            }
        }
        ?>

    </main>

    <footer>
        <p>&copy; 2024 Manage Faculty. All rights reserved.</p>
    </footer>
</body>
</html>
