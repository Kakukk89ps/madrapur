<?php

namespace backend\modules\Modmail\models;

use backend\modules\MadActiveRecord\models\MadActiveRecord;
use backend\modules\Product\models\ProductAdminSearchModel;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

/**
 * Default model for the `Modmail` module
 */
class Modmail extends MadActiveRecord {
 
    public static function tableName() {
        return 'modmail';
    }

    public function rules() {
        return [
            [['id'], 'integer'],
            [['type','from','to','status','subject'], 'string', 'max' => 255],
            [['body'], 'string', 'max' => 100000],
            [['date'], 'date', 'format' => 'yyyy-MM-dd'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'source' => Yii::t('app', 'Forrás'),
            'randomDate' => Yii::t('app', 'Véletlenszerű dátum'),
        ];
    }

    public function search($params)
    {
        #  $invoiceDate = '2016-02-05';
        # $bookingDate = '2020-08-20';

        $what = ['*'];
        $from = self::tableName();
        $where = self::andWhereFilter([
            ['id', '!=', '0'],
        ]);


        $rows = self::aSelect(Modmail::class, $what, $from,$where);

        $dataProvider = new ActiveDataProvider([
            'query' => $rows,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->load($params);

        return $dataProvider;
    }
}
