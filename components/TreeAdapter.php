<?php
namespace app\components;

use yii;
use yii\base\Component;
use app\models\NestedSets;

class TreeAdapter extends Component
{
private $temp_tree = [];
private $id;
private $nodes_left;

    function __construct($node_quant)
    {
        $this->nodes_left = $node_quant;
    }

    /**
     * This function remove old nodes from db generate and write to db new Nested Sets tree
     */
    public function GenerateTree()
    {
        $this->temp_tree[] = ['id' => 0, 'name' => '0', 'left_key' => 0, 'right_key' => 1, 'depth_level' => 1];
        $this->nodes_left--;
        $this->id = 0;
        while($this->nodes_left > 0)
        {
            $parent_node_id = $this->selectParent();
            $this->addNodeAfter($parent_node_id);
            $this->nodes_left--;;
        }
        $this->addTableToDb(); 
    }

    /**
     * Select random node to add after him new node
     * @return int
     */
    private function selectParent()
    {
        $count_nodes = count($this->temp_tree);
        $parent_node_id = rand(0, $count_nodes - 1);
        return $parent_node_id;
    }

    /**
     * Drop db and write new nodes
     */
    private function addTableToDb()
    {
        NestedSets::deleteAll(); // drop all old nodes
        foreach ($this->temp_tree as $key=>$value)
        {
            $model = new NestedSets();
            $model->id = $value['id'];
            $model->depth_level = $value['depth_level'];
            $model->name = $value['name'];
            $model->left_key = $value['left_key'];
            $model->right_key = $value['right_key'];

            if(!$model->save())
            {
                var_dump($model->errors);
            }
        }
    }

    /**
     * Adds new node to tree after node which 'id' in param
     * @param int $node_id
     */
    private function addNodeAfter($node_id)
    {
        $this->id++;
        $name = (string)$this->id;
        $depth_level = $this->temp_tree[$node_id]['depth_level'] + 1;
        $parent_right_key = $this->temp_tree[$node_id]['right_key'];
        $this->updadeParentKeys($parent_right_key);

        $this->temp_tree[] = [
            'id' => $this->id,
            'name' => $name,
            'depth_level' => $depth_level,
            'left_key' => $parent_right_key,
            'right_key' => $parent_right_key + 1
        ];
    }

    /**
     * Adds space to nodes whose nodes more than right key of new node's parent
     * @param int $parent_right_key - right key of parent node, after which the node item will be created
     */
    private function updadeParentKeys($parent_right_key)
    {
        foreach ($this->temp_tree as $key => &$node)
        {
            if($node['left_key'] >= $parent_right_key)
            {
                $node['left_key'] +=2;
            }

            if($node['right_key'] >= $parent_right_key)
            {
                $node['right_key'] +=2;
            }
        }
    }    
}