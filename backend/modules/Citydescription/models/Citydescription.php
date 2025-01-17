<?php

namespace backend\modules\Citydescription\models;

use Yii;
use zxbodya\yii2\galleryManager\GalleryBehavior;
use backend\components\extra;
use backend\modules\Citydescription\models\CitydescriptionTranslate;
use backend\modules\Citydescription\models\Citydescriptiongallery;
use backend\modules\Citydescription\models\Countries;
use yii\helpers\ArrayHelper;
use backend\modules\Citydescription\models\Citydescriptionsights;

class Citydescription extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'citydescription';
    }

    public function rules()
    {
        return [
            [['title', 'country_id'], 'required'],
            [['country_id'], 'integer'],
            [['content','extra_info'], 'string'],
            [['title', 'link'], 'string', 'max' => 255],
            [['language', 'currency', 'phone_code', 'transport', 'plug', 'short_info'], 'string', 'max' => 200],
            [['when'], 'string', 'max' => 500],
            [['image'], 'string', 'max' => 64],
            [['comment'], 'string', 'max' => 1000],
            [['sights_info'], 'string', 'max' => 3000]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'country_id' => Yii::t('app', 'Ország'),
            'title' => Yii::t('app', 'Név'),
            'short_info' => Yii::t('app', 'Extra info'),
            'content' => Yii::t('app', 'Like a local'),
            'extra_info' => Yii::t('app', 'HOT Stuff'),
            'language' => Yii::t('app', 'Nyelv'),
            'currency' => Yii::t('app', 'Pénznem'),
            'phone_code' => Yii::t('app', 'Országkód'),
            'transport' => Yii::t('app', 'Közlekedés'),
            'link' => Yii::t('app', 'Link'),
            'image' => Yii::t('app', 'Kép'),
            'plug' => Yii::t('app', 'Csatlakozó'),
            'when' => Yii::t('app', 'Mikor'),
            'sights_info' => Yii::t('app', 'Látványosságok leírás'),
            'comment' => Yii::t('app', 'Megjegyzés'),
        ];
    }

    public function behaviors()
    {
        return [
             'galleryBehavior' => [
                 'class' => GalleryBehavior::className(),
                 'type' => 'citydescription',
                 'extension' => 'jpg',
                 'tableName' => 'citydescription_gallery',
                 'directory' => WEB_ROOT . '/images/citydescription/gallery',
                 'url' => '/images/citydescription/gallery',
                 'versions' => [
                     'small' => function ($img) {
                         return $img
                             ->copy()
                             ->thumbnail(new \Imagine\Image\Box(200, 200));
                     },
                     'medium' => function ($img) {
                         $dstSize = $img->getSize();
                         $maxWidth = 800;
                         if ($dstSize->getWidth() > $maxWidth) {
                             $dstSize = $dstSize->widen($maxWidth);
                         }
                         return $img
                             ->copy()
                             ->resize($dstSize);
                     },
                 ]
             ]
        ];
    }

    public function beforeSave($insert)
    {
        $this->link = extra::stringToUrl($this->title);
        return true;
    }

    public function afterFind()
    {
        parent::afterFind();

        if(Yii::$app->language!=Yii::$app->sourceLanguage && !empty($this->translation))
            $this->attributes=$this->translation->attributes;
    }

    public function getUrl()
    {
	$title=(Yii::$app->language==Yii::$app->sourceLanguage)?$this->link:extra::stringToUrl($this->title);
        return Yii::$app->urlManager->createAbsoluteUrl(['citydescription/citydescription/view', 'id'=>$this->id, 'title'=>$title]);
    }

    public static function getUrlbyid($id)
    {
        $spage=Citydescription::findOne($id);
        if(!empty($spage)) {
            return Yii::$app->urlManager->createAbsoluteUrl(['citydescription/citydescription/view', 'id'=>$spage->id, 'title'=>$spage->link]);
        }
        return '/';
    }

    public static function getContentbyid($id)
    {
        $spage=Citydescription::findOne($id);
        if(!empty($spage)) return $spage->content;
        return '';
    }

    public function getTranslations()
    {
        return $this->hasMany(CitydescriptionTranslate::className(), ['citydescription_id' => 'id']);
    }

    public function getTranslation()
    {
        return CitydescriptionTranslate::findOne(['citydescription_id' => $this->id, 'lang_code'=>Yii::$app->language]);
    }

    public function getSights()
    {
        return $this->hasMany(Citydescriptionsights::className(), ['citydescription_id' => 'id'])->orderBy(['sort_order' => SORT_ASC]);
    }

    public function getThumb()
    {
        if($this->image!='' && file_exists(WEB_ROOT.Yii::$app->params['citiesPictures'].$this->image))
            return Yii::$app->imagecache->createUrl('city-thumb',Yii::$app->params['citiesPictures'].$this->image);
        else
            return Yii::$app->params['citiesPictures'].'no-pic-thumb.png';
    }

    public function getCover()
    {
        if($this->image!='' && file_exists(WEB_ROOT.Yii::$app->params['citiesPictures'].$this->image))
            return Yii::$app->imagecache->createUrl('city-cover',Yii::$app->params['citiesPictures'].$this->image);
        else
            return Yii::$app->params['citiesPictures'].'no-pic-cover.jpg';
    }

    public function getCountry()
    {
        return $this->hasOne(Countries::className(), ['id' => 'country_id']);
    }

    /*public function beforeDelete() {
        parent::beforeDelete();
        CitydescriptionTranslate::deleteAll(['citydescription_id'=>$this->id]);

        return true;
    }*/

    public static function getList()
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'title');
    }

}

