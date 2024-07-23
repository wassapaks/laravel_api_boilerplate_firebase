<?php

namespace App\Classes;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use App\Enums\HttpStatusCodeEnum;
use Illuminate\Contracts\Support\Responsable;

class ApiResponseClass implements Responsable
{
    protected int $httpCode;
    protected $data;
    protected string $errorMessage;
    /**
     * Create a new class instance.
     */
    public function __construct(int $httpCode, $data, string $errorMessage = HttpStatusCodeEnum::HTTP_OK->value)
    {
        if (! (($httpCode >= 200 && $httpCode <= 300) || ($httpCode >= 400 && $httpCode <= 600))) {
            throw new \RuntimeException($httpCode . ' is not valid');
          }
  
          $this->httpCode = $httpCode;
          $this->data = $data;
          $this->errorMessage = $errorMessage;
    }

    public function toResponse($request): \Illuminate\Http\JsonResponse
    {
        $payload = match (true) {
            $this->httpCode >= 400 => ['status' => 'FAILED', 'error_message' => $this->errorMessage],
            $this->httpCode >= 200 => ['status' => 'OK','data' => $this->data],
            //... add your logic to this block
        };
        
        return response()->json(
            data: $payload,
            status: $this->httpCode,
            options: JSON_UNESCAPED_UNICODE
        );
    }

    public static function ok($data){
        return new static(HttpStatusCodeEnum::HTTP_OK->value, $data);
    }

    public static function deleted($message = 'Successfully deleted.'){
        return new static(HttpStatusCodeEnum::HTTP_OK->value, [$message]);
    }

    public static function created($data, $message = "Successfuly added record."){
        return new static(HttpStatusCodeEnum::HTTP_CREATED->value, $data, $message);
    }

    public static function updated($data, $message = "Successfuly updated record."){
        return new static(HttpStatusCodeEnum::HTTP_ACCEPTED->value, $data, $message);
    }

    public static function rollback($e, $message = "Something went wrong, process not completed!", $code = HttpStatusCodeEnum::HTTP_INTERNAL_SERVER_ERROR->value){
        DB::rollBack();
        return self::throw($e, $message, $code);
    }

    public static function throw($e, $message = "Something went wrong, process not completed!", $code = HttpStatusCodeEnum::HTTP_INTERNAL_SERVER_ERROR->value){
        self::log($e);
        return new static($code, [], $message);
    }

    public static function forbidden($e){
        return new static (HttpStatusCodeEnum::HTTP_FORBIDDEN->value,[],$e->getMessage());
    }

    public static function badRequest($message){
        return new static (HttpStatusCodeEnum::HTTP_BAD_REQUEST->value,[], $message);
    }

    public static function accessDenied(){
        self::throw('Somebody is trying to access from ', HttpStatusCodeEnum::HTTP_FORBIDDEN->message(), HttpStatusCodeEnum::HTTP_FORBIDDEN->value);
    }

    public static function tooManyRequest($e){
        self::throw('Too many request from ' . $e, HttpStatusCodeEnum::HTTP_TOO_MANY_REQUESTS->message(), HttpStatusCodeEnum::HTTP_TOO_MANY_REQUESTS->value);
    }

    private static function log($e){
        Log::info($e);
        return (is_string($e)) ? \Sentry\captureMessage($e) : \Sentry\captureException($e);
    }

}
