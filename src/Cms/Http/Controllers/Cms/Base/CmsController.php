<?php
namespace Cms\Http\Controllers\Cms\Base;

use App\Http\Controllers\Controller;
use Cms\Managers\ImageManager;
use Cms\Managers\UserManager;
use Cms\Traits\CmsRepo;
use Faker\Factory;

/**
 * Class CmsController
 * @package Cms\Http\Controllers
 */
class CmsController extends Controller
{

    use CmsRepo;

    /**
     * @var Repository
     */
    protected $repo;

    /**
     * Enable DB Logging
     */
    public function enableDBLog()
    {
        DB::enableQueryLog();
    }


    /**
     * Ouput DB Logging
     */
    public function outputDBLog()
    {
        $log = DB::getQueryLog();
        var_dump($log);
    }

    /**
     * Create a UUID
     *
     * @return string
     */
    public function createUuid()
    {
        $faker = Factory::create();
        return $faker->uuid;
    }

}