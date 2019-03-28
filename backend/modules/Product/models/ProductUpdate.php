<?php

namespace backend\modules\Product\models;

use backend\modules\MadActiveRecord\models\MadActiveRecord;
use Yii;
use yii\db\ActiveRecord;


class ProductUpdate extends MadActiveRecord
{
    public $dateFrom;
    public $dateTo;
    public $currency;
    public $status;
    public $title;
    public $description;
    public $short_description;
    public $image;
    public $start_date;
    public $end_date;
    public $category;
    public $capacity;
    public $duration;




    public static function tableName() {
        return 'modulusProducts'; //TODO: dynamic table name
    }


    public function rules()
    {
        return [
            // define validation rules here
        ];
    }
}