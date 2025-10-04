<?php
trait SingletonTrait
{ 
    protected static $instance;

    /**
     * Make this capsule instance available globally.
     *
     * @return void
     */
    public function setAsGlobal()
    {
        static::$instance = $this;
    }

    private $jsonAssoc = false;


    public static function getInstance() {
        return static::$instance;
    }

}