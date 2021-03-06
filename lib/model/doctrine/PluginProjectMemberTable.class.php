<?php

/**
 * PluginProjectMemberTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PluginProjectMemberTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object PluginProjectMemberTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('PluginProjectMember');
    }

    public function retrieveProjectByMemberId($memberId, $isActive = true)
    {
      $q = $this->createQuery('m')
                ->addFrom('Project p')
                ->addWhere('p.id = m.project_id')
                ->select('m.*');
      $q->addWhere('m.member_id = ?', $memberId);

      if (true === $isActive)
      {
        $q->addWhere('p.start_date <= ? OR p.start_date IS NULL', date('Y-m-d'));
        $q->addWhere('p.end_date >= ? OR p.end_date IS NULL', date('Y-m-d'));
      }
      return $q->execute();
    }
}
