<?php

namespace backend\modules\Citydescription\models;

use Yii;
use backend\modules\Citydescription\models\Citydescription;

class CitydescriptionTranslate extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'citydescription_translate';
    }

    public function rules()
    {

        return [
            //['content', 'required'],
            //['title', 'skipIfEmpty'],
            [['citydescription_id'], 'integer'],
            [['content','extra_info'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['language', 'currency', 'phone_code', 'transport', 'plug', 'short_info'], 'string', 'max' => 200],
            [['when'], 'string', 'max' => 500],
            [['lang_code'], 'string', 'max' => 5],
            [['sights_info'], 'string', 'max' => 3000]
        ];

    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'citydescription_id' => Yii::t('app', 'Statikus oldal ID'),
            'title' => Yii::t('app', 'Cím'),
            'short_info' => Yii::t('app', 'Extra info'),
            'content' => Yii::t('app', 'Like a local'),
            'extra_info' => Yii::t('app', 'HOT Stuff'),
            'language' => Yii::t('app', 'Nyelv'),
            'currency' => Yii::t('app', 'Pénznem'),
            'phone_code' => Yii::t('app', 'Országkód'),
            'transport' => Yii::t('app', 'Közlekedés'),
            'lang_code' => Yii::t('app', 'Nyelv kód'),
            'plug' => Yii::t('app', 'Csatlakozó'),
            'when' => Yii::t('app', 'Mikor'),
            'sights_info' => Yii::t('app', 'Látványosságok leírás'),
        ];
    }

    public function skipIfEmpty($attribute, $params)
    {
        if(!empty($this->title) && empty($this->content))
        //Yii::$app->extra->e($this);
        //if (true)
            $this->addError($attribute, Yii::t('app', "hiba"));
    }

    public function getCity()
    {
        return $this->hasOne(Citydescription::className(), ['id' => 'citydescription_id']);
    }

}


