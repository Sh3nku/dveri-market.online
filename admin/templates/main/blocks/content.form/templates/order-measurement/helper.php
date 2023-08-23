<?require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/core/core.php';

$Mail = new Mail();

if ( $_POST['formSend'] ) {

    $arResult = $Functions -> SerializeArray( $_POST['formSend'] );
    $answer = array();

    $arToken = $Content -> GetIblockIdByToken( $arResult['token_form'] );

    if ( is_array( $arToken ) && $arToken['iblock_id'] ) {

        $arResult = array_merge(
            $arResult,
            array(
                'iblock_id' => $arToken['iblock_id']
            )
        );

    }

    if ( empty( $arResult['phone'] ) ) {

        $answer['errors'][] = array(
            'input' => 'phone',
            'text' => 'Заполните Телефон'
        );

    }

    if ( !$answer['errors'] ) {

        $answer = $Content -> Add( $arResult );

        if ( $answer['success'] ) {

            $Mail -> SendEmail( 'order-measurement', array(
                'default_name' => DEFAULT_NAME,
                'default_email' => DEFAULT_EMAIL,
                'name' => $arResult['name'],
                'phone' => $arResult['phone'],
                'email' => $arResult['email'],
                'text' => $arResult['text'],
                'iblock_id' => $arToken['iblock_id'],
                'id' => $answer['success']['id'],
                'domain' => DOMAIN
            ));

        }

    }

    echo json_encode( $answer, JSON_UNESCAPED_UNICODE );

}