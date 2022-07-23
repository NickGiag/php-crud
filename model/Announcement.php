<?php 

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');

use OpenApi\Annotations as OA;

class Announcement {

    protected $collection;

    protected $generalFunctions;

    public function __construct($connection) {
        try {
            $this->collection = $connection->connect_to_announcement();
            error_log("Connection to collection Announcement");
            $this->generalFunctions = new GeneralFunctions();
        }
        catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            error_log("Problem in connection with collection Announcement".$e);
        }
    }
    
    /**
     * @OA\Get(
     *   path="/announcements/list",
     *   description="List announcements",
     *   operationId="showAnnouncements",
     *   tags={"Announcement"},
     *   @OA\Response(
     *     response="200",
     *     description="A list with announcements"
     *   ),
     *   @OA\Response(
     *     response="404",
     *     description="Error"
     *   )
     * )
     */
    public function showAnnouncements() {
        try {
            $result = $this->collection->find()->toArray();
            if (count($result)>0):
                return $this->generalFunctions->returnValue($result,true);
            else:
                return $this->generalFunctions->returnValue(false);
            endif;
        }
        catch (MongoDB\Exception\UnsupportedException $e){
            error_log("Problem in find announcements \n".$e);
            return $this->generalFunctions->returnValue(false);
        }
        catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
            error_log("Problem in find announcements \n".$e);
            return $this->generalFunctions->returnValue(false);
        }
        catch (MongoDB\Driver\Exception\RuntimeException $e){
            error_log("Problem in find users \n".$e);
            return $this->generalFunctions->returnValue(false);
        };
    }

    /**
     * @OA\Get(
     *   path="/announcements/{id}/list",
     *   description="List an announcement",
     *   operationId="showAnnouncement",
     *   tags={"Announcement"},
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
     *      description="User Category id to show",
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
    public function showAnnouncement($id) {
        if( isset( $id )) {
            try {
                $result = $this->collection->findOne([
                    '_id'=>new MongoDB\BSON\ObjectId($id)
                ]);
                if ($result):
                    return $this->generalFunctions->returnValue($result,true);
                else:
                    return $this->generalFunctions->returnValue(false);
                endif;
            }
            catch (MongoDB\Exception\UnsupportedException $e){
                error_log("Problem in findOne announcement \n".$e);
                return $this->generalFunctions->returnValue(false);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in findOne announcement \n".$e);
                return $this->generalFunctions->returnValue(false);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in findOne announcement \n".$e);
                return $this->generalFunctions->returnValue(false);
            }
        } else 
            return $this->generalFunctions->returnValue(false);
    }

    /**
     * @OA\Post(
     *     path="/announcements/create",
     *     description="Create an announcement",
     *     operationId="createAnnouncements",
     *     tags={"Announcement"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(property="department",type="object"),
     *                  @OA\Property(property="subdepartment",type="object"),
     *                  @OA\Property(property="category",type="object"),
     *                  @OA\Property(property="subject",type="string"),
     *                  @OA\Property(property="message",type="string"),
     *                  @OA\Property(property="period",type="object"),
     *                  example={
     *                      "department": {
     *                          "id":"1",
     *                          "name":"Διεύθυνση Τεχνικών Υπηρεσιών"
     *                      }, 
     *                      "subdepartment": {
     *                          "id":"6250932b62a9e94948207113",
     *                          "name":"Τμήμα Εκτέλεσης Έργων"
     *                      },
     *                      "category": {
     *                          "id":"6250932b62a9e94948207114",
     *                          "name":"Βλάβες"
     *                      },
     *                      "subject":"Διακοπή ρεύματος",
     *                      "message":"XXXXXXX",
     *                      "period": {
     *                          "start":"1/4/2022",
     *                          "finish":"20/4/2022"
     *                     },
     *                  }
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
    public function createAnnouncement($data) {
        $department_id = $data->department->id;
        $department_name = $data->department->name;
        $subdepartment_id = $data->subdepartment->id;
        $subdepartment_name = $data->subdepartment->name;
        $category_id = $data->category->id;
        $category_name = $data->category->name;
        $subject = $data->subject;
        $message = $data->message;
        $start = $data->period->start;
        $finish = $data->period->finish;
        $attachments = $data->attachments;
       
        if( isset( $department_id ) && isset($subdepartment_id) && isset($category_id) && isset($subject) && isset($message)) {
            try {
                $result = $this->collection->insertOne( [ 
                    'department' => [
                        'id' => $department_id,
                        'name' => $department_name
                    ],
                    'subdepartment' => [
                        'id' => $subdepartment_id,
                        'name' => $subdepartment_name
                    ],
                    'category' => [
                        'id' => $category_id,
                        'name' => $category_name
                    ],
                    'subject' => $subject,
                    'message' => $message,
                    'period' => [
                        'start' => $start,
                        'finish' => $finish
                    ],
                    'attachments' => $attachments
                ]);
                
                if ($result->getInsertedCount()==1)
                    return $this->generalFunctions->returnValue("",true);
                else 
                    return $this->generalFunctions->returnValue("",false);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in insert user announcement \n".$e);
                return $this->generalFunctions->returnValue("",false);
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in insert user announcement \n".$e);
                return $this->generalFunctions->returnValue("",false);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in insert user announcement \n".$e);
                return $this->generalFunctions->returnValue("",false);
            };
        } else 
            return $this->generalFunctions->returnValue("",false);
    }

    /**
     * @OA\Delete(
     *     path="/announcements/{id}/delete",
     *     description="Delete an announcement",
     *     operationId="deleteAnannouncement",
     *     tags={"Announcement"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Announcement id to delete",
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
    public function deleteAnnouncement($id) {
        if( isset( $id ) ) {

            try {
                $result = $this->collection->deleteOne([
                    '_id'=>new MongoDB\BSON\ObjectId($id)
                ]);
                if ($result->getDeletedCount()==1)
                    return $this->generalFunctions->returnValue(true);
                else 
                    return $this->generalFunctions->returnValue(false);
            }
            catch (MongoDB\Exception\UnsupportedException $e){
                error_log("Problem in delete announcement \n".$e);
                return $this->generalFunctions->returnValue(false);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in delete announcement \n".$e);
                return $this->generalFunctions->returnValue(false);
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in delete announcement \n".$e);
                return $this->generalFunctions->returnValue(false);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in delete announcement \n".$e);
                return $this->generalFunctions->returnValue(false);
            };
        } else 
            return $this->generalFunctions->returnValue(false);
    }

    /**
     * @OA\Patch(
     *     path="/announcements/update",
     *     description="Update an announcement",
     *     operationId="updateAnnouncement",
     *     tags={"Announcement"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(property="_id",type="string"),
     *                  @OA\Property(property="department",type="object"),
     *                  @OA\Property(property="subdepartment",type="object"),
     *                  @OA\Property(property="category",type="object"),
     *                  @OA\Property(property="subject",type="string"),
     *                  @OA\Property(property="message",type="string"),
     *                  @OA\Property(property="period",type="object"),
     *                  example={
     *                      "_id":"6244840de0c3d34f620e5dd6",
     *                      "department": {
     *                          "id":"1",
     *                          "name":"Διεύθυνση Τεχνικών Υπηρεσιών"
     *                      }, 
     *                      "subdepartment": {
     *                          "id":"6250932b62a9e94948207113",
     *                          "name":"Τμήμα Εκτέλεσης Έργων"
     *                      },
     *                      "category": {
     *                          "id":"6250932b62a9e94948207114",
     *                          "name":"Βλάβες"
     *                      },
     *                      "subject":"Διακοπή ρεύματος",
     *                      "message":"XXXXXXX",
     *                      "period": {
     *                          "start":"1/4/2022",
     *                          "finish":"20/4/2022"
     *                     },
     *                  }
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
    public function updateAnnouncement($data) {
        $id = $data->_id;
        $department_id = $data->department_id;
        $department_name = $data->department_name;
        $subdepartment_id = $data->subdepartment_id;
        $subdepartment_name = $data->subdepartment_name;
        $category_id = $data->category_id;
        $category_name = $data->category_name;
        $password = $data->password;
        $subject = $data->subject;
        $message = $data->message;
        $start = $data->period->start;
        $finish = $data->period->finish;
        $attachments = $data->attachments;

        if( isset( $department_id ) && isset($subdepartment_id) && isset($category_id) && isset($subject) && isset($message)) {

            try {
                $result = $this->collection->updateOne( 
                    [ '_id' => new MongoDB\BSON\ObjectId($id) ],
                    [ '$set' => [
                            'department' => [
                                'id' => $department_id,
                                'name' => $department_name
                            ],
                            'subdepartment' => [
                                'id' => $subdepartment_id,
                                'name' => $subdepartment_name
                            ],
                            'category' => [
                                'id' => $category_id,
                                'name' => $category_name
                            ],
                            'subject' => $subject,
                            'message' => $message,
                            'period' => [
                                'start' => $start,
                                'finish' => $finish
                            ],
                            'attachments' => $attachments
                        ]
                    ]
                );
                if ($result->getModifiedCount()==1)
                    return $this->generalFunctions->returnValue(true);
                else 
                    return $this->generalFunctions->returnValue(false);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in update announcement \n".$e);
                return $this->generalFunctions->returnValue(false);
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in update announcement \n".$e);
                return $this->generalFunctions->returnValue(false);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in update announcement \n".$e);
                return $this->generalFunctions->returnValue(false);
            };
        } else 
            return $this->generalFunctions->returnValue(false);
    }
}
?>