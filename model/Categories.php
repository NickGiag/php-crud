<?php 

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');

use OpenApi\Annotations as OA;

class Categories {

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
     *   path="/categories/list",
     *   description="List departments",
     *   operationId="showCategories",
     *   tags={"Categories"},
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
    public function showCategories($id) {
        if( isset( $id )) {
            try {
                $result = $this->collection->findOne(
                    [ '_id'=>new MongoDB\BSON\ObjectId($id) ],
                    [
                        'projection' => [
                            'categories' => 1
                        ],
                    ]);
                if (count($result)>0):
                    return $this->generalFunctions->returnValue($result,true);
                else:
                    return $this->generalFunctions->returnValue("",false);
                endif;
            }
            catch (MongoDB\Exception\UnsupportedException $e){
                error_log("Problem in findOne categories \n".$e);
                return $this->generalFunctions->returnValue("",false);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in findOne categories \n".$e);
                return $this->generalFunctions->returnValue("",false);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in findOne categories \n".$e);
                return $this->generalFunctions->returnValue("",false);
            }
        } else 
            return $this->generalFunctions->returnValue("",false); 
    }

   
    public function createCategories($data) {
        $identifier = $data->identifier;
        $subdepartment_id = $data->subdepartment_id;
        $name = $data->name;
        if( isset( $identifier ) && isset($name) && isset($subdepartment_id) ) {
            try {
                $result = $this->collection->updateOne( 
                    [ 'identifier'=>$identifier ],
                    [ 
                        '$push' => [
                            'categories' => [
                                '_id' => new MongoDB\BSON\ObjectId(),
                                'subdepartment_id' => new MongoDB\BSON\ObjectId($subdepartment_id), 
                                'name' => $name,                            
                            ]
                        ]
                    ]
                );
                if ($result->getModifiedCount()==1)
                        return $this->generalFunctions->returnValue("",true);
                    else 
                        return $this->generalFunctions->returnValue("",false);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in insert categories \n".$e);
                return $this->generalFunctions->returnValue("",false);
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in insert categories \n".$e);
                return $this->generalFunctions->returnValue("",false);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in insert categories \n".$e);
                return $this->generalFunctions->returnValue("",false);
            };
        } else 
            return $this->generalFunctions->returnValue(false);
    }

   
    public function deleteCategories($identifier,$id) {
        if( isset( $identifier ) && isset($id)) {
            try {
                $result = $this->collection->updateOne( 
                    [ 'identifier'=>intval($identifier) ],
                    [ 
                        '$pull' => [
                            'categories' => [
                                '_id' => new MongoDB\BSON\ObjectId($id)
                            ]
                        ]
                    ]
                );
                if ($result->getModifiedCount()==1)
                    return $this->generalFunctions->returnValue("",true);
                else 
                    return $this->generalFunctions->returnValue("",false);
            }
            catch (MongoDB\Exception\UnsupportedException $e){
                error_log("Problem in delete categories \n".$e);
                return $this->generalFunctions->returnValue("",false);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in delete categories \n".$e);
                return $this->generalFunctions->returnValue("",false);
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in delete categories \n".$e);
                return $this->generalFunctions->returnValue("",false);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in delete categories \n".$e);
                return $this->generalFunctions->returnValue("",false);
            };
        } else
            return $this->generalFunctions->returnValue("",false);    
    }

    
    public function updateCategories($data) {
        $identifier = $data->identifier;
        $id = $data->id;
        $name = $data->name;
       
        if( isset( $identifier ) && isset($name) && isset($id)) {
            try {
                $result = $this->collection->updateOne( 
                    [ 
                        'identifier' => $identifier,
                        'categories._id' => new MongoDB\BSON\ObjectId($id)
                    ],
                    [ '$set' => [ 'categories.$.name' => $name ]]
                );
                if ($result->getModifiedCount()==1)
                    return $this->generalFunctions->returnValue("",true);
                else 
                    return $this->generalFunctions->returnValue("",false);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in update categories \n".$e);
                return $this->generalFunctions->returnValue("",false);
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in update categories \n".$e);
                return $this->generalFunctions->returnValue("",false);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in update categories \n".$e);
                return $this->generalFunctions->returnValue("",false);
            };
        } else
            return $this->generalFunctions->returnValue("",false);
    }
}
?>