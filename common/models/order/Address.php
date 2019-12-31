<?php

namespace common\models\order;

use Yii;

/**
 * This is the model class for table "{{%order_address}}".
 *
 * @property int $order_id 订单ID
 * @property int $merchant_id
 * @property int $member_id 用户id
 * @property int $country_id 国家ID
 * @property int $province_id 省id
 * @property int $city_id 市id
 * @property string $firstname 名字
 * @property string $lastname 姓氏
 * @property string $country_name
 * @property string $province_name
 * @property string $city_name
 * @property string $address_details 详细地址
 * @property int $zip_code 邮编
 * @property string $mobile 手机号码
 * @property string $mobile_code 手机区号
 * @property string $email 邮箱地址
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class Address extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order_address}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['order_id', 'merchant_id', 'member_id', 'country_id', 'province_id', 'city_id', 'zip_code', 'created_at', 'updated_at'], 'integer'],
            [['firstname', 'lastname', 'country_name', 'province_name', 'city_name', 'email'], 'string', 'max' => 60],
            [['address_details'], 'string', 'max' => 200],
            [['mobile'], 'string', 'max' => 20],
            [['mobile_code'], 'string', 'max' => 10],
            [['order_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'order_id' => '订单ID',
            'merchant_id' => 'Merchant ID',
            'member_id' => '用户id',
            'country_id' => '国家ID',
            'province_id' => '省id',
            'city_id' => '市id',
            'firstname' => '名字',
            'lastname' => '姓氏',
            'country_name' => 'Country Name',
            'province_name' => 'Province Name',
            'city_name' => 'City Name',
            'address_details' => '详细地址',
            'zip_code' => '邮编',
            'mobile' => '手机号码',
            'mobile_code' => '手机区号',
            'email' => '邮箱地址',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
