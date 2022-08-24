<?php
    require dirname(__FILE__,2)."/vendor/autoload.php";
    include dirname(__FILE__,2)."/connect.php";
    include dirname(__FILE__,2)."/model/Department.php";
    include dirname(__FILE__,2)."/model/Categories.php";
    include dirname(__FILE__,2)."/helper_files/GeneralFunctions.php";

    // Uncomment for localhost running
    // $dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__,2));
    // $dotenv->load();

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
    $frmDepartmentErr = $frmSubdepartmentErr = $frmCategoriesErr = "";
    $frmDepartment = $frmSubdepartment = $frmCategories = "";

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $category_name = $_POST["frmCategories"];
        $department_key = explode("-",$_POST['frmDepartment']);
        $department_identifier = $department_key[1];
        $subdepartment_id = $_POST["frmSubdepartment"];

        if (empty($category_name)) {
            $frmCategoriesErr = "Category name is required";
        } else {
            $category_name = test_input($category_name);
            // check if name only contains letters and whitespaces or Greek letters
            if (!preg_match("/^[a-zA-Z\p{Greek}\s]+$/u",$category_name)) {
                $frmCategoriesErr = "Invalid format for field name";
            }
        }
        if (empty($department_identifier)) {
            $frmDepartmentErr = "Department identifier is required";
        } else {
            $department_identifier = test_input($department_identifier);
            // check if identifier is number
            if (!is_numeric($department_identifier)) {
                $frmDepartmentErr = "Identifier is not a number";
            }
        }

        if (empty($subdepartment_id)) {
            $frmSubdepartmentErr = "Subdepartment id is required";
        } else {
            $subdepartment_id = test_input($subdepartment_id);
            // check if subdepartmentID is string
            if (!is_string($subdepartment_id)) {
                $frmSubdepartmentErr = "Invalid Subdepartment ID format";
            }
        }
        
        if (empty($frmCategoriesErr) && empty($frmDepartmentErr) && empty($frmSubdepartmentErr)) {
            $data = array(
                'identifier' => $department_identifier,
                'subdepartment_id' => $subdepartment_id,
                'name' => $category_name
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>
        .error {color: #FF0000;}
    </style>
</head>
<body>
    <h2>Εισαγωγή νέου Category</h2>
    <!-- <?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?> -->
    <p><span class="error">* required field</span></p>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <select name="frmDepartment" id="frmDepartment" onchange="findSubdepartment(this)">
            <option value="" default>Επιλέξτε Διεύθυνση</option>
            <?php
                foreach($allDepartments as $value) {
                    echo '<option value="'.$value['_id']['$oid']."-".$value['identifier'].'">'.$value['name']."</option>";
                }
            ?>
        </select>
        <span class="error">*<?php echo $frmDepartmentErr;?></span>
        <br><br>
        <select name="frmSubdepartment" id="frmSubdepartment">
            <option value="" default>Επιλέξτε Τμήμα</option>
        </select>
        <span class="error">*<?php echo $frmSubdepartmentErr;?></span>
        <br><br>
        Name: <input type="text" name="frmCategories" value="<?php echo $frmCategories;?>">
        <span class="error">* <?php echo $frmCategoriesErr;?></span>
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
    <script>
        function findSubdepartment(dvalue) {
            var value = dvalue.value;
            value = value.split("-");
            url = `/subdepartment/${value[0]}/list`
            // console.log(value, url);

            $.getJSON(url, function(data) {
                data = JSON.parse(data['data']);
                subdepartment = data['subdepartment']
                // console.log(subdepartment);

                $('#frmSubdepartment').empty();
                $('#frmSubdepartment').append($('<option>',{
                        value : "",
                        text:"Επιλέξτε Τμήμα"
                    }))

                $.each(subdepartment, function(index, value) {
                    name = value['name'];
                    id = value['_id']['$oid'];
                    $('#frmSubdepartment').append($('<option>',{
                        value :id,
                        text:name
                    }))
                }) 
            })
        }
    </script>
</body>
</html>