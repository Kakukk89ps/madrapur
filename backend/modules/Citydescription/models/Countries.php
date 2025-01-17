<?php

namespace backend\modules\Citydescription\models;

use Yii;
use zxbodya\yii2\galleryManager\GalleryBehavior;
use backend\components\extra;
use yii\helpers\ArrayHelper;

class Countries extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'countries';
    }

    public function rules()
    {
        return [
            [['population'], 'integer'],
            [['area_size'], 'number'],
            [['content','extra_info'], 'string'],
            [['country_code', 'fips_code'], 'string', 'max' => 2],
            [['country_name', 'link'], 'string', 'max' => 200],
            [['currency_code', 'iso_name'], 'string', 'max' => 3],
            [['capital'], 'string', 'max' => 30],
            [['image'], 'string', 'max' => 64]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'country_code' => Yii::t('app', 'Kód'),
            'country_name' => Yii::t('app', 'Név'),
            'currency_code' => Yii::t('app', 'Valuta kód'),
            'population' => Yii::t('app', 'Népesség'),
            'fips_code' => Yii::t('app', 'Fips kód'),
            'capital' => Yii::t('app', 'Főváros'),
            'area_size' => Yii::t('app', 'Terület'),
            'iso_name' => Yii::t('app', 'Iso név'),
            'content' => Yii::t('app', 'Tartalom'),
            'link' => Yii::t('app', 'Link'),
            'image' => Yii::t('app', 'Kép'),
            'content' => Yii::t('app', 'Tartalom'),
            'extra_info' => Yii::t('app', 'További információk'),
        ];
    }

    public function behaviors()
    {
        return [
             'galleryBehavior' => [
                 'class' => GalleryBehavior::className(),
                 'type' => 'countries',
                 'extension' => 'jpg',
                 'tableName' => 'countries_gallery',
                 'directory' => WEB_ROOT . '/images/countries/gallery',
                 'url' => '/images/countries/gallery',
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
        $this->link = extra::stringToUrl($this->country_name);
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
        $title=(Yii::$app->language==Yii::$app->sourceLanguage)?$this->link:extra::stringToUrl($this->country_name);
        return Yii::$app->urlManager->createAbsoluteUrl(['/citydescription/countries/view', 'id'=>$this->id, 'title'=>$title]);
    }

    public static function getUrlbyid($id)
    {
        $spage=Countries::findOne($id);
        if(!empty($spage)) {
            return Yii::$app->urlManager->createAbsoluteUrl(['/citydescription/countries/view', 'id'=>$spage->id, 'title'=>$spage->link]);
        }
        return '/';
    }

    public static function getContentbyid($id)
    {
        $spage=Countries::findOne($id);
        if(!empty($spage)) return $spage->content;
        return '';
    }

    public function getTranslations()
    {
        return $this->hasMany(Countriestranslate::className(), ['country_id' => 'id']);
    }

    public function getTranslation()
    {
        return Countriestranslate::findOne(['country_id' => $this->id, 'lang_code'=>Yii::$app->language]);
    }

    /*public function getThumb()
    {
        $thumb=Citydescriptiongallery::find()->where(['ownerid'=>$this->id])->orderBy('rank')->one();
        if(!empty($thumb)) {
            foreach($this->getBehavior('galleryBehavior')->getImages() as $image) {
                return Yii::$app->imagecache->createUrl('product-thumb',$image->getUrl("original"));
            }
        } else {
            return Yii::$app->params['countriesPictures'].'no-product-pic-thumb.png';
        }
    }*/

    public static function getList()
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'country_name');
    }

    public function getThumb()
    {
        if($this->image!='' && file_exists(WEB_ROOT.Yii::$app->params['countriesPictures'].$this->image))
            return Yii::$app->imagecache->createUrl('country-thumb',Yii::$app->params['countriesPictures'].$this->image);
        else
            return Yii::$app->params['countriesPictures'].'no-pic-thumb.png';
    }

    public function getCover()
    {
        if($this->image!='' && file_exists(WEB_ROOT.Yii::$app->params['countriesPictures'].$this->image))
            return Yii::$app->imagecache->createUrl('country-cover',Yii::$app->params['countriesPictures'].$this->image);
        else
            return Yii::$app->params['countriesPictures'].'no-pic-cover.jpg';
    }

    public static function getCountriesddlinks()
    {
        $items=[];
        foreach(self::find()->orderBy('country_name')->all() as $country){
            $items[$country->url]=$country->country_name;
        }
        return $items;
    }

}

