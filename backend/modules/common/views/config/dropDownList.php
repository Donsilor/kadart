<?php

use yii\helpers\Json;
use common\helpers\Html;
use common\enums\StatusEnum;

$value = isset($row['value']['data']) ? Json::decode($row['value']['data']) : $row['default_value'];
?>

<div class="form-group">
    <?= Html::label($row['title'], $row['name'], ['class' => 'control-label demo']); ?>
    <?php if ($row['is_hide_remark'] != StatusEnum::ENABLED) { ?>
        <small><?= Html::decode(Html::encode($row['remark'])) ?></small>
    <?php } ?>
    <?= Html::dropDownList('config[' . $row['name'] . ']', $value, $option, ['class' => 'form-control']); ?>
</div>