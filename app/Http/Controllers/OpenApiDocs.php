<?php
/**
 * @OA\OpenApi(
 *     info=@OA\Info(
 *         version="1.0.0",
 *         title="Backend Technical Test - Product API",
 *         description="Complete REST API with authentication, product CRUD, filtering, sorting, pagination, and file uploads."
 *     ),
 *     servers={
 *         @OA\Server(url="/api/v1", description="API v1")
 *     },
 *     @OA\SecurityScheme(
 *         type="http",
 *         name="sanctum",
 *         scheme="bearer"
 *     ),
 *     @OA\PathItem(
 *         path="/auth/login",
 *         @OA\Post(
 *             tags={"Authentication"},
 *             summary="User login",
 *             description="Authenticate user with email and password",
 *             @OA\RequestBody(
 *                 required=true,
 *                 @OA\JsonContent(
 *                     required={"email","password"},
 *                     @OA\Property(property="email", type="string", format="email"),
 *                     @OA\Property(property="password", type="string", format="password")
 *                 )
 *             ),
 *             @OA\Response(
 *                 response=200,
 *                 description="Successful login",
 *                 @OA\JsonContent(
 *                     @OA\Property(property="token", type="string"),
 *                     @OA\Property(property="token_type", type="string"),
 *                     @OA\Property(property="user", type="object")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\PathItem(
 *         path="/auth/me",
 *         @OA\Get(
 *             tags={"Authentication"},
 *             summary="Get current user",
 *             security={{"sanctum":{}}},
 *             @OA\Response(response=200, description="Current user")
 *         )
 *     ),
 *     @OA\PathItem(
 *         path="/auth/logout",
 *         @OA\Post(
 *             tags={"Authentication"},
 *             summary="User logout",
 *             security={{"sanctum":{}}},
 *             @OA\Response(response=200, description="Successfully logged out")
 *         )
 *     ),
 *     @OA\PathItem(
 *         path="/products",
 *         @OA\Get(
 *             tags={"Products"},
 *             summary="List products",
 *             @OA\Parameter(name="page", in="query", schema={"type":"integer"}, description="Page number"),
 *             @OA\Parameter(name="per_page", in="query", schema={"type":"integer"}, description="Items per page"),
 *             @OA\Response(response=200, description="List of products")
 *         ),
 *         @OA\Post(
 *             tags={"Products"},
 *             summary="Create product",
 *             security={{"sanctum":{}}},
 *             @OA\RequestBody(
 *                 required=true,
 *                 @OA\JsonContent(
 *                     required={"title","category","price","stock"},
 *                     @OA\Property(property="title", type="string"),
 *                     @OA\Property(property="description", type="string"),
 *                     @OA\Property(property="category", type="string"),
 *                     @OA\Property(property="price", type="number", format="float"),
 *                     @OA\Property(property="discount_percentage", type="number"),
 *                     @OA\Property(property="rating", type="number"),
 *                     @OA\Property(property="stock", type="integer")
 *                 )
 *             ),
 *             @OA\Response(response=201, description="Product created")
 *         )
 *     ),
 *     @OA\PathItem(
 *         path="/products/{id}",
 *         @OA\Get(
 *             tags={"Products"},
 *             summary="Get product",
 *             @OA\Parameter(name="id", in="path", required=true, schema={"type":"integer"}),
 *             @OA\Response(response=200, description="Product details")
 *         ),
 *         @OA\Patch(
 *             tags={"Products"},
 *             summary="Update product",
 *             security={{"sanctum":{}}},
 *             @OA\Parameter(name="id", in="path", required=true, schema={"type":"integer"}),
 *             @OA\RequestBody(
 *                 @OA\JsonContent(
 *                     @OA\Property(property="title", type="string"),
 *                     @OA\Property(property="description", type="string"),
 *                     @OA\Property(property="category", type="string"),
 *                     @OA\Property(property="price", type="number", format="float"),
 *                     @OA\Property(property="discount_percentage", type="number"),
 *                     @OA\Property(property="rating", type="number"),
 *                     @OA\Property(property="stock", type="integer")
 *                 )
 *             ),
 *             @OA\Response(response=200, description="Product updated")
 *         ),
 *         @OA\Delete(
 *             tags={"Products"},
 *             summary="Delete product",
 *             security={{"sanctum":{}}},
 *             @OA\Parameter(name="id", in="path", required=true, schema={"type":"integer"}),
 *             @OA\Response(response=204, description="Product deleted")
 *         )
 *     ),
 *     @OA\PathItem(
 *         path="/products/{id}/thumbnail",
 *         @OA\Post(
 *             tags={"Products"},
 *             summary="Upload product thumbnail",
 *             security={{"sanctum":{}}},
 *             @OA\Parameter(name="id", in="path", required=true, schema={"type":"integer"}),
 *             @OA\RequestBody(
 *                 required=true,
 *                 @OA\MediaType(mediaType="multipart/form-data", schema=@OA\Schema(@OA\Property(property="thumbnail", type="string", format="binary")))
 *             ),
 *             @OA\Response(response=200, description="Thumbnail uploaded")
 *         )
 *     )
 * )
 */
