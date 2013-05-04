<?php
$options = array(
  'title' => '稼働報告を編集',
  'url' => url_for('kintai/edit?id='.$id),
);
op_include_form('reportForm', $form, $options)
?>
