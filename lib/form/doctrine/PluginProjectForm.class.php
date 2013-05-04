<?php

/**
 * PluginProject form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginProjectForm extends BaseProjectForm
{
  public function setup()
  {
    parent::setup();
    $this->useFields(array('name', 'description', 'start_date', 'end_date'));

    $params['culture'] = sfContext::getInstance()->getUser()->getCulture();
    $params['month_format'] = 'number';


    $isRequired = $this->validatorSchema['start_date']->getOption('required', false);
    if (!$isRequired)
    {
      $params['can_be_empty'] = true;
    }
    $this->widgetSchema['start_date'] = new opWidgetFormDate($params);
    
    $isRequired = $this->validatorSchema['end_date']->getOption('required', false);
    if (!$isRequired)
    {
      $params['can_be_empty'] = true;
    }
    $this->widgetSchema['end_date'] = new opWidgetFormDate($params);


    $this->validatorSchema['start_date'] = new opValidatorDate(array('required' => false, 'date_format_range_error' => 'Y-m-d'));
    $this->validatorSchema['end_date'] = new opValidatorDate(array('required' => false, 'date_format_range_error' => 'Y-m-d'));

    $members = Doctrine::getTable('Member')->findAll();
    $choices = array();
    foreach($members as $member)
    {
      $choices[$member->getId()] = $member->getName();
    }

    $this->widgetSchema['project_members'] = new sfWidgetFormChoice(array(
      'multiple' => true,
      'expanded' => true,
      'choices' => $choices
    ));

    $this->validatorSchema['project_members'] = new sfValidatorChoice(array('multiple' => true, 'choices' => array_keys($choices)));

    if (!$this->isNew)
    {
      $projectMember = Doctrine::getTable('ProjectMember')->findByProjectId($this->getObject()->getId());
      foreach($projectMember as $pm)
      {
        $defaults[$pm->getMember()->getId()] = $pm->getId();
      }
      $this->setDefault('project_members', array_keys($defaults));
    }  

    $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkDate'))));
  }

  public function save()
  {
    if (!$this->isValid())
    {
      return false;
    }

    $obj = parent::save();

    $values = $this->getValues();
    $projectMember = Doctrine::getTable('ProjectMember')->findByProjectId($obj->id);
    foreach($projectMember as $pm)
    {
      $pm->delete();
    }
     
    foreach($values['project_members'] as $v)
    { 
      $projectMember = new ProjectMember();
      $projectMember->setMemberId($v);
      $projectMember->setProjectId($obj->id);
      $projectMember->setDescription('管理画面より初期追加');
      $projectMember->save();
    }

  }

  public function checkDate($validator, $value)
  {
    $start_date = strtotime($value['start_date']);
    $end_date = strtotime($value['end_date']);
    if (false !== $start_date && false !== $end_date)
    {
      if ($start_date > $end_date) throw new sfValidatorError($validator, 'invalid');
    }
    return $value;
  }
}
