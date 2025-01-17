<?php

namespace backend\modules\Citydescription\controllers;

use Yii;
use backend\modules\Citydescription\models\Citydescription;
use backend\modules\Citydescription\models\CitydescriptionTranslate;
use backend\modules\Citydescription\models\CitydescriptionSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use zxbodya\yii2\galleryManager\GalleryManagerAction;
use lajax\translatemanager\models\Language;
use backend\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use backend\components\extra;
use yii\db\Query;
use backend\modules\Products\models\ProductsSearchbycity;
use backend\modules\Products\models\Productscities;
use backend\modules\Products\models\Products;
use backend\modules\Citydescription\models\Citydescriptionsights;
use backend\modules\Citydescription\models\CitydescriptionsightsTranslate;
use yii\helpers\Json;

class CitydescriptionController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index','view','galleryApi'],
                        'allow' => true,
                        /*'roles' => ['*'],*/
                    ],
                    [
                        'actions' => ['admin','create','update','delete','getlist'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->getModule('users')->isAdmin();
                        }
                    ],
                    /*[
                        'actions' => ['create','delete'],
                        'allow' => false,
                    ],*/
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

    public function actionAdmin()
    {
        $searchModel = new CitydescriptionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->post('hasEditable')) {
            $uId = Yii::$app->request->post('editableKey');
            $model = Citydescription::findOne($uId);

            $out = Json::encode(['output'=>'', 'message'=>'']);

            $posted = current($_POST['Citydescription']);
            $post = ['Citydescription' => $posted];

            if ($model->load($post)) {
            $model->save(false);

            $output = '';

            if (isset($posted['comment'])) {
                $output = $model->comment;
            }

            $out = Json::encode(['output'=>$output, 'message'=>'']);
            }
            // return ajax json encoded response and exit
            echo $out;
            return;
        }

        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGetlist($q = null, $id = null) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('id, title AS text')
                ->from('citydescription')
                ->where(['like', 'title', $q])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => City::find($id)->name];
        }
        return $out;
    }

    public function actionView($id)
    {
        //$this->layout = "@app/themes/mandelan/layouts/filters";
        $this->layout = "@app/themes/mandelan/layouts/main";

        $prodcities=ArrayHelper::map(Productscities::find()->where(['city_id'=>$id])->all(), 'product_id', 'product_id');

        //$products=Products::find()->where(['status'=>Products::STATUS_ACTIVE])->andWhere(['IN','id',$prodcities])->orderBy(['id' => SORT_DESC])->limit(8)->all();

        $searchModel = new ProductsSearchbycity();

        $searchModel->status=Products::STATUS_ACTIVE;
        $searchModel->marketplace=Products::STATUS_INACTIVE;
        $searchModel->city_id=$id;

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort = ['defaultOrder' => ['id' => 'DESC']];

        return $this->render('view', [
            'model' => $this->findModel($id),
            //'products' => $products,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Citydescription();

        $modelTranslations = [];

        foreach (Language::getLanguages() as $language) {
            if($language->language_id!=Yii::$app->sourceLanguage) {
                $langId=$language->language_id;
        	$translation = new CitydescriptionTranslate;
		$translation->lang_code = $langId;
                $modelTranslations[] = $translation;
            }
        }

        if ($model->load(Yii::$app->request->post())) {

            $image = UploadedFile::getInstance($model, 'image');
            if(!empty($image)){
                $model->image = extra::generateFilename($image->name);
                $path = WEB_ROOT.'/'.Yii::$app->params['citiesPictures'] . $model->image;
                if (!empty($image)) $image->saveAs($path);
            }

            $model->save();

            $modelTranslationsIDs = ArrayHelper::map($modelTranslations, 'id', 'id');
            //$modelTranslations = Model::createMultiple(CitydescriptionTranslate::classname(), $modelTranslations);
            Model::loadMultiple($modelTranslations, Yii::$app->request->post());

            //Yii::$app->extra->e($modelTranslations);

            foreach ($modelTranslations as $modelTranslation) {
                //Yii::$app->extra->e($modelTranslation);
                if(!empty($modelTranslation->title)) {
                    $modelTranslation->citydescription_id = $model->id;
                    //Yii::$app->extra->e($modelTranslation);
                    $modelTranslation->save();
                }
            }
            return $this->redirect('admin');
        } else {
            return $this->render('create', [
                'model' => $model,
                'modelTranslations' => $modelTranslations,
                'sights' => (empty($sights)) ? [new Citydescriptionsights] : $sights
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $sights = $model->sights;
        //$modelTranslations = $model->translations;
        $modelTranslations=[];

        foreach (Language::getLanguages() as $language) {
            if($language->language_id!=Yii::$app->sourceLanguage) {
                $langId=$language->language_id;
        	$translation = CitydescriptionTranslate::findOne(['citydescription_id'=>$model->id, 'lang_code'=>$langId]);
        	if(empty($translation)) {
        		$translation = new CitydescriptionTranslate;
                        $translation->citydescription_id = $model->id;
			$translation->lang_code = $langId;
        	}
                $modelTranslations[] = $translation;
            }
        }

        foreach ($sights as $sight)
        {
            foreach (Language::getLanguages() as $language) {
                if($language->language_id!=Yii::$app->sourceLanguage) {
                    $langId=$language->language_id;
                    $translation = CitydescriptionsightsTranslate::findOne(['citydescription_sights_id'=>$sight->id, 'lang_code'=>$langId]);

                    $sight->nametranslate[$langId]=(!empty($translation))?$translation->name:'';
                    $sight->descriptiontranslate[$langId]=(!empty($translation))?$translation->description:'';
                }
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            $modelTranslationsIDs = ArrayHelper::map($modelTranslations, 'id', 'id');
            Model::loadMultiple($modelTranslations, Yii::$app->request->post());

            $oldIDs = ArrayHelper::map($sights, 'id', 'id');
            $sights = Model::createMultiple(Citydescriptionsights::classname(), $sights);
            Model::loadMultiple($sights, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($sights, 'id', 'id')));
            foreach ($sights as $index => $sight) {
                $sight->sort_order = $index;
                $img = UploadedFile::getInstance($sight, "[{$index}]image");
                if($sight->deleteImg==1 && !$sight->isNewRecord && $sight->OldAttributes['image']!='' && file_exists(WEB_ROOT.'/'.Yii::$app->params['citiessightsPictures'] . $sight->OldAttributes['image'])) {
                    unlink(WEB_ROOT.'/'.Yii::$app->params['citiessightsPictures'] . $sight->OldAttributes['image']);
                } elseif(!empty($img) && $sight->deleteImg==0) {
                    $sight->image = extra::generateFilename($img->name);
                    $path = WEB_ROOT.Yii::$app->params['citiessightsPictures'] . $sight->image;
                    //extra::e($path);
                    if(!$sight->isNewRecord && $sight->OldAttributes['image']!='' && file_exists(WEB_ROOT.'/'.Yii::$app->params['citiessightsPictures'] . $sight->OldAttributes['image']))unlink(WEB_ROOT.'/'.Yii::$app->params['citiessightsPictures'] . $sight->OldAttributes['image']);
                    $img->saveAs($path);
                } elseif(!$sight->isNewRecord) {
                    $sight->image=$sight->OldAttributes['image'];
                }
            }

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($sights) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    foreach ($modelTranslations as $modelTranslation) {
                        if(!$modelTranslation->isNewRecord && empty($modelTranslation->title) && empty($model->content==''))
                            $modelTranslation->delete();
                        elseif(!empty($modelTranslation->title))
                            $modelTranslation->save();
                    }

                    $image = UploadedFile::getInstance($model, 'image');
                    if(!empty($image)){
                        $model->image = extra::generateFilename($image->name);
                        $path = WEB_ROOT.'/'.Yii::$app->params['citiesPictures'] . $model->image;
                        if($model->OldAttributes['image']!='' && file_exists(WEB_ROOT.'/'.Yii::$app->params['citiesPictures'] . $model->OldAttributes['image']))unlink(WEB_ROOT.'/'.Yii::$app->params['citiesPictures'] . $model->OldAttributes['image']);
                        if (!empty($image)) $image->saveAs($path);
                    } else {
                        $model->image=$model->OldAttributes['image'];
                    }

                    if ($flag = $model->save(false)) {

                        if (!empty($deletedIDs)) {
                            $flag = Citydescriptionsights::deleteAll(['id' => $deletedIDs]);
                        }

                        if ($flag) {
                            foreach ($sights as $sight) {
                                $sight->citydescription_id = $model->id;
                                if (($flag = $sight->save(false)) === false) {
                                    $transaction->rollBack();
                                    break;
                                }

                                ///látnivalók fordítása
                                foreach (Language::getLanguages() as $language) {
                                    if($language->language_id!=Yii::$app->sourceLanguage) {
                                        $langId=$language->language_id;
                                        $translation = CitydescriptionsightsTranslate::findOne(['citydescription_sights_id'=>$sight->id, 'lang_code'=>$langId]);
                                        if(empty($translation) && ($sight->nametranslate[$langId]!='' || $sight->descriptiontranslate[$langId]!='')) {
                                            $translation = new CitydescriptionsightsTranslate;
                                            $translation->citydescription_sights_id=$sight->id;
                                            $translation->lang_code=$langId;
                                            $translation->name=$sight->nametranslate[$langId];
                                            $translation->description=$sight->descriptiontranslate[$langId];
                                            $translation->save(false);
                                        } else {
                                            if($sight->nametranslate[$langId]!='' || $sight->descriptiontranslate[$langId]!='') {
                                                $translation->name=$sight->nametranslate[$langId];
                                                $translation->description=$sight->descriptiontranslate[$langId];
                                                $translation->save(false);
                                            } else {
                                                CitydescriptionsightsTranslate::deleteAll(['citydescription_sights_id'=>$sight->id, 'lang_code'=>$langId]);
                                            }
                                        }
                                    }
                                }

                            }
                        }

                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect('admin');
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }

        }

        return $this->render('update', [
            'model' => $model,
            'modelTranslations' => $modelTranslations,
            'sights' => (empty($sights)) ? [new Citydescriptionsights] : $sights
        ]);
    }

    public function actionDelete($id)
    {
        CitydescriptionTranslate::deleteAll(['citydescription_id'=>$id]);

        $this->findModel($id)->delete();

        return $this->redirect(['admin']);
    }

    protected function findModel($id)
    {
        if (($model = Citydescription::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actions()
    {
        return [
           'galleryApi' => [
               'class' => GalleryManagerAction::className(),
               // mappings between type names and model classes (should be the same as in behaviour)
               'types' => [
                   'citydescription' => Citydescription::className()
               ]
           ],
        ];
    }

}

