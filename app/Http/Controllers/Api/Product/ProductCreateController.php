<?php

namespace App\Http\Controllers\Api\Product;

class ProductCreateController
{
    /**
     * @OA\Post (
     *      path="/product",
     *      summary="Create a Product",
     *      tags={"Product"},
     *      security={{"bearerAuth": {} }},
     *      @OA\Response(response="201", description="Product created successfully"),
     *      @OA\Response(response="400", description="Bad request, please review the parameters")
     * )
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function create()
    {

    }
}
