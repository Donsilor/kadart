<?php

namespace api\modules\v1\controllers\article;

use addons\RfArticle\common\models\ArticleCate;
use api\controllers\OnAuthController;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;

/**
 * 文章分类
 *
 * Class ArticleCateController
 * @package addons\RfArticle\api\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class ArticleCateController extends OnAuthController
{
    public $modelClass = ArticleCate::class;

    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['index'];


    public function actionIndex(){
        $id = \Yii::$app->request->get('id',0);
        $result = array(
            'pid'=>'',
            'title'=>'',
        );
        if($id){
            $model =  ArticleCate::find()->alias('m')
                ->leftJoin(ArticleCate::tableName()." as a",'a.pid = m.id')
                ->where(['m.status' => StatusEnum::ENABLED])
                ->andWhere(['a.id'=>$id])
                ->andWhere(['m.merchant_id' => \Yii::$app->services->merchant->getId()])
                ->select(['m.id','m.title','m.pid'])
                ->one();
            if($model){
                $pid = $model->id;
                $result = array(
                    'pid'=>$model->id,
                    'title'=>$model->title,
                );
            }

        }else{
            $pid = 0;
        }


        $cates = ArticleCate::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['merchant_id' => \Yii::$app->services->merchant->getId()])
            ->select(['id','title','pid'])
            ->asArray()
            ->all();
        foreach ($cates as &$cate){
            $cate['url'] = "/news-".StringHelper::parseCatgory($cate['title'])."/id_".$cate['id'];
        }
        $list =  ArrayHelper::itemsMerge($cates,$pid,$idField = "id", $pidField = 'pid', $child = 'items');
        $result['lists'] = $list;


        return $result;

    }
    /**
     * 权限验证
     *
     * @param string $action 当前的方法
     * @param null $model 当前的模型类
     * @param array $params $_GET变量
     * @throws \yii\web\BadRequestHttpException
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        // 方法名称
        if (in_array($action, ['delete', 'create', 'update', 'view'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}