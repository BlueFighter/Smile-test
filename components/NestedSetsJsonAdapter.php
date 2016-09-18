<?php
namespace app\components;

use yii;
use yii\base\Component;
use yii\helpers\Json;

class NestedSetsJsonAdapter extends Component
{
    private $nested_tree;
    private $adapt_tree;
    private $flag = 0;
    private $p_elements = []; // массив указателей на узлы
    function __construct($nested_tree)
    {
        $this->nested_tree = $nested_tree;
    }

    /**
     * 
     * @return string JSON string with adapted Nested sets array to display it with js
     */
    public function adaptTree()
    {
        foreach ($this->nested_tree as $key => $value)
        {
            $this->appendNode($value);
        }
        return Json::encode($this->adapt_tree);
    }

    /**
     * @param array $new_element - add node to adapt array after his father
     */
    private function appendNode($new_element)
    {
        if($this->flag == 0)
        {
            $this->adapt_tree[] = $new_element;
            $this->p_elements[] = &$this->adapt_tree[0]; //Записываем указатель на первый элемент
        }
        else
        {

            $index = count($this->p_elements) -1;
            $p_last_element = &$this->p_elements[$index];
            while (1) 
            {
                if ($new_element['depth_level'] == $p_last_element['depth_level'] + 1 &&
                    $new_element['left_key'] > $p_last_element['left_key'] &&
                    $new_element['right_key'] < $p_last_element['right_key']
                   )
                {
                    break;
                }
                else 
                {
                    $index --;
                    $p_last_element = &$this->p_elements[$index];
                    continue;
                }
            }

            $p_last_element['children'][] = $new_element;
            $index = count($p_last_element['children']) -1;
            $this->p_elements[] = &$p_last_element['children'][$index];
        }
        $this->flag++;
    }
}
