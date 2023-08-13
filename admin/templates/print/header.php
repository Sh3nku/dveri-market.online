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

$ASSET -> AddCss( TPL . '/css/style.css' );
?>


</head>

<body>

<?//if ( $User -> IsAuthorized() ) $Includes::Panel()?>

<header>
    <div class="header__logo">
        <img src="<?=TPL . '/images/logo.svg'?>">
    </div>

    <div class="header__socials">
        <div class="header__socials-item">
            <a class="header__socials-link" href="https://www.facebook.com/dveri.market.online" target="_blank">
                <svg class="header__socials-svg _icon-social-fb">
                    <use xlink:href="/admin/templates/main/images/icons/social-fb.svg#social-fb"></use>
                </svg>

                <span>/dveri.market.online</span>
            </a>
        </div>

        <div class="header__socials-item">
            <a class="header__socials-link" href="https://instagram.com/dveri_market.online" target="_blank">
                <svg class="header__socials-svg _icon-social-insta">
                    <use xlink:href="/admin/templates/main/images/icons/social-insta.svg#social-insta"></use>
                </svg>

                <span>/dveri_market.online</span>
            </a>
        </div>

        <div class="header__socials-item">
            <a class="header__socials-link" href="https://vk.com/dveri_market.online" target="_blank">
                <svg class="header__socials-svg _icon-social-vk">
                    <use xlink:href="/admin/templates/main/images/icons/social-vk.svg#social-vk"></use>
                </svg>

                <span>/dveri_market.online</span>
            </a>
        </div>
    </div>

    <div class="header__work_time">
        <p class="header__work_time__title">График работы</p>
        <p class="header__work_time__text">Ежедневно<br>с 11:00 до 20:00 </p>
    </div>
</header>

<main>