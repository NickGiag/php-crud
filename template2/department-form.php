<?php include 'header-scripts.php' ?>

<?php 
    function saveDepartment($data) {
        global $department;

        $datatosave = json_decode(json_encode($data));
        $result = $department->createDepartment($datatosave);
        return $result;
    }

    $nameErr = $identifierErr = "";
    $name = $identifier = "";

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

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
            $data = array(
                'identifier' => $_POST['identifier'],
                'name' => $_POST['name']
            );
            $result = saveDepartment($data);
        }                           
    }

    $data = json_decode($department->showDepartments(),true);
    $data = json_decode($data['data'],true);
    // print_r($data);
    ?>

    <?php include 'header.php'?>

        <div class="container mt-4">
            <h2>Εισαγωγή νέας διεύθυνσης</h2>
            <p><span class="text-danger">* required field</span></p>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div class="mb-3 col-md-5">
                    <label for="exampleInputEmail1" class="form-label">Identifier: </label>
                    <input type="text" class="identifier form-control" id="exampleInputEmail1" aria-describedby="emailHelp" value="<?php echo $identifier;?>">
                    <span class="text-danger">* <?php echo $identifierErr;?></span>
                </div>
                <div class="mb-3 col-md-5">
                    <label for="exampleInputPassword1" class="form-label">Name: </label>
                    <input type="text" name="name" class="form-control" id="name" value="<?php echo $name;?>">
                    <span class="text-danger">* <?php echo $nameErr;?></span> 
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>



            <hr>
            <table class="mytable table table-bordered">
                <tr>
                    <th>Διεύθυνση</th>
                    <th>Αναγνωριστικό</th>
                    <th>Τμήματα</th>
                    <th>Κατηγορίες</th>
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
                        echo "</tr>";
                    }   
                ?>
            </table>
        </div>

<?php include 'footer.php' ?>