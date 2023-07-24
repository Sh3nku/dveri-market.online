<!DOCTYPE html>

<html>

<head>

<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">

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

<?//if ( $User -> IsAuthorized() ) $Includes::Panel()?>

<header>

    <div class="container">
        <div class="header">
            <div class="header__left">
                <a class="header__logo" href="/">
                    <img src="<?=TPL?>/images/logo.png">
                </a>

                <button class="header__search-button button _transperent _with-icon | js-search-button">
                    <svg class="_icon-search">
                        <use xlink:href="<?=TPL?>/images/icons/search.svg#search"></use>
                    </svg>
                    <span>Поиск</span>
                </button>

                <button class="header__catalog-button button _with-icon | js-catalog-button">
                    <div class="button__icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <span>Каталог</span>
                </button>
            </div>

            <div class="header__right">
                <div class="header__socials">
                    <div class="header__socials-item">
                        <a href="https://www.facebook.com/dveri.market.online" target="_blank">
                            <svg class="_icon-social-fb">
                                <use xlink:href="<?=TPL?>/images/icons/social-fb.svg#social-fb"></use>
                            </svg>
                        </a>
                    </div>

                    <div class="header__socials-item">
                        <a href="https://instagram.com/dveri_market.online" target="_blank">
                            <svg class="_icon-social-insta">
                                <use xlink:href="<?=TPL?>/images/icons/social-insta.svg#social-insta"></use>
                            </svg>
                        </a>
                    </div>

                    <div class="header__socials-item">
                        <a href="https://vk.com/dveri_market.online" target="_blank">
                            <svg class="_icon-social-vk">
                                <use xlink:href="<?=TPL?>/images/icons/social-vk.svg#social-vk"></use>
                            </svg>
                        </a>
                    </div>
                </div>

                <a class="header__phone _underline" href="tel:+79817180108">+7 981 718-01-08</a>

                <button class="header__call-button button _black | js-get-call-form">Заказать звонок</button>

                <div class="header__call-button__mobile | js-get-call-form">
                    <svg class="_icon-phone">
                        <use xlink:href="<?=TPL?>/images/icons/phone.svg#phone"></use>
                    </svg>
                </div>

                <?$basket_count = 0;

                if ( !empty( $_COOKIE['basket'] ) ) {

                    $Shop = new Shop();
                    $basket_count = $Shop -> GetBasketCount(
                        array('sum'),
                        array(
                            'cookie' => $_COOKIE['basket']
                        ),
                    );

                }?>

                <div class="header__basket | js-open-basket">
                    <svg class="_icon-basket">
                        <use xlink:href="<?=TPL?>/images/icons/basket.svg#basket"></use>
                    </svg>
                    <span class="js-basket-count"><?=$basket_count?></span>
                </div>

                <div class="header__profile">
                    <?if ( $User -> IsAuthorized() ) {

                        $arResUser = $User -> GetList( array(), array( 'id' => $_SESSION['user']['id'] ) );
                        $user_name = explode( '@', $arResUser['items'][0]['email'] )[0]?>

                        <a href="/profile/" class="header__profile-name _underline"><?=$user_name?></a>
                        <div class="header__profile-exit _underline | js-exit">выход</div>

                    <?} else {?>
                        <div class="header__profile-auth">
                            <svg class="_icon-enter | js-auth">
                                <use xlink:href="<?=TPL?>/images/icons/enter.svg#enter"></use>
                            </svg>
                        </div>
                    <?}?>
                </div>
            </div>
        </div>
    </div>

    <!--<div class="header">

        <?$Includes::Template(
            'main:menu',
            'top',
            array(
                'type' => 'top'
            )
        )?>

    </div-->

</header>

<?$Includes::Template(
    'main:section.list',
    'sections',
    array(
        'select' => array(),
        'filter' => array(
            'iblock_id' => 29,
            'active' => 1
        ),
        'order' => array(
            'sort' => 'asc'
        ),
        'params' => array(
            'reset_keys' => 'N'
        )
    )
)?>

