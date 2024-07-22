<?php

namespace App\Classes;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use App\Enums\HttpStatusCodeEnum;

class ApiResponseClass
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

    public static function throw($e, $message = "Something went wrong, process not completed!", $code = HttpStatusCodeEnum::HTTP_INTERNAL_SERVER_ERROR->value){
        self::log($e);
        throw new HttpResponseException(response()->json(["message"=>$message], $code));
    }

    public static function sendResponse($result , $message , $code=HttpStatusCodeEnum::HTTP_OK->value){
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
        self::throw('Somebody is trying to access from ', HttpStatusCodeEnum::HTTP_FORBIDDEN->message(), HttpStatusCodeEnum::HTTP_FORBIDDEN->value);
    }

    public static function tooManyRequest($e){
        self::throw('Too many request from ' . $e, HttpStatusCodeEnum::HTTP_TOO_MANY_REQUESTS->message(), HttpStatusCodeEnum::HTTP_TOO_MANY_REQUESTS->value);
    }

    public static function notFound($e){
        self::throw($e, HttpStatusCodeEnum::HTTP_NOT_FOUND->message(), HttpStatusCodeEnum::HTTP_NOT_FOUND->value);
    }

    public static function badRequest($e){
        self::throw($e, HttpStatusCodeEnum::HTTP_BAD_REQUEST->message(), HttpStatusCodeEnum::HTTP_BAD_REQUEST->value);
    }
}
