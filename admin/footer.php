<?require $_SERVER['DOCUMENT_ROOT'] . TPL . '/footer.php';

$breadcrumb_url = $_SERVER['DOCUMENT_ROOT'] . TPL . '/breadcrumb.php';

if ( file_exists( $breadcrumb_url ) ) require $breadcrumb_url;

$data = ob_get_contents();
ob_end_clean();

echo $data;