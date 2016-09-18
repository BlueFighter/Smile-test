<?php

namespace app\models;

use yii;

/**
 * This is the model class for table "nested_sets".
 *
 * @property integer $id
 * @property integer $depth_level
 * @property integer $left_key
 * @property integer $right_key
 * @property string $name
 */
class NestedSets extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nested_sets';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'depth_level', 'left_key', 'right_key', 'name'], 'required'],
            [['id', 'depth_level', 'left_key', 'right_key'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }   

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'depth_level' => 'Depth Level',
            'left_key' => 'Left Key',
            'right_key' => 'Right Key',
            'name' => 'Name',
        ];
    }

}
