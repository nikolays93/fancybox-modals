/**
 * global FBModals, FBM_Settings
 */
jQuery(document).ready(function($) {
    // const
    var hour = 60 * 60 * 1000;
    var cookieLive = hour * FBM_Settings.expires;
    // let
    var disabled = {};

    function increaseClickCount( modal_id ) {
        $.post( FBM_Settings.ajax_url, {
            action: 'increase_click_count',
            nonce: FBM_Settings.nonce,
            modal_id: modal_id
        });
    }

    function writeCookieTime( modal_id, time ) {
        if( 0 >= time ) return;
        var now = new Date().getTime();

        disabled[ modal_id ] = now + (hour * time);
        document.cookie = FBM_Settings.cookie +"="+ JSON.stringify(disabled) +"; path=/; expires=" + new Date(now + cookieLive).toUTCString();
    }

    function openLWModal(modal_id, args) {
        /**
         * Get cookie
         */
        var o=document.cookie.match(new RegExp("(?:^|; )"+e.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g,"\\$1")+"=([^;]*)"));
        var cookie = o?decodeURIComponent(o[1]):void 0;

        if( cookie ) {
            try {
                disabled = JSON.parse( cookie );
            } catch(e) {
                console.log(e);
            }
        }

        if( args.disable_ontime <= 0 || !(modal_id in disabled) || new Date().getTime() > disabled[ modal_id ] ) {
            try {
                $.fancybox.open({
                    src  : '#modal_' + modal_id,
                    type : 'inline',
                    opts : {
                        afterShow : function( instance, current ) {
                            writeCookieTime( modal_id, args.disable_ontime );
                            increaseClickCount( modal_id );
                        }
                    }
                });
            } catch(e) {
                console.error('Библиотека не установленна');
                console.log(e);
            }
        }
    }

    if( FBM_Settings.selector ) {
        // back compatibility
        $( FBM_Settings.selector ).each(function(index, el) {
            $(this).attr('data-fancybox', $(this).attr('rel') );
        });

        $( FBM_Settings.selector ).fancybox({
            animationEffect : FBM_Settings.lib_args.openCloseEffect,
            transitionEffect : FBM_Settings.lib_args.nextPrevEffect,
        });
    }

    /**
     * Set events
     */
    $.each(FBModals, function(modal_id, modal) {
        if( 'shortcode' == modal.trigger_type ) return;

        switch ( modal.trigger_type ) {
            case 'onclick':
                $( modal.trigger ).on('click', function(event) {
                    openLWModal(modal_id, modal);
                });
            break;
            case 'onload':
                setTimeout(function() {
                     openLWModal(modal_id, modal);
                }, modal.trigger * 1000 );
            break;
            case 'onclose':
                $(document).one('mouseleave', function(event) {
                    openLWModal(modal_id, modal);
                });
            break;
        }
    });

    // Open by shortcode
    $('[data-modal-id]').on('click', function(event) {
        var modal_id = +$(this).attr( 'data-modal-id' );
        if( modal_id >= 1 ) {
            increaseClickCount( modal_id );
        }
    });
});