<?php
namespace App\Application;

require_once __DIR__ . '/../../../vendor/autoload.php';

use Tracy\Debugger;
use \PDO;

/**
 * This class is used to find and retrieve configuration settings in both the
 * database and files.
 *
 * @category Configuration
 * @package  Application
 * @author   Nikolaj Jepsen <nj@codefighter.dk>
 * @license  No License
 * @link     http://progressmedia.dk
 */
class Config
{
    
    /**
     * Object dbh
     * Database handler
     */
    protected $dbh;

    /**
     * Object sth
     * Statement handler
     */
    protected $sth;


    /**
     * __construct
     *
     * @return void
     */


    public function __construct()
    {
        $this->dbh = \App\Database\Instance::get();
    }

    private function validate($param, $value) {
        if ($param == 'name') {
            $this->sth = $this->dbh->prepare("SELECT * FROM `settings` WHERE `name` = :value LIMIT 1");
        } elseif ($param == 'id') {
            $this->sth = $this->dbh->prepare("SELECT * FROM `settings` WHERE `id` = :value LIMIT 1");
        }
        $this->sth->execute(
            [
                ':value' => $value
            ]
        );

        if ($setting = $this->sth->fetch(PDO::FETCH_OBJ)) {
            return $setting;
        }

        return false;
    }

    public function getRowById($id) {
        if (!$setting = $this->validate('id', $id)) {
            return false;
        }

        return $setting;
    }

    /**
     * Find value from name in settings table.
     * get
     *
     * @param string $settingName Identifying name
     *
     * @return mixed
     */
    public function get($settingName)
    {
        if ($setting = $this->validate('name', $settingName)) {
            if (preg_match_all('/{{([^}]*)}}/', $setting->value, $matches)) {
                foreach ($matches as $match) {
                    if ($replace = $this->get($match[0])) {
                        $setting->value = str_replace('{{' . $match[0] . '}}', $replace, $setting->value);
                    }
                }
            }
            return $setting->value;
        }
        return false;
    }

    public function list() {
        $this->sth = $this->dbh->prepare("SELECT * FROM `settings`");
        $this->sth->execute();
        if ($settings = $this->sth->fetchAll(PDO::FETCH_ASSOC)) {
            return $settings;
        }

        return false;
    }

    public function create($name, $value)
    {
        if ($setting = $this->validate('name', $name)) {
            return false;
        }
        try {
            $this->sth = $this->dbh->prepare("INSERT INTO `settings` (`value`, `name`) VALUES (:value, :name)");
            $this->sth->bindParam(':value', $value, PDO::PARAM_STR);
            $this->sth->bindParam(':name', $name, PDO::PARAM_STR);
            $this->sth->execute();
        } catch (\Exception $exception) {
            Debugger::log('Error adding config row ' . $name . ': ' . $value);
            return false;
        }
        return true;
    }

    public function update($id, $name, $value) {
        if (!$setting = $this->validate('id', $id)) {
            return false;
        }

        try {
            $this->sth = $this->dbh->prepare("UPDATE `settings` SET `value` = :value, `name` = :name WHERE `id` = :id");
            $this->sth->bindParam(':name', $name, PDO::PARAM_STR);
            $this->sth->bindParam(':value', $value, PDO::PARAM_STR);
            $this->sth->bindParam(':id', $id, PDO::PARAM_INT);
            $this->sth->execute();
        } catch (\Exception $exception) {
            Debugger::log('Error updating config row ' . $name . ': ' . $value . ' - ' . $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Load environment files and find variable
     * get
     *
     * @param string $var Variable name
     *
     * @return string
     */
    public function getenv($var)
    {
        $env = \Dotenv\Dotenv::create(__DIR__ . '/../../../');
        $env = $env->load();

        return getenv($var) ?? '';
    }
}
