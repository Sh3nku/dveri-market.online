<!DOCTYPE html>

<html>

<head>

<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1" name="viewport">

<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
<link rel="canonical" href="<?$BUFFER -> ShowBuffer( 'canonical' )?>"/>
<?$BUFFER -> SetBuffer( 'canonical', explode( '?', $_SERVER['REQUEST_URI'] )[0] )?>
<meta name="msapplication-TileColor" content="#ffc40d">
<meta name="theme-color" content="#ffffff">

<title><?$MAIN -> ShowTitle()?></title>
<meta name="keywords" content="<?$BUFFER -> ShowBuffer( 'keywords' )?>">
<meta name="description" content="<?$BUFFER -> ShowBuffer( 'description' )?>">

<?
$BUFFER -> SetBuffer( 'H1', $PAGE_H1 );

$MAIN -> Head();

$Includes -> SystemFiles();

$ASSET -> AddCss( TPL . '/js/plugins/modal/modal.css' );
$ASSET -> AddCss( TPL . '/css/swiper.css' );
$ASSET -> AddCss( TPL . '/css/style.css' );
$ASSET -> AddCss( TPL . '/css/responsive.css' );

$ASSET -> AddJs( TPL . '/js/jquery-3.6.0.min.js' );
$ASSET -> AddJs( TPL . '/js/swiper.js' );
$ASSET -> AddJs( TPL . '/js/plugins/modal/modal.js' );
$ASSET -> AddJs( TPL . '/js/main.js' );
?>

<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>

</head>

<body class="js-body-no-transition">

<?if ( $User -> IsAuthorized() ) $Includes::Panel()?>

<header>

    <div class="header-top-wrapper">

        <div class="header-top">

            <div class="button small js-get-call-form">Заказать звонок</div>

            <div class="header-top-phone"><a class="_underline" href="tel:+79817180108">+7 981 718-01-08</a></div>

            <div class="header-top-socials">

                <a class="header-top-social-a" href="https://instagram.com/dveri_market.online" target="_blank">
                    <div class="header-top-social header-top-instagram"></div>
                </a>

                <a class="header-top-social-a" href="https://vk.com/dveri_market.online" target="_blank">
                    <div class="header-top-social header-top-vk"></div>
                </a>

                <a class="header-top-social-a" href="https://www.facebook.com/dveri.market.online" target="_blank">
                    <div class="header-top-social header-top-facebook"></div>
                </a>

            </div>

        </div>

    </div>

    <div class="header">

        <?$Includes::Template(
            'main:menu',
            'top',
            array(
                'type' => 'top'
            )
        )?>

        <div class="header-right">

            <div class="header-auth <?=( ( !$User -> IsAuthorized() ) ? 'js-auth' : '' )?>">

                <?if ( $User -> IsAuthorized() ) {

                    $arResUser = $User -> GetList( array(), array( 'id' => $_SESSION['user']['id'] ) );
                    $arUser = $arResUser['items'][0]?>

                    <div class="profile-wrapper">
                        <a href="/profile/" class="_underline"><?=$arUser['email']?></a>
                        <span class="js-exit _underline">выход</span>
                    </div>

                <?} else {?>

                    <div class="header-enter">
                        <svg>
                            <use xlink:href="<?=TPL?>/images/enter.svg#icon-enter"></use>
                        </svg>
                    </div>

                <?}?>

            </div>

            <div class="header-basket js-open-basket">

                <svg xmlns="http://www.w3.org/2000/svg" width="28.256" height="27.5" viewBox="0 0 28.256 27.5">
                    <g transform="translate(0 -6.028)">
                        <g transform="translate(0 6.028)">
                            <g transform="translate(0 0)">
                                <path d="M99.6,350.322a2.95,2.95,0,1,0,2.95,2.95A2.95,2.95,0,0,0,99.6,350.322Zm0,4.589a1.639,1.639,0,1,1,1.639-1.639A1.639,1.639,0,0,1,99.6,354.911Z" transform="translate(-90.589 -328.722)" />
                                <path d="M298.134,350.322a2.95,2.95,0,1,0,2.95,2.95A2.95,2.95,0,0,0,298.134,350.322Zm0,4.589a1.639,1.639,0,1,1,1.639-1.639A1.639,1.639,0,0,1,298.134,354.911Z" transform="translate(-276.665 -328.722)" />
                                <path d="M28.123,10.42a.82.82,0,0,0-.524-.262L6.26,9.863l-.59-1.8A3.016,3.016,0,0,0,2.852,6.028H.656a.656.656,0,0,0,0,1.311h2.2A1.7,1.7,0,0,1,4.425,8.486L8.588,21.04l-.328.754a3.147,3.147,0,0,0,.295,2.852,3.048,3.048,0,0,0,2.458,1.377h12.75a.656.656,0,1,0,0-1.311H11.013a1.672,1.672,0,0,1-1.377-.787,1.8,1.8,0,0,1-.164-1.573l.262-.59,13.8-1.442a3.606,3.606,0,0,0,3.114-2.753l1.573-6.588A.557.557,0,0,0,28.123,10.42Zm-2.753,6.85a2.229,2.229,0,0,1-2,1.737L9.735,20.417,6.687,11.174l20.092.295Z" transform="translate(0 -6.028)" />
                            </g>
                        </g>
                    </g>
                </svg>

                <?$basket_count = 0;

                if ( $_COOKIE['basket'] ) {

                    $Shop = new Shop();
                    $basket_count = $Shop -> GetBasketCount(
                        array('sum'),
                        array(
                            'cookie' => $_COOKIE['basket']
                        ),
                    );

                }?>

                <span class="header-basket-count"><?=$basket_count?></span>

            </div>

        </div>

    </div>

</header>

<?//if ( $_SESSION['user']['id'] == 1 ) $Functions -> Pre( $_SERVER )?>

<main class="text-content">

    <?if ( $_SERVER['PHP_SELF'] !== '/index.php' ) {?>
        <div class="container">
            <?if ( preg_match( '/katalog/', $_SERVER['REQUEST_URI'] ) || preg_match( '/furnitura/', $_SERVER['REQUEST_URI'] ) ) require_once $_SERVER['DOCUMENT_ROOT'] . '/breadcrumb.php';

            if (
                ( empty( $_GET['product_code'] ) && empty( $_GET['offer_code'] ) )
                || !empty( $arTagPage )
            ) {?>
                <h1><?$BUFFER -> ShowBuffer( 'H1' )?></h1>
            <?}
    }?>