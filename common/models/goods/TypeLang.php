<?php

namespace common\models\goods;

use Yii;

/**
 * This is the model class for table "{{%goods_category_lang}}".
 *
 * @property int $id 主键
 * @property int $attr_id
 * @property string $language 语言类型
 * @property string $cat_name 分类名称
 * @property string $remark 备注
 * @property string $meta_title meta标题
 * @property string $meta_desc meta描述
 * @property string $meta_word meta关键词
 */
class TypeLang extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%goods_type_lang}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['master_id'], 'integer'],
            [['language'], 'string', 'max' => 5],
            [['type_name'], 'string', 'max' => 30],
            [['meta_title', 'meta_desc', 'meta_word'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'master_id' => Yii::t('goods_type', 'Attr ID'),
            'language' => Yii::t('common', '语言类型'),
            'type_name' => Yii::t('goods_type', '产品线'),
            'meta_title' => Yii::t('common', 'SEO标题'),
            'meta_desc' => Yii::t('common', 'SEO描述'),
            'meta_word' => Yii::t('common', 'SEO关键词'),
        ];
    }
}
