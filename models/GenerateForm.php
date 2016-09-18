<?php

namespace app\models;

use yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class GenerateForm extends Model
{
    public $nodes;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['nodes', 'required', 'message' => 'Ведите количество узлов'],
            ['nodes', 'integer', 'message' => 'Количество узлов должно быть целым числом']

        ];
    }

}
