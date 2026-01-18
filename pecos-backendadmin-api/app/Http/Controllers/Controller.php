<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Pecos River Traders API",
 *     description="API for Pecos River Traders e-commerce platform",
 *     @OA\Contact(
 *         email="admin@pecosrivertraders.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url="/pecos-backendadmin-api/public/api/v1",
 *     description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Tag(name="Auth", description="Authentication endpoints")
 * @OA\Tag(name="Products", description="Product management")
 * @OA\Tag(name="Categories", description="Category management")
 * @OA\Tag(name="Orders", description="Order management")
 * @OA\Tag(name="Cart", description="Shopping cart")
 * @OA\Tag(name="Wishlist", description="User wishlist")
 * @OA\Tag(name="Reviews", description="Product reviews")
 * @OA\Tag(name="Loyalty", description="Loyalty points program")
 * @OA\Tag(name="Coupons", description="Discount coupons")
 * @OA\Tag(name="Blog", description="Blog posts")
 * @OA\Tag(name="Events", description="Store events")
 * @OA\Tag(name="FAQs", description="Frequently asked questions")
 * @OA\Tag(name="Admin", description="Admin management endpoints")
 */
abstract class Controller
{
    //
}
