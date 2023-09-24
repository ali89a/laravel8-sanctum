<?php

namespace App\Providers;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Response::macro('successResponse', function ($message, $data, $code=200) {
            return \response()->json([
                'success'=>true,
                'message'=>$message,
                'data'=>$data
            ], $code);
        });

        Response::macro('errorResponse', function ($message='Opps! something went wrong', $code=500) {
            return \response()->json([
                'success'=>false,
                'message'=>$message,
            ], $code);
        });

        Response::macro('sendValidationErrorResponse', function ($errors) {
            throw new HttpResponseException(\response()->json([
                'success' => false,
                'message' => 'Ops! Some errors occurred',
                'errors' => $errors
            ], 422));
        });
    }
}
