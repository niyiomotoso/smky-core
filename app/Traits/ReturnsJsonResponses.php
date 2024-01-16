<?php
namespace App\Traits;

trait ReturnsJsonResponses
{
    public function success_response($data=[], $message="Successful", $http_status=200)
    {
        $status = true;
        $success_data = compact('status', 'message', 'data');
        return response()->json($success_data, $http_status);
    }

    public function error_response($error="An error occurred", $message="Failed", $http_status=500)
    {
        $status = false;
        $error_data = compact('status', 'message', 'error');
        return response()->json($error_data, $http_status);
    }
    public function bad_request_response(\Exception $exception, $message="Failed")
    {
        $error_data = [
            "status" => false,
            "message" => $message,
            "error" => $exception->getMessage()
        ];
        return response()->json($error_data, 400);
    }

    public function exception_response(\Exception $exception, $message="An error occurred")
    {
        $error_data = [
            "status" => false,
            "message" => $message,
            "error" => $exception->getMessage()
        ];
        return response()->json($error_data, 500);
    }
}
