<?php

namespace backend\modules\Product\models;

use backend\modules\MadActiveRecord\models\MadActiveRecord;
use Yii;
use yii\db\ActiveRecord;

/**
 * Default model for the `Product` module
 */
class ProductSource extends MadActiveRecord{

    public static function tableName()
    {
        return 'modulusProdSources';
    }

    public function rules()
    {
        return [

            [['name'], 'required'],
            [['product_id','id'], 'integer'],
            [['name','url','prodIds','color'], 'string', 'max' => 100],

        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'product_id' => Yii::t('app', 'Product ID'),
            'name' => Yii::t('app', 'Name'),
            'url' => Yii::t('app', 'Site Url:'),
            'color' => Yii::t('app', 'Color:'),
            'prodIds' => Yii::t('app', 'Product Id'),
        ];
    }

    public function init() {
        parent::init();



        return true;
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    public function afterFind() {
        parent::afterFind();
        return true;
    }

}
