<?php

use common\helpers\Html;
use common\enums\StatusEnum;

?>

<div class="form-group">
    <?= Html::label($row['title'], $row['name'], ['class' => 'control-label demo']); ?>
    <?php if ($row['is_hide_remark'] != StatusEnum::ENABLED) { ?>
        <small><?= Html::decode(Html::encode($row['remark'])) ?></small>
    <?php } ?>
    <?= Html::input('password', 'config[' . $row['name'] . ']', $row['value']['data'] ?? $row['default_value'],
        ['class' => 'form-control']); ?>
</div>