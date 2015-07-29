<?php
    namespace common\models;

    use Yii;
    use yii\behaviors\TimestampBehavior;
    use yii\db\ActiveRecord;

    class CourseType extends ActiveRecord {
        const STATUS_DELETED = 0;
        const STATUS_ACTIVE = 10;

        public static function tableName()
        {
            return '{{%course_type}}';
        }

        public function behaviors()
        {
            return [
                TimestampBehavior::className(),
            ];
        }

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                ['status', 'default', 'value' => self::STATUS_ACTIVE],
                ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ];
        }

        public function updateTypeList($newTypeList)
        {
            $updateSuccess = true;
            $connection = $this->getDb();
            $oriTypeList = static::find()
                ->where(['status' => self::STATUS_ACTIVE])
                ->all();
            $newTypeList = $this->getHierarchy($newTypeList);
            $typeListDiff = $this->compareList($oriTypeList, $newTypeList);
            $typeListNeedUpdate = $typeListDiff['update'];
            $typeListNeedDelete = $typeListDiff['delete'];
            $typeListNeedInsert = $typeListDiff['insert'];

            $transaction = $connection->beginTransaction();
            try {
                foreach ($typeListNeedUpdate as $typeNeedUpdate) {
                    $query = 'UPDATE ' . $this->tableName() .
                        ' SET name=\'' . $typeNeedUpdate['name'] . '\'' .
                        ', parent_id=' . $typeNeedUpdate['parent_id'] .
                        ' WHERE id=' . $typeNeedUpdate['id'];
                    $updateAction = $connection->createCommand($query)->execute();
                    if ($updateAction == 0) {
                        $updateSuccess = false;
                    }
                }

                foreach ($typeListNeedDelete as $typeNeedDelete) {
                    $query = 'DELETE FROM ' . $this->tableName() .
                        ' WHERE id=' . $typeNeedDelete['id'];
                    $deleteAction = $connection->createCommand($query)->execute();
                    if ($deleteAction == 0) {
                        $updateSuccess = false;
                    }
                }

                foreach ($typeListNeedInsert as $typeNeedInsert) {
                    $time = time();
                    $query = 'INSERT INTO ' . $this->tableName() .
                        ' (`name`, `parent_id`, `created_at`, `updated_at`) VALUES ' .
                        '("' . $typeNeedInsert['name'] . '", ' . $typeNeedInsert['parent_id']
                        . ", $time, $time" . ')';
                    $insertAction = $connection->createCommand($query)->execute();
                    if ($insertAction == 0) {
                        $updateSuccess = false;
                    }
                }

                if ($updateSuccess) {
                    $transaction->commit();
                } else {
                    $transaction->rollback();
                }
            } catch (\Exception $e) {
                $transaction->rollback();
                throw $e;
            }

            return $updateSuccess;
        }

        public function getTypeList()
        {
            $typeList = static::find()
                ->where(['status' => self::STATUS_ACTIVE])
                ->orderBy(['id' => SORT_DESC])
                ->all();
            if($typeList) {
                $maxId = $typeList[0]->id;
                $typeHierarchyList = $this->setHierarchy($typeList);
            } else {
                $maxId = 0;
                $typeHierarchyList = [];
            }

            return ['types' => $typeHierarchyList, 'maxId' => $maxId];
        }

        public function setHierarchy($typeList)
        {
            // first traverse build each level's relationship map, but we know that 0 is first level which has no parent.
            $levelMap = [];
            $indexMapPerLevel = [];
            $indexCountFirstLevel = 0;
            $queue = new \SplQueue();
            $typeHierarchy = [];

            foreach ($typeList as $type) {
                $levelMap[$type['parent_id']][] = $type;
                if ($type['parent_id'] == 0) {
                    $queue->enqueue($type['id']);
                    $typeHierarchy[] = [
                        'id' => $type['id'],
                        'name' => $type['name'],
                        'subTypes' => []
                    ];
                    $indexMapPerLevel[$type['id']] = $indexCountFirstLevel++;
                }
            }

            // What we have now is
            // $stack: [{type_id_list which has no parent},...]
            // $levelMap: [type_parent_id => [child_nodes],...]
            // $typeHierarchy: [[type_node],...]
            // $indexMapPerLevel: [type_id => index,...]
            // traverse from $map[0], DFS
            $nextLevelQueue = new \SplQueue();
            $indexCountNewLevel = 0;
            $typeHierarchyRef = &$typeHierarchy;
            while (!$queue->isEmpty()) {
                if (($indexCountFirstLevel--) == 0) {
                    $indexCountFirstLevel = $indexCountNewLevel;
                    $queue = $nextLevelQueue;
                    $typeHierarchyRef = &$subTypeHierarchy;
                    if ($queue->isEmpty()) {
                        break;
                    }
                }

                $parentId = $queue->dequeue();
                $childIndex = 0;
                if (isset($levelMap[$parentId])) {
                    foreach ($levelMap[$parentId] as $key => $child) {

                        $nextLevelQueue->enqueue($child['id']);
                        $typeHierarchyRef[$indexMapPerLevel[$parentId]]['subTypes'][$childIndex] = [
                            'id' => $child['id'],
                            'name' => $child['name'],
                            'subTypes' => []
                        ];
                        $subTypeHierarchy[$indexCountNewLevel] = &$typeHierarchyRef[$indexMapPerLevel[$parentId]]['subTypes'][$childIndex];
                        $childIndex++;
                        $indexMapPerLevel[$child['id']] = $indexCountNewLevel++;
                    }
                }
            }
            return $typeHierarchy;
        }

        public function getHierarchy($typeList)
        {
            $parsedTypeList = [
                'idList' => [],
                'objList' => []
            ];

            $queue = new \SplQueue();

            foreach ($typeList as $type) {
                array_push($parsedTypeList['idList'], $type['id']);
                $typeNode = [
                    'id' => $type['id'],
                    'parent_id' => 0,
                    'name' => $type['name']
                ];
//            array_push($parsedTypeList['objList'], $typeNode);
                $parsedTypeList['objList'][$type['id']] = $typeNode;
                $queue->enqueue($type);
            }
            while (!$queue->isEmpty()) {
                $type = $queue->dequeue();
                if (sizeof($type['subTypes']) != 0) {
                    foreach ($type['subTypes'] as $subType) {
                        array_push($parsedTypeList['idList'], $subType['id']);
                        $subTypeNode = [
                            'id' => $subType['id'],
                            'parent_id' => $type['id'],
                            'name' => $subType['name']
                        ];
//                    array_push($parsedTypeList['objList'], $subTypeNode);
                        $parsedTypeList['objList'][$subType['id']] = $subTypeNode;
                        $queue->enqueue($subType);
                    }
                }
            }

            return $parsedTypeList['objList'];
        }

        public function compareList($oldList, $newList) {
            $diffNodes = [
                'update' => [],
                'delete' => [],
                'insert' => []
            ];
            foreach ($oldList as $oldNode) {
                if(!array_key_exists($oldNode['id'], $newList)) {
                    // deleted node
                    $diffNodes['delete'][] = $oldNode;
                } else {
                    //updated node
                    $newNode = $newList[$oldNode['id']];
                    if($newNode['name'] != $oldNode['name'] || $newNode['parent_id'] !=
                        $oldNode['parent_id']) {
                        $diffNodes['update'][] = $newNode;
                    }
                    unset($newList[$oldNode['id']]);
                }
            }

            ksort($newList);
            foreach ($newList as $newNode) {
                // added node
                $diffNodes['insert'][] = $newNode;
            }

            return $diffNodes;
        }
    }