<?php

namespace backend\modules\Citydescription\controllers;

use Yii;
use backend\modules\Citydescription\models\Countries;
use backend\modules\Citydescription\models\CountriesSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use backend\components\extra;
use lajax\translatemanager\models\Language;
use backend\base\Model;
use yii\helpers\ArrayHelper;
use backend\modules\Citydescription\models\Countriestranslate;
use backend\modules\Citydescription\models\Citydescription;
use backend\modules\Products\models\Productscities;
use backend\modules\Products\models\Products;
use backend\modules\Products\models\Productscountires;

class CountriesController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index','view','galleryApi','dest'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['admin','create','update','delete'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->getModule('users')->isAdmin();
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionDest($q = null, $id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q)) {
            foreach(Countries::find()->orderBy('country_name')->all() as $country){
                $out['results'][]=['id'=>$country->url,'text'=>$country->country_name];
            }
        }

        return $out;
    }


    /**

     * Lists all Countries models.

     * @return mixed

     */

    public function actionAdmin()

    {

        $searchModel = new CountriesSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);



        return $this->render('admin', [

            'searchModel' => $searchModel,

            'dataProvider' => $dataProvider,

        ]);

    }



    /**

     * Displays a single Countries model.

     * @param integer $id

     * @return mixed

     */

    public function actionView($id)
    {

        if(!extra::isAdminpage()) $this->layout = "@app/themes/mandelan/layouts/main"; //$this->layout = "@app/themes/mandelan/layouts/filters";

        $model=$this->findModel($id);

        $more=Yii::$app->request->get('page');

        $activeprods=Products::getActiveids();

        $db = Yii::$app->db;
        $cities = $db->cache(function ($db) use ($id) {
            return Citydescription::find()->where(['country_id'=>$id])->all();
        }, 60); //86400 - 1 nap
        $cities=ArrayHelper::map($cities,'id','id');

        $db = Yii::$app->db;
        $citycount = $db->cache(function ($db) use ($activeprods, $cities, $more) {
            return Productscities::find()
            ->select(['COUNT(city_id) AS citycount, city_id'])
            ->where(['IN','city_id',$cities])
            ->andWhere(['in','product_id',$activeprods])
            ->groupBy('city_id')
            ->orderBy('citycount DESC, city_id ASC')
            ->limit(($more=='more')?9999:12)
            ->asArray()
            ->all();
        }, 60);

        /*$fullcount = Productscities::find()
        ->select(['COUNT(city_id) AS citycount, city_id'])
        ->where(['IN','city_id',$cities])
        ->andWhere(['in','product_id',$activeprods])
        ->groupBy('city_id')
        ->orderBy('citycount DESC')
        ->asArray()
        ->all();*/

        $ccounts=[]; $cityids=[];
        foreach($citycount as $cc){
            $ccounts[$cc['city_id']]=$cc['citycount'];
            $cityids[]=$cc['city_id'];
        }

        $limit=(count($citycount)>=12)?0:(12-count($citycount));

        $db = Yii::$app->db;
        $morecities = $db->cache(function ($db) use ($id, $cityids, $limit) {
            return Citydescription::find()->where(['country_id'=>$id])->andWhere(['NOT IN','id',$cityids])->limit($limit)->orderBy('title ASC')->all();
        }, 60);

        $morecities=ArrayHelper::map($morecities,'id','id');

        $db = Yii::$app->db;
        $fullcount = $db->cache(function ($db) use ($id, $cityids) {
            return Citydescription::find()->where(['country_id'=>$id])->andWhere(['NOT IN','id',$cityids])->orderBy('title ASC')->all();
        }, 60);
        $fullcount=ArrayHelper::map($fullcount,'id','id');

        $morepage=(count($fullcount)>12 && $more!='more')?true:false;

        $db = Yii::$app->db;
        $bestcities = $db->cache(function ($db) use ($activeprods, $cities, $more) {
            return Productscities::find()
            ->joinWith('city')
            ->where(['IN','city_id',$cities])
            ->andWhere(['in','product_id',$activeprods])
            ->groupBy('city_id')
            ->orderBy('COUNT(city_id) DESC, city_id ASC')
            ->limit(($more=='more')?9999:12)
            ->all();
        }, 60);

        //if($model->id==100) extra::e($bestcities);

        $db = Yii::$app->db;
        $morecities = $db->cache(function ($db) use ($activeprods, $morecities, $more) {
            return Productscities::find()
            ->joinWith('city')
            ->where(['IN','city_id',$morecities])
            ->andWhere(['in','product_id',$activeprods])
            ->groupBy('city_id')
            ->orderBy('COUNT(city_id) DESC, city_id ASC')
            ->all();
        }, 60);

        $countryproducts=ArrayHelper::map(Productscountires::find()->where(['country_id'=>$model->id])->all(),'product_id','product_id');
        $graylineproducts=Products::find()->where(['status'=>Products::STATUS_ACTIVE]) ->andWhere(['in','id',$countryproducts])->limit(8)->orderBy('RAND ()')->all();
        $highlighted=Products::find()->where(['status'=>Products::STATUS_ACTIVE]) ->andWhere(['in','id',$countryproducts])->limit(12)->orderBy('RAND ()')->all();

        return $this->render('view', [
            'model' => $model,
            'count' => $ccounts,
            'bestcities' => $bestcities,
            'morepage' => $morepage,
            'morecities' => $morecities,
            'products' => $graylineproducts,
            'highlighted' => $highlighted,
        ]);

    }



    /**

     * Creates a new Countries model.

     * If creation is successful, the browser will be redirected to the 'view' page.

     * @return mixed

     */

    public function actionCreate()

    {

        $model = new Countries();



        $modelTranslations = [];



        foreach (Language::getLanguages() as $language) {

            if($language->language_id!=Yii::$app->sourceLanguage) {

                $langId=$language->language_id;

        	$translation = new Countriestranslate;

		$translation->lang_code = $langId;

                $modelTranslations[] = $translation;

            }

        }



        if ($model->load(Yii::$app->request->post())) {



            $image = UploadedFile::getInstance($model, 'image');

            if(!empty($image)){

                $model->image = extra::generateFilename($image->name);

                $path = WEB_ROOT.'/'.Yii::$app->params['countriesPictures'] . $model->image;

                if (!empty($image)) $image->saveAs($path);

            }



            $model->save();



            $modelTranslationsIDs = ArrayHelper::map($modelTranslations, 'id', 'id');

            Model::loadMultiple($modelTranslations, Yii::$app->request->post());



            foreach ($modelTranslations as $modelTranslation) {

                if(!empty($modelTranslation->country_name)) {

                    $modelTranslation->country_id = $model->id;

                    $modelTranslation->save();

                }

            }

            return $this->redirect('admin');

        } else {

            return $this->render('create', [

                'model' => $model,

                'modelTranslations' => $modelTranslations

            ]);

        }

    }



    /**

     * Updates an existing Countries model.

     * If update is successful, the browser will be redirected to the 'view' page.

     * @param integer $id

     * @return mixed

     */

    public function actionUpdate($id)

    {

        $model = $this->findModel($id);



        foreach (Language::getLanguages() as $language) {

            if($language->language_id!=Yii::$app->sourceLanguage) {

                $langId=$language->language_id;

        	$translation = Countriestranslate::findOne(['country_id'=>$model->id, 'lang_code'=>$langId]);

        	if(empty($translation)) {

        		$translation = new Countriestranslate;

                        $translation->country_id = $model->id;

			$translation->lang_code = $langId;

        	}

                $modelTranslations[] = $translation;

            }

        }



        if ($model->load(Yii::$app->request->post())) {

            $modelTranslationsIDs = ArrayHelper::map($modelTranslations, 'id', 'id');

            Model::loadMultiple($modelTranslations, Yii::$app->request->post());



            foreach ($modelTranslations as $modelTranslation) {

                if(!$modelTranslation->isNewRecord && empty($modelTranslation->country_name))

                    $modelTranslation->delete();

                elseif(!empty($modelTranslation->country_name))

                    $modelTranslation->save();

            }



            $image = UploadedFile::getInstance($model, 'image');

            if(!empty($image)){

                $model->image = extra::generateFilename($image->name);

                $path = WEB_ROOT.'/'.Yii::$app->params['countriesPictures'] . $model->image;

                if($model->OldAttributes['image']!='' && file_exists(WEB_ROOT.'/'.Yii::$app->params['countriesPictures'] . $model->OldAttributes['image']))unlink(WEB_ROOT.'/'.Yii::$app->params['countriesPictures'] . $model->OldAttributes['image']);

                if (!empty($image)) $image->saveAs($path);

            } else {

                $model->image=$model->OldAttributes['image'];

            }



            $model->save();



            return $this->redirect('admin');

        } else {

            return $this->render('update', [

                'model' => $model,

                'modelTranslations' => $modelTranslations

            ]);

        }

    }



    /**

     * Deletes an existing Countries model.

     * If deletion is successful, the browser will be redirected to the 'admin' page.

     * @param integer $id

     * @return mixed

     */

    public function actionDelete($id)

    {

        $this->findModel($id)->delete();



        return $this->redirect(['admin']);

    }



    /**

     * Finds the Countries model based on its primary key value.

     * If the model is not found, a 404 HTTP exception will be thrown.

     * @param integer $id

     * @return Countries the loaded model

     * @throws NotFoundHttpException if the model cannot be found

     */

    protected function findModel($id)

    {

        if (($model = Countries::findOne($id)) !== null) {

            return $model;

        } else {

            throw new NotFoundHttpException('The requested page does not exist.');

        }

    }

}

