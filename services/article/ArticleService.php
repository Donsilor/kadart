<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/20 0020
 * Time: 12:27
 */
namespace services\article;

use addons\RfArticle\common\models\Article;
use common\helpers\StringHelper;
use Yii;
use common\components\Service;
use addons\RfArticle\common\models\ArticleCate;


class ArticleService extends Service
{

    public function getArticleUrl($id){
        $model = Article::find()->alias('a')
            ->leftJoin(ArticleCate::tableName()." as c",'a.cate_id=c.id')
            ->where(['a.id' => $id])
            ->select(['a.id','a.title','c.title as category_name'])
            ->asArray()
            ->one();
        $url = '/news-'.StringHelper::parseCatgory($model['category_name']).'/'.StringHelper::parseCatgory(substr($model['title'],0,70))."/".$id;
        return $url;
    }
    public function getArticleCateUrl($id){
       $model = ArticleCate::find()
           ->where(['id'=>$id])
           ->select(['title'])
           ->asArray()
           ->one();
       $url = "/news-".StringHelper::parseCatgory($model['title'])."/".$id;
       return $url;
    }
}