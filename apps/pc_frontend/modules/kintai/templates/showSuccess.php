<!-- // TODO あとで view.yml に移植する -->
<?php use_helper('Javascript') ?>
<?php use_javascript('jquery.min.js') ?>
<?php use_javascript('jquery.tmpl.min.js') ?>
<?php use_javascript('/opKintaiManagePlugin/js/jquery.colorbox.js') ?>
<?php use_stylesheet('/opKintaiManagePlugin/css/colorbox.css', 'last') ?>
<script type="text/javascript">
$(function(){
  $('#change-year-month').change(function(){
    date = $(this).val();
    loadResource(date);
  });
  loadResource('<?php echo $date ?>');
});

function loadResource(date) {
  $('#resource-loading').show();
  $('#memberResourceList').hide();
 
  var data = { date: date, apiKey: openpne.apiKey };

  var result = $.ajax({
    url: openpne.apiBase + 'kintai/resource.json',
    type: 'get',
    data: data,
    dataType: 'json',
    success: function(json) {
      $tmplData = $('#memberResourceListTemplate').tmpl(json.result, { countObject: countObject, renderList: renderList });
      // $('.kintai-add', $tmplData).colorbox({ iframe: true, innerWidth: 700, innerHeight: 420 });
      $('#memberResourceList > tbody').html($tmplData);
      $('#memberResourceList').show();
    },
    error: function(x, r, e){
      // $tmplData = $('#salesMonthlyTemplate').tmpl({0: 0});
      // $('#salesMonthly > tbody').append($tmplData);
    },
  });

  $.when(result).done(function(){
    $('#resource-loading').hide();
  });
}

function countObject(obj) {
  var count = 0;
  for (var key in obj) {
    count++;
  }
  if (count > 0) { count++; }
  return count;
}

function renderList(detail, isDelete) {
  if (true == isDelete) {
    delete detail[0];
  }
  return $('#memberResourceDetailListTemplate').tmpl(detail, { isDelete: isDelete });
}
</script>


<script id="memberResourceListTemplate" type="text/x-jquery-tmpl">
{{if typeof detail !== 'undefined'}}

<tr>
  <td rowspan="${$item.countObject(detail)}">${info.day} (${info.week})</td>
  {{if typeof detail !== "undefined"}}
    {{each $item.renderList(detail[0], false)}}
      {{html $value.innerHTML}}
    {{/each}}
  {{/if}}
</tr>
  {{if typeof detail !== "undefined"}}
    {{each $item.renderList(detail, true)}}
      {{html $value.outerHTML}}
    {{/each}}
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>{{if info.can_add == true}} <a class="kintai-add" href="/kintai/report?date=${info.date}">追加</a>{{/if}}</td>
    <td></td>
  </tr>
  {{/if}}
{{else}}
<tr>
  <td>${info.day} (${info.week})</td>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td>{{if info.can_add == true}} <a class="kintai-add" href="/kintai/report?date=${info.date}">追加</a>{{/if}}</td>
  <td></td>
</tr>
{{/if}}
</script>
<script id="memberResourceDetailListTemplate" type="text/x-jquery-tmpl">
<tr>
  <td>{{if typeof project_name !== "undefined"}}${project_name}{{/if}}</td>
  <td>{{if typeof working !== "undefined"}}${working}{{/if}}</td>
  <td>{{if typeof working_sum !== "undefined"}}${working_sum}{{/if}}</td>
  <td>{{if typeof description !== "undefined"}}{{html description}}{{/if}}</td>
  <td>{{if typeof is_editable !== "undefined"}}{{if is_editable == true}} <a href="/kintai/edit?id=${id}">編集</a>{{/if}}{{/if}}</td>
  <td>{{if typeof is_editable !== "undefined"}}{{if is_editable == true}} <a href="/kintai/delete?id=${id}">削除</a>{{/if}}{{/if}}</td>
</tr>
</script>

<div class="dparts">
<div class="parts">
  <div class="partsHeading"><h3><?php echo $sf_user->getMember()->getName() ?>さん の稼働報告</h3></div>

<select id="change-year-month">
  <option value="<?php echo date('Y-m') ?>"><?php echo date('Y年m月'); ?></option>
  <option value="<?php echo date('Y-m', strtotime(date('Y-m-1').' -1 month')) ?>"><?php echo date('Y年m月', strtotime(date('Y-m-1').' -1 month')); ?></option>
  <option value="<?php echo date('Y-m', strtotime(date('Y-m-1').' -2 month')) ?>"><?php echo date('Y年m月', strtotime(date('Y-m-1').' -2 month')); ?></option>
</select>

<a href="/kintai/report">新しく稼働報告をする</a>
<br />
<br />
    <div id="resource-loading" style="width: 100%; height: 100px; text-align: center;">
      <?php echo op_image_tag('/opKintaiManagePlugin/images/indicator.gif', array('alt' => 'Loading')) ?><br />
      現在集計中です...
    </div>

    <table id="memberResourceList" class="table table-bordered hide" width="810">
      <colgroup>
        <col width="50">
        <col width="150">
        <col width="80">
        <col width="80">
        <col width="200">
        <col width="50">
        <col width="50">
      </colgroup>
    <thead>
      <tr>
        <th>日付</th>
        <th>プロジェクト名</th>
        <th class="bg666">実質勤務時間</th>
        <th>累計勤務時間</th>
        <th>説明</th>
        <th colspan="2">操作</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
    </table>

  </div>
</div>
