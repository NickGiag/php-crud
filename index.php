<?php 

require __DIR__.'/vendor/autoload.php'; // include Composer's autoloader

// Uncomment for localhost running
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

include 'connect.php';

include 'model/Department.php';
include 'model/Subdepartment.php';
include 'model/Categories.php';
include 'model/UserCategory.php';
include 'model/User.php';
include 'model/Roles.php';
include 'model/Subscription.php';
include 'model/Announcement.php';

include "helper_files/GeneralFunctions.php";

include "jwt_authenticate.php";

$MDB_USER = $_ENV['MDB_USER'];
$MDB_PASS = $_ENV['MDB_PASS'];
$ATLAS_CLUSTER_SRV = $_ENV['ATLAS_CLUSTER_SRV'];

$SECRET_KEY = $_ENV['SECRET_KEY'];
$ISSUER_CLAIM = $_ENV['ISSUER_CLAIM'];

$jwt = new JWTAuthentication($SECRET_KEY, $ISSUER_CLAIM);

$connection = new Connection($MDB_USER, $MDB_PASS, $ATLAS_CLUSTER_SRV);
$generalFunctions = new GeneralFunctions();

$department = new Department($connection);
$subdepartment = new Subdepartment($connection);
$categories = new Categories($connection);
$usercategory = new UserCategory($connection);
$user = new User($connection);
$roles = new Roles($connection);
$subscription = new Subscription($connection);
$announcement = new Announcement($connection);

// Use this namespace
use Steampixel\Route;

// JWT Authentication
use \Firebase\JWT\JWT;


// =====================================//
//       Add Routes for Department      //
// =====================================//

Route::add('/department/list', function() {
    global $department;
    global $jwt;

    $result = $jwt->validateJWT();
    $checkvalidation = json_decode($result);
 
    if ($checkvalidation) {
        $result = $department->showDepartments(); 
        return $result;
    } else {
        return $result;
    }
});

Route::add('/department/(.*)/list', function($id) {
    global $department;
       
    $result = $department->showDepartment($id); 
    return $result;
});

Route::add('/department/create', function() {
    global $department;

    // Get the JSON contents
    $json = file_get_contents('php://input');
    
    if(isset($json) && !empty($json))  {
        // decode the json data
        $data = json_decode($json);
        $result = $department->createDepartment($data);
        return $result;
    }
    else {
        error_log("Error in create department");
        return false;
    }
},'post');

Route::add('/department/update', function() {
    global $department;

    // Get the JSON contents
    $json = file_get_contents('php://input');
    
    if(isset($json) && !empty($json))  {
        // decode the json data
        $data = json_decode($json);
        $result = $department->updateDepartment($data);
        return $result;
    }
    else {
        error_log("Error in update department");
        return false;
    }
},'patch');

Route::add('/department/(.*)/delete', function($id) {
    global $department;
    
    $result = $department->deleteDepartment($id);
    return $result;

},'delete');

// ========================================//
//       Add Routes for Subdepartment      //
// ========================================//

Route::add('/subdepartment/(.*)/list', function($id) {
    global $subdepartment;

    $result = $subdepartment->showSubdepartment($id); 
    return $result;
});

Route::add('/subdepartment/create', function() {
    global $subdepartment;

    // Get the JSON contents
    $json = file_get_contents('php://input');
    
    if(isset($json) && !empty($json))  {
        // decode the json data
        $data = json_decode($json);
        $result = $subdepartment->createSubdepartment($data);
        return $result;
    }
    else {
        error_log("Error in create subdepartment");
        return false;
    }
},'post');

Route::add('/subdepartment/update', function() {
    global $subdepartment;

    // Get the JSON contents
    $json = file_get_contents('php://input');
    
    if(isset($json) && !empty($json))  {
        // decode the json data
        $data = json_decode($json);
        $result = $subdepartment->updateSubdepartment($data);
        return $result;
    }
    else {
        error_log("Error in update subdepartment");
        return false;
    }
},'patch');

Route::add('/subdepartment/(.*)/(.*)/delete', function($identifier,$id) {
    global $subdepartment;
    
    $result = $subdepartment->deleteSubdepartment($identifier,$id);
    return $result;

},'delete');

// ===============================//
//     Add Routes for Categories  //
// ===============================//

Route::add('/categories/(.*)/list', function($id) {
    global $categories;

    $result = $categories->showCategories($id); 
    return $result;
});

Route::add('/categories/create', function() {
    global $categories;

    // Get the JSON contents
    $json = file_get_contents('php://input');
    
    if(isset($json) && !empty($json))  {
        // decode the json data
        $data = json_decode($json);
        $result = $categories->createCategories($data);
        return $result;
    }
    else {
        error_log("Error in create categories ");
        return false;
    }
},'post');

Route::add('/categories/update', function() {
    global $categories;

    // Get the JSON contents
    $json = file_get_contents('php://input');
    
    if(isset($json) && !empty($json))  {
        // decode the json data
        $data = json_decode($json);
        $result = $categories->updateCategories($data);
        return $result;
    }
    else {
        error_log("Error in update categories");
        return false;
    }
},'patch');

Route::add('/categories/(.*)/(.*)/delete', function($identifier,$id) {
    global $categories;
    
    $result = $categories->deleteCategories($identifier,$id);
    return $result;

},'delete');

// ========================================//
// Add Routes for User_category Collection //
// ========================================//

Route::add('/usercategory/list', function() {
    global $usercategory;

    $result = $usercategory->showUsercategories(); 
    return $result;
});

Route::add('/usercategory/(.*)/list', function($id) {
    global $usercategory;

    $result = $usercategory->showUsercategory($id); 
    return $result;
});

