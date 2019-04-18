## About My Framework

This framework consists below functionalities

- ORM
- CRUD operations
- Soft Deletes
- Relationship declaration in Models
- Fetch records with relationships from Database
- Request Validation
- Request throttling to prevent more than 10 requests from same session in a second. Attempts can be configured
- MVC pattern
- Custom error pages for HTTP errors
- Routing
- Restrict access to routes for logged in users / guests
- Restrict access to certain routes for logged in users as well
- Authentication Class to manage sessions
- Helper functions which can be used throughout the application
- Config file to configure certain settings in the framework
- Templating

## Future Work

As I have started development of this framework only a few weeks back, there are loads of functionalities pending. Some of them being

- A lot more to update in Readme.
- Custom logging for application.
- More functions for Database such as LIMIT and Pagination.
- More error handling.
- And many more!

## Declaring Routes for Application
Routes can be defined in [routes.php](https://github.com/ArsalanThange/myphpframework/blob/master/routes/routes.php) inside the routes folder.
Currently only supports `GET` and `POST` requests.
First parameter is the route which can be accessed in your application.
Second parameter is the Controller and Method (Controller@method) to which this route must go to.
```php
$route->get('/login', 'LoginController@showlogin');
$route->post('/login', 'LoginController@login');
```
Routes can be declared with `middlewares`. If defined, Middlewares are executed whenever someone tries to access the route. Middlewares are covered more in depth below!
```php
$route->get('/login', 'LoginController@showlogin')->middleware('guest');
$route->get('/', 'HomeController@index')->middleware('auth');
```
Appropriate HTTP errors are thrown if someone tries to access routes not declared or if they do not have access.

## Middlewares
Middlewares are used to guard routes against the defined logic. They are executed whenever someone tries to access the route.
Middlewares are defined in [middlewares](https://github.com/ArsalanThange/myphpframework/tree/master/app/middlewares) folder and must extend `Middleware` Class.

Middlewares must be registered in `registerMiddlewares` of `Middleware` Class.

#### Creating a middleware
The `Authenticate` middleware redirects the user to Login Page if the user is not logged in.

```php
namespace App\Middlewares;

use Core\Auth;

class Authenticate extends Middleware
{
    /**
     * Construct authentication check.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authenticateCheck();
    }

    /**
     * Check if the user is logged in, if not redirect to login page.
     *
     * @return void
     */
    public function authenticateCheck()
    {
        if (!Auth::check()) {
            redirect('/login');
        }
    }
}
```

Routes can be declared with `middlewares`.
```php
$route->get('/login', 'LoginController@showlogin')->middleware('guest');
$route->get('/', 'HomeController@index')->middleware('auth');
```

## Models
All models for the application are declared in [models](https://github.com/ArsalanThange/myphpframework/tree/master/app/models) folder and must extend the `Model` class.

#### Declaring a User Model
```php
namespace App\Models;

class User extends Model
{
    /**
     * Setting table value for User class.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Get the database column to be used for logging in.
     * Default returns username. (Can be email, mobile etc)
     *
     * @return string
     */
    public function getUsername()
    {
        return 'username';
    }
}
```

#### Access User Model in Controller
```php
namespace App\Controllers;

use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $user = new User;
        echo $user->getUsername();
    }
}

//Output: username
```

## Controllers
All controllers for the application are declared in [controllers](https://github.com/ArsalanThange/myphpframework/tree/master/app/controllers) folder and must extend the `COntroller` class.

#### Declaring a Home Controller
```php
namespace App\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        echo 'Hello World';
    }
}
```

#### Access GET/POST parameters in controllers
Assuming the GET URL is `?id=1&foo=bar`
```php
namespace App\Controllers;

use Core\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        echo $request->id;
        echo $request->foo;
    }
}

//Output: 1bar
```

#### Loading a HTML view in controllers.
This will load the `index` view defined in [views](https://github.com/ArsalanThange/myphpframework/tree/master/app/views) folder. More details on `View` and how to use them can be found in Views section.
```php
namespace App\Controllers;

use Core\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $message = 'Welcome to my Framework';

        $this->view('index', $message)->render();
    }
}
```
