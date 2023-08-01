<?require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/header.php';

$Includes::Template(
    'main:content.list',
    'contacts',
    array(
        'filter' => array(
            'iblock_id' => 24
        )
    )
);

require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/footer.php';