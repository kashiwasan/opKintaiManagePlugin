<?php

class opKintaiApi
{
  const PATTERN_DATE_DAY = '/^\d{4}-\d{2}-\d{2}$/';
  const PATTERN_DATE_MONTH = '/^\d{4}-\d{2}$/';

  static public function retrieveByDateAndMemberId($date, $memberId, $options = array())
  {
    $conn = Doctrine_Core::getTable('ProjectResourceMaster')->getConnection();
    
    $query = 'SELECT prm.*, p.name AS project_name FROM project_resource_master prm '
           . 'LEFT OUTER JOIN project p ON p.id = prm.project_id '
           . 'WHERE prm.status = :status AND member_id = :member_id AND prm.day LIKE :day '
           . 'ORDER BY prm.day ASC';
    $params = array('member_id' => $memberId, 'status' => 1, 'day' => $date.'%');

    $stmt = $conn->execute($query, $params); 
    $results = array();

    while($row = $stmt->fetch(Doctrine_Core::FETCH_ASSOC))
    {
      $result = $row;
      $time = strtotime($date.' '.$row['end']) - strtotime($date.' '.$row['start']);
      if ($time < 0)
      {
        $time = strtotime($date.' '.$row['end'].' +1 day') - strtotime($date.' '.$row['start']);
      }
      $restParam = explode(':', $row['rest']);
      $restTime = $restParam[0] * 60 * 60 + $restParam[1] * 60 + $restParam[2];
      $time = $time - $restTime;
      $sum += $time;
      $result['working_unix'] = $time;
      $result['working_sum_unix'] = $sum;
      $result['working'] = gmdate('H:i', $time);
      $result['working_sum'] = floor($sum / 3600).gmdate(':i', $sum);
      $result['description'] = nl2br($result['description']);
      $timediff = strtotime(date('Y-m-d')) - strtotime($result['day']);
      if ($timediff >= 0 && $timediff < 259200)
      {
        $result['is_editable'] = true;
      }
      else
      {
        $result['is_editable'] = false;
      }

      $results[$result['day']] = !empty($results[$result['day']]) ? array_merge($results[$result['day']], array($result)) : array($result);
    }

    if (empty($results)) return false;
    return $results;
  }
}
