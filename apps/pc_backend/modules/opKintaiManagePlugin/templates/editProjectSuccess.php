<?php slot('submenu') ?>
<?php include_partial('submenu') ?>
<?php end_slot() ?>

<?php slot('title', __('プロジェクト編集')); ?>

<h3>プロジェクトを新規に追加する</h3>
<form action="<?php echo url_for('opKintaiManagePlugin/editProject?id='.$project->getId()) ?>" method="post">
<table>
<?php echo $form ?>
<tr>
<td colspan="2"><input type="submit" value=<?php echo __('Edit') ?> /></td>
</tr>
</table>
</form>
