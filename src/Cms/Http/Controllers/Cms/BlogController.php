<?php
namespace Cms\Http\Controllers\Cms;

use Cms\Http\Controllers\Cms\Base\CmsController;
use Illuminate\Http\Request;
use CmsRepository;
use Cache;


/**
 * Class BlogController
 * @package Cms\Http\Controllers
 */
class BlogController extends CmsController
{
    const CACHE_EXPIRE = 0.5;
    /**
     * @param $year
     * @param $month
     * @param $day
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPost(Request $request, $year, $month, $day, $slug)
    {
        $route = Cache::get($request->url(), function() use($request) {
            $value = CmsRepository::get('route')->findBy('url', $slug);
            Cache::put($request->url(), $value, self::CACHE_EXPIRE);
            return $value;
        });

        abort_if(!$route || $route->page->status != 'published', 404, 'Page Not Found');

        return $route->page->render();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(Request $request)
    {
        $posts = Cache::get($request->url(), function() use($request) {
            $value = CmsRepository::get('blog')->findLatestBlogPosts(10);

            $pageArray = $value->map(function($item) {
                return $item->id;
            });

            $routes = CmsRepository::get('route')->findForPages($pageArray);

            foreach ($value as $page) {
                $page->route = $routes->first(function($key, $item) use($page) {
                    return $item->page_id == $page->id;
                });
            }

            Cache::put($request->url(), $value, self::CACHE_EXPIRE);
            return $value;
        });

        return view('cms.themes.default.blog.blog_index', [
            'posts' => $posts,
        ]);
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Cms\Traits\Exception
     */
    public function getCategory(Request $request, $slug)
    {
        $posts = [];

        $category = Cache::get('blog:category:slug:'.$request->url(), function() use($request, $slug) {
            $value = CmsRepository::get('category')->findBy('slug', $slug);
            Cache::put('blog:category:slug:'.$request->url(), $value, self::CACHE_EXPIRE);
            return $value;
        });

        abort_if(!$category, 404, 'Page Not Found');

        $posts = Cache::get('blog:category:'.$request->url(), function() use($request, $category) {
            $value =  CmsRepository::get('blog')->findLatestBlogPosts(10, $category->id);

            $pageArray = $value->map(function($item) {
                return $item->id;
            });

            $routes = CmsRepository::get('route')->findForPages($pageArray);

            foreach ($value as $page) {
                $page->route = $routes->first(function($key, $item) use($page) {
                    return $item->page_id == $page->id;
                });
            }

            Cache::put('blog:category:'.$request->url(), $value, self::CACHE_EXPIRE);
            return $value;
        });

        return view('cms.themes.default.blog.blog_index', [
            'posts' => $posts,
        ]);
    }
}