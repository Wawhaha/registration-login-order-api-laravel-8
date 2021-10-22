<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\User;
class AuthController extends Controller
{
    public function register(Request $request)
    {
    
        if(is_null($request->email)) {

            return response()->json([
                'message' => 'Email is required'
            ], 400);

        }
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {

            return response()->json([
                'message' => 'Email is not valid, please input valid email'
            ], 400);

        }
        if(is_null($request->password)) {

            return response()->json([
                'message' => 'Password is required'
            ], 400);

        }

        $user_status = User::where("email", $request->email)->first();

        if(!is_null($user_status)) {

            return response()->json([
                'message' => 'Email already taken'
            ], 400);

        }else {

            $user = new User([
                'name' => 'NULL',
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);

            $user->save();

            return response()->json([
                'message' => 'User successfully registered'
            ], 201);

        }

    }

    public function login(Request $request)
    {
        
        if(is_null($request->email)) {

            return response()->json([
                'message' => 'Email is required'
            ], 400);

        }

        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {

            return response()->json([
                'message' => 'Email is not valid'
            ], 400);

        }

        if(is_null($request->password)) {

            return response()->json([
                'message' => 'Password is required'
            ], 400);

        }

        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials)){

            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Highly Succeed');
        //Problemm on setup 
        /*
        $token = $tokenResult->$token;
        $token->expires_at = Carbon::now()->addminutes(5);
        $token->save();
        */

        return response()->json([
            'access_token' => $tokenResult->plainTextToken
        ], 201);

    }

    public function order(Request $request)
    {

        if(is_null($request->product_id)) {

            return response()->json([
                'message' => 'Product ID is required'
            ], 400);

        }

        if (!is_numeric($request->product_id)) {

            return response()->json([
                'message' => 'Product ID is not valid, Please input numeric value'
            ], 400);

        }

        if(is_null($request->quantity)) {

            return response()->json([
                'message' => 'Quantity is required'
            ], 400);

        }

        if (!is_numeric($request->quantity)) {

            return response()->json([
                'message' => 'Quantity is not valid, Please input numeric value'
            ], 400);

        }

        $product = \DB::table('products')
            ->where(['product_id' => $request->product_id])
            ->pluck('stock')
            ->first();
        if(!is_null($product)){

            $stock_left = $product;

            if($request->quantity > $stock_left){

                return response()->json([
                    'message' => 'Failed to order this product due to unavailability of the stock'
                ], 400);

            }else{

                $stock_diff = $stock_left - $request->quantity;
                $product = \DB::table('products')
                ->where(['product_id' => $request->product_id])
                ->update(['stock' => $stock_diff]);
                
                return response()->json([
                    'message' => 'You have successfully ordered this product.'
                ], 201);

            }

        }else{

            return response()->json([
                'message' => 'Product not found'
            ], 400);

        }
    }

    
  
    
}