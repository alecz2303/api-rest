<?php


namespace App\Http\Controllers;

use App\Models\User;
use App\Filters\UsrFilter;
use Illuminate\Http\Request;
use App\Http\Resources\ApiUserResource;
use App\Http\Resources\ApiUserCollection;
use App\Http\Requests\StoreApiUserRequest;

/**
 * @OA\Info(
 *     description="This is an example API for users management",
 *     version="1.0.0",
 *     title="User Management API"
 * )
 */
class ApiUserController extends Controller
{
    /**
     * Returns all users
     */
    public function index(Request $request) {
        $filter = new \App\Filters\UsrFilter();
        $queryItems = $filter->transform($request);
        $users = User::where($queryItems);
        return new ApiUserCollection($users->paginate()->appends($request->query()));
    }

    /**
     * @OA\Post(
     *     path="/v1/apiusers",
     *     operationId="storeUser",
     *     tags={"Users"},
     *     summary="Create a new user",
     *     description="Creates a new user with the provided information",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User information",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe", description="User's name"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com", description="User's email"),
     *             @OA\Property(property="password", type="string", format="password", example="secret", description="User's password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}})
     *         )
     *     ),
     * )
     *
     * @param StoreApiUserRequest $request
     * @return \App\Http\Resources\ApiUserResource
     */
    public function store(StoreApiUserRequest $request){
        return $request->all();
        return new ApiUserResource(User::create($request->all()));
    }

    public function show(){}

    public function update(){}

    public function destroy(){}
}
