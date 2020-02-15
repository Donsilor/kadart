<?php

namespace api\modules\v1\controllers\common;

use common\enums\StatusEnum;
use common\helpers\ResultHelper;
use common\models\common\Advert;
use common\models\common\AdvertImages;
use common\models\common\AdvertImagesLang;
use common\models\goods\Type;
use Yii;
use api\controllers\OnAuthController;

/**
 * Class ProvincesController
 * @package api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class AdvertImagesController extends OnAuthController
{
    /**
     * @var Provinces
     */
    public $modelClass = AdvertImages::class;
    protected $authOptional = ['index','baner'];
    /**
     * 根据分类ID获取广告图
     *
     * @param int $pid
     * @return array|yii\data\ActiveDataProvider
     */
    public function actionIndex()
    {
        $adv_id = Yii::$app->request->get('acdv_id',null);
        if($adv_id == null) return ResultHelper::api(400, '缺省参数');
        $language = Yii::$app->params['language'];
        $time = date('Y-m-d H:i:s', time());
        $model = $this->modelClass::find()->alias('m')
            ->where(['m.status' => StatusEnum::ENABLED, 'm.adv_id'=>$adv_id])
            ->andWhere(['or',['and',['<=','m.start_time',$time], ['>=','m.end_time',$time]],['m.end_time'=>null]])
            ->leftJoin(AdvertImagesLang::tableName().' lang','lang.master_id = m.id and lang.language =  "'.$language.'"')
            ->select(['lang.title','m.adv_image','adv_url'])
            ->orderby('m.sort asc, m.created_at asc')
            ->asArray()
            ->all();
        return $model;
    }



    /**
     * 根据产品线ID获取Banner
     *
     * @param int $pid
     * @return array|yii\data\ActiveDataProvider
     */
    public function actionBaner()
    {
        $type_id = Yii::$app->request->get('type_id');
        $adv_id = Yii::$app->request->get('acdv_id');
        if($type_id == null) {
            return ResultHelper::api(400, '产品线不能为空');
        }
        if($adv_id == null) {
            return ResultHelper::api(400, '广告位置ID不能为空');
        }
        $language = Yii::$app->params['language'];
        $model = $this->getParentImage($type_id,$adv_id,$language);
        return $model;
    }


    public function getParentImage($type_id,$adv_id,$language){
        $time = date('Y-m-d H:i:s', time());
        $query =  AdvertImages::find()->alias('m')
            ->where([ 'm.status'=>StatusEnum::ENABLED, 'm.adv_id'=>$adv_id])
            ->andWhere(['or',['and',['<=','m.start_time',$time], ['>=','m.end_time',$time]],['m.end_time'=>null]])
            ->leftJoin(AdvertImagesLang::tableName().' lang','lang.master_id = m.id and lang.language =  "'.$language.'"')
            ->select(['lang.title','m.adv_image','adv_url'])
            ->orderby('m.sort asc, m.created_at asc');

        if($type_id == 0){
            // 如果父父级没有，则直接获取位置图片
            $model = $query->asArray()->all();
            return $model;
        }
        $model = $query ->andWhere(['m.type_id'=>$type_id])->asArray()->all();
        if(empty($model)){
            //获取父级生产线图片
            $parent = Type::find()->where(['id'=>$type_id])->asArray()->one();
            $type_id = $parent['pid'];
            return $this->getParentImage($type_id, $adv_id, $language);
        }else{
            return $model;
        }
    }


}