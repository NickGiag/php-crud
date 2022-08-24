<?php include 'header-scripts.php' ?>

<?php
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

<?php include 'header.php'?>

    <div class="container mt-4">
        <h2>Εισαγωγή νέου Category</h2>
        <!-- <?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?> -->
        <p><span class="text-danger">* required field</span></p>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="mb-3 col-md-5">
                <label for="departmentName" class="form-label">Επιλέξτε Διεύθυνση: </label>
                <select name="frmDepartment" class="form-select" id="frmDepartment" onchange="findSubdepartment(this)">
                    <option value="" default>Επιλέξτε Διεύθυνση</option>
                    <?php
                        foreach($allDepartments as $value) {
                            echo '<option value="'.$value['_id']['$oid']."-".$value['identifier'].'">'.$value['name']."</option>";
                        }
                    ?>
                </select>
                <span class="text-danger">* <?php echo $frmDepartmentErr;?></span>
            </div>
            <div class="mb-3 col-md-5">
                <label for="subdepartmentName" class="form-label">Επιλέξτε Τμήμα: </label>
                <select name="frmSubdepartment" class="form-select" id="frmSubdepartment">
                    <option value="" default>Επιλέξτε Τμήμα</option>
                </select>
                </select>
                <span class="text-danger">* <?php echo $frmSubdepartmentErr;?></span>
            </div>
            <div class="mb-3 col-md-5">
                <label for="categoryName" class="form-label">Name: </label>
                <input type="text" class="form-control" name="frmCategories" value="<?php echo $frmCategories;?>">
                <span class="text-danger">* <?php echo $frmCategoriesErr;?></span>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>


        <hr>

        <table class="table table-bordered">
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
    </div>
<?php include 'footer.php' ?>