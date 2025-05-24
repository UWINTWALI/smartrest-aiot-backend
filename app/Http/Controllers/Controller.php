<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="SmartRest AIoT API",
 *     version="1.0.0",
 *     description="RESTful API for smart mattress monitoring system supporting patients, doctors, customers, and admins.",
 *     @OA\Contact(
 *         email="support@smartrest.example.com",
 *         name="SmartRest Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="/api",
 *     description="SmartRest API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="Authentication & Session Management"
 * )
 * @OA\Tag(
 *     name="Users",
 *     description="User Management Operations"
 * )
 * @OA\Tag(
 *     name="Products",
 *     description="Product Catalog Operations"
 * )
 * @OA\Tag(
 *     name="Sensors",
 *     description="Sensor Data Collection & Querying"
 * )
 * @OA\Tag(
 *     name="Messaging",
 *     description="Messaging & Notifications System"
 * )
 * @OA\Tag(
 *     name="Analytics",
 *     description="Sleep Reports & Health Summaries"
 * )
 * @OA\Tag(
 *     name="System",
 *     description="Device Status & Management"
 * )
 * @OA\Tag(
 *     name="Authentication",
 *     description="User Authentication & Registration"
 * )
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
