<?php 

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');

use \Firebase\JWT\JWT;

use OpenApi\Annotations as OA;

class User {

    protected $collection;

    public function __construct($connection) {
        try {
            $this->collection = $connection->connect_to_user();
            error_log("Connection to collection User");
        }
        catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            error_log("Problem in connection with collection User".$e);
        }
    }
    
    /**
     * @OA\Get(
     *   path="/user/list",
     *   description="List users",
     *   operationId="showUsers",
     *   tags={"User"},
     *   @OA\Response(
     *     response="200",
     *     description="A list with users"
     *   ),
     *   @OA\Response(
     *     response="404",
     *     description="Error"
     *   )
     * )
     */
    public function showUsers() {
        try {
            $result = $this->collection->find()->toArray();
            if (count($result)>0):
                return json_encode($result);
            else:
                return $this->returnValue('false');
            endif;
        }
        catch (MongoDB\Exception\UnsupportedException $e){
            error_log("Problem in find users \n".$e);
            return $this->returnValue('false');
        }
        catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
            error_log("Problem in find users \n".$e);
            return $this->returnValue('false');
        }
        catch (MongoDB\Driver\Exception\RuntimeException $e){
            error_log("Problem in find users \n".$e);
            return $this->returnValue('false');
        };
    }

    /**
     * @OA\Get(
     *   path="/user/{id}/list",
     *   description="List a user",
     *   operationId="showUser",
     *   tags={"User"},
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
     *      description="User id to show",
     *      required=true,
     *      @OA\Schema(
     *          type="string",
     *          example="6250932b62a9e94948207113"
     *       ),
     *     ),
     *   @OA\Response(
     *     response="200",
     *     description="Returns a user"
     *   ),
     *   @OA\Response(
     *     response="404",
     *     description="Error"
     *   )
     * )
     */
    public function showUser($id) {
        if( isset( $id )) {
            try {
                $result = $this->collection->findOne([
                    '_id'=>new MongoDB\BSON\ObjectId($id)
                ]);
                if ($result):
                    return json_encode($result);
                else:
                    return $this->returnValue('false');
                endif;
            }
            catch (MongoDB\Exception\UnsupportedException $e){
                error_log("Problem in findOne user \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in findOne user \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in findOne user \n".$e);
                return $this->returnValue('false');
            }
        } else 
            return $this->returnValue('false'); 
    }

    /**
     * @OA\Post(
     *     path="/user/create",
     *     description="Create a user",
     *     operationId="createUser",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="username",type="string"),
     *                 @OA\Property(property="password",type="string"),
     *                 @OA\Property(property="user_category_identifier",type="string"),
     *                 @OA\Property(property="user_category_name",type="string"),
     *                 @OA\Property(property="name",type="string"),
     *                 @OA\Property(property="surname",type="string"),
     *                 @OA\Property(property="email",type="string"),
     *                 example={"username": "xxxxx", 
     *                          "password": "1234",
     *                          "user_category": {
     *                               "identifier":"1",
     *                               "name":"Προπτυχιακός Φοιτητής"
     *                          },
     *                          "name": "Kώστας",
     *                          "surname":"Τουρνάς",
     *                          "email":"xxxxx@aueb.gr"
     *                         },
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retuns a json object with true or false value to field success",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(type="boolean")
     *             },
     *             @OA\Examples(example="False bool", value={"success": false}, summary="A false boolean value."),
     *             @OA\Examples(example="True bool", value={"success": true}, summary="A true boolean value."),
     *         )
     *     )
     * )
     */
    public function createUser($data) {
        $username = $data->username;
        $password = $data->password;
        $user_category_identifier = $data->user_category->identifier;
        $user_category_name = $data->user_category->name;
        $name = $data->name;
        $surname = $data->surname;
        $email = $data->email;
        
        if( isset( $username ) && isset($password) && 
            isset($user_category_identifier) && isset($user_category_name) 
            && isset($name) && isset($surname) && isset($email) ) {
            try {
                $result = $this->collection->insertOne( [ 
                    'username' => $username,
                    'password' => password_hash($password, PASSWORD_BCRYPT),
                    'user_category' => [
                        'identifier' => $user_category_identifier,
                        'name' => $user_category_name
                    ],
                    'surname' => $surname,
                    'name' => $name,
                    'email' => $email,
                    'send_email' => false,
                    'verified' => false,
                    'roles' => [],
                    'subscription_list' => []
                ] );
                if ($result->getInsertedCount()==1)
                    return $this->returnValue('true');
                else 
                    return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in insert user category \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in insert user category \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in insert user category \n".$e);
                return $this->returnValue('false');
            };
        } else 
            return $this->returnValue('false');
    }

    /**
     * @OA\Delete(
     *     path="/user/{id}/delete",
     *     description="Delete a User",
     *     operationId="deleteUser",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User id to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="6250932b62a9e94948207113"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retuns a json object with true or false value to field success",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(type="boolean")
     *             },
     *             @OA\Examples(example="False bool", value={"success": false}, summary="A false boolean value."),
     *             @OA\Examples(example="True bool", value={"success": true}, summary="A true boolean value."),
     *         )
     *     )
     * )
     */
    public function deleteUser($id) {
        if (isset( $id )){
            try {
                $result = $this->collection->deleteOne([
                    '_id'=>new MongoDB\BSON\ObjectId($id)
                ]);
                if ($result->getDeletedCount()==1)
                    return $this->returnValue('true');
                else 
                    return $this->returnValue('false');
            }
            catch (MongoDB\Exception\UnsupportedException $e){
                error_log("Problem in delete user \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in delete user \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in delete user \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in delete user \n".$e);
                return $this->returnValue('false');
            };
        } else 
            return $this->returnValue('false');
    }

    /**
     * @OA\Patch(
     *     path="/user/update",
     *     description="Update a User",
     *     operationId="updateUser",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="_id",type="string"),
     *                 @OA\Property(property="username",type="string"),
     *                 @OA\Property(property="user_category_identifier",type="string"),
     *                 @OA\Property(property="user_category_name",type="string"),
     *                 @OA\Property(property="name",type="string"),
     *                 @OA\Property(property="surname",type="string"),
     *                 @OA\Property(property="email",type="string"),
     *                 example={"_id":"6244840de0c3d34f620e5dd6",
     *                          "username": "cv20999", 
     *                          "user_category_identifier":"1",
     *                          "user_category_name":"Προπτυχιακός Φοιτητής",
     *                          "name": "Kώστας",
     *                          "surname":"Τουρνάς",
     *                          "email":"cv20999@aueb.gr"
     *                         }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retuns a json object with true or false value to field success",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(type="boolean")
     *             },
     *             @OA\Examples(example="False bool", value={"success": false}, summary="A false boolean value."),
     *             @OA\Examples(example="True bool", value={"success": true}, summary="A true boolean value."),
     *         )
     *     )
     * )
     */
    public function updateUser($data) {
        $id = $data->_id;
        $username = $data->username;
        $user_category_identifier = $data->user_category->identifier;
        $user_category_name = $data->user_category->name;
        $name = $data->name;
        $surname = $data->surname;
        $email = $data->email;
        $send_email = $data->send_email;
        $verified = $data->verified;

        if( isset( $id ) && isset( $username ) && isset($password) && 
            isset($user_category_identifier) && isset($user_category_name) && 
            isset($name) && isset($surname) && isset($email)) {
            try {
                $result = $this->collection->updateOne( 
                    [ '_id' => new MongoDB\BSON\ObjectId($id) ],
                    [ '$set' => [
                            'username' => $username,
                            'user_category' => [
                                'identifier' => $user_category_identifier,
                                'name' => $user_category_name
                            ],
                            'surname' => $surname,
                            'name' => $name,
                            'send_email' => $send_email,
                            'verified' => $verified
                        ]
                    ]
                );
                if ($result->getModifiedCount()==1)
                    return $this->returnValue('true');
                else 
                    return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in update user \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in update user \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in update user \n".$e);
                return $this->returnValue('false');
            };
        } else 
            return $this->returnValue('false');
    }

    /**
     * @OA\Post(
     *     path="/user/login",
     *     description="login a user",
     *     operationId="loginUser",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="username",type="string"),
     *                 @OA\Property(property="password",type="string"),
     *                example={"username": "akosta", "password": "1234"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retuns a json object with true or false value to field success",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(type="boolean")
     *             },
     *             @OA\Examples(example="False bool", value={"success": false}, summary="A false boolean value."),
     *             @OA\Examples(example="True bool", value={"success": true}, summary="A true boolean value."),
     *         )
     *     )
     * )
     */
    public function loginUser($data) {
        $username = $data->username;
        $password = $data->password;
        
        $findUser = $this->collection->findOne([
            'username'=> $username
        ]);

        if( $findUser && isset( $password )){
            try {

                $roles = $findUser->roles;
                
                foreach ($roles as $key => $value) {
                    if ($value->app === "announcement"){
                        $permission = $value->permission;
                        $authorizations = $value->authorizations;
                    }
                }

                if (password_verify($password, $findUser->password)) {
                    $data = json_encode(array( 
                        'success' => true,
                        'username' => $username,
                        'permission' => $permission,
                        'authorizations' => $authorizations
                        ));

                    return $data;
                    //return $this->returnValue('true');
                }   
                else 
                    return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in update user \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in update user \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in update user \n".$e);
                return $this->returnValue('false');
            };
        } else 
            return $this->returnValue('false');
    }

    private function returnValue($value){
        if ($value==='true')
            return json_encode(array('success' => true));
        else 
            return json_encode(array('success' => false));
    }
}
?>