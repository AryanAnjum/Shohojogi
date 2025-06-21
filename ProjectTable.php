<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Manage Projects</h1>
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
                    <option value="insert">Insert New Project</option>
                    <option value="search">Search Project</option>
                    <option value="view">View All Projects</option>
                    <option value="update">Update Project</option>
                    <option value="delete">Delete Project</option>
                </select>
                <input type="submit" name="choose-action" value="Go">
            </form>
        </section>

        <?php
        if (isset($_POST['choose-action']) || isset($_GET['sortColumn'])) {
            $action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
            $sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'ProjectID';
            $sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'ASC';

            if ($action == 'insert') {
                echo '
                <section id="insert">
                    <h2>Insert New Project</h2>
                    <form method="post" action="">
                        <label>ProjectID:</label>
                        <input type="number" name="ProjectID" required><br>
                        <label>ProjectName:</label>
                        <input type="text" name="ProjectName" required><br>
                        <label>ActiveStatus (1 for Active, 0 for Inactive):</label>
                        <input type="number" name="ActiveStatus" min="0" max="1" required><br>
                        <label>StartDate:</label>
                        <input type="date" name="StartDate"><br>
                        <label>EndDate:</label>
                        <input type="date" name="EndDate"><br>
                        <input type="submit" name="insert" value="Insert">
                    </form>
                </section>';
            } elseif ($action == 'search') {
                echo '
                <section id="search">
                    <h2>Search Project</h2>
                    <form method="post" action="">
                        <label>Project Name:</label>
                        <input type="text" name="search_project_name"><br>
                        <input type="submit" name="search" value="Search">
                    </form>';
            } elseif ($action == 'view') {
                echo '
                <section id="view">
                    <h2>All Projects</h2>';
                    
                    $sql = "SELECT * FROM Project ORDER BY $sortColumn $sortOrder";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<table id='projectTable'>
                                <tr>
                                    <th><a href='?action=view&sortColumn=ProjectID&sortOrder=" . ($sortColumn == 'ProjectID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>ProjectID</a></th>
                                    <th><a href='?action=view&sortColumn=ProjectName&sortOrder=" . ($sortColumn == 'ProjectName' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>ProjectName</a></th>
                                    <th><a href='?action=view&sortColumn=ActiveStatus&sortOrder=" . ($sortColumn == 'ActiveStatus' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>ActiveStatus</a></th>
                                    <th><a href='?action=view&sortColumn=StartDate&sortOrder=" . ($sortColumn == 'StartDate' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>StartDate</a></th>
                                    <th><a href='?action=view&sortColumn=EndDate&sortOrder=" . ($sortColumn == 'EndDate' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>EndDate</a></th>
                                    <th>Action</th>
                                </tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['ProjectID']}</td>
                                <td>{$row['ProjectName']}</td>
                                <td>{$row['ActiveStatus']}</td>
                                <td>{$row['StartDate']}</td>
                                <td>{$row['EndDate']}</td>
                                <td>
                                    <form method='post' action=''>
                                        <input type='hidden' name='deleteProjectID' value='{$row['ProjectID']}'>
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
                    <h2>Update Project</h2>
                    <form method="post" action="">
                        <label>ProjectID:</label>
                        <input type="number" name="updateProjectID" required><br>
                        <label>New ProjectName:</label>
                        <input type="text" name="newProjectName"><br>
                        <label>New ActiveStatus (1 for Active, 0 for Inactive):</label>
                        <input type="number" name="newActiveStatus" min="0" max="1"><br>
                        <label>New StartDate:</label>
                        <input type="date" name="newStartDate"><br>
                        <label>New EndDate:</label>
                        <input type="date" name="newEndDate"><br>
                        <input type="submit" name="update" value="Update">
                    </form>
                </section>';
            } elseif ($action == 'delete') {
                echo '
                <section id="delete">
                    <h2>Delete Project</h2>
                    <form method="post" action="">
                        <label>ProjectID:</label>
                        <input type="number" name="deleteProjectID" required><br>
                        <input type="submit" name="delete" value="Delete">
                    </form>';
            }
        }

        if (isset($_POST['insert'])) {
            $ProjectID = $_POST['ProjectID'];
            $ProjectName = $_POST['ProjectName'];
            $ActiveStatus = $_POST['ActiveStatus'];
            $StartDate = $_POST['StartDate'];
            $EndDate = $_POST['EndDate'];

            $sql = "INSERT INTO Project (ProjectID, ProjectName, ActiveStatus, StartDate, EndDate) VALUES ('$ProjectID', '$ProjectName', '$ActiveStatus', '$StartDate', '$EndDate')";
            if ($conn->query($sql) === TRUE) {
                echo "New project added.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        if (isset($_POST['search'])) {
            $search_project_name = $_POST['search_project_name'];
            $sql = "SELECT * FROM Project WHERE ProjectName LIKE '%$search_project_name%'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<section id='search-results'><h2>Search Results</h2><table><tr><th>ProjectID</th><th>Name</th><th>Status</th><th>StartDate</th><th>EndDate</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['ProjectID']}</td>
                        <td>{$row['ProjectName']}</td>
                        <td>{$row['ActiveStatus']}</td>
                        <td>{$row['StartDate']}</td>
                        <td>{$row['EndDate']}</td>
                    </tr>";
                }
                echo "</table></section>";
            } else {
                echo "<section id='search-results'><h2>Search Results</h2>No records found.</section>";
            }
        }
            if (isset($_POST['update'])) {
                $ProjectID = $_POST['updateProjectID'];
                $newProjectName = $_POST['newProjectName'];
                $newActiveStatus = $_POST['newActiveStatus'];
                $newStartDate = $_POST['newStartDate'];
                $newEndDate = $_POST['newEndDate'];
                
                $sql = "UPDATE Project SET ProjectName='$newProjectName', ActiveStatus='$newActiveStatus', StartDate='$newStartDate', EndDate='$newEndDate' WHERE ProjectID='$ProjectID'";
                if ($conn->query($sql) === TRUE) {
                    echo "Project updated.";
                } else {
                    echo "Error updating: " . $conn->error;
                }
            }
            
            if (isset($_POST['delete'])) {
                $ProjectID = $_POST['deleteProjectID'];
                $sql = "DELETE FROM Project WHERE ProjectID='$ProjectID'";
                if ($conn->query($sql) === TRUE) {
                    echo "Project deleted.";
                    header("Refresh:0");
                } else {
                    echo "Error deleting: " . $conn->error;
                }
            }
            ?>
            
            </main>
            
            <footer>
                <p>&copy; 2024 Manage Projects. All rights reserved.</p>
            </footer>
            </body>
            </html>
            