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
