<?php

use Dotenv\Dotenv;
use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Slim\Factory\AppFactory;
use Slim\Csrf\Guard;
use Twig\Extension\DebugExtension;
use DI\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Utilities\Env;
use App\Utilities\Language;
use App\Extensions\CsrfExtension;
use App\Extensions\ClientVariablesExtension;
use App\Extensions\Translate;

class App
{
    public $instance;
    public $container;


    public function run(): void
    {
        (Dotenv::createImmutable(ABSPATH))->safeLoad();

        $this->loadContainer();
        $this->loadSettings();
        $this->loadLocale();
        $this->loadTwig();
        $this->loadDatabase();
        $this->loadSecurity();
        $this->loadRoutes();

        $this->instance->run();
    }

    /**
     * Loads the application container ($container) and application instance ($app)
     *
     * @return void
     * @throws RuntimeException
     */
    private function loadContainer(): void
    {
        $this->container = new Container();
        AppFactory::setContainer($this->container);

        $this->instance = AppFactory::create();
        $this->instance->addErrorMiddleware(true, true, true);
    }


    /**
     * Defines the application settings from the enviroment variables
     *
     * @return void
     */
    private function loadSettings(): void
    {
        $container =& $this->container;

        // Settings
        $container->set('settings', function () {
            return array(
                'app' => array(
                    'name'         => Env::get('APP_NAME'),
                    'langDefault'  => Env::get('APP_LANG_DEFAULT')
                )
            );
        });


        // Javascript client settings
        $container->set('clientVariables', function () {
            return array(
                'salt'      => Env::get('APP_TOKEN_CLIENT_SALT'),
                'rotations' => (int) Env::get('APP_TOKEN_CLIENT_ROTATIONS'),
                'keySize'   => (int) Env::get('APP_TOKEN_CLIENT_KEYSIZE')
            );
        });


        // Detect and set language automatically
        $container->set('lang', function () use ($container) {
            $settings = $container->get('settings');

            return (isset($settings['app']['langDefault']) ?
                Language::getLanguage($settings['app']['langDefault']) :
                'en_US'
            );
        });
    }


    /**
     * Loads Twig and thethe default views
     *
     * @return void
     */
    private function loadTwig(): void
    {
        $container =& $this->container;

        // Twig
        $container->set('view', function (ContainerInterface $container) {
            $views = sprintf('%s/app/Views/', ABSPATH);

            $twig = Twig::create($views, array(
                'cache' => false
            ));

            $twig->addExtension(new DebugExtension());
            $twig->addExtension(new CsrfExtension($container->get('csrf')));
            $twig->addExtension(new ClientVariablesExtension($container));
            $twig->addExtension(new Translate($container));
            return $twig;
        });
    }


    /**
     * Loads the database
     *
     * @return void
     */
    private function loadDatabase(): void
    {
        // Database
        $capsule = new Capsule();
        $capsule->addConnection(array(
            'driver'    => Env::get('APP_DATABASE_DRIVER'),
            'host'      => Env::get('APP_DATABASE_HOST'),
            'database'  => Env::get('APP_DATABASE_NAME'),
            'username'  => Env::get('APP_DATABASE_USER'),
            'password'  => Env::get('APP_DATABASE_PASS'),
            'charset'   => Env::get('APP_DATABASE_CHARSET'),
            'collation' => Env::get('APP_DATABASE_COLLATION'),
            'prefix'    => Env::get('APP_DATABASE_PREFIX'),
        ));

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }


    /**
     * Loads security settings and middlewares
     *
     * @return void
     */
    private function loadSecurity(): void
    {
        $app       = & $this->instance;
        $container = & $this->container;

        // Anti-csrf$contai
        $container->set('csrf', function () use ($app) {
            return new Guard($app->getResponseFactory());
        });

        $app->add('csrf');
        $app->addBodyParsingMiddleware();
    }


    private function loadLocale(): void
    {
        $lang = $this->container->get('lang');

        setlocale(LC_MESSAGES, $lang);
        bindtextdomain('app', __DIR__ . '/Language');
        textdomain('app');
    }


    /**
     * Loads the defined routes
     *
     * @return void
     */
    private function loadRoutes(): void
    {
        $app =& $this->instance; // Required for routes
        require_once __DIR__ . '/routes.php';
    }
}
