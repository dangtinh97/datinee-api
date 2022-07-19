<?php


namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Jenssegers\Mongodb\Eloquent\Model;
use MongoDB\Operation\FindOneAndUpdate;

class BaseRepository
{

    const OPTION_RESPONSE = [
        'typeMap' => [
            'array' => 'array',
            'document' => 'array',
            'root' => 'array'
        ]
    ];

    /**
     * @var Model
     */
    protected $model;

    /**
     * AbstractRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritdoc
     */
    public function find(array $conditions = [])
    {
        return $this->model->where($conditions)->get();
    }

    public function countWithCondition(array $conditions = [])
    {
        return $this->model->where($conditions)->count();
    }

    /**
     * @param array $conditions
     * @return Model|null
     */
    public function findOne(array $conditions): ?Model
    {
        return $this->model->where($conditions)->first();
    }

    /**
     * @inheritdoc
     */
    public function findById(string $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): ?Model
    {
        return $this->model->create(array_merge($attributes,[
            'deleted_flag' => false
        ]));
    }

    /**
     * @param Model $model
     * @param array $attributes
     * @return Model|null
     */
    public function update(Model $model, array $attributes = []): ?Model
    {
        $model->fill($attributes);

        return $model->save() ? $model : null;
    }

    /**
     * @inheritdoc
     */
    public function save(Model $model)
    {
        return $model->save();
    }

    /**
     * @inheritdoc
     */
    public function delete(Model $model)
    {
        return $model->delete();
    }

    /**
     * @param array $cond
     *
     * @return mixed
     */
    public function deleteByCond(array $cond):mixed
    {
        return $this->model::query()->where($cond)->delete();
    }

    /**
     * @inheritdoc
     */
    public function deleteByIds(array $ids)
    {
        return $this->model::destroy($ids);
    }

    public function findOneLast($conditions)
    {
        return $this->model->where($conditions)->orderBy('_id', 'DESC')->first();
    }

    public function insert(array $data)
    {
        return $this->model->insert($data);
    }

    /**
     * @return int
     */
    public function counter():int
    {
        $data = DB::table('counters')->raw(function ($collection) {
            return $collection->findOneAndUpdate(
                [
                    'name_collection' => $this->model->getTable()
                ],
                [
                    '$inc' => [
                        'id' => 1
                    ]
                ],
                [
                    'upsert' => true,
                    'new' => true,
                    'returnNewDocument' => true,
                    'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER,
                    'typeMap' => [
                        'array' => 'array',
                        'document' => 'array',
                        'root' => 'array'
                    ]
                ]
            );
        });

        return $data['id'];
    }

    public function findOneAndUpdate($cond,$update){
        $raw = DB::table($this->model->getTable())->raw(function ($collection) use ($cond,$update) {
            return $collection->findOneAndUpdate($cond,$update, [
                'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER,
            ]);
        });
        return $raw;
    }
}
