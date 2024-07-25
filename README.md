
## Laravel API Boilerplate (Firebase Edition) for Laravel 11

This is a Laravel API Boilerplate you can use to build your first API in seconds. Built on top of Laravel 11 Framework. I developed this for one of my project. This api requires Firebase for authentication, but you can configure to use JWT, Cognito, Sanctum or its up to you. Sharing this as most of the components and framework I used are all open source. 

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

#### Creating Routes

TO BE CONTINUED...
