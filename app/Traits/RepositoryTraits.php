<?php
/**
 * Class RepositoryTraits
 *
 * @date      4/9/2016
 * @author    Mosufy <mosufy@gmail.com>
 * @copyright Copyright (c) Mosufy
 */

namespace App\Traits;

use App\Helpers\CommonHelper;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

/**
 * Trait RepositoryTrait
 *
 * Contains common methods used in Repositories.
 */
trait RepositoryTraits
{
    /**
     * Get limit from request parameters
     *
     * @param array $params
     * @return int
     */
    protected function getLimit($params)
    {
        return !empty($params['limit']) ? $params['limit'] : 25;
    }

    /**
     * Get page from request parameters
     *
     * @param array $params
     * @return int
     */
    protected function getPage($params)
    {
        return !empty($params['page']) ? $params['page'] : 1;
    }

    /**
     * Get offset from request parameters
     *
     * @param array $params
     * @return int
     */
    protected function getOffset($params)
    {
        $page  = $this->getPage($params);
        $limit = $this->getLimit($params);

        return (($page - 1) * $limit);
    }

    /**
     * Get path
     *
     * @return mixed
     */
    protected function getPath()
    {
        return Request::url();
    }

    /**
     * Get order by value
     *
     * @param array $params
     * @return bool|string
     */
    protected function getOrderBy($params)
    {
        if (!empty($params['sort'])) {
            return ltrim($params['sort'], '-');
        }

        return false;
    }

    /**
     * Get sort by
     *
     * @param array $params
     * @return bool|string
     */
    protected function getSortBy($params)
    {
        if (empty($params['sort'])) {
            return false;
        }

        if (substr($params['sort'], 0, 1) === '-') {
            return 'desc';
        }

        return 'asc';
    }

    /**
     * Generate paginated data
     *
     * @param mixed $object
     * @param array $params
     * @return LengthAwarePaginator
     */
    protected function getPaginated($object, $params)
    {
        $params = CommonHelper::unsetInternalParams($params);

        $params['page']  = $this->getPage($params);
        $params['limit'] = $this->getLimit($params);
        $offset          = $this->getOffset($params);
        $path            = $this->getPath();
        $orderBy         = $this->getOrderBy($params);
        $sortBy          = $this->getSortBy($params);

        // Fetch the scoped data
        $result = $object->skip($offset)->limit($params['limit']);

        // Check if require order by
        if (!empty($orderBy)) {
            $result = $result->orderBy($orderBy, $sortBy)->get();
        } else {
            $result = $result->get();
        }

        // Get total count
        $total_count = $object->select(DB::raw('count(*) as count'))->value('count');

        // Generate paginator
        $paginated = new LengthAwarePaginator($result, $total_count, $params['limit'], $params['page'], ['path' => $path]);
        $paginated = $paginated->appends($params);

        return $paginated;
    }

    /**
     * Store cached data
     *
     * @param string $key
     * @param mixed  $data
     * @param int    $expire
     * @param string $subKey
     * @return void
     */
    protected function putCache($key, $data, $expire = 30, $subKey = '')
    {
        if (empty($subKey)) {
            Cache::put($key, $data, $expire);
            return;
        }

        $cached          = Cache::get($key);
        $cached[$subKey] = $data;

        Cache::put($key, $cached, $expire);
    }

    /**
     * Return cached data if exists
     *
     * @param string $key
     * @param string $subKey
     * @return mixed
     */
    protected function getCache($key, $subKey = '')
    {
        if (Cache::has($key)) {
            $cached = Cache::get($key);

            if (is_array($cached)) {
                if (!empty($cached[$subKey])) {
                    return $cached[$subKey];
                }
            } else {
                return $cached;
            }
        }

        return false;
    }
}