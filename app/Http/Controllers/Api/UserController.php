<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\AuthFormRequest;
use App\Http\Requests\Api\User\ChangeEmailFormRequest;
use App\Http\Requests\Api\User\UserCreateFormRequest;

use App\Models\User;

/**
 * @OA\Info(
 *   title="API Example",
 *   version="1.0.0",
 *   contact={
 *     "email": "developer@example.org"
 *   }
 * )
 * @OA\SecurityScheme(
 *  type="http",
 *  description="Acess token obtido na autenticação",
 *  name="Authorization",
 *  in="header",
 *  scheme="bearer",
 *  bearerFormat="JWT",
 *  securityScheme="bearerToken"
 * )
 */

class UserController extends Controller
{
    /**
     * @OA\POST(
     *  tags={"Sanctum Authentication"},
     *  summary="Get a autentication user token",
     *  description="This endpoints return a new token user authentication for use on protected endpoints",
     *  path="/api/sanctum/token",
     *  @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              required={"email","password","device_name"},
     *              @OA\Property(property="email", type="string", example="gabriel_nunes@example.org"),
     *              @OA\Property(property="password", type="string", example="#sdasd$ssdaAA@"),
     *              @OA\Property(property="device_name", type="string", example="IOS"),
     *          )
     *      ),
     *  ),
     *  @OA\Response(
     *    response=200,
     *    description="Token generated",
     *    @OA\JsonContent(
     *       @OA\Property(property="plainTextToken", type="string", example="2|MZEBxLy1zulPtND6brlf8GOPy57Q4DwYunlibXGj")
     *    )
     *  ),
     *  @OA\Response(
     *    response=422,
     *    description="Incorrect credentials",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="The provided credentials are incorrect."),
     *       @OA\Property(property="errors", type="string", example="..."),
     *    )
     *  )
     * )
     */
    public function authenticate(AuthFormRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken($request->device_name);

        return $token;
    }

    /**
     * @OA\POST(
     *  tags={"User"},
     *  summary="Create a new user",
     *  description="This endpoint creates a new user",
     *  path="/api/user",
     *  @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/x-www-form-urlencoded",
     *          @OA\Schema(
     *             required={"email","password","name","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Gabriel Nunes"),
     *             @OA\Property(property="email", type="string", example="gabriel_nunes@example.org"),
     *             @OA\Property(property="password", type="string", example="#sdasd$ssdaAA@"),
     *             @OA\Property(property="password_confirmation", type="string", example="#sdasd$ssdaAA@"),
     *          )
     *      ),
     *  ),
     *  @OA\Response(
     *    response=200,
     *    description="User created",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="User created successfully!")
     *    )
     *  ),
     *  @OA\Response(
     *    response=422,
     *    description="Incorrect fields",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="The name has already been taken. (and 2 more errors)"),
     *       @OA\Property(property="errors", type="string", example="..."),
     *    )
     *  )
     * )
     */
    public function store(UserCreateFormRequest $request)
    {
        $request->merge([
            'password' => bcrypt($request->password)
        ]);
        $user = User::create($request->all());

        return [
            'message' => 'User created successfully!',
            'user'    => $user
        ];
    }

    /**
     * @OA\Get(
     *     tags={"User"},
     *     summary="Get data about authenticated user",
     *     description="This endpoint returns all authenticated user data",
     *     path="/api/user/me",
     *     security={ {"bearerToken":{}} },
     *     @OA\Response(
     *          response=200,
     *          description="authenticated user data",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="string", example="5"),
     *              @OA\Property(property="name", type="string", example="Gabriel Nunes"),
     *              @OA\Property(property="email", type="string", example="gabriel_nunes@example.org"),
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Incorrect fields",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The name has already been taken. (and 2 more errors)"),
     *              @OA\Property(property="errors", type="string", example="..."),
     *          )
     *      )
     * ),
     */
    public function me(Request $request)
    {
        return $request->user();
    }

    /**
     * @OA\PATCH(
     *  tags={"User"},
     *  summary="Change user email",
     *  description="This endpoints change an user email",
     *  path="/api/user/change-email",
     *  security={ {"bearerToken":{}} },
     *  @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="application/x-www-form-urlencoded",
     *          @OA\Schema(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", example="gabriel_robert@example.org"),
     *          )
     *      ),
     *  ),
     *  @OA\Response(
     *    response=200,
     *    description="User e-mail updated successfully!",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="User e-mail updated successfully!"),
     *       @OA\Property(property="user", type="string", example="..."),
     *    )
     *  ),
     *  @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated"),
     *    )
     *  ),
     *  @OA\Response(
     *      response=422,
     *      description="Incorrect fields",
     *      @OA\JsonContent(
     *         @OA\Property(property="message", type="string", example="The name has already been taken. (and 2 more errors)"),
     *         @OA\Property(property="errors", type="string", example="..."),
     *      )
     *   )
     * )
     */
    public function updateEmail(ChangeEmailFormRequest $request)
    {
         /** @var User */
         $user = $request->user();
         $user->email = $request->email;
         $user->save();

         return [
                     'message' => 'User e-mail updated successfully!',
                     'user'    => $user
                ];
    }

     /**
     * @OA\DELETE(
     *  tags={"User"},
     *  summary="Revoke all user tokens",
     *  description="This endpoint provides a logout for user, revoking all actived user tokens.",
     *  path="/api/user/logout",
     *  security={ {"bearerToken":{}} },
     *  @OA\Response(
     *    response=200,
     *    description="All user tokens revoked",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="All user tokens were revoked !")
     *    )
     *  ),
     *  @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthenticated"),
     *    )
     *  ),
     *  @OA\Response(
     *    response=422,
     *    description="Incorrect fields",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="The email field is required."),
     *       @OA\Property(property="errors", type="string", example="..."),
     *    )
     *  )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return [
            'message' => 'All user tokens were revoked !',
       ];

    }
}
