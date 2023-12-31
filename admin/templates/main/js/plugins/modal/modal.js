function show_overlay_modal( array ) {

    var settings = {
        'loader': false
    };

    settings = $.extend( settings, array );

    $( 'body' ).prepend( '<div class="modal-overlay' + ( ( settings.loader ) ? ' loader' : '' ) + '"></div>' ).addClass( '_overflow-hidden' );

    var overlay = $( '.modal-overlay' );

    overlay.fadeIn( 300 );

}

function open_modal ( data, array ) {

    var settings = {
        'width': false
    };

    settings = $.extend( settings, array );

    var overlay = $( '.modal-overlay' ).removeClass( 'loader' );

    if ( !overlay.length ) {

        show_overlay_modal();
        overlay = $( '.modal-overlay' )

    }

    overlay.html(
        '<div class="modal' + ( ( settings.classes ) ? ' ' + settings.classes : '' ) + '"' + ( ( settings.width ) ? ' style="width: 100%; max-width: ' + settings.width + 'px"' : '' ) + '>' +
        '<div class="modal-close"><svg class="_icon-close-modal"><use xlink:href="/admin/templates/main/images/icons/close-modal.svg#close-modal"></use></svg></div>' +
        '<div class="modal-content">' + data + '</div>' +
        '</div>'
    );

    var modal = $( '.modal' );

    center_modal();

    modal.animate( { 'opacity': 1 }, 300 );

    document.querySelectorAll( 'input[type=tel]' ).forEach( e => IMask( e, {
        mask: '+{7} ( 000 ) 000-00-00'
    }));

}

function center_modal() {

    let modal = $( '.modal' ),
        modalHeight = modal.outerHeight() + 100,
        windowHeight = $( window ).height();

    let top = 0;

    if ( windowHeight > modalHeight ) {
        top = ( windowHeight - modalHeight ) / 3;
    }

    modal.css( 'top', top );

}

function close_modal() {

    var overlay = $( '.modal-overlay' );

    overlay.animate({ 'opacity': 0 }, 300, function () {

        $( this ).remove();
        $( 'body' ).removeClass( '_overflow-hidden' )

    })

}

$( function () {

    $( document ).on( 'click', '.modal-close', function() {
        close_modal()
    });

    $( this ).keydown( function( e ) {

        if ( $( '.modal-overlay' ).is( ':visible' ) ) {
            if ( e.which == 27 ) close_modal()
        }

    });

});