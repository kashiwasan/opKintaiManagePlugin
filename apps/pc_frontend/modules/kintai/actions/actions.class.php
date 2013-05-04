<?php

/**
 * kintai actions.
 *
 * @package    OpenPNE
 * @subpackage kintai
 * @author     Your name here
 */
class kintaiActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfWebRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('kintai', 'show');
  }

  // 月ごとの稼働時間を見れるページ
  public function executeShow(sfWebRequest $request)
  {
    $this->date = date('Y-m');
    
    return sfView::SUCCESS;
  }

  // 稼働報告
  public function executeReport(sfWebRequest $request)
  {
    $this->date = $request->getParameter('date', date('Y-m-d'));
    $dateParam = explode('-', $this->date);
    if (false === $this->cleanDate($dateParam[0], $dateParam[1], $dateParam[2]))
    {
      $this->forward404();
    }
    $timediff = strtotime(date('Y-m-d')) - strtotime($this->date);
    if ($timediff < 0 || $timediff > sfConfig::get('app_max_kintai_input_day', 3) * 24 * 60 * 60)
    {
      $this->forward404();
    }
    $prm = new ProjectResourceMaster();
    $prm->setMemberId($this->getUser()->getMemberId());
    $prm->setDay($request->getParameter('date', $this->date));
    $prm->setStatus(1);
    $this->form = new ProjectResourceMasterForm($prm, array('member_id' => $this->getUser()->getMemberId(), 'day' => $this->date));
    if ($request->isMethod(sfWebRequest::POST))
    {
      $this->form->bind($request->getParameter('project_resource_master'));
      if ($this->form->isValid())
      {
        $this->form->save();
        // $this->setLayout(false);
        // return 'Complete';

        $this->redirect('kintai/show');
      }
    }  
    // $this->setLayout(false);
    return sfView::SUCCESS;
  }

  public function executeEdit(sfWebRequest $request)
  {
    $prm = Doctrine::getTable('ProjectResourceMaster')->retrieveOne(array('id' => $request->getParameter('id'), 'status' => 1));
    if (!$prm) $this->forward404();

    if ($prm->getMemberId() !== $this->getUser()->getMemberId()) $this->forward404();
 
    $timediff = strtotime(date('Y-m-d')) - strtotime($prm->getDay());
    if ($timediff < 0 || $timediff > sfConfig::get('app_max_kintai_input_day', 3) * 24 * 60 * 60) $this->forward404();

    $this->id = $prm->getId();
    $this->form = new ProjectResourceMasterForm($prm, array('member_id' => $this->getUser()->getMemberId(), 'day' => $prm->getDay()));
    if ($request->isMethod(sfWebRequest::POST))
    {
      $this->form->bind($request->getParameter('project_resource_master'));
      if ($this->form->isValid())
      {
        // $prm->save();
        $obj = $this->form->save();

        $this->redirect('kintai/show');
      }
    }  
  }

  public function executeDelete(sfWebRequest $request)
  {
    $prm = Doctrine::getTable('ProjectResourceMaster')->find($request->getParameter('id'));
    if (!$prm) $this->forward404();

    if ($prm->getMemberId() !== $this->getUser()->getMemberId()) $this->forward404();
 
    $timediff = strtotime(date('Y-m-d')) - strtotime($prm->getDay());
    if ($timediff < 0 || $timediff > sfConfig::get('app_max_kintai_input_day', 3) * 24 * 60 * 60) $this->forward404();

    $prm->setStatus(2);
    $prm->save();

    $this->redirect('kintai/show');
  }

  protected function cleanDate($year, $month, $day)
  {
    $validator = new opValidatorDate(array(
      'required' => false,
      'date_output' => 'U',
      'empty_value' => time(),
    ));

    try
    {
      return (int)$validator->clean(array(
        'year' => $year,
        'month' => $month,
        'day' => $day,
      ));
    }
    catch (sfValidatorError $e)
    {
      return false;
    }
  }
}
