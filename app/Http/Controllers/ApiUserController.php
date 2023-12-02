<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Filters\UsrFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\ApiUserResource;
use App\Http\Resources\ApiUserCollection;
use App\Http\Requests\StoreApiUserRequest;
use App\Http\Requests\UpdateApiUserRequest;

/**
 * @OA\Info(
 *     description="API para la gestión de usuarios",
 *     version="1.0.0",
 *     title="User Management API"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * )
 */

class ApiUserController extends Controller
{
    /**
     *
     * @OA\Schema(
     *     schema="ApiUserResource",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="John Doe"),
     *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time"),
     * )
     * @OA\Get(
     *     path="/api/v1/apiusers",
     *     operationId="getUsers",
     *     tags={"Users"},
     *     summary="Get all users",
     *     description="Retrieve a paginated list of all users",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ApiUserResource")),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     ),
     * )
     */
    public function index(Request $request) {
        try {
            $filter = new \App\Filters\UsrFilter();
            $queryItems = $filter->transform($request);
            $users = User::where($queryItems);
            return new ApiUserCollection($users->paginate()->appends($request->query()));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/apiusers",
     *     operationId="createUser",
     *     tags={"Users"},
     *     summary="Create a new user",
     *     description="Creates a new user with the provided information",
     *     security={{"bearerAuth":{}}},
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
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/ApiUserResource"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     ),
     * )
     */
    public function store(StoreApiUserRequest $request)
    {
        //
        try {
            // Validar los datos antes de crear el usuario
            $validatedData = $request->validated();

            // Verificar duplicado de correo electrónico
            if (User::where('email', $validatedData['email'])->exists()) {
                return response()->json(['error' => 'Email has already been taken.'], 422);
            }

            $validatedData['password'] = bcrypt($validatedData['password']);

            return new ApiUserResource(User::create($validatedData));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ApiUser $apiUser)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/api/v1/apiusers/{id}",
     *     operationId="updateUser",
     *     tags={"Users"},
     *     summary="Update a user",
     *     description="Update user information by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to be updated",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated user information",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe", description="User's name"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com", description="User's email"),
     *             @OA\Property(property="password", type="string", format="password", example="newsecret", description="User's new password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/ApiUserResource"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="User not found"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={
     *                 "name": {"The name field is required."},
     *                 "email": {"The email field must be a valid email address."},
     *                 "password": {"The password field must be at least 6 characters."},
     *             })
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal Server Error"),
     *         ),
     *     ),
     * )
     */
    public function update(UpdateApiUserRequest $request, $id)
    {
        try {
            $method = $request->method();
            $user = User::findOrFail($id);

            // Validar los datos antes de actualizar el usuario
            $validatedData = $request->all();

            // Hash de la nueva contraseña antes de actualizarla
            if (isset($validatedData['password'])) {
                $validatedData['password'] = bcrypt($validatedData['password']);
            }

            $user->update($validatedData);

            return new ApiUserResource($user);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/apiusers/{id}",
     *     operationId="deleteUser",
     *     tags={"Users"},
     *     summary="Delete a user",
     *     description="Delete a user by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to be deleted",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="User deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="User not found"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Internal Server Error"),
     *         ),
     *     ),
     * )
     *
     * @param User $apiUser
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $apiUser)
    {
        //
        return dd($apiUser);
    }
}
