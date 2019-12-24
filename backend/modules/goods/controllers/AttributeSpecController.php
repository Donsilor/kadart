<?php

namespace backend\modules\goods\controllers;

use Yii;
use common\models\goods\Attribute;
use common\components\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;
use common\models\goods\AttributeSpec;
use common\models\goods\AttributeSpecValue;
use yii\base\Exception;
use common\enums\StatusEnum;

/**
 * Attribute
 *
 * Class AttributeController
 * @package backend\modules\goods\controllers
 */
class AttributeSpecController extends BaseController
{
    use Curd;
    
    /**
     * @var Attribute
     */
    public $modelClass = AttributeSpec::class;
    
    
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        //Yii::$app->language = 'zh-TW';
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
            ->search(Yii::$app->request->queryParams,['attr_name','language']);
        
        $this->setLocalLanguage($searchModel->language);
        
        $dataProvider->query->andWhere(['>','status',-1]);
        $dataProvider->query->joinWith(['attr']);
        $dataProvider->query->with(['type']);
        
        $dataProvider->query->andFilterWhere(['like', 'attr.attr_name',$searchModel->attr_name]) ;
        
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
        ]);
    }
    /**
     * 编辑/创建 多语言
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $id = Yii::$app->request->get('id');
        $returnUrl = Yii::$app->request->get('returnUrl',['index']);
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $is_new = $model->isNewRecord;
            try{
                $trans = Yii::$app->db->beginTransaction();
                if(false === $model->save()){
                    throw new Exception($this->getError($model));
                } 
                $this->editSpecValue($model);
                $trans->commit();
                return $is_new ?
                $this->message("添加成功", $this->redirect(['edit','id'=>$model->id]), 'success'):
                $this->message("保存成功", $this->redirect($returnUrl), 'success');
            }catch (Exception $e){
                $trans->rollBack();
                $error = $e->getMessage();
                \Yii::error($error);
                return $this->message("保存失败:".$error, $this->redirect([$this->action->id,'id'=>$model->id]), 'error');
            }
        }
        
        $dataProvider = null;
        if(isset($id)){
            $searchModel = new SearchModel([
                    'model' => AttributeSpecValue::class,
                    'scenario' => 'default',
                    'partialMatchAttributes' => [], // 模糊查询
                    'defaultOrder' => [
                            'sort'=>SORT_ASC,
                            'id' => SORT_DESC
                    ],
                    'pageSize' => 100,
            ]);
            
            $dataProvider = $searchModel
                ->search(Yii::$app->request->queryParams);
            
            //$dataProvider->query->with(['lang']);
            $dataProvider->query->andWhere(['spec_id'=>$id]);
            $dataProvider->query->andWhere(['>','status',-1]);
            
            
            $dataProvider->setSort(false);
        }       
        return $this->render($this->action->id, [
                'model' => $model,
                'dataProvider'=>$dataProvider,
        ]);
    }
    /**
     * 规格属性值 新增/编辑
     * @param unknown $specModel
     * @throws Exception
     */
    private function editSpecValue($specModel)
    {
        $spec_id = $specModel->id;
        $attr_id = $specModel->attr_id;
        $attr_values = explode(",",$specModel->attr_values);
        
        AttributeSpecValue::deleteAll(['and',['spec_id'=>$spec_id],['not in','attr_value_id',$attr_values]]);
        foreach ($attr_values as $val_id){
            $model = AttributeSpecValue::find()->where(['spec_id'=>$spec_id,'attr_value_id'=>$val_id])->one();
            if(!$model) {                
                $model = new AttributeSpecValue();
                $model->spec_id = $spec_id;
                $model->attr_id = $attr_id;
                $model->attr_value_id = $val_id;
            } 
            $model->status = StatusEnum::ENABLED;
            if(false === $model->save()){
                throw new Exception($this->getError($model));
            }
        }
    }
    /**
     * ajax编辑/创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $id = Yii::$app->request->get('id');
        $returnUrl = Yii::$app->request->get('returnUrl');
        $model = $this->findModel($id);     
        
        $this->activeFormValidate($model);
        
        if ($model->load(Yii::$app->request->post())) {
            $is_new = $model->isNewRecord;
            try{
                $trans = Yii::$app->db->beginTransaction();
                if(false === $model->save()){
                    throw new Exception($this->getError($model));
                }
                $this->editSpecValue($model);
                $trans->commit();
                return $is_new ?
                $this->message("添加成功", $this->redirect(['edit','id'=>$model->id]), 'success'):
                $this->message("保存成功", $this->redirect($returnUrl), 'success');
            }catch (Exception $e){
                $trans->rollBack();
                $error = $e->getMessage();
                \Yii::error($error);
                return $this->message("保存失败:".$error, $this->redirect([$this->action->id,'id'=>$model->id]), 'error');
            }
        }
        return $this->renderAjax($this->action->id, [
                'model' => $model,
        ]);
    }
    
    /**
     * 获取属性值
     */
    public function actionAjaxAttrValues()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $id = Yii::$app->request->post("id");
        $attr_id = Yii::$app->request->post("attr_id");
               
        $checked_values = false;
        if ($id && $model = $this->findModel($id)) {
            $checked_values = explode(",",trim($model->attr_values,','));
        }        
        $html = '';
        $values = Yii::$app->services->goodsAttribute->getValuesByAttrId($attr_id);
        foreach ($values as $key=>$val) {
            $checked = $checked_values === false || in_array($key,$checked_values)?" checked":'';
            $html .= '<label style="color:#636f7a"><input type="checkbox" name="AttributeSpecValue[attr_values][]" value="'.$key.'"'.$checked.'>'.$val.'</label>&nbsp;';  
        } 
        return $html;
    }

    /**
     * 删除
     * @param unknown $id
     * @return mixed|string
     */
    public function actionDelete($id)
    {
        if ($model = $this->findModel($id)) {
            $model->status = -1;
            $model->save();
            return $this->message("删除成功", $this->redirect(['index', 'id' => $model->id]));
        }
        
        return $this->message("删除失败", $this->redirect(['index', 'id' => $model->id]), 'error');
    }
}
