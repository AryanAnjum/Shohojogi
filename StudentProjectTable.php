<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Student Projects</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Manage Student Projects</h1>
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
                    <option value="insert">Assign Project to Student</option>
                    <option value="view">View All Student Projects</option>
                    <option value="update">Update Project Assignment</option>
                    <option value="delete">Unassign Project from Student</option>
                </select>
                <input type="submit" name="choose-action" value="Go">
            </form>
        </section>

        <?php
        if (isset($_POST['choose-action']) || isset($_GET['sortColumn'])) {
            $action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
            $sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'StudentID';
            $sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'ASC';

            if ($action == 'insert') {
                echo '
                <section id="insert">
                    <h2>Assign Project to Student</h2>
                    <form method="post" action="">
                        <label>StudentID:</label>
                        <input type="number" name="StudentID" required><br>
                        <label>ProjectID:</label>
                        <input type="number" name="ProjectID" required><br>
                        <input type="submit" name="insert" value="Assign">
                    </form>
                </section>';
            } elseif ($action == 'view') {
                echo '
                <section id="view">
                    <h2>All Student Projects</h2>';
                    
                    $sql = "SELECT * FROM StudentProject ORDER BY $sortColumn $sortOrder";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<table id='studentProjectTable'>
                                <tr>
                                    <th><a href='?action=view&sortColumn=StudentID&sortOrder=" . ($sortColumn == 'StudentID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>StudentID</a></th>
                                    <th><a href='?action=view&sortColumn=ProjectID&sortOrder=" . ($sortColumn == 'ProjectID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>ProjectID</a></th>
                                    <th>Action</th>
                                </tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['StudentID']}</td>
                                <td>{$row['ProjectID']}</td>
                                <td>
                                    <form method='post' action=''>
                                        <input type='hidden' name='deleteStudentID' value='{$row['StudentID']}'>
                                        <input type='hidden' name='deleteProjectID' value='{$row['ProjectID']}'>
                                        <input type='submit' name='delete' value='Unassign'>
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
                    <h2>Update Project Assignment</h2>
                    <form method="post" action="">
                        <label>StudentID:</label>
                        <input type="number" name="updateStudentID" required><br>
                        <label>Old ProjectID:</label>
                        <input type="number" name="oldProjectID" required><br>
                        <label>New ProjectID:</label>
                        <input type="number" name="newProjectID" required><br>
                        <input type="submit" name="update" value="Update">
                    </form>
                </section>';
            } elseif ($action == 'delete') {
                echo '
                <section id="delete">
                    <h2>Unassign Project from Student</h2>
                    <form method="post" action="">
                        <label>StudentID:</label>
                        <input type="number" name="deleteStudentID" required><br>
                        <label>ProjectID:</label>
                        <input type="number" name="deleteProjectID" required><br>
                        <input type="submit" name="delete" value="Unassign">
                    </form>';
            }
        }

        if (isset($_POST['insert'])) {
            $StudentID = $_POST['StudentID'];
            $ProjectID = $_POST['ProjectID'];

            $sql = "INSERT INTO StudentProject (StudentID, ProjectID) VALUES ('$StudentID', '$ProjectID')";
            if ($conn->query($sql) === TRUE) {
                echo "Project assigned to student.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        if (isset($_POST['update'])) {
            $StudentID = $_POST['updateStudentID'];
            $oldProjectID = $_POST['oldProjectID'];
            $newProjectID = $_POST['newProjectID'];
            $sql = "UPDATE StudentProject SET ProjectID='$newProjectID' WHERE StudentID='$StudentID' AND ProjectID='$oldProjectID'";
            if ($conn->query($sql) === TRUE) {
                echo "Project assignment updated.";
            } else {
                echo "Error updating: " . $conn->error;
            }
        }

        if (isset($_POST['delete'])) {
            $StudentID = $_POST['deleteStudentID'];
            $ProjectID = $_POST['deleteProjectID'];
            $sql = "DELETE FROM StudentProject WHERE StudentID='$StudentID' AND ProjectID='$ProjectID'";
            if ($conn->query($sql) === TRUE) {
                echo "Project unassigned.";
                header("Refresh:0");
            } else {
                echo "Error deleting: " . $conn->error;
            }
        }
        ?>

    </main>

    <footer>
        <p>&copy; 2024 Manage Student Projects. All rights reserved.</p>
    </footer>
</body>
</html>