<?/*
<nav class="menu">
    <div class="container">
        <div class="menu__main">
            <div class="menu__first-level small-scroll">
                <ul class="menu__first-level__ul">
                    <li class="menu__first-level__li">
                        <a class="menu__first-level__a | js-menu-choice" href="#" data-menu_id="1">
                            <div class="menu__first-level__svg">
                                <svg class="_icon-menu-1">
                                    <use xlink:href="<?=TPL?>/images/icons/menu-1.svg#menu-1"></use>
                                </svg>
                            </div>

                            <span>Двери на заказ</span>

                            <div class="menu__first-level__svg-arrow">
                                <svg class="_icon-arrow-menu-item">
                                    <use xlink:href="<?=TPL?>/images/icons/arrow-menu-item.svg#arrow-menu-item"></use>
                                </svg>
                            </div>
                        </a>
                    </li>

                    <li class="menu__first-level__li">
                        <a class="menu__first-level__a | js-menu-choice" href="#" data-menu_id="2">
                            <div class="menu__first-level__svg">
                                <svg class="_icon-menu-2">
                                    <use xlink:href="<?=TPL?>/images/icons/menu-2.svg#menu-2"></use>
                                </svg>
                            </div>

                            <span>Межкомнатные двери</span>

                            <div class="menu__first-level__svg-arrow">
                                <svg class="_icon-arrow-menu-item">
                                    <use xlink:href="<?=TPL?>/images/icons/arrow-menu-item.svg#arrow-menu-item"></use>
                                </svg>
                            </div>
                        </a>
                    </li>

                    <li class="menu__first-level__li | js-menu-choice" data-menu_id="3">
                        <a class="menu__first-level__a" href="#">
                            <div class="menu__first-level__svg">
                                <svg class="_icon-menu-3">
                                    <use xlink:href="<?=TPL?>/images/icons/menu-3.svg#menu-3"></use>
                                </svg>
                            </div>

                            <span>Скрытые двери</span>
                        </a>
                    </li>

                    <li class="menu__first-level__li | js-menu-choice" data-menu_id="4">
                        <a class="menu__first-level__a" href="#">
                            <div class="menu__first-level__svg">
                                <svg class="_icon-menu-4">
                                    <use xlink:href="<?=TPL?>/images/icons/menu-4.svg#menu-4"></use>
                                </svg>
                            </div>

                            <span>Входные двери</span>
                        </a>
                    </li>

                    <li class="menu__first-level__li | js-menu-choice" data-menu_id="5">
                        <a class="menu__first-level__a" href="#">
                            <div class="menu__first-level__svg">
                                <svg class="_icon-menu-5">
                                    <use xlink:href="<?=TPL?>/images/icons/menu-5.svg#menu-5"></use>
                                </svg>
                            </div>

                            <span>Напольный плинтус</span>
                        </a>
                    </li>

                    <li class="menu__first-level__li | js-menu-choice" data-menu_id="6">
                        <a class="menu__first-level__a" href="#">
                            <div class="menu__first-level__svg">
                                <svg class="_icon-menu-6">
                                    <use xlink:href="<?=TPL?>/images/icons/menu-6.svg#menu-6"></use>
                                </svg>
                            </div>

                            <span>Специализированные двери</span>
                        </a>
                    </li>

                    <li class="menu__first-level__li | js-menu-choice" data-menu_id="7">
                        <a class="menu__first-level__a" href="#">
                            <div class="menu__first-level__svg">
                                <svg class="_icon-menu-7">
                                    <use xlink:href="<?=TPL?>/images/icons/menu-7.svg#menu-7"></use>
                                </svg>
                            </div>

                            <span>Фурнитура</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="menu__second small-scroll">
            <div class="menu__second-level" data-menu_target="1">
                <div class="menu__subtitle">
                    <div class="menu__subtitle__svg-arrow | js-menu-back">
                        <svg class="_icon-arrow-menu-item">
                            <use xlink:href="<?=TPL?>/images/icons/arrow-menu-item.svg#arrow-menu-item"></use>
                        </svg>
                    </div>
                    Двери на заказ
                </div>

                <ul class="menu__second-level__ul small-scroll">
                    <li>
                        <a href="#">Фабрика Престиж</a>
                    </li>

                    <li>
                        <a href="#">Фабрика Гармония</a>
                    </li>

                    <li>
                        <a href="#">Фабрика Илыш</a>
                    </li>
                </ul>
            </div>

            <div class="menu__second-level _active" data-menu_target="2">
                <div class="menu__subtitle">
                    <div class="menu__subtitle__svg-arrow | js-menu-back">
                        <svg class="_icon-arrow-menu-item">
                            <use xlink:href="<?=TPL?>/images/icons/arrow-menu-item.svg#arrow-menu-item"></use>
                        </svg>
                    </div>
                    Межкомнатные двери
                </div>

                <ul class="menu__second-level__ul small-scroll">
                    <li class="menu__second-level__li"><a class="menu__second-level__a" href="#">перейти в раздел "Межкомнатные двери"</a></li>
                    <li class="menu__second-level__li">
                        <a class="menu__second-level__a | js-menu-choice" href="#" data-menu_id="11">
                            ПВХ
                            <div class="menu__first-level__svg-arrow">
                                <svg class="_icon-arrow-menu-item">
                                    <use xlink:href="<?=TPL?>/images/icons/arrow-menu-item.svg#arrow-menu-item"></use>
                                </svg>
                            </div>
                        </a>

                        <div class="menu__third-level" data-menu_target="11">
                            <div class="menu__subtitle">
                                <div class="menu__subtitle__svg-arrow | js-menu-back">
                                    <svg class="_icon-arrow-menu-item">
                                        <use xlink:href="<?=TPL?>/images/icons/arrow-menu-item.svg#arrow-menu-item"></use>
                                    </svg>
                                </div>
                                ПВХ
                            </div>

                            <ul class="menu__third-level__ul small-scroll">
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">перейти в раздел "ПВХ"</a></li>
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">Белые</a></li>
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">Светлые</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="menu__second-level__li">
                        <a class="menu__second-level__a" href="#">Винил</a>
                    </li>

                    <li class="menu__second-level__li">
                        <a class="menu__second-level__a" href="#">Эмаль</a>

                        <div class="menu__third-level">
                            <ul class="menu__third-level__ul">
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">Белые</a></li>
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">Белые глухие</a></li>
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">Белые остеклённые</a></li>
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">Со стеклом</a></li>
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">Глухие</a></li>
                                <li class="menu__third-level__li _more"><a class="menu__third-level__a js-menu-more" href="#">Ещё 2</a></li>
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">Скинни</a></li>
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">Классика</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="menu__second-level__li">
                        <a class="menu__second-level__a" href="#">Эмалит</a>

                        <div class="menu__third-level">
                            <ul class="menu__third-level__ul">
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">Белые</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="menu__second-level__li">
                        <a class="menu__second-level__a" href="#">Ламинат</a>

                        <div class="menu__third-level">
                            <ul class="menu__third-level__ul">
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">Белые</a></li>
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">Глухие</a></li>
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">Со стеклом</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="menu__second-level__li">
                        <a class="menu__second-level__a" href="#">Эко шпон</a>

                        <div class="menu__third-level">
                            <ul class="menu__third-level__ul">
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">Глухие</a></li>
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">Остеклённые</a></li>
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">Классика</a></li>
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">Белые</a></li>
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">Светлые</a></li>
                                <li class="menu__third-level__li _more"><a class="menu__third-level__a js-menu-more" href="#">Ещё 2</a></li>
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">Тёмные</a></li>
                                <li class="menu__third-level__li"><a class="menu__third-level__a" href="#">Браво</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="menu__second-level__li">
                        <a class="menu__second-level__a" href="#">CPL пластиком</a>
                    </li>

                    <li class="menu__second-level__li">
                        <a class="menu__second-level__a" href="#">Финиш Флекс</a>
                    </li>

                    <li class="menu__second-level__li">
                        <a class="menu__second-level__a" href="#">Хард Флекс</a>
                    </li>

                    <li class="menu__second-level__li">
                        <a class="menu__second-level__a" href="#">Soft Touch</a>
                    </li>

                    <li class="menu__second-level__li">
                        <a class="menu__second-level__a" href="#">Soft Touch</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
*/?>

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