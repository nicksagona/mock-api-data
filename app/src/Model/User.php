<?php

namespace Mock\Api\Model;

use Mock\Api\Table;

class User extends AbstractModel
{

    /**
     * Search fields to select
     * @var array
     */
    protected $searchFields = ['id', 'username', 'first_name', 'last_name', 'email'];

    /**
     * Method to get all users
     *
     * @param  int    $page
     * @param  int    $limit
     * @param  string $sort
     * @param  mixed  $filter
     * @param  mixed  $fields
     * @return array
     */
    public function getAll($page = null, $limit = null, $sort = null, $filter = null, $fields = null)
    {
        if ((null !== $limit) && (null !== $page)) {
            $page = ((int)$page > 1) ? ($page * $limit) - $limit : null;
        } else if (null !== $limit){
            $limit = (int)$limit;
        } else {
            $page  = null;
            $limit = null;
        }

        $orderBy = null;

        if (null !== $sort) {
            if (strpos($sort, ',') !== false) {
                $sortAry = explode(',', $sort);
            } else {
                $sortAry = [$sort];
            }
            foreach ($sortAry as $sort) {
                if (substr($sort, 0, 1) == '-') {
                    $sort  = substr($sort, 1);
                    $order = 'DESC';
                } else {
                    $order = 'ASC';
                }
                $orderBy .= ((null !== $orderBy) ? ', ' : '') . $sort . ' ' . $order;
            }
        }

        if (!empty($filter)) {
            return Table\Users::findBy($this->getFilter($filter), [
                'select' => $this->getSearchFields($fields),
                'offset' => $page,
                'limit'  => $limit,
                'order'  => $orderBy,
            ])->toArray();
        } else {
            return Table\Users::findAll([
                'select' => $this->getSearchFields($fields),
                'offset' => $page,
                'limit'  => $limit,
                'order'  => $orderBy
            ])->toArray();
        }
    }

    /**
     * Method to get user by ID
     *
     * @param  int $id
     * @return array
     */
    public function getById($id)
    {
        return Table\Users::findById($id)->toArray();
    }

    /**
     * Method to save new user
     *
     * @param  array $data
     * @return array
     */
    public function save(array $data)
    {
        $user = new Table\Users([
            'username'   => (isset($data['username'])) ? $data['username'] : null,
            'first_name' => (isset($data['first_name'])) ? $data['first_name'] : null,
            'last_name'  => (isset($data['last_name'])) ? $data['last_name'] : null,
            'email'      => (isset($data['email'])) ? $data['email'] : null
        ]);
        $user->save();

        return $user->toArray();
    }

    /**
     * Method to update user
     *
     * @param  int   $id
     * @param  array $data
     * @return array
     */
    public function update($id, array $data)
    {
        $user = Table\Users::findById($id);

        if (isset($user->id)) {
            $user->username   = (isset($data['username'])) ? $data['username'] : $user->username;
            $user->first_name = (isset($data['first_name'])) ? $data['first_name'] : $user->first_name;
            $user->last_name  = (isset($data['last_name'])) ? $data['last_name'] : $user->last_name;
            $user->email      = (isset($data['email'])) ? $data['email'] : $user->email;
            $user->save();
        }

        return $user->toArray();
    }

    /**
     * Delete user
     *
     * @param  int   $id
     * @return int
     */
    public function delete($id)
    {
        $user = Table\Users::findById($id);
        if (isset($user->id)) {
            $user->delete();
            return 204;
        } else {
            return 404;
        }
    }

    /**
     * Remove users
     *
     * @param  array $data
     * @return int
     */
    public function remove(array $data)
    {
        foreach ($data as $id) {
            $user = Table\Users::findById($id);
            if (isset($user->id)) {
                $user->delete();
            }
        }
        return 204;
    }

    /**
     * Method to get user count
     *
     * @param  mixed  $filter
     * @return int
     */
    public function getCount($filter = null)
    {
        if (!empty($filter)) {
            return Table\Users::getTotal($this->getFilter($filter));
        } else {
            return Table\Users::getTotal();
        }
    }

}
