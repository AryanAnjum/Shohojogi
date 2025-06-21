<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grant Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Manage Grants</h1>
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
                    <option value="insert">Insert New Grant</option>
                    <option value="search">Search Grant</option>
                    <option value="view">View All Grants</option>
                    <option value="update">Update Grant</option>
                    <option value="delete">Delete Grant</option>
                </select>
                <input type="submit" name="choose-action" value="Go">
            </form>
        </section>

        <?php
        if (isset($_POST['choose-action']) || isset($_GET['sortColumn'])) {
            $action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
            $sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'GRANT_ID';
            $sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'ASC';

            if ($action == 'insert') {
                echo '
                <section id="insert">
                    <h2>Insert New Grant</h2>
                    <form method="post" action="">
                        <label>GRANT_ID:</label>
                        <input type="number" name="GRANT_ID" required><br>
                        <label>Grant Name:</label>
                        <input type="text" name="Grant_Name" required><br>
                        <label>Grant Amount:</label>
                        <input type="number" step="0.01" name="Grant_Amount" required><br>
                        <label>LabID:</label>
                        <input type="number" name="LabID"><br>
                        <input type="submit" name="insert" value="Insert">
                    </form>
                </section>';
            } elseif ($action == 'search') {
                echo '
                <section id="search">
                    <h2>Search Grant</h2>
                    <form method="post" action="">
                        <label>Grant Name:</label>
                        <input type="text" name="search_grant_name"><br>
                        <input type="submit" name="search" value="Search">
                    </form>';
            } elseif ($action == 'view') {
                echo '
                <section id="view">
                    <h2>All Grants</h2>';
                    
                    $sql = "SELECT * FROM Grants_ ORDER BY $sortColumn $sortOrder";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<table id='grantTable'>
                                <tr>
                                    <th><a href='?action=view&sortColumn=GRANT_ID&sortOrder=" . ($sortColumn == 'GRANT_ID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>GRANT_ID</a></th>
                                    <th><a href='?action=view&sortColumn=Grant_Name&sortOrder=" . ($sortColumn == 'Grant_Name' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>Grant Name</a></th>
                                    <th><a href='?action=view&sortColumn=Grant_Amount&sortOrder=" . ($sortColumn == 'Grant_Amount' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>Grant Amount</a></th>
                                    <th><a href='?action=view&sortColumn=LabID&sortOrder=" . ($sortColumn == 'LabID' && $sortOrder == 'ASC' ? 'DESC' : 'ASC') . "'>LabID</a></th>
                                    <th>Action</th>
                                </tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['GRANT_ID']}</td>
                                <td>{$row['Grant_Name']}</td>
                                <td>{$row['Grant_Amount']}</td>
                                <td>{$row['LabID']}</td>
                                <td>
                                    <form method='post' action=''>
                                        <input type='hidden' name='deleteGRANT_ID' value='{$row['GRANT_ID']}'>
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
                    <h2>Update Grant</h2>
                    <form method="post" action="">
                        <label>GRANT_ID:</label>
                        <input type="number" name="updateGRANT_ID" required><br>
                        <label>New Grant Name:</label>
                        <input type="text" name="newGrant_Name"><br>
                        <label>New Grant Amount:</label>
                        <input type="number" step="0.01" name="newGrant_Amount"><br>
                        <label>New LabID:</label>
                        <input type="number" name="newLabID"><br>
                        <input type="submit" name="update" value="Update">
                    </form>
                </section>';
            } elseif ($action == 'delete') {
                echo '
                <section id="delete">
                    <h2>Delete Grant</h2>
                    <form method="post" action="">
                        <label>GRANT_ID:</label>
                        <input type="number" name="deleteGRANT_ID" required><br>
                        <input type="submit" name="delete" value="Delete">
                    </form>';
            }
        }

        if (isset($_POST['insert'])) {
            $GRANT_ID = $_POST['GRANT_ID'];
            $Grant_Name = $_POST['Grant_Name'];
            $Grant_Amount = $_POST['Grant_Amount'];
            $LabID = $_POST['LabID'];

            $sql = "INSERT INTO Grants_ (GRANT_ID, Grant_Name, Grant_Amount, LabID) VALUES ('$GRANT_ID', '$Grant_Name', '$Grant_Amount', '$LabID')";
            if ($conn->query($sql) === TRUE) {
                echo "New grant added.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        if (isset($_POST['search'])) {
            $search_grant_name = $_POST['search_grant_name'];
            $sql = "SELECT * FROM Grants_ WHERE Grant_Name LIKE '%$search_grant_name%'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<section id='search-results'><h2>Search Results</h2><table><tr><th>GRANT_ID</th><th>Name</th><th>Amount</th><th>LabID</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['GRANT_ID']}</td>
                        <td>{$row['Grant_Name']}</td>
                        <td>{$row['Grant_Amount']}</td>
                        <td>{$row['LabID']}</td>
                    </tr>";
                }
                echo "</table></section>";
            } else {
                echo "<section id='search-results'><h2>Search Results</h2>No records found.</section>";
            }
        }

        if (isset($_POST['update'])) {
            $GRANT_ID = $_POST['updateGRANT_ID'];
            $newGrant_Name = $_POST['newGrant_Name'];
            $newGrant_Amount = $_POST['newGrant_Amount'];
            $newLabID = $_POST['newLabID'];
            $sql = "UPDATE Grants_ SET Grant_Name='$newGrant_Name', Grant_Amount='$newGrant_Amount', LabID='$newLabID' WHERE GRANT_ID='$GRANT_ID'";
            if ($conn->query($sql) === TRUE) {
                echo "Grant updated.";
            } else {
                echo "Error updating: " . $conn->error;
            }
        }

        if (isset($_POST['delete'])) {
            $GRANT_ID = $_POST['deleteGRANT_ID'];
            $sql = "DELETE FROM Grants_ WHERE GRANT_ID='$GRANT_ID'";
            if ($conn->query($sql) === TRUE) {
                echo "Grant deleted.";
                header("Refresh:0");
            } else {
                echo "Error deleting: " . $conn->error;
            }
        }
        ?>

    </main>
