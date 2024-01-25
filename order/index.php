<?require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/header.php';

    $Includes::Template(
        'main:order',
        'order',
        array(
            'basket' => array(
                'select' => array(
                    29 => array( 'name', 'model', 'picture' ),
                    30 => array( 'name' ),
                    21 => array( 'name', 'picture' ),
                    33 => array( 'name', 'picture' )
                ),
                'filter' => array(
                    'cookie' => $_COOKIE['basket']
                )
            ),
            'order_properties' => array(
                'last_name',
                'first_name',
                'middle_name',
                'email',
                'phone',
                'comment',
                'city',
                'street',
                'house',
                'flat'
            )
        )
    );

require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/footer.php';