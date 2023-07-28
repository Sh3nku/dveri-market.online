<?php require $_SERVER['DOCUMENT_ROOT'] . '/admin/header.php';

$MAIN -> SetTitle( '404 Страница не найдена' )?>

<section class="page-404">
    <div class="container">
        <div class="page-404__title">
            <img src="<?=TPL?>/images/title-404.svg">
        </div>

        <div class="page-404__text">Такой страницы нет, зато есть много дверей, которые можно найти у нас в каталоге.</div>

        <a class="button _with-icon" href="/katalog/">
            <span>В каталог</span>
            <svg class="_icon-arrow-link">
                <use xlink:href="/admin/templates/main/images/icons/arrow-link.svg#arrow-link"></use>
            </svg>
        </a>
    </div>
</section>

<script>
    $( 'h1' ).remove();
    $( 'main > .container' ).css({
        'padding': 0,
        'max-width': 'none'
    })
</script>

<?require $_SERVER['DOCUMENT_ROOT'] . '/admin/footer.php';