
## Laravel API Boilerplate (Firebase Edition) for Laravel 11

This is a Laravel API Boilerplate you can use to build your API in seconds. Built on top of Laravel 11 Framework. Developed this for one of my personal projects. This api requires Firebase for authentication, but you can configure to use JWT, Cognito, Sanctum or its up to you. Sharing this as most of the components and framework I used are all open source and to help someone who has the same need, feel free to PR for improvements and add components I have missed. 

In building this api, I have the following rest components checklist in mind:

- Firebase Authentication
- Created a basic Endpoint for CRUD
- User Management
- Integrate Rate Limiter base on IP and User using Redis
- Caching Example using Redis
- Apply CSP, CORS and Security Headers
- Versioning using Headers
- Apply Sentry in monitoring Logs
- Apply Gates and Policies to Api
- Apply Repository Design Pattern
- Apply Scrable API Documentation
- Applied Hateoas using https://github.com/willdurand/Hateoas

#### Requirements
Before you start you need to have the following.
- Install [Redis](https://redis.io/docs/latest/operate/oss_and_stack/install/install-redis/)
- Register [Sentry](https://sentry.io/)
- Register [Firebase](https://console.firebase.google.com/)


## Installation

Make sure you have `composer` installed.

```bash
composer create-project wassapaks/laravel_api_boilerplate_firebase
```
Then execute `php artisan migrate --seed` to initialize the tables and seed tables.

## Configure your .env

To run this project, you will need to add the following environment variables to your .env file.

#### Firebase
Download your firebase auth json credentials place them in the storage app firebase
```env
FIREBASE_CREDENTIALS=storage/app/firebase-auth.json
FIREBASE_CONTINUE_URL="http://localhost:8080"
```

#### Redis
```env
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

#### Sentry
```env
SENTRY_LARAVEL_DSN=
SENTRY_TRACES_SAMPLE_RATE=1.0
SENTRY_PROFILES_SAMPLE_RATE=1.0VITE_SENTRY_DSN_PUBLIC="${SENTRY_LARAVEL_DSN}"
```

#### Version Header
```env
X_API_VERSION=v1
```
## Versioning
In your routes folder you will see `api.php` and `api.v2.php`, this is how I implemented versioning, this is mapped in `bootstrap/app.php` `withrouting()` when making a request. You need to have `X_API_VERSION` in your postman request and the value should be the second segment of the filename. Initially your authentication is on `api.v2.php` and the Books CRUD example is on `api.php` which in your header it should be `X_API_VERSION: v1`.

To add a new version just create a new file ex: `api.v3.php`

Here is the `bootstrap/app.php` versioning implementation.
```php
->withRouting(
	commands: __DIR__  .  '/../routes/console.php',
	health: '/up',
	using: function (Request  $request) {
		$apiVersion = $request->header('X-Api-Version', env('X_API_VERSION'));
		$routeFile = $apiVersion != env('X_API_VERSION') ? 
			base_path(sprintf('routes/%s.%s.php', 'api', $apiVersion)) : 
			base_path(sprintf('routes/%s.php', 'api'));

		  if (!file_exists($routeFile)) {
			return  response()->json([
				'error_message' => 'Wrong Version Request.',
				], 400);
		}
		Route::middleware('api')
			->prefix('api')
			->group($routeFile);
},)
```
Add the following headers in your request:
`X_API_VERSION:`

## Firebase Authentication and User Routes
I have place the authentication and routes in api.v2.php. To test add `X_API_VERSION: v2` in your header request.

- `POST api/auth/signin` to request for token
- `GET api/auth/verify` to verify token
- `POST api/user-management/users` to create user and firebase user
- `GET api/user-management/users/{$id}` to show singe user
- `DELETE  api/user-management/users/{$id}` to delete user   
- `GET  api/user-management/users` show all user

## CORS Config
Laravel 11 can already respond to [CORS](https://laravel.com/docs/11.x/routing#cors)  

```php
<?php
return [
/*
|--------------------------------------------------------------------------
| Cross-Origin Resource Sharing (CORS) Configuration
|--------------------------------------------------------------------------
|
| Here you may configure your settings for cross-origin resource sharing
| or "CORS". This determines what cross-origin operations may execute
| in web browsers. You are free to adjust these settings as needed.
|
| To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
|
*/
'paths' => ['api/*', 'sanctum/csrf-cookie'],

'allowed_methods' => ['POST','GET', 'PATCH', 'PUT', 'DELETE', 'OPTIONS'],  

'allowed_origins' => ['*'],

'allowed_origins_patterns' => [],

'allowed_headers' => ['Origin', 'X-Api-Version','Authorization', 'Content-Type', 'Accept'],

'exposed_headers' => [],

'max_age' => 0,

'supports_credentials' => false,
];
```

## Security Headers and CSP
You can modify the security headers in `app/Http/Middleware/SecurityHeadersMiddleware.php`. 

```php
// For Reference:
// https://cheatsheetseries.owasp.org/cheatsheets/REST_Security_Cheat_Sheet.html#security-headers

$response->headers->set('Content-Security-Policy', "default-src 'none'; frame-ancestors 'none'");
$response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
$response->headers->set('X-Content-Type-Options', 'no-sniff');
$response->headers->set('Content-Type', 'application/json');
$response->headers->set('X-Frame-Options:', 'DENY');
$response->headers->set('Referrer-Policy:', 'no-referrer');
$response->headers->set('Permissions-Policy', 'aaccelerometer=(), ambient-light-sensor=(), autoplay=(), battery=(), camera=(), cross-origin-isolated=(), display-capture=(), document-domain=(), encrypted-media=(), execution-while-not-rendered=(), execution-while-out-of-viewport=(), fullscreen=(), geolocation=(), gyroscope=(), keyboard-map=(), magnetometer=(), microphone=(), midi=(), navigation-override=(), payment=(), picture-in-picture=(), publickey-credentials-get=(), screen-wake-lock=(), sync-xhr=(), usb=(), web-share=(), xr-spatial-tracking=()');

$response->header('X-Api-Version', $request->header('X-Api-Version', 'v1'));
$response->header('X-Api-Latest-Version', config('app.latestApiVersion'));
```

## Book Routes (CRUD Example)

`routes/api.php`
```php
Route::middleware(['firebase.auth', 'throttle:api'])->group(function () {
	Route::get('/books', [BookController::class, 'index']);
	Route::get('/books/{id}', [BookController::class, 'show']);
	Route::post('/books', [BookController::class, 'store']);
	Route::put('/books/{id}', [BookController::class, 'update']);
	Route::delete('/books/{id}', [BookController::class, 'destroy']);
});
```

Here is how things happen
- `app/Http/Controllers/BookController.php` handles your inputs and outputs
- `app/Interfaces/BookRepositoryInterface.php` is binded with `app/Repositories/BookRepository.php` in `app/Providers/RepositoryServiceProvider.php` handling your resource data processes
- `app/Http/Resources/BookResource.php` handles the resource data return, you can add your hateoas here
-  `app/Http/Requests/StoreBookRequest.php`, `app/Http/Requests/UpdateBookRequest`handles validation and user policies

#### Creating Routes
- You can use existing versions or add a new `api.[new version].php` in the routes directory 
- In your api the middleware you can specify authentication and throttle 
```php
Route::middleware(['firebase.auth', 'throttle:api'])->group(function ()
```
- Check [Laravel 11 Routing Document](https://laravel.com/docs/11.x/routing)

## Rate Limiter
- The rate limiter can be configured in the `app/Http/Providers/RouteServiceProvider.php` 
```php
public  function  boot(): void
{
	RateLimiter::for('api', function (Request  $request) {
		$limit = ($request->bearerToken()) ? 1000 : 50;
			return  Limit::perMinute($limit)->by($request->ip())->response(function (Request $request, array  $headers) {
				return  ApiResponseClass::tooManyRequest($request->ip());
			});
	});
}
```
- Or you can place specific Rate limiter in your specific endpoint in the controller, example:
```php
public  function  login(Request  $request): ApiResponseClass

{
	if (RateLimiter::tooManyAttempts('login-attempt:'.$request->ip(), $perMinute = 5)) {
		return ApiResponseClass::tooManyRequest($request->ip());
	}
	RateLimiter::increment('login-attempt:'.$request->ip());
}
```


## Hateoas

Using the [HATEOAS](https://github.com/willdurand/Hateoas) Library. You can check the library documentation for more details.

#### Usage 
1. Place your Hypermedia on the `app/Hateoas/` directy. You can use annotations, XML, Yaml. The example I used is using annotations.

Example
```php
<?php
declare(strict_types=1);
namespace  App\Hateoas;

use Hateoas\Configuration\Annotation  as Hateoas;
use JMS\Serializer\Annotation  as Serializer;

/**
* @Hateoas\Relation("self", href = "expr('/api/books/' ~ object.getId())")
*/

class  Books
{
	/** @Serializer\XmlAttribute */
	private  $id;
	public  function  __construct($id)
	{
		$this->id = $id;
	}

	public  function  getId()
	{
		return  $this->id;
	}

}
```
 2. On your Request class you can add the Hateos and merge it to the response data.
```php
public  function  toArray(Request  $request): array
{
	$data = [
		'id' => $this->id,
		'name' => $this->name,
		'author' => $this->author,
		'publish_date' => $this->publish_date,
	];
	
	$links = new  HateoasClass(new  Books($this->id));
	return  $links->getLinks() ? array_merge($data, $links->getLinks()) : $data;
}
```
 
