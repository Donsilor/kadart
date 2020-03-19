<?php

namespace api\modules\v1\controllers\article;

use addons\RfArticle\common\models\ArticleCate;
use common\helpers\ImageHelper;
use common\helpers\ResultHelper;
use common\helpers\StringHelper;
use yii\data\ActiveDataProvider;
use common\enums\StatusEnum;
use addons\RfArticle\common\models\Article;
use api\controllers\OnAuthController;

/**
 * 文章接口
 *
 * Class ArticleController
 * @package addons\RfArticle\api\controllers
 * @property \yii\db\ActiveRecord|\yii\base\Model $modelClass;
 * @author jianyan74 <751393839@qq.com>
 */
class ArticleController extends OnAuthController
{
    public $modelClass = Article::class;

    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['search', 'detail'];

    /**
     * 文章列表（搜索）
     *
     * @return ActiveDataProvider
     */
    public function actionSearch()
    {
        $pid = \Yii::$app->request->get('pid');
        $keyword = \Yii::$app->request->get('keyword');
        $page = \Yii::$app->request->get("page",1);//页码
        $page_size = \Yii::$app->request->get("page_size",20);//每页大小
        $query = $this->modelClass::find()->alias('a')
            ->leftJoin(ArticleCate::tableName()." as c",'a.cate_id=c.id')
            ->where(['a.status' => StatusEnum::ENABLED])
            ->andFilterWhere(['a.merchant_id' => $this->getMerchantId()]);
        if($pid){
            $query->andFilterWhere(['a.cate_id'=>$pid]);
        }
        if($keyword){
            $query->andFilterWhere(['or',['like','a.title',$keyword],['like','a.seo_content',$keyword],['like','a.content',$keyword]]);
        }
        $query->select(['a.id', 'a.title','c.title as category_name', 'a.cover', 'a.seo_content', 'a.view'])
        ->orderBy('a.sort asc, a.id desc');

        $result = $this->pagination($query,$page,$page_size);
        $result['pid'] = $pid;
        $result['category_name'] = '';
        if($pid){
            $model = ArticleCate::find()
                ->where(['status' => StatusEnum::ENABLED])
                ->andWhere(['id'=>$pid])
                ->andWhere(['merchant_id' => \Yii::$app->services->merchant->getId()])
                ->select(['id','title','pid'])
                ->one();
            $result['pid'] = $model->id;
            $result['category_name'] = $model->title;
        }


        foreach($result['data'] as & $val) {

            $val['url'] = '/news-'.StringHelper::parseCatgory($val['category_name']).'/'.StringHelper::parseCatgory($val['title'])."/id_".$val['id'];
            $val['img'] = ImageHelper::goodsThumb($val['cover'],'mid');
        }
        return $result;

    }

    /**
     * 文章详情
     * @return mixed
     */
    public function actionDetail()
    {
        $id = \Yii::$app->request->get('id');
        if(empty($id)) {
            return ResultHelper::api(422,"id不能为空");
        }
        $model = $this->modelClass::find()->where(['id'=>$id,'status'=>StatusEnum::ENABLED])->asArray()->one();
        if(empty($model)) {
            return ResultHelper::api(422,"文章不存在或者下架");
        }
        return $model;
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
        if (in_array($action, ['delete', 'create', 'update'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}