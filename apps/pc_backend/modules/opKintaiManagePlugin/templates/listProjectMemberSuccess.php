<?php slot('submenu') ?>
<?php include_partial('submenu') ?>
<?php end_slot() ?>

<?php slot('title', __('プロジェクトメンバー管理マスター')); ?>

<h3>メンバーを新規にプロジェクトに参加させる</h3>
<form action="<?php echo url_for('opKintaiManagePlugin/listProjectMember') ?>" method="post">
<table>
<?php echo $form ?>
<tr>
<td colspan="2"><input type="submit" value=<?php echo __('Add') ?> /></td>
</tr>
</table>
</form>

<h3>プロジェクトメンバー一覧</h3>
<?php if ($list): ?>
<table>
<?php foreach ($list as $projectMember): ?>
<tr>
<td><?php echo $projectMember->getProject()->getName() ?></td>
<td><?php echo $projectMember->getMember()->getName() ?></td>
<td><?php echo $projectMember->getDescription() ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
現在誰も参加していません．
<?php endif ?>
