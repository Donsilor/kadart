<?php

namespace common\models\order;

use Yii;

/**
 * This is the model class for table "{{%order_log}}".
 *
 * @property int $id 主键
 * @property int $merchant_id 商户ID
 * @property int $order_id 订单id
 * @property string $log_msg 文字描述
 * @property int $log_time 处理时间
 * @property string $log_role 操作角色
 * @property string $log_user 操作人
 * @property int $log_order_status 订单状态
 */
class OrderLog extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'order_id', 'log_time', 'log_order_status'], 'integer'],
            [['order_id', 'log_time', 'log_role'], 'required'],
            [['log_msg'], 'string', 'max' => 150],
            [['log_role'], 'string', 'max' => 10],
            [['log_user'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'merchant_id' => '商户ID',
            'order_id' => '订单id',
            'log_msg' => '文字描述',
            'log_time' => '处理时间',
            'log_role' => '操作角色',
            'log_user' => '操作人',
            'log_order_status' => '订单状态',
        ];
    }
}
