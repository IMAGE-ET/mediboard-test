<?php 
$ds = CSQLDataSource::get("std");
$sql = "SELECT * 
        FROM temp_cr";

$list_cr = $ds->exec($sql);

while($row = $ds->fetchArray($list_cr)) {
	$sql = "UPDATE content_html SET content = '" . addslashes($row['content']) . "' WHERE content_id = {$row['content_id']}";
	mbTrace($ds->exec($sql), 'Content id ' . $row['content_id']);
}
?>