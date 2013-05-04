<?php

/**
 * PluginProjectResourceMaster form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginProjectResourceMasterForm extends BaseProjectResourceMasterForm
{
  public function setup()
  {
    parent::setup();

    $projectList = Doctrine::getTable('ProjectMember')->retrieveProjectByMemberId($this->getOption('member_id'));
    // $projectList = Doctrine::getTable('ProjectMember')->findByMemberId($this->getOption('member_id'));
    $choices = array();
    foreach($projectList as $p)
    {
      $choices[$p->getProjectId()] = $p->getProject()->getName();
    }

    $this->widgetSchema['project_id'] = new sfWidgetFormChoice(array('choices' => $choices));
    $this->validatorSchema['project_id'] = new sfValidatorChoice(array('choices' => array_keys($choices)));
    
    $this->useFields(array('project_id', 'start', 'end', 'rest', 'description'));
    $this->widgetSchema->setLabels(array(
      'project_id' => 'プロジェクト',
      'start' => '勤務開始',
      'end' => '勤務終了',
      'rest' => '休憩時間',
      'description' => '業務内容',
    ));
    $descriptionHelp = '説明には、きちんと業務内容を入力してください。<br />'
                     . '説明が曖昧な場合、こちらから尋ねたり、あるいは稼働として認められない場合があります。';
    $this->widgetSchema->setHelp('description', $descriptionHelp);
    
  }

  public function save()
  {
    if (!$this->isValid())
    {
      return false;
    }
    $this->values = $this->getValues();
    $params = array('member_id' => $this->getOption('member_id'), 'day' => $this->getOption('day'), 'project_id' => $this->values['project_id'], 'status' => 1);
    $prm = Doctrine::getTable('ProjectResourceMaster')->retrieveOne($params);

    if ($prm)
    {
      $prm->setStatus(0);
      $prm->save();
    }
    $prm = new ProjectResourceMaster();
    $prm->setProjectId($this->values['project_id']);
    $prm->setMemberId($this->getOption('member_id'));
    $prm->setDay($this->getOption('day'));
    $prm->setStart($this->values['start']);
    $prm->setEnd($this->values['end']);
    $prm->setRest($this->values['rest']);
    $prm->setDescription($this->values['description']);
    $prm->setStatus(1); // 新規に公開保存
    $prm->save();

    return true;    
  }
}
