<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class CategoryProduct extends ActiveRecord {
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    public static function tableName()
    {
        return '{{%category_product}}';
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

    public static function categoryUpdate($newCategoryList)
    {
        $categoryList = static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->all();


    }

    public static function categoryList()
    {
        $categoryList = static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->all();

    }

    public function setHierarchy($categoryList)
    {
        // first traverse build each level's relationship map, but we know that 0 is first level which has no parent.
        $levelMap = [];
        $indexMapPerLevel = [];
        $indexCountFirstLevel = 0;
        $queue = new \SplQueue();
        foreach ($categoryList as $category) {
            $levelMap[$category['parent_id']][] = $category;
            if ($category['parent_id'] == 0) {
                $queue->enqueue($category['id']);
                $categoryHierarchy[] = [
                    'id' => $category['id'],
                    'name' => $category['name'],
                    'subCategories' => []
                ];
                $indexMapPerLevel[$category['id']] = $indexCountFirstLevel++;
            }
        }
        // What we have now is
        // $stack: [{category_id_list which has no parent},...]
        // $levelMap: [category_parent_id => [child_nodes],...]
        // $categoryHierarchy: [[category_node],...]
        // $indexMapPerLevel: [category_id => index,...]
        // traverse from $map[0], DFS
        $nextLevelQueue = new \SplQueue();
        $indexCountNewLevel = 0;
        $categoryHierarchyRef = &$categoryHierarchy;
        while (!$queue->isEmpty()) {
            if (($indexCountFirstLevel--) == 1) {
                $indexCountFirstLevel = $indexCountNewLevel;
                $queue = $nextLevelQueue;
                $categoryHierarchyRef = &$subCategoryHierarchy;
            }
            $parentId = $queue->dequeue();
            $childIndex = 0;
            if (isset($levelMap[$parentId])) {
                foreach ($levelMap[$parentId] as $key => $child) {

                    $nextLevelQueue->enqueue($child['id']);
                    $categoryHierarchyRef[$indexMapPerLevel[$parentId]]['subCategories'][$childIndex] = [
                        'id' => $child['id'],
                        'name' => $child['name'],
                        'subCategories' => []
                    ];
                    $subCategoryHierarchy[$indexCountNewLevel] = &$categoryHierarchyRef[$indexMapPerLevel[$parentId]]['subCategories'][$childIndex];
                    $childIndex++;
                    $indexMapPerLevel[$child['id']] = $indexCountNewLevel++;
                }
            }
        }
        return $categoryHierarchy;
    }

    public function getHierarchy($categoryList)
    {
        $parsedCategoryList = [
            'idList' => [],
            'objList' => []
        ];

        $queue = new \SplQueue();

        foreach ($categoryList as $category) {
            array_push($parsedCategoryList['idList'], $category['id']);
            $categoryNode = [
                'id' => $category['id'],
                'parent_id' => 0,
                'name' => $category['name']
            ];
//            array_push($parsedCategoryList['objList'], $categoryNode);
            $parsedCategoryList['objList'][$category['id']] = $categoryNode;
            $queue->enqueue($category);
        }
        while (!$queue->isEmpty()) {
            $category = $queue->dequeue();
            if (sizeof($category['subCategories']) != 0) {
                foreach ($category['subCategories'] as $subCategory) {
                    var_dump($subCategory);
                    array_push($parsedCategoryList['idList'], $subCategory['id']);
                    $subCategoryNode = [
                        'id' => $subCategory['id'],
                        'parent_id' => $category['id'],
                        'name' => $subCategory['name']
                    ];
//                    array_push($parsedCategoryList['objList'], $subCategoryNode);
                    $parsedCategoryList['objList'][$subCategory['id']] = $subCategoryNode;
                    $queue->enqueue($subCategory);
                }
            }
        }

        return $parsedCategoryList;
    }
}