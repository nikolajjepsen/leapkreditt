<?php
namespace App\Database;

require_once __DIR__ . '/../../../vendor/autoload.php';
use Tracy\Debugger;
use \PDO;

/**
 * PDO connection using a singleton pattern. Injectable in classes if need be.
 * 
 * @category Database
 * @package  Application
 * @author   Nikolaj Jepsen <nj@codefighter.dk>
 * @license  No License
 * @link     http://progressmedia.dk
 */
class Instance
{

    /**
     * Carry the instance of the database class.
     * 
     * @var static
     */
    protected static $instance = null;

    /**
     * The database object
     * 
     * @var object
     */
    protected $pdo;

    protected $config;

    
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        Debugger::$logSeverity = E_NOTICE | E_WARNING;
        Debugger::enable(Debugger::DETECT, __DIR__ . '/../../log');

        try {
            $this->pdo = new PDO(
                sprintf(
                    '%s:host=%s;port=%s;dbname=%s', 
                    'mysql',
                    '127.0.0.1',
                    3306,
                    ''
                ),
                '',
                ''
            );   
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec("SET NAMES utf8");
        } catch (Exception $e) {
            Debugger::log('Unable to connect to database: ' . $e->getMessage());
        }
    }
    
    
    /**
     * Get the current instance or return a new.
     * get
     *
     * @return void
     */
    public static function get()
    {
        if (is_null(self::$instance))
            self::$instance = new Instance();
        return self::$instance;
    }

    
    /**
     * __call
     *
     * @param mixed $method Method
     * @param mixed $args   Argument
     *
     * @return void
     */
    public function __call($method, $args)
    {
        $callable = array(
            $this->pdo,
            $method
        );
        if (is_callable($callable)) {
            return call_user_func_array($callable, $args);
        }
    }
}