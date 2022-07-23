<?php 

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');

use OpenApi\Annotations as OA;

class Department {

    protected $collection;

    protected $generalFunctions;

    public function __construct($connection) {
        try {
            $this->collection = $connection->connect_to_department();
            error_log("Connection to collection Department");
            $this->generalFunctions = new GeneralFunctions();
        }
        catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            error_log("Problem in connection with collection Department".$e);
        }
    }
    
     /**
     * @OA\Get(
     *   path="/department/list",
     *   description="List departments",
     *   operationId="showDepartments",
     *   tags={"Department"},
     *   @OA\Response(
     *     response="200",
     *     description="A list with departments"
     *   ),
     *   @OA\Response(
     *     response="404",
     *     description="Error"
     *   )
     * )
     */
    public function showDepartments() {
        try {
            $result = $this->collection->find()->toArray();
            if (count($result)>0):
                return $this->generalFunctions->returnValue($result,true);
            else:
                return $this->generalFunctions->returnValue("Problem in Department: query is empty",false);
            endif;
        }
        catch (MongoDB\Exception\UnsupportedException $e){
            error_log("Problem in find departments \n".$e);
            return $this->generalFunctions->returnValue("Unsupported mongoDB exception: ".$e, false);
        }
        catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
            error_log("Problem in find departments \n".$e);
            return $this->generalFunctions->returnValue("Invalid Argument mongoDB exception: ".$e, false);
        }
        catch (MongoDB\Driver\Exception\RuntimeException $e){
            error_log("Problem in find departments \n".$e);
            return $this->generalFunctions->returnValue("Runtime mongoDB exception: ".$e, false);
        };
    }

    /**
     * @OA\Get(
     *   path="/department/{id}/list",
     *   description="List a department",
     *   operationId="showDepartment",
     *   tags={"Department"},
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
     *      description="This is the mongo id of the department that we will return",
     *      required=true,
     *      @OA\Schema(
     *          type="string",
     *          example="6250932b62a9e94948207113"
     *       ),
     *     ),
     *   @OA\Response(
     *     response="200",
     *     description="Returns a department"
     *   ),
     *   @OA\Response(
     *     response="404",
     *     description="Error"
     *   )
     * )
     */
    public function showDepartment($id) {
        if( isset( $id )) {
            try {
                $result = $this->collection->findOne([
                    '_id'=>new MongoDB\BSON\ObjectId($id)
                ]);
                if ($result):
                    return $this->generalFunctions->returnValue($result, true);
                else:
                    return $this->generalFunctions->returnValue("Problem in Department: query is empty",false);
                endif;
            }
            catch (MongoDB\Exception\UnsupportedException $e){
                error_log("Problem in find departments \n".$e);
                return $this->generalFunctions->returnValue("Unsupported mongoDB exception: ".$e, false);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in find departments \n".$e);
                return $this->generalFunctions->returnValue("Invalid Argument mongoDB exception: ".$e, false);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in find departments \n".$e);
                return $this->generalFunctions->returnValue("Runtime mongoDB exception: ".$e, false);
            };
        } else 
            return $this->generalFunctions->returnValue("Problem in Department: no id received",false); 
    }

    /**
     * @OA\Post(
     *     path="/department/create",
     *     description="Create a department",
     *     operationId="createDepartment",
     *     tags={"Department"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="identifier",type="integer"),
     *                 @OA\Property(property="name",type="string"),
     *                 example={"identifier": 1, "name": "Διεύθυνση Σπουδών"}
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
    public function createDepartment($data) {
        $identifier = $data->identifier;
        $name = $data->name;

        if( isset( $identifier ) && isset($name)) {
            try {
                $result = $this->collection->insertOne( [
                    'identifier' => intval($identifier), 
                    'name' => $name,
                    'subdepartment' => [],
                    'categories' => [] 
                ] );
                if ($result->getInsertedCount()==1)
                    return $this->generalFunctions->returnValue("Department created",true);
                else 
                    return $this->generalFunctions->returnValue("Problem in creating department",false);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in insert department \n".$e);
                return $this->generalFunctions->returnValue("Invalid Argument mongoDB exception: ".$e,false);
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in insert department \n".$e);
                return $this->generalFunctions->returnValue("Bulk Write mongoDB exception: ".$e,false);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in insert department \n".$e);
                return $this->generalFunctions->returnValue("Runtime mongoDB exception: ".$e,false);
            };
        } else 
            return $this->generalFunctions->returnValue("Problem in Department: wrong info received",false); 
    }

     /**
     * @OA\Delete(
     *     path="/department/{id}/delete",
     *     description="Delete a department",
     *     operationId="deleteDepartment",
     *     tags={"Department"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Department mongo id to delete",
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
    public function deleteDepartment($id) {
        if (isset( $id )){
            try {
                $result = $this->collection->deleteOne([
                    '_id'=>new MongoDB\BSON\ObjectId($id)
                ]);
                if ($result->getDeletedCount()==1)
                    return $this->generalFunctions->returnValue("Department deleted",true);
                else 
                    return $this->generalFunctions->returnValue("Problem in deleting department",false);
            }
            catch (MongoDB\Exception\UnsupportedException $e){
                error_log("Problem in delete department \n".$e);
                return $this->generalFunctions->returnValue("Unsupported mongoDB exception: ".$e,false);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in delete department \n".$e);
                return $this->generalFunctions->returnValue("Invalid Argument mongoDB exception: ".$e,false);
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in delete department \n".$e);
                return $this->generalFunctions->returnValue("Bulk Write mongoDB exception: ".$e,false);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in delete department \n".$e);
                return $this->generalFunctions->returnValue("Runtime mongoDB exception: ".$e,false);
            }
        } else 
            return $this->generalFunctions->returnValue("Problem in Department: no id received",false);
    }

     /**
     * @OA\Patch(
     *     path="/department/update",
     *     description="Update a department",
     *     operationId="updateDepartment",
     *     tags={"Department"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="_id",type="string"),
     *                 @OA\Property(property="identifier",type="integer"),
     *                 @OA\Property(property="name",type="string"),
     *                 example={"_id":"6244840de0c3d34f620e5dd6", "identifier": 1, "name": "Διεύθυνση Σπουδών"}
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
    public function updateDepartment($data) {
        $id = $data->_id;
        $identifier = $data->identifier;
        $name = $data->name;
        if( isset( $id ) && isset( $identifier ) && isset($name)) {
            try {
                $result = $this->collection->updateOne( 
                    [ '_id' => new MongoDB\BSON\ObjectId($id) ],
                    [ '$set' => [
                            'identifier' => $identifier,
                            'name' => $name 
                        ]
                    ]
                );
                if ($result->getModifiedCount()==1)
                    return $this->generalFunctions->returnValue("Department updated",true);
                else 
                    return $this->generalFunctions->returnValue("Problem in creating department",false);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in update department \n".$e);
                return $this->generalFunctions->returnValue("Invalid Argument mongoDB exception: ".$e,false);
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in update department \n".$e);
                return $this->generalFunctions->returnValue("Bulk Write mongoDB exception: ".$e,false);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in update department \n".$e);
                return $this->generalFunctions->returnValue("Runtime mongoDB exception: ".$e,false);
            };
        } else 
            return $this->generalFunctions->returnValue("Problem in Department: wrong info received",false);
    }

    private function returnValue($result, $value){
        if ($value==='true')
            return json_encode(array(
                'data' => json_encode($result),
                'success' => true
                )
            );
        else 
            return json_encode(array('success' => false));
    }
}
?>