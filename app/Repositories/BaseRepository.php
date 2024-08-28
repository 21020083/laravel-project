<?php

namespace App\Repositories;

use App\Common\CommonConst;
use App\Helpers\CommonHelper;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Hash;

abstract class BaseRepository
{
    protected $model;

    /**
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->setModel();
    }

    /**
     * @throws BindingResolutionException
     */
    public function setModel(): void
    {
        $this->model = app()->make($this->getModel());
    }

    abstract protected function getModel();

    public function find($id, array $relations = [])
    {
        if (is_array($id)) {
            $entity = $this->model->whereIn('id', $id)->get();
        } else {
            $entity = $this->model->find($id);
        }

        if (count($relations)) {
            return $entity->load($relations);
        }

        return $entity;
    }

    public function findOrFail($id, $relations = [])
    {
        $entity = $this->model->findOrFail($id);

        if (count($relations)) {
            return $entity->load($relations);
        }

        return $entity;
    }

    public function findAll($columns = ['*'])
    {
        return $this->model::select($columns)->get();
    }

    public function all()
    {
        return $this->model->all();
    }

    public function findByCondition(mixed $condition, array $relations = [], array $relationCounts = [])
    {
        $entities = $this->model->select($this->model->selectable ?? ['*']);

        if (count($relations)) {
            $entities = $entities->with($relations);
        }

        // load relation counts
        if (count($relationCounts)) {
            $entities = $entities->withCount($relationCounts);
        }

        if (count($condition) && method_exists($this, 'search')) {
            foreach ($condition as $key => $value) {
                $entities = $this->search($entities, $key, $value);
            }
        }

        return $entities;
    }

    private function stripAllFields($fields)
    {
        foreach ($fields as $key => $value) {
            if (is_array($value)) {
                $fields[$key] = $this->stripAllFields($value);
            } else {
                if (is_string($value)) {
                    $fields[$key] = strip_tags($value);
                }
            }
        }

        return $fields;
    }

    public function create(array $attributes)
    {
        $attributes = $this->stripAllFields($attributes);
        if(isset($attributes['password'])){
            $attributes['password'] = Hash::make($attributes['password']);
        }

        return $this->model->create($attributes);
    }

    public function update($ids, array $attributes)
    {
        $attributes = $this->stripAllFields($attributes);
        if (is_array($ids)) {
            return $this->model->whereIn('id', $ids)->update($attributes);
        }

        $object = $this->model->findOrFail($ids);
        $object->fill($attributes);
        $object->save();

        return $object;
    }

    public function delete(string $id)
    {
        $result = $this->find($id);
        if ($result) {
            $result->delete();

            return true;
        }

        return false;
    }

    public function deleteMulti(array $ids = [])
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    public function deleteBy(array $conditions): bool|null
    {
        return $this->model->where($conditions)->delete();
    }

    public function getListByConditions($condition, $relations = [], $relationCounts = [])
    {
        $condition = CommonHelper::removeNullValue($condition);
        $data = collect($condition);

        // select list column
        $entities = $this->model->select($this->model->selectable ?? ['*']);

        // load relation counts
        if (count($relationCounts)) {
            $entities = $entities->withCount($relationCounts);
        }

        // load relations
        if (count($relations)) {
            $entities = $entities->with($relations);
        }

        // filter list by condition
        if (count($condition) && method_exists($this, 'search')) {
            foreach ($condition as $key => $value) {
                $entities = $this->search($entities, $key, $value);
            }
        }

        // order list
        $orderBy = $data->has('sort') && in_array($data['sort'], $this->model->sortable) ? $data['sort'] : $this->model->getKeyName();
        $entities = $entities->orderBy($orderBy, $data->has('sortType') && $data['sortType'] == CommonConst::ORDER_TYPE_ASC ? CommonConst::ORDER_TYPE_ASC : CommonConst::ORDER_TYPE_DESC);

        // limit result
        $limit = $data->has('limit') ? (int)$data['limit'] : CommonConst::DEFAULT_PER_PAGE;
        if ($limit) {
            return $entities->paginate($limit);
        }

        return $entities->get();
    }
}
