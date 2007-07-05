<?php

global $version;

// Global system version
$version = array (
  // Manual numbering
  "major" => 0,
  "minor" => 4,
  "patch" => 0,
  
  // Automated numbering (should be incremented at each commit)
  "build" => 128,
);

$version["string"] = join($version, ".");
?>