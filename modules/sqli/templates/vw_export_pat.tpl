{{*
  * Export patient database to CSV file
  *  
  * @category Sqli
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  OXOL, see http://www.mediboard.org/public/OXOL
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

{{mb_script module="sqli" script="sqliAction"}}

<table class="tbl">
  <thead>
    <tr><th>{{tr}}Sqli-export-type{{/tr}}</th><th>{{tr}}Exec{{/tr}}</th></tr>
  <tr><td colspan="2">
    <div class="warning">{{tr}}Sqli-warning-load{{/tr}}</div>
  </td></tr>
  </thead>
  <tbody>
    <tr>
      <th>
        {{tr}}Sqli-export-pat-desc{{/tr}}
      </th>
      <td>
          <a class="button hslip" href="?m=sqli&amp;raw=ajax_export_patient_sqli">EXPORT 2</a>
      </td>
    </tr>
  </tbody>
</table>