<?php

/**
 * opKintaiManagePlugin actions.
 *
 * @package    OpenPNE
 * @subpackage opKintaiManagePlugin
 * @author     Your name here
 */
class opKintaiManagePluginActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfWebRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('default', 'module');
  }

  public function executeListProject(sfWebRequest $request)
  {
    $this->form = new ProjectForm();
    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('project'));
      if ($this->form->isValid())
      {
        $this->form->save();
        $this->getUser()->setFlash('notice', '新規にプロジェクトを追加しました．');
        $this->form = new ProjectForm();
      }
    }
    $this->list = Doctrine::getTable('Project')->findAll();
    $this->projectMember = array();
    foreach($this->list as $project)
    {
      $obj = Doctrine::getTable('ProjectMember')->findByProjectId($project->getId());
      $this->projectMember[$project->getId()] = $obj;
    }

    return sfView::SUCCESS;
  }

  public function executeEditProject(sfWebRequest $request)
  {
    $this->project = Doctrine::getTable('Project')->find($request->getParameter('id'));
    if (!$this->project) $this->forward404();
    
    $this->form = new ProjectForm($this->project);
    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('project'));
      if ($this->form->isValid())
      {
        $this->form->save();
        $this->getUser()->setFlash('notice', 'プロジェクトを編集しました．');
        $this->form = new ProjectForm();
        $this->redirect('opKintaiManagePlugin/listProject');
      }
    }
    return sfView::SUCCESS;  
  }

  public function executeEndProject(sfWebRequest $request)
  {
    $this->project = Doctrine::getTable('Project')->find($request->getParameter('id'));
    if (!$this->project) $this->forward404();
    $this->project->setEndDate(date('Y-m-d'));
    $this->project->save();
    $this->redirect('opKintaiManagePlugin/listProject');
  }

  public function executeListProjectMember(sfWebRequest $request)
  {
    $this->form = new ProjectMemberForm();
    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('project_member'));
      if ($this->form->isValid())
      {
        $obj = $this->form->save();
        $this->getUser()->setFlash('notice', 'メンバーID: '.$obj->member_id.' を，プロジェクトID:'.$obj->project_id.' のメンバーに追加しました．');
      }
    }
    $this->list = Doctrine::getTable('ProjectMember')->findAll();
    return sfView::SUCCESS;
  }

  public function executeListMemberResource(sfWebRequest $request)
  {
    $this->form = new MemberResourceForm();
    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('member_resource'));
      if ($this->form->isValid())
      {
        $obj = $this->form->save();
        $this->getUser()->setFlash('notice', 'メンバーID: '.$obj->member_id.' の稼働時間を,「'.$obj->resource.' 時間/月」で追加しました．');
      }
    }
    $this->list = Doctrine::getTable('MemberResource')->findAll();
    return sfView::SUCCESS;
  }
}
