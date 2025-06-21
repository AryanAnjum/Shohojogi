<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Manage Students</h1>
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
                    <option value="ins">Insert New Student</option>
                    <option value="search">Search Student</option>
                    <option value="view">View All Students</option>
                    <option value="update">Update Student</option>
                    <option value="delete">Delete Student</option>
                </select>
                <input type="submit" name="choose-action" value="Go">
            </form>
        </section>

        <?php
        if (isset($_POST['choose-action']) || isset($_GET['sortColumn'])) {
            $action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
            $sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'StudentID';
            $sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'ASC';

            if ($action == 'ins') {
                echo '
                <section id="ins">
                    <h2>Insert New Student</h2>
                    <form method="post" action="">
                        <label>StudentID:</label>
                        <input type="number" name="StudentID" required><br>
                        <label>Name:</label>
                        <input type="text" name="Name" required><br>
                        <label>Department:</label>
                        <input type="text" name="Department" required><br>
                        <label>Email:</label>
                        <input type="email" name="Email" required><br>
                        <label>FacultyID:</label>
                        <input type="number" name="FacultyID"><br>
                        <input type="submit" name="insert" value="Insert">
                    </form>
                </section>';
            } elseif ($action == 'search') {
                echo '
                <section id="search">
                    <h2>Search Student</h2>
                    <form method="post" action="">
                        <label>Name:</label>
                        <input type="text" name="search_name"><br>
                        <input type="submit" name="search" value="Search">
                    </form>';
            } elseif ($action == 'view') {
                echo '
                <section id="view">
                    <h2>All Students</h2>';
                    
                    $sql = "SELECT * FROM Student ORDER BY $sortColumn $sortOrder";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<table id='studentTable'>
                                <tr>
                                    <th><a href='?action=view&sortColumn=StudentID&sortOrder=" . ($sortColumn == 'StudentID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>StudentID</a></th>
                                    <th><a href='?action=view&sortColumn=Name&sortOrder=" . ($sortColumn == 'Name' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>Name</a></th>
                                    <th><a href='?action=view&sortColumn=Department&sortOrder=" . ($sortColumn == 'Department' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>Department</a></th>
                                    <th><a href='?action=view&sortColumn=Email&sortOrder=" . ($sortColumn == 'Email' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>Email</a></th>
                                    <th>Action</th>
                                </tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['StudentID']}</td>
                                <td>{$row['Name']}</td>
                                <td>{$row['Department']}</td>
                                <td>{$row['Email']}</td>
                                <td>
                                    <form method='post' action=''>
                                        <input type='hidden' name='deleteStudentID' value='{$row['StudentID']}'>
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
                    <h2>Update Student</h2>
                    <form method="post" action="">
                        <label>StudentID:</label>
                        <input type="number" name="updateStudentID" required><br>
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
                    <h2>Delete Student</h2>
                    <form method="post" action="">
                        <label>StudentID:</label>
                        <input type="number" name="deleteStudentID" required><br>
                        <input type="submit" name="delete" value="Delete">
                    </form>';
            }
        }

        if (isset($_POST['ins'])) {
            $StudentID = $_POST['StudentID'];
            $Name = $_POST['Name'];
            $Department = $_POST['Department'];
            $Email = $_POST['Email'];
            $FacultyID = $_POST['FacultyID'];

            $sql = "INSERT INTO Student (StudentID, Name, Department, Email, FacultyID) VALUES ('$StudentID', '$Name', '$Department', '$Email', '$FacultyID')";
            if ($conn->query($sql) === TRUE) {
                echo "New student added.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        if (isset($_POST['search'])) {
            $search_name = $_POST['search_name'];
            $sql = "SELECT * FROM Student WHERE Name LIKE '%$search_name%'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<section id='search-results'><h2>Search Results</h2><table><tr><th>ID</th><th>Name</th><th>Department</th><th>Email</th><th>FacultyID</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['StudentID']}</td>
                        <td>{$row['Name']}</td>
                        <td>{$row['Department']}</td>
                        <td>{$row['Email']}</td>
                        <td>{$row['FacultyID']}</td>
                    </tr>";
                }
                echo "</table></section>";
            } else {
                echo "<section id='search-results'><h2>Search Results</h2>No records found.</section>";
            }
        }

        if (isset($_POST['update'])) {
            $StudentID = $_POST['updateStudentID'];
            $newName = $_POST['newName'];
            $newDepartment = $_POST['newDepartment'];
            $newEmail = $_POST['newEmail'];
            
            $sql = "UPDATE Student SET Name='$newName', Department='$newDepartment', Email='$newEmail' WHERE StudentID='$StudentID'";
            if ($conn->query($sql) === TRUE) {
                echo "Student updated.";
            } else {
                echo "Error updating: " . $conn->error;
            }
        }

        if (isset($_POST['delete'])) {
            $StudentID = $_POST['deleteStudentID'];
            $sql = "DELETE FROM Student WHERE StudentID='$StudentID'";
            if ($conn->query($sql) === TRUE) {
                echo "Student deleted.";
                header("Refresh:0");
            } else {
                echo "Error deleting: " . $conn->error;
            }
        }
        ?>

    </main>

    <footer>
        <p>&copy; 2024 Manage Students. All rights reserved.</p>
    </footer>
</body>
</html>
