<?php slot('submenu') ?>
<?php include_partial('submenu') ?>
<?php end_slot() ?>

<?php slot('title', __('メンバーリソース管理マスター')); ?>

<h3>リソースを新規に追加する</h3>
<form action="<?php echo url_for('opKintaiManagePlugin/listMemberResource') ?>" method="post">
<table>
<?php echo $form ?>
<tr>
<td colspan="2"><input type="submit" value=<?php echo __('Add') ?> /></td>
</tr>
</table>
</form>

<h3>リソース一覧</h3>
<?php if ($list): ?>
<table>
<?php foreach ($list as $memberResource): ?>
<tr>
<td><?php echo $memberResource->getMember()->getName() ?></td>
<td><?php echo $memberResource->getResource() ?> 時間/月</td>
<td><?php echo $memberResource->getStartDate() ?></td>
<td><?php echo $memberResource->getEndDate() ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
現在プロジェクトはありません．
<?php endif ?>
