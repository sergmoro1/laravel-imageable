<?php

namespace Sergmoro1\Imageable\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API documentation for blog example.",
 *     description="Implementation of Swagger with in Laravel",
 *     @OA\Contact(
 *         email="sergmoro1@ya.ru"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://mit-license.org/"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8080",
 *     description="Blog API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="basicAuth",
 *     in="header",
 *     name="Authorization",
 *     type="http",
 *     description="Basic authentication by email & password.",
 *     scheme="basic"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Prepare NotFound response.
     *
     * @return \Illuminate\Http\Response
     */
    protected function responseNotFound() {
        return response()->json([
            'success' => false,
            'message' => __('Not found'), 
        ], 404);
    }
}
