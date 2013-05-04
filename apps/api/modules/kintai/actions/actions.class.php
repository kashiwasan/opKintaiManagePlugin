<?php

/**
 * kintai actions.
 *
 * @package    OpenPNE
 * @subpackage kintai
 * @author     Your name here
 */
class kintaiActions extends opJsonApiActions
{
 /**
  * Executes index action
  *
  * @param sfWebRequest $request A request object
  */
  public function executeResource(sfWebRequest $request)
  {
    $date = $request->getParameter('date', date('Y-m-d'));
    if (!preg_match(opKintaiApi::PATTERN_DATE_DAY, $date) && !preg_match(opKintaiApi::PATTERN_DATE_MONTH, $date) )
    {
      $this->forward400('The parameter "date" parameter is invalid.');
    }

    $weekjp = array('日', '月', '火', '水', '木', '金', '土');

    if (preg_match(opKintaiApi::PATTERN_DATE_MONTH, $date))
    {
      $dateParam = explode('-', $date);
      $count = date('t', mktime(0, 0, 0, $dateParam[1], 1, $dateParam[0]));
      $results = array();

      for ($i=1;$i<=$count;$i++)
      {
        $requestDate = sprintf('%04d-%02d-%02d', $dateParam[0], $dateParam[1], $i);
        $results[$requestDate]['info']['date'] = $requestDate;
        $results[$requestDate]['info']['year'] = $dateParam[0];
        $results[$requestDate]['info']['month'] = $dateParam[1];
        $results[$requestDate]['info']['day'] = sprintf('%02d', $i);
        $results[$requestDate]['info']['week'] = $weekjp[date('w', mktime(0, 0, 0, $dateParam[1], $i, $dateParam[0]))];
        $timediff = strtotime(date('Y-m-d')) - strtotime($requestDate);
        if ($timediff >= 0 && $timediff < 259200) { $results[$requestDate]['info']['can_add'] = true; }
        else { $results[$requestDate]['info']['can_add'] = false; }
      }

      $kintaiList = opKintaiApi::retrieveByDateAndMemberId(sprintf('%04d-%02d', $dateParam[0], $dateParam[1]), $this->getUser()->getMemberId());
      foreach($results as $key => $result)
      {
        if (!empty($kintaiList[$key]))
        {
          $results[$key]['detail'] = $kintaiList[$key];
        }
      }
      $results = array_values($results);
    }
    elseif(preg_match(opKintaiApi::PATTERN_DATE_DAY, $date))
    {
      $dateParam = explode('-', $date);
      $count = date('t', mktime(0, 0, 0, $dateParam[1], $dateParam[2], $dateParam[0]));
      $results = array();

        $requestDate = sprintf('%04d-%02d-%02d', $dateParam[0], $dateParam[1], $dateParam[2]);
        $result['info']['date'] = $requestDate;
        $result['info']['year'] = $dateParam[0];
        $result['info']['month'] = $dateParam[1];
        $result['info']['day'] = sprintf('%02d', $dateParam[2]);
        $result['info']['week'] = $weekjp[date('w', mktime(0, 0, 0, $dateParam[1], $dateParam[2], $dateParam[0]))];
        $timediff = strtotime(date('Y-m-d')) - strtotime($requestDate);
        if ($timediff >= 0 && $timediff < 259200) $result['info']['can_add'] = true;
        else $result['info']['can_add'] = false;

        $result = array_merge($result, opKintaiApi::retrieveByDateAndMemberId($date, $this->getUser()->getMemberId()));
        $results[] = $result;
    }
    return $this->renderJSON(array('status' => 'success', 'result' => $results)); 
  }
}
