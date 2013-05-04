<?php

/**
 * PluginMemberResource form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginMemberResourceForm extends BaseMemberResourceForm
{
  public function setup()
  {
    parent::setup();
    $this->useFields(array('member_id', 'resource', 'start_date', 'end_date'));

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


    $this->validatorSchema['start_date'] = new opValidatorDate(array('date_format_range_error' => 'Y-m-d'));
    $this->validatorSchema['end_date'] = new opValidatorDate(array('date_format_range_error' => 'Y-m-d'));
    $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkDate'))));
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
