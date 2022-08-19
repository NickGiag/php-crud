<?php
    require dirname(__FILE__,2)."/vendor/autoload.php";
    include dirname(__FILE__,2)."/connect.php";
    include dirname(__FILE__,2)."/model/Department.php";
    include dirname(__FILE__,2)."/model/Categories.php";
    include dirname(__FILE__,2)."/helper_files/GeneralFunctions.php";

    // Uncomment for localhost running
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__,2));
    $dotenv->load();

    $MDB_USER = $_ENV['MDB_USER'];
    $MDB_PASS = $_ENV['MDB_PASS'];
    $ATLAS_CLUSTER_SRV = $_ENV['ATLAS_CLUSTER_SRV'];

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    function saveCategories($data) {
        global $categories;
    
        $data = json_decode(json_encode($data));
        $result = $categories->createCategories($data);
        return $result;
    }

    $connection = new Connection($MDB_USER, $MDB_PASS, $ATLAS_CLUSTER_SRV);
    $department = new Department($connection);
    $categories = new Categories($connection);
    header('Content-type: text/html; charset=UTF-8');

    // // define variables and set to empty values
    $nameErr = $identifierErr = $subdepartmentIDErr = "";
    $name = $identifier = $subdepartmentID = "";

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        if (empty($_POST['name'])) {
            $nameErr = "Name is required";
        } else {
            $name = test_input($_POST["name"]);
            // check if name only contains letters and whitespaces or Greek letters
            if (!preg_match("/^[a-zA-Z\p{Greek}\s]+$/u",$_POST['name'])) {
                $nameErr = "Invalid format for field name";
            }
        }
        if (empty($_POST['identifier'])) {
            $identifierErr = "Department identifier is required";
        } else {
            $identifier = test_input($_POST["identifier"]);
            // check if identifier is number
            if (!is_numeric($identifier)) {
                $identifierErr = "Identifier is not a number";
            }
        }

        if (empty($_POST["subdepartmentID"])) {
            $subdepartmentIDErr = "Subdepartment is required";
        } else {
            $subdepartmentID = test_input($_POST['subdepartmentID']);
            // check if subdepartmentID is string
            if (!is_string($subdepartmentID)) {
                $subdepartmentIDErr = "Invalid Subdepartment ID format";
            }
        }
        
        if (empty($nameErr) && empty($identifierErr) && empty($subdepartmentIDErr)) {
            $data = array(
                'identifier' => intval($identifier),
                'subdepartment_id' => $subdepartmentID,
                'name' => $name
            );
            $result = saveCategories($data);
        }                           
    }

    $allDepartments = json_decode($department->showDepartments(),true);
    $allDepartments = json_decode($allDepartments['data'],true);

    // print_r($allDepartments);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Category</title>
    <style>
        .error {color: #FF0000;}
    </style>
</head>
<body>
    <h2>Εισαγωγή νέου Category</h2>
    <!-- <?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?> -->
    <p><span class="error">* required field</span></p>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <select name="identifier" id="identifier">
            <option value="" default>Επιλέξτε Διεύθυνση</option>
            <?php
                foreach($allDepartments as $value) {
                    echo '<option value="'.$value['identifier'].'">'.$value['name']."</option>";
                }
            ?>
        </select>
        <span class="error">*<?php echo $identifierErr;?></span>
        <br><br>
        <select name="subdepartmentID" id="subdepartmentID">
            <option value="" default>Επιλέξτε Τμήμα</option>
            <?php
                foreach($allDepartments as $value) {
                    foreach($value['subdepartment'] as $value) {
                        echo '<option value="'.$value['_id']['$oid'].'">'.$value['name']."</option>";
                    }
                }
            ?>
        </select>
        <span class="error">*<?php echo $subdepartmentIDErr;?></span>
        <br><br>
        Name: <input type="text" name="name" value="<?php echo $name;?>">
        <span class="error">* <?php echo $nameErr;?></span>
        <br><br>
        <input type="submit" name="submit" value="submit">
    </form>

    <hr>

    <table border="1px">
        <tr>
            <th>Διεύθυνση</th>
            <th>Αναγνωριστικό</th>
            <th>Τμήματα</th>
            <th>Κατηγορίες</th>
        </tr>
        <?php
            foreach($allDepartments as $value) {
                echo '<tr>';
                    echo '<td>'.$value['name'].'</td>';
                    echo '<td>'.$value['identifier'].'</td>';
                    echo '<td>';
                        foreach ($value['subdepartment'] as $subvalue) {
                            echo $subvalue['name']."<br>";
                        }
                    echo '</td>';
                    echo '<td>';
                        foreach ($value['categories'] as $catvalue) {
                            echo $catvalue['name']."<br>";
                        }
                    echo '</td>';
                echo '</tr>';
            }
        ?>

    </table>
</body>
</html>