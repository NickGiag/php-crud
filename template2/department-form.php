<?php include 'header-scripts.php' ?>

<?php 
    function saveDepartment($data) {
        global $department;

        $datatosave = json_decode(json_encode($data));
        $result = $department->createDepartment($datatosave);
        return $result;
    }

    function updateDepartment($data) {
        global $department;

        $datatosave = json_decode(json_encode($data));
        $result = $department->updateDepartment($datatosave);
        return $result;
    }

    function deleteDepartment($data) {
        global $department;

        $result = $department->deleteDepartment($data);
        return $result;
    }

    $nameErr = $identifierErr = "";
    $name = $identifier = "";

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        if (empty($_POST['id'])) {
            $update = false;
        } else {
            $update = true;
        }

        if (empty($_POST['name'])) {
            $nameErr = "Name is required";
        } else {
            if (!preg_match("/^[a-zA-Z\p{Greek}\s]+$/u",$_POST['name'])) {
                $nameErr = "Invalid format for field name";
            }
        }
        if (empty($_POST['identifier'])) {
            $identifierErr = "Identifier is required";
        } else {
            if (!is_numeric($_POST['identifier'])) {
                $identifierErr = "Identifier is not a number";
            }
        }
        
        if (empty($nameErr) && empty($identifierErr)) {
            if ($update){
                $data = array(
                    '_id' => $_POST['id'],
                    'identifier' => $_POST['identifier'],
                    'name' => $_POST['name']
                );
                $result = updateDepartment($data);
            } else {
                $data = array(
                    'identifier' => $_POST['identifier'],
                    'name' => $_POST['name']
                );
                $result = saveDepartment($data);
                $result = json_decode($result,true);
                if (!$result['success']) {
                    $alert = trim($result['data'],'"');
                } else {
                    $alert = "";
                }
            }            
        }                            
    }

    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = $_GET['id'];
            $result = deleteDepartment($id);
        }
    }

    $data = json_decode($department->showDepartments(),true);
    $data = json_decode($data['data'],true);
    // print_r($data);
    ?>

    <?php include 'header.php'?>

        <div class="container mt-4">
            <h2>Εισαγωγή νέας διεύθυνσης</h2>

            
                <?php 
                    if (isset($alert) && !empty($alert)): 
                ?>
                    <div class="alert alert-danger col-md-4">
                        <?php echo $alert; ?>
                    </div>
                <?php 
                    endif;
                ?>


            <p><span class="text-danger">* required field</span></p>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div class="mb-3 col-md-5">
                    <label for="identifier" class="form-label">Identifier: </label>
                    <input type="text" class="form-control" id="identifier" name="identifier" aria-describedby="identifier of department" value="<?php echo $identifier;?>">
                    <span class="text-danger">* <?php echo $identifierErr;?></span>
                </div>
                <div class="mb-3 col-md-5">
                    <label for="name" class="form-label">Name: </label>
                    <input type="text" name="name" class="form-control" id="name" value="<?php echo $name;?>">
                    <span class="text-danger">* <?php echo $nameErr;?></span> 
                </div>
                <input type="hidden" name="id" id="id">
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>



            <hr>
            <table class="mytable table table-bordered">
                <tr>
                    <th>Διεύθυνση</th>
                    <th>Αναγνωριστικό</th>
                    <th>Τμήματα</th>
                    <th>Κατηγορίες</th>
                    <th>Διαδικασίες</th>
                </tr>
                <?php
                    foreach ($data as $value) {
                        echo "<tr>";
                            echo "<td>".$value['name']."</td>";
                            echo "<td>".$value['identifier']."</td>";
                            echo "<td>";
                                foreach ($value['subdepartment'] as $valueXS) {
                                    echo $valueXS['name']."<br>";
                                }
                            echo "</td>";
                            echo "<td>";
                                foreach ($value['categories'] as $valueXS) {
                                    echo $valueXS['name'].'<br>';
                                }
                            echo "</td>";
                            echo "<td>";
                ?>
                            <button class="btn btn-primary btn-sm" onclick="loadform(<?php echo '\''.$value['_id']['$oid'].'\',\''.$value['name'].'\',\''.$value['identifier'].'\''?>)">Update</button>
                            <form method="delete" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                <input type="hidden" name="id" value="<?php echo $value['_id']['$oid'];?>">
                                <input type="submit" class="btn btn-danger btn-sm" name="submit" value="Delete">
                            </form>
                <?php
                            echo "</td>";
                        echo "</tr>";
                    }   
                ?>
            </table>
        </div>
        <script>
            function loadform(id,name,identifier) {
                // console.log(id, name ,identifier)
                $('#name').val(name);
                $('#identifier').val(identifier);
                $('#id').val(id);
            }
        </script>
<?php include 'footer.php' ?>