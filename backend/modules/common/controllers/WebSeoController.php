<?php

namespace backend\modules\common\controllers;

use Yii;
use common\models\common\WebSeo;
use common\components\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;

/**
* WebSeo
*
* Class WebSeoController
* @package backend\modules\setting\controllers
*/
class WebSeoController extends BaseController
{
    use Curd;

    /**
    * @var WebSeo
    */
    public $modelClass = WebSeo::class;


    /**
    * 首页
    *
    * @return string
    * @throws \yii\web\NotFoundHttpException
    */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);

        $dataProvider->query->with(['lang'=>function($query){
            $query->where(['language'=>Yii::$app->language]);
        }]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
}
