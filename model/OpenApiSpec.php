<?php

// namespace Model;

use OpenApi\Annotations as OA;

/**
* @OA\Tag(
*     name="Department",
*     description="Operations about Department collection",
* )
* @OA\Tag(
*     name="Subdepartment",
*     description="Operations about Subdepartment field in Department collection",
* )
* @OA\Tag(
*     name="Announcement",
*     description="Operations about Announcement collection",
* )
* @OA\Tag(
*     name="Categories",
*     description="Operations about Categories field in Department collection",
* )
* @OA\Tag(
*     name="Roles",
*     description="Operations about Roles field in User collection",
* )
* @OA\Tag(
*     name="Subscription",
*     description="Operations about Subscription field in User collection",
* )
* @OA\Tag(
*     name="User",
*     description="Operations about User collection",
* )
* @OA\Tag(
*     name="UserCategory",
*     description="Operations about UserCategory collection",
* )
* @OA\Info(
*     version="1.0",
*     title="API for App Anouncements",
*     description="Anouncements API",
* )
* @OA\Server(
*     url="http://coding-factory-php.herokuapp.com/",
*     description="API server"
* )
* @OA\Server(
*     url="http://localhost/",
*     description="API server"
* )
* @OA\Components(
*     @OA\SecurityScheme(
*         securityScheme="bearerAuth",
*         type="http",
*         in="header",
*         scheme="bearer",
*         bearerFormat="JWT",
*         name="Authorization",
*     ),
*     @OA\Attachable
* )
*/
class OpenApiSpec
{
}

?>