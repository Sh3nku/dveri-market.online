<?require $_SERVER['DOCUMENT_ROOT'] . '/admin/header.php';

$Includes::Template(
    'print:basket.list',
    'basket',
    array(
        'select' => array(
            29 => array( 'name', 'model', 'picture' ),
            30 => array( 'name' ),
            21 => array( 'name', 'picture' ),
            27 => array( 'name' ),
            28 => array( 'name', 'picture' )
        ),
        'filter' => array(
            'cookie' => $_COOKIE['basket']
        ),
        'order' => array(
            'id' => 'asc'
        ),
        array()
    )
);?>

<div style="margin-top: 32px">
    <?if ( !empty( $_GET['comment'] ) ) {?>
        <p style="margin-bottom: 4px; font-size: 14px; line-height: 18px">Комментарий:</p>
        <p style="margin-bottom: 16px"><?=$_GET['comment']?></p>
    <?}?>

    <p>Ваш менеджер: <b><?=$_GET['manager'] . '</b> ' . $_GET['phone']?></p>
</div>

<script>
    window.print()
</script>

<?require $_SERVER['DOCUMENT_ROOT'] . '/admin/footer.php';
