<?php slot('submenu') ?>
<?php include_partial('submenu') ?>
<?php end_slot() ?>

<?php slot('title', __('プロジェクト管理マスター')); ?>

<h3>プロジェクトを新規に追加する</h3>
<form action="<?php echo url_for('opKintaiManagePlugin/listProject') ?>" method="post">
<table>
<?php echo $form ?>
<tr>
<td colspan="2"><input type="submit" value=<?php echo __('Add') ?> /></td>
</tr>
</table>
</form>

<h3>プロジェクト一覧</h3>
<?php if ($list): ?>
<table>
<tr>
<th>ID</th>
<th>プロジェクト名</th>
<th>開始日</th>
<th>終了日</th>
<th>プロジェクトメンバー</th>
<th>説明</th>
<th colspan="2">操作</th>
</tr>
<?php foreach ($list as $project): ?>
<tr>
<td><?php echo $project->getId() ?></td>
<td><?php echo $project->getName() ?></td>
<td><?php echo $project->getStartDate() ?></td>
<td><?php echo $project->getEndDate() ?></td>
<td>
<ul>
<?php foreach($projectMember[$project->getId()] as $pm): ?>
<li><?php echo $pm->getMember()->getName() ?></li>
<?php endforeach; ?>
</ul>
</td>
<td><?php echo nl2br($project->getDescription()) ?></td>
<td><?php echo link_to('編集', 'opKintaiManagePlugin/editProject?id='.$project->getId()) ?></td>
<td><?php echo link_to('終了', 'opKintaiManagePlugin/endProject?id='.$project->getId()) ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
現在プロジェクトはありません．
<?php endif ?>
