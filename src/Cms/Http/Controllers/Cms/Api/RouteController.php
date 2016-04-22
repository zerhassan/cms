<?php
namespace Cms\Http\Controllers\Cms\Api;

use Illuminate\Http\Request;
use Cms\Repository\RouteRepository;
use Cms\Http\Controllers\Cms\Base\ApiController;
use Cms;
use Cms\Models\Route;

class RouteController extends ApiController
{
    public function __construct(RouteRepository $repo)
    {
        $this->repo = $repo;
    }

    public function update(Request $request, $id)
    {

        $route = $this->repo->findById($id);
        $route->fill($request->all());
//        $route->slugify();
//        $route->setPrimaryDir();
        $route->save();

        return $this->show($request, $route->id);
    }
}
