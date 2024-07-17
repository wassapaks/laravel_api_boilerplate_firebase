<?php

namespace App\Classes;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Response;

class ApiResponseClass extends StatusCode
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function rollback($e, $message = "Something went wrong, process not completed!") : void{
        DB::rollBack();
        self::throw($e, $message);
    }

    private static function log($e){
        Log::info($e);
        return (is_string($e)) ? \Sentry\captureMessage($e) : \Sentry\captureException($e);
    }

    public static function throw($e, $message = "Something went wrong, process not completed!", $code = parent::HTTP_INTERNAL_SERVER_ERROR){
        self::log($e);
        throw new HttpResponseException(response()->json(["message"=>$message], $code));
    }

    public static function sendResponse($result , $message , $code=200){
        $response=[
            'success' => true,
            'data' => $result
        ];
        if(!empty($message)){
            $response['message']= $message;
        }
        return response()->json($response, $code);
    }

    public static function accessDenied(){
        throw new HttpResponseException(response()->json([
            "message" => parent::$statusTexts[parent::HTTP_FORBIDDEN]
        ], parent::HTTP_FORBIDDEN));
    }

    public static function tooManyRequest($e){
        self::throw('Too many request from ' . $e, parent::$statusTexts[429], parent::HTTP_TOO_MANY_REQUESTS);
    }

    public static function notFound($e){
        self::throw($e, parent::$statusTexts[404], parent::HTTP_NOT_FOUND);
    }
}
