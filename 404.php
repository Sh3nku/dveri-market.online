<?php require $_SERVER['DOCUMENT_ROOT'] . '/admin/header.php';

$MAIN -> SetTitle( '404 Страница не найдена' );
$BUFFER -> SetBuffer( 'H1', '404' );

echo '<div style="font-size: 100px; line-height: 100px">404</div>';

require $_SERVER['DOCUMENT_ROOT'] . '/admin/footer.php';