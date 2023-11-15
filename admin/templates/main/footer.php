    <?if ( $_SERVER['REQUEST_URI'] !== '/' ) {?>
        </div>
    <?}?>

</main>

<footer>

    <div class="container">

        <div class="footer">Внимание! Данный сайт носит исключительно информационный характер и не является публичной офертой, определяемой положениями части 2 статьи 437 ГК РФ. Цвет продукции, представленной на сайте может отличаться от реального, в связи с различными настройками ваших устройств для просмотра.</div>

        <div class="copyright">

            <div class="copyright-logo">
                <img src="/admin/templates/main/images/logo.png">
            </div>

            <div class="copyright-body">

                <?$Includes::Template(
                    'main:menu',
                    'bottom',
                    array(
                        'type' => 'bottom'
                    )
                )?>

                <div class="copyright-text">© <?=date( 'Y' )?>. Интернет магазин дверей "Двери маркет". Все права защищены.</div>

            </div>

        </div>

    </div>

</footer>

<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
    (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

    ym(87597326, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true,
        ecommerce:"dataLayer"
    });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/87597326" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

    <div class="body-overlay"></div>
    <div class="search-popup">
        <div class="container">
            <form class="search-popup__form" action="/search/">
                <svg class="_icon-search">
                    <use xlink:href="<?=TPL?>/images/icons/search.svg#search"></use>
                </svg>

                <input name="q" type="text" class="search-popup__input | js-search-popup-input" placeholder="Поиск по сайту">

                <svg class="_icon-close | js-search-popup-close">
                    <use xlink:href="<?=TPL?>/images/icons/close.svg#close"></use>
                </svg>
            </form>
        </div>
    </div>

    <div class="search-popup__content-wrapper">
        <div class="container">
            <div class="search-popup__content small-scroll | js-search-popup-content"></div>
        </div>
    </div>

    <script src="<?=TPL . '/js/imask.js'?>"></script>

</body>

</html>