<?php
$options = array(
  'title' => '稼働報告を追加',
  'url' => url_for('kintai/report?date='.$date),
);
op_include_form('reportForm', $form, $options)
?>