Route::add('/usercategory/create', function() {
    global $usercategory;

    // Get the JSON contents
    $json = file_get_contents('php://input');
    
    if(isset($json) && !empty($json))  {
        // decode the json data
        $data = json_decode($json);
        $result = $usercategory->createUsercategory($data);
        return $result;
    }
    else {
        error_log("Error in create user category");
        return false;
    }
},'post');

Route::add('/usercategory/update', function() {
    global $usercategory;

    // Get the JSON contents
    $json = file_get_contents('php://input');
    
    if(isset($json) && !empty($json))  {
        // decode the json data
        $data = json_decode($json);
        $result = $usercategory->updateUsercategory($data);
        return $result;
    }
    else {
        error_log("Error in update user category");
        return false;
    }
},'patch');

Route::add('/usercategory/(.*)/delete', function($id) {
    global $usercategory;
    
    $result = $usercategory->deleteUsercategory($id);
    return $result;

},'delete');


// ========================================//
//      Add Routes for User Collection     //
// ========================================//

Route::add('/user/list', function() {
    global $user;

    $result = $user->showUsers(); 
    return $result;
});

Route::add('/user/(.*)/list', function($id) {
    global $user;

    $result = $user->showUser($id); 
    return $result;
});

Route::add('/user/create', function() {
    global $user;

    // Get the JSON contents
    $json = file_get_contents('php://input');
    
    if(isset($json) && !empty($json))  {
        // decode the json data
        $data = json_decode($json);
        $result = $user->createUser($data);
        return $result;
    }
    else {
        error_log("Error in create user");
        return false;
    }
},'post');

Route::add('/user/update', function() {
    global $user;

    // Get the JSON contents
    $json = file_get_contents('php://input');
    
    if(isset($json) && !empty($json))  {
        // decode the json data
        $data = json_decode($json);
        $result = $user->updateUser($data);
        return $result;
    }
    else {
        error_log("Error in update user");
        return false;
    }
},'patch');

Route::add('/user/(.*)/delete', function($id) {
    global $user;
    
    $result = $user->deleteUser($id);
    return $result;

},'delete');

Route::add('/user/login', function() {
    global $user;

    // Get the JSON contents
    $json = file_get_contents('php://input');
    
    if(isset($json) && !empty($json))  {
        // decode the json data
        $data = json_decode($json);
        $result = $user->loginUser($data);
        $token = json_decode($result);

        if ($token->success) {
            $result = $jwt->createJWT($token->username,$token->permission,$token->authorizations);
            return $result;
        } else {
            return $result;
        }

        
    }
    else {
        error_log("Error in login user");
        return false;
    }
},'post');

// ========================================//
//         Add Routes for Roles            //
// ========================================//

Route::add('/roles/(.*)/list', function($id) {
    global $roles;

    $result = $roles->showRoles($id); 
    return $result;
});

Route::add('/roles/create', function() {
    global $roles;

    // Get the JSON contents
    $json = file_get_contents('php://input');
    
    if(isset($json) && !empty($json))  {
        // decode the json data
        $data = json_decode($json);
        $result = $roles->createRoles($data);
        return $result;
    }
    else {
        error_log("Error in create roles");
        return false;
    }
},'post');

Route::add('/roles/update', function() {
    global $roles;

    // Get the JSON contents
    $json = file_get_contents('php://input');
    
    if(isset($json) && !empty($json))  {
        // decode the json data
        $data = json_decode($json);
        $result = $roles->updateRoles($data);
        return $result;
    }
    else {
        error_log("Error in update roles");
        return false;
    }
},'patch');

Route::add('/roles/(.*)/(.*)/delete', function($userid,$roleid) {
    global $roles;
    
    $result = $roles->deleteRoles($userid,$roleid);
    return $result;

},'delete');

// ========================================//
//      Add Routes for Subscription        //
// ========================================//

Route::add('/subscription/(.*)/list', function($id) {
    global $subscription;

    $result = $subscription->showSubscription($id); 
    return $result;
});

Route::add('/subscription/create', function() {
    global $subscription;

    // Get the JSON contents
    $json = file_get_contents('php://input');
    
    if(isset($json) && !empty($json))  {
        // decode the json data
        $data = json_decode($json);
        $result = $subscription->createSubscription($data);
        return $result;
    }
    else {
        error_log("Error in create subscription");
        return false;
    }
},'post');

// ========================================//
//  Add Routes for Announcement Collection //
// ========================================//

Route::add('/announcements/list', function() {
    global $announcement;

    $result = $announcement->showAnnouncements(); 
    return $result;
});

Route::add('/announcements/(.*)/list', function($id) {
    global $announcement;

    $result = $user->showAnnouncement($id); 
    return $result;
});

Route::add('/announcements/create', function() {
    global $announcement;

    // Get the JSON contents
    $json = file_get_contents('php://input');
    
    if(isset($json) && !empty($json))  {
        // decode the json data
        $data = json_decode($json);
        $result = $announcement->createAnnouncement($data);
        return $result;
    }
    else {
        error_log("Error in create announcement");
        return false;
    }
},'post');

Route::add('/announcements/update', function() {
    global $announcement;

    // Get the JSON contents
    $json = file_get_contents('php://input');
    
    if(isset($json) && !empty($json))  {
        // decode the json data
        $data = json_decode($json);
        $result = $announcement->updateAnnouncement($data);
        return $result;
    }
    else {
        error_log("Error in update announcement");
        return false;
    }
},'patch');

Route::add('/announcements/(.*)/delete', function($id) {
    global $announcement;
    
    $result = $announcement->deleteAnnouncement($id);
    return $result;

},'delete');

// Run the router
Route::run('/');

?>