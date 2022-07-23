<?php 

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');

use OpenApi\Annotations as OA;

class UserCategory {

    protected $collection;

    protected $generalFunctions; 

    public function __construct($connection) {
        try {
            $this->collection = $connection->connect_to_user_category();
            error_log("Connection to collection User_category");
            $this->generalFunctions = new GeneralFunctions();
        }
        catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            error_log("Problem in connection with collection User_category".$e);
        }
    }
        
    /**
     * @OA\Get(
     *   path="/usercategory/list",
     *   description="List departments",
     *   operationId="showUsercategories",
     *   tags={"UserCategory"},
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
    public function showUsercategories() {
        try {
            $result = $this->collection->find()->toArray();
            if (count($result)>0):
                return $this->generalFunctions->returnValue($result, true);
            else:
                return $this->generalFunctions->returnValue("Problem in UserCategories: query is empty",false);
            endif;
        }
        catch (MongoDB\Exception\UnsupportedException $e){
            error_log("Problem in find user categories \n".$e);
            return $this->generalFunctions->returnValue("Unsupported mongoDB exception: ".$e,false);
        }
        catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
            error_log("Problem in find user categories \n".$e);
            return $this->generalFunctions->returnValue("Invalid Argument mongoDB exception".$e,false);
        }
        catch (MongoDB\Driver\Exception\RuntimeException $e){
            error_log("Problem in find user categories \n".$e);
            return $this->generalFunctions->returnValue("Runtime mongoDB exception: ".$e,false);
        };
        
    }

  
    public function showUsercategory($id) {
        if( isset( $id )) {
            try {
                $result = $this->collection->findOne([
                    '_id'=>new MongoDB\BSON\ObjectId($id)
                ]);
                if ($result):
                    return $this->generalFunctions->returnValue($result,true);
                else:
                    return $this->generalFunctions->returnValue("Problem in UserCategories: query is empty",false);
                endif;
            }
            catch (MongoDB\Exception\UnsupportedException $e){
                error_log("Problem in findOne user category \n".$e);
                return $this->generalFunctions->returnValue("Unsupported mongoDB exception: ".$e,false);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in findOne user category \n".$e);
                return $this->generalFunctions->returnValue("Invalid Argument mongoDB exception".$e,false);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in findOne user category \n".$e);
                return $this->generalFunctions->returnValue("Runtime mongoDB exception: ".$e,false);
            };
        } else 
            return $this->generalFunctions->returnValue("Problem in Usercategories: no id received",false); 
    }

  
    public function createUsercategory($data) {
        $identifier = $data->identifier;
        $name = $data->name;

        if( isset( $identifier ) && isset($name)) {
            try {
                $result = $this->collection->insertOne( [ 
                    'identifier' => $identifier,
                    'name' => $name
                ] );
                if ($result->getInsertedCount()==1)
                    return $this->generalFunctions->returnValue("Usercategory created",true);
                else 
                    return $this->generalFunctions->returnValue("Problem in creating usercategory",false);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in insert user category \n".$e);
                return $this->generalFunctions->returnValue("Invalid Argument mongoDB exception".$e,false);
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in insert user category \n".$e);
                return $this->generalFunctions->returnValue("Bulk Write mongoDB exception: ".$e,false);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in insert user category \n".$e);
                return $this->generalFunctions->returnValue("Runtime mongoDB exception: ".$e,false);
            };
        } else 
            return $this->generalFunctions->returnValue("Problem in Usercategories: wrong info received",false);
    }

   
    public function deleteUsercategory($id) {
        if (isset( $id )){
            try {
                $result = $this->collection->deleteOne([
                    '_id'=>new MongoDB\BSON\ObjectId($id)
                ]);
                if ($result->getDeletedCount()==1)
                    return $this->generalFunctions->returnValue("User category deleted",true);
                else 
                    return $this->generalFunctions->returnValue("Problem in deleting usercategory",false);
            }
            catch (MongoDB\Exception\UnsupportedException $e){
                error_log("Problem in delete user category \n".$e);
                return $this->generalFunctions->returnValue("Unsupported mongoDB exception: ".$e,false);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in delete user category \n".$e);
                return $this->generalFunctions->returnValue("Invalid Argument mongoDB exception".$e,false);
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in delete user category \n".$e);
                return $this->generalFunctions->returnValue("Bulk Write mongoDB exception: ".$e,false);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in delete user category \n".$e);
                return $this->generalFunctions->returnValue("Runtime mongoDB exception: ".$e,false);
            };
        } else 
            return $this->generalFunctions->returnValue("Problem in Usercategories: no id received",false);
    }

    
    public function updateUsercategory($data) {
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
                    return $this->generalFunctions->returnValue("Usercategory updated",true);
                else 
                    return $this->generalFunctions->returnValue("Problem in updating usercategory",false);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in update user category \n".$e);
                return $this->generalFunctions->returnValue("Invalid Argument mongoDB exception".$e,false);
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in update user category \n".$e);
                return $this->generalFunctions->returnValue("Bulk Write mongoDB exception: ".$e,false);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in update user category \n".$e);
                return $this->generalFunctions->returnValue("Runtime mongoDB exception: ".$e,false);
            };
        } else 
            return $this->generalFunctions->returnValue("Problem in Usercategories: wrong info received",false);
    }

    private function returnValue($result, $value){
        if ($value===true)
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