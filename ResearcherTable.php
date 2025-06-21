<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Researchers</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Manage Researchers</h1>
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
                    <option value="insert">Insert New Researcher</option>
                    <option value="search">Search Researcher</option>
                    <option value="view">View All Researchers</option>
                    <option value="update">Update Researcher</option>
                    <option value="delete">Delete Researcher</option>
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
                    <h2>Insert New Researcher</h2>
                    <form method="post" action="">
                        <label>ResearcherID:</label>
                        <input type="number" name="ResearcherID" required><br>
                        <label>Name:</label>
                        <input type="text" name="Name" required><br>
                        <label>Department:</label>
                        <input type="text" name="Department"><br>
                        <label>Email:</label>
                        <input type="email" name="Email" required><br>
                        <label>FacultyID:</label>
                        <input type="number" name="FacultyID"><br>
                        <label>LabID:</label>
                        <input type="number" name="LabID"><br>
                        <input type="submit" name="insert" value="Insert">
                    </form>
                </section>';
            } elseif ($action == 'search') {
                echo '
                <section id="search">
                    <h2>Search Researcher</h2>
                    <form method="post" action="">
                        <label>Name:</label>
                        <input type="text" name="search_name"><br>
                        <input type="submit" name="search" value="Search">
                    </form>';
            } elseif ($action == 'view') {
                echo '
                <section id="view">
                    <h2>All Researchers</h2>';
                    
                    $sql = "SELECT * FROM Researcher ORDER BY $sortColumn $sortOrder";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<table id='researcherTable'>
                                <tr>
                                    <th><a href='?action=view&sortColumn=ResearcherID&sortOrder=" . ($sortColumn == 'ResearcherID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>ResearcherID</a></th>
                                    <th><a href='?action=view&sortColumn=Name&sortOrder=" . ($sortColumn == 'Name' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>Name</a></th>
                                    <th><a href='?action=view&sortColumn=Department&sortOrder=" . ($sortColumn == 'Department' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>Department</a></th>
                                    <th><a href='?action=view&sortColumn=Email&sortOrder=" . ($sortColumn == 'Email' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>Email</a></th>
                                    <th>Action</th>
                                </tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['ResearcherID']}</td>
                                <td>{$row['Name']}</td>
                                <td>{$row['Department']}</td>
                                <td>{$row['Email']}</td>
                                <td>
                                    <form method='post' action=''>
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
                    <h2>Update Researcher</h2>
                    <form method="post" action="">
                        <label>ResearcherID:</label>
                        <input type="number" name="updateResearcherID" required><br>
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
                    <h2>Delete Researcher</h2>
                    <form method="post" action="">
                        <label>ResearcherID:</label>
                        <input type="number" name="deleteResearcherID" required><br>
                        <input type="submit" name="delete" value="Delete">
                    </form>';
            }
        }

        if (isset($_POST['insert'])) {
            $ResearcherID = $_POST['ResearcherID'];
            $Name = $_POST['Name'];
            $Department = $_POST['Department'];
            $Email = $_POST['Email'];
            $FacultyID = $_POST['FacultyID'];
            $LabID = $_POST['LabID'];

            $sql = "INSERT INTO Researcher (ResearcherID, Name, Department, Email, FacultyID, LabID) VALUES ('$ResearcherID', '$Name', '$Department', '$Email', '$FacultyID', '$LabID')";
            if ($conn->query($sql) === TRUE) {
                echo "New researcher added.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        if (isset($_POST['search'])) {
            $search_name = $_POST['search_name'];
            $sql = "SELECT * FROM Researcher WHERE Name LIKE '%$search_name%'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<section id='search-results'><h2>Search Results</h2><table><tr><th>ID</th><th>Name</th><th>Department</th><th>Email</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['ResearcherID']}</td>
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
            $ResearcherID = $_POST['updateResearcherID'];
            $newName = $_POST['newName'];
            $newDepartment = $_POST['newDepartment'];
            $newEmail = $_POST['newEmail'];
            $sql = "UPDATE Researcher SET Name='$newName', Department='$newDepartment', Email='$newEmail' WHERE ResearcherID='$ResearcherID'";
            if ($conn->query($sql) === TRUE) {
                echo "Researcher updated.";
            } else {
                echo "Error updating: " . $conn->error;
            }
        }

        if (isset($_POST['delete'])) {
            $ResearcherID = $_POST['deleteResearcherID'];
            $sql = "DELETE FROM Researcher WHERE ResearcherID='$ResearcherID'";
            if ($conn->query($sql) === TRUE) {
                echo "Researcher deleted.";
                header("Refresh:0");
            } else {
                echo "Error deleting: " . $conn->error;
            }
        }
        ?>

    </main>

    <footer>
        <p>&copy; 2024 Manage Researchers. All rights reserved.</p>
