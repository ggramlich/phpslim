<?php
$x = java_values(java_context()->getAttribute('x', 100));
$y = $x+1; java_context()->setAttribute('y', $y, 100);
?>