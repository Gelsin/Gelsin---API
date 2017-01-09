<?php


namespace App\Gelsin\Repositories\Eloquents;


use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository
{
    protected $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function getNew(array $attributes = [])
    {
        return $this->model->newInstance($attributes);
    }
}