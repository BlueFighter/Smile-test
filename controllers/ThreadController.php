<?php

namespace app\controllers;

use yii;
use app\models\NestedSets;
use app\models\GenerateForm;
use yii\web\Controller;
use app\components\TreeAdapter;
use app\components\NestedSetsJsonAdapter;


class ThreadController extends Controller
{

    /**
     * @inheritdoc
     */
    /**
     * @param null $ids - id of node which will be root, or string with id's '1,2,3'
     * for find them parent 
     * @return string
     */
    public function actionIndex($ids = null)
    {
        $modelForm = new GenerateForm(); // Объект формы
        if ($modelForm->load(Yii::$app->request->post()) && $modelForm->validate()) {
            $tree = new TreeAdapter($modelForm->nodes); // При правильной отправке формы
            $tree->GenerateTree();                      // в базе создается новое дерево
        }
        if(!isset($modelForm->nodes))
            $modelForm->nodes = 20; // Значение по умолчанию для поля "Количество узлов"
        // Обработка ситуаций, когда пришли некие параметры на вход
        if (!is_null($ids))
        {
            $ar_tread = explode(',', $ids);
            // Если параметр только один, выбираем его и все элменты между его правым и левым ключом
            if(count($ar_tread) == 1)
            {
                $root = NestedSets::find()
                    ->where(['id' => $ar_tread[0]])
                    ->asArray()
                    ->one();
                if (is_array($root))
                {
                    $query = NestedSets::find()
                        ->where(['>=', 'left_key', $root['left_key']])
                        ->andWhere(['<=', 'right_key', $root['right_key']])
                        ->orderBy('left_key')
                        ->asArray()
                        ->all();
                }
            }
            // Если в url несколько параметров разделенных запятой находим их по id в базе данных
            // Проверям сходится ли количество пришедших id и найденых по ним в  базе элементов
            // Если несуществующих не было, проверяем существует ли для них общий предок
            // выбираем ветки корнями которых являются элменты id которых пришли в параметре
            // помещаем их под общего родителя и выводим
            if(count($ar_tread) > 1)
            {
                    $ar_root = NestedSets::find()
                        ->where(['id' => $ar_tread])
                        ->orderBy('left_key')
                        ->asArray()
                        ->all();
                if(count($ar_root) == count($ar_tread))
                {
                    $parent = NestedSets::find()
                        ->where(['<', 'left_key' , $ar_root[0]['left_key']])
                        ->andWhere(['>', 'right_key', $ar_root[count($ar_root)-1]['right_key']])
                        ->andWhere(['depth_level' => $ar_root[0]['depth_level'] -1])
                        ->andWhere(['depth_level' => $ar_root[count($ar_root)-1]['depth_level'] -1])
                        ->asArray()
                        ->one();
                    if(is_array($parent))
                    {
                        $res_query[] = $parent;
                        foreach ($ar_root as $root)
                        {
                            $query = NestedSets::find()
                                ->where(['>=', 'left_key', $root['left_key']])
                                ->andWhere(['<=', 'right_key', $root['right_key']])
                                ->orderBy('left_key')
                                ->asArray()
                                ->all();
                            $res_query = array_merge($res_query, $query);
                        }
                        $query = $res_query;
                    }
                }
            }
        }
    // Полная выборка дерева
        if(!isset($query)) {
            $query = NestedSets::find()
                ->asArray()
                ->orderBy('left_key')
                ->all();
        }
        $adaptTree = new NestedSetsJsonAdapter($query); 
        $query = $adaptTree->adaptTree();    // Адаптация массива для визуализации через плагин

        return $this->render('index', [
            'modelForm' =>$modelForm,
            'query' => $query,
        ]);
    }
}