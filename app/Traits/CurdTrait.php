<?php


namespace App\Traits;


use App\Exceptions\CurdException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait CurdTrait
{
    public function create($fields)
    {
        /** @var Model $entity */
        $entity = new $this->entityClass();
        foreach ($entity::DEFAULT_VALUES ?? [] as $field => $defaultVal) {
            if (!isset($fields[$field])) {
                $fields[$field] = $defaultVal;
            }
        }
        foreach ($fields as $field => $val) {
            if ($field == $entity->getKeyName()) {
                continue;
            }
            $setMethod = $this->getFieldSetterName($field);
            if (method_exists($entity, $setMethod)) {
                $entity->{$setMethod}($val);
            }
        }
        $entity->save();

        return $entity;
    }

    public function update($condition, $params)
    {
        if (empty($params)) {
            return null;
        }
        $this->checkClassMember('CHANGEABLE_FIELDS');
        $entity = $this->retrieve($condition);
        if (!$entity) {
            throw new CurdException('数据不存在', Response::HTTP_NOT_FOUND);
        }
        foreach ($entity::CHANGEABLE_FIELDS as $field) {
            $entity->{$this->getFieldSetterName($field)}($params[$field]);
        }
        $entity->save();

        return $entity;
    }

    public function updateOrCreate($condition, $params)
    {
        $this->checkClassMember('CHANGEABLE_FIELDS');
        $entity = $this->retrieve($condition);
        if (!$entity) {
            $entity = new $this->entityClass();
            foreach ($condition as $field => $val) {
                if ($field == $entity->getKeyName()) {
                    continue;
                }
                $setMethod = $this->getFieldSetterName($field);
                if (method_exists($entity, $setMethod)) {
                    $entity->{$setMethod}($val);
                }
            }
        }
        foreach ($entity::CHANGEABLE_FIELDS as $field) {
            if (isset($params[$field])) {
                $entity->{$this->getFieldSetterName($field)}($params[$field]);
            }
        }
        $entity->save();

        return $entity;
    }

    public function retrieve($params)
    {
        return is_array($params)
            ? $this->entityClass::where($params)->first()
            : $this->getSearchQuery($params)->first();
    }

    /**
     * @param array $params
     * @param null $page
     * @param null $limit
     * @param string|array $orderBy
     * @param array $retrieveExtraData 额外数据，
     * @return array|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     * @throws CurdException
     *@example $retrieveExtraData = [
     *  [
     *     'extra_fields' => ['user_name' => 'name'], //可为多个
     *     'entity_class' => '\App\Entity\User',
     *     'foreign_key' => 'user_id',
     *     'local_key' => 'id'
     *   ]
     * ]
     *
     * @example $retrieveExtraData = [
     *   'extra_fields' => 'levels',
     *   'processor' => function ($lineData) {} //传入自定义闭包方法处理器，或可调用的方法名
     * ]
     */
    public function retrieveList($params = [], $page = null, $limit = null, $orderBy = null, $retrieveExtraData = [])
    {
        $query = $this->getSearchQuery($params);
        if ($orderBy) {
            if (is_array($orderBy)) {
                foreach ($orderBy as $column => $direction) {
                    $query->orderBy($column, $direction);
                }
            } else {
                $query->orderBy($orderBy);
            }
        }
        if ($page && $limit) {
            $result = $query->paginate(intval($limit));
        }
        $data = ($page && $limit)
            ? ['list' => $result->items() ,'total' => $result->total()]
            : ['list' => $query->get()->toArray()];

        // 拆分规则
        $extraDataMap = [];
        $localKeys = [];
        foreach ($retrieveExtraData as $rule) {
            $fields = Arr::wrap($rule['extra_fields']);
            if (isset($rule['processor']) && is_callable($rule['processor'])) {
                foreach ($data['list'] as $key => $item) {
                    foreach ($fields as $column) {
                        $data['list'][$key][$column] = $rule['processor']($item);
                    }
                }
                continue;
            }

            $localKeys[$rule['entity_class']][$rule['local_key']] = array_unique(array_values($fields));
            foreach ($fields as $key => $targetField) {
                $current = $rule;
                $extraField = is_numeric($key) ? $targetField : $key;
                $current['extra_field'] = $extraField;
                $current['target'] = $targetField;
                $extraDataMap[$extraField] = $current;
            }
        }

        // 汇总 查询依据字段 数据
        $localKeyValues = [];
        $targetValues = [];
        foreach ($extraDataMap as $extraField => $rule) {
            $values = array_unique(array_column($data['list'], $rule['foreign_key']));
            $localKeyValues[$rule['local_key']] = array_unique(array_merge($localKeyValues[$rule['local_key']] ?? [], $values));
        }

        // 查询目标表数据
        foreach ($localKeys as $entityClass => $arr) {
            foreach ($arr as $localKey => $targets) {
                $entities = $entityClass::query()->whereIn($localKey, $localKeyValues[$localKey])->get();
                foreach ($targets as $targetField) {
                    $targetValues[$entityClass][$targetField] = $entities->pluck($targetField, $localKey)->toArray();
                }
            }
        }

        // 补充进列表数据
        foreach ($data['list'] as $key => $item) {
            foreach ($extraDataMap as $extraField => $rule) {
                $entityClass = $rule['entity_class'];
                $targetField = $rule['target'];
                $foreignKey = $rule['foreign_key'];
                $data['list'][$key][$extraField] = $targetValues[$entityClass][$targetField][$item[$foreignKey]];
            }
        }

        return $data;
    }

    public function delete($key)
    {
        /** @var Model $entityObj */
        $entityObj = new $this->entityClass();

        return $this->entityClass::where($entityObj->getKeyName(), $key)->delete();
    }

    /**
     * @param $params
     * @return Builder
     * @throws CurdException
     */
    protected function getSearchQuery($params)
    {
        if (is_array($params)) {
            foreach ($params as $key => $val) {
                if ($val === '') {
                    unset($params[$key]);
                }
            }
        }

        $this->checkClassMember('SEARCH_FIELDS');

        /** @var Builder $query */
        $query = $this->entityClass::query();
        foreach ($this->entityClass::SEARCH_FIELDS as $field) {
            if (isset($params[$field])) {
                if (is_array($params[$field])) {
                    if (array_keys($params[$field]) === range(0, count($params[$field]) - 1)) { //值为索引数组，表示in查询
                        $query->whereIn($field, $params[$field]);
                    } else { //值为关联数组，表示指定操作如 ['like' => '123%']为模糊搜索
                        $query->where($field, array_keys($params[$field])[0], array_values($params[$field])[0]);
                    }
                } else {
                    $query->where($field, $params[$field]);
                }
            }
        }

        return $query;
    }

    protected function checkClassMember($constantName)
    {
        if (!isset($this->entityClass)) {
            throw new CurdException('请先定义成员变量: $entityClass');
        }
        if (!defined($this->entityClass . "::{$constantName}")) {
            throw new CurdException("请先定义实体类常量: {$constantName}");
        }
    }

    protected function getFieldSetterName($field)
    {
        return 'set' . Str::studly($field);
    }
}
