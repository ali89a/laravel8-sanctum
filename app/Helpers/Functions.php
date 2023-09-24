<?php

function send_error($message, $errors = [], $code = 404)
{
    $response = [
        'status' => false,
        'message' => $message
    ];
    !empty($errors) ? $response['errors'] = $errors : null;
    return response()->json($response, $code);
}
function send_response($message, $data = [], $code)
{
    $response = [
        'status' => true,
        'message' => $message,
        'data' => $data
    ];
    return response()->json($response, $code);
}
function authUser(bool $get_id = false)
{
    if (!$get_id) {
        return auth()->user();
    }
    return auth()->user()->id;
}

function saveApiErrorLog($messageType, $exception, $filename = 'laravel')
{
    if ($filename == 'laravel') {
        $filename = class_basename(Route::current()->controller);
    }
    $message = $exception->getMessage();
    Log::build([
        'driver' => 'single',
        'path' => storage_path('logs/api/' . date('Y-m-d') . '/' . $filename . '.log'),
    ])->$messageType([
        'method' => getCurrentMethodName(),
        'controller' => class_basename(Route::current()->controller),
        'message' => $message
    ]);

    $slack_error_data = [
        'error_from' => 'API',
        'method' => getCurrentMethodName(),
        'controller' => class_basename(Route::current()->controller),
        'message' => $message
    ];
    Log::channel('slack')->$messageType($slack_error_data);

}
function getCurrentMethodName(): string
{
    return Route::current()->getActionMethod();
}
