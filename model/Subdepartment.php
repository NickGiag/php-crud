<?php 

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');

class Subdepartment {

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
     *   path="/subdepartment/list",
     *   description="List departments",
     *   operationId="showSubdepartment",
     *   tags={"Subdepartment"},
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
    public function showSubdepartment($id) {
        if( isset( $id )) {
            try {
                $result = $this->collection->findOne(
                    [ '_id'=>new MongoDB\BSON\ObjectId($id) ],
                    [
                        'projection' => [
                            'subdepartment' => 1
                        ],
                    ]);
                if (count($result)>0):
                    return $this->generalFunctions->returnValue($result, true);
                else:
                    return $this->generalFunctions->returnValue("Problem in Subdepartment: query is empty",false);
                endif;
            }
            catch (MongoDB\Exception\UnsupportedException $e){
                error_log("Problem in findOne subdepartment \n".$e);
                return $this->generalFunctions->returnValue("Unsupported mongoDB exception: ".$e,false);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in findOne subdepartment \n".$e);
                return $this->generalFunctions->returnValue("Invalid Argument mongoDB exception".$e,false);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in findOne subdepartment \n".$e);
                return $this->generalFunctions->returnValue("Runtime mongoDB exception: ".$e,false);
            };
        } else 
            return $this->generalFunctions->returnValue("Problem in Subdepartment: wrong info received",false); 
    }

    public function createSubdepartment($data) {
        $identifier = $data->identifier;
        $name = $data->name;
        if( isset( $identifier ) && isset($name)) {
            try {
                $result = $this->collection->updateOne( 
                    [ 'identifier'=>intval($identifier) ],
                    [ 
                        '$push' => [
                            'subdepartment' => [
                                '_id' => new MongoDB\BSON\ObjectId(), 
                                'name' => $name,                            
                            ]
                        ]
                    ]
                );
                if ($result->getModifiedCount()==1)
                    return $this->generalFunctions->returnValue("Subdepartment created",true);
                else 
                    return $this->generalFunctions->returnValue("Problem creating subdepartment",false);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in insert subdepartment \n".$e);
                return $this->generalFunctions->returnValue("Invalid Argument mongoDB exception".$e,false);
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in insert subdepartment \n".$e);
                return $this->generalFunctions->returnValue("Bulk Write mongoDB exception: ".$e,false);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in insert subdepartment \n".$e);
                return $this->generalFunctions->returnValue("Runtime mongoDB exception: ".$e,false);
            };
        } else 
            return $this->generalFunctions->returnValue("Problem in Subdepartment: wrong info received",false);

    }

    public function deleteSubdepartment($identifier,$id) {
        if( isset( $identifier ) && isset($id)) {
            try {
                $result = $this->collection->updateOne( 
                    [ 'identifier'=>intval($identifier) ],
                    [ 
                        '$pull' => [
                            'subdepartment' => [
                                '_id' => new MongoDB\BSON\ObjectId($id)
                            ]
                        ]
                    ]
                );
                if ($result->getModifiedCount()==1)
                    return $this->generalFunctions->returnValue("Subdepartment deleted",true);
                else 
                    return $this->generalFunctions->returnValue("Problem in deleting ubdepartment",false);
            }
            catch (MongoDB\Exception\UnsupportedException $e){
                error_log("Problem in delete subdepartment \n".$e);
                return $this->generalFunctions->returnValue("Unsupported mongoDB exception: ".$e,false);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in delete subdepartment \n".$e);
                return $this->generalFunctions->returnValue("Invalid Argument mongoDB exception".$e,false);
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in delete subdepartment \n".$e);
                return $this->generalFunctions->returnValue("Bulk Write mongoDB exception: ".$e,false);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in delete subdepartment \n".$e);
                return $this->generalFunctions->returnValue("Runtime mongoDB exception: ".$e,false);
            };
        } else 
            return $this->generalFunctions->returnValue("Problem in Subdepartment: wrong info received",false);
    }

    public function updateSubdepartment($data) {
        $identifier = $data->identifier;
        $id = $data->_id;
        $name = $data->name;
        
        if( isset( $identifier ) && isset($name) && isset($id)) {
            try {
                $result = $this->collection->updateOne( 
                    [ 
                        'identifier' => intval($identifier),
                        'subdepartment._id' => new MongoDB\BSON\ObjectId($id)
                    ],
                    [ '$set' => [ 'subdepartment.$.name' => $name ]]
                );
                if ($result->getModifiedCount()==1)
                    return $this->generalFunctions->returnValue("Subdepartment updated",true);
                else 
                    return $this->generalFunctions->returnValue("Problem in updating subdepartment",false);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in update subdepartment \n".$e);
                return $this->generalFunctions->returnValue("Invalid Argument mongoDB exception".$e,false);
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in update subdepartment \n".$e);
                return $this->generalFunctions->returnValue("Bulk Write mongoDB exception: ".$e,false);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in update subdepartment \n".$e);
                return $this->generalFunctions->returnValue("Runtime mongoDB exception: ".$e,false);
            };
        } else 
            return $this->generalFunctions->returnValue("Problem in Subdepartment: wrong info received",false);
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