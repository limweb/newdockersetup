<?php
require_once __DIR__ . '/BaseService.php';

class BasedbService extends BaseService
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function model()
    {
        return null;
    }
}
