<?php
$targetFolder = __DIR__.'/../storage/app/public'; // Sesuaikan jika folder storage Anda berbeda level
$linkFolder = __DIR__.'/storage';
symlink($targetFolder, $linkFolder);
echo 'Symlink process successfully completed';
?>
