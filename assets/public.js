jQuery(document).ready(function($) {

    function isLinkToImage() {
        return /\.(jpe?g|png|gif|bmp|webp)$/i.test(this.getAttribute('href'));
    }

    /**
     * Get list of disabled modals from cookie
     * @return Object from json
     */
    function getDisabledList() {
        var disabled = {};

        var o = document.cookie.match(new RegExp("(?:^|; )" + args.cookie.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, "\\$1") + "=([^;]*)"));
        var cookie = o ? decodeURIComponent(o[1]) : void 0;

        if (cookie) {
            try {
                disabled = JSON.parse(cookie);
            } catch (e) {
                console.log('Can\'t get disabled modal\'s cookie data');
                console.log(e);
            }
        }

        return disabled;
    };

    /**
     * Const int for human acceptable
     */
    var oneHour = 60 * 60 * 1000;

    /** global FBModals list of modal posts */
    var modals = FBModals;

    /** global FBM_Settings general plugin settings */
    var args = FBM_Settings;

    args.disabled = getDisabledList();

    /**
     * Installed fancybox settings
     */
    var libraryArgs = {
        animationEffect: args.lib_args.openCloseEffect,
        transitionEffect: args.lib_args.nextPrevEffect,
    };

    /** @type {String} Custom selector for ignore links */
    var stoplist = '.nolightbox';

    /** @type {String} Gallery items selector (need custom prepare @todo may be add filter) */
    var gallerySelector = '.gallery-item a';

    /**
     * Default buttons and russian language
     */
    $.fancybox.defaults.buttons = args.buttons;
    $.fancybox.defaults.lang = args.lang;
    $.fancybox.defaults.i18n[args.lang] = args.i18n;

    /**
     * Constructs
     */
    var fancyboxModal = function(modalID, modalArgs) {
        this.modal_id = parseInt(modalID);
        this.modal_args = modalArgs ? modalArgs : {};

        // @todo
        // this.defaults = {};
    }

    /**
     * Methods
     */
    fancyboxModal.prototype = {

        /**
         * Write new click count for analitics
         */
        increaseClickCount: function() {
            $.post(args.ajax_url, {
                action: 'increase_click_count',
                nonce: args.nonce,
                modal_id: this.modal_id
            });
        },

        /**
         * Write cookie for temporary disable
         */
        writeCookieTime: function() {
            var now = new Date().getTime();
            var time = parseFloat(this.modal_args.disable_ontime);

            if (!time || 0 >= time) return;

            args.disabled[this.modal_id] = now + (oneHour * time);
            document.cookie = args.cookie + "=" + JSON.stringify(args.disabled) + "; path=/; expires=" + new Date(now + (oneHour * args.expires)).toUTCString();
        },

        /**
         * Try open modal
         */
        open: function() {
            var self = this;

            if (self.modal_args.disable_ontime <= 0 || !(this.modal_id in args.disabled) || new Date().getTime() > args.disabled[this.modal_id]) {
                try {
                    var fancy = {
                        src: '#modal-' + this.modal_id,
                        type: 'inline',
                        opts: {
                            afterShow: function(instance, current) {
                                self.writeCookieTime();
                                self.increaseClickCount();
                            },
                        }
                    };

                    if ('script' == self.modal_args.modal_type) {
                        fancy.src = self.modal_args.src;
                        fancy.type = 'html';
                    }

                    $.fancybox.open(fancy);
                } catch (e) {
                    console.error('Do you hooked up the Fancybox library?');
                    console.log(e);
                }
            }
        }
    }

    /**
     * Set events by wordpress gallery
     */
    if (args.gallery) {
        var $galleryItems = $(gallerySelector).not(stoplist).filter(isLinkToImage);

        $galleryItems.not(stoplist).filter(isLinkToImage).each(function() {
            var galleryID = $(this).closest('.gallery').attr("id");
            var $image = $(this).find("img");

            $(this).attr({
                'data-fancybox': galleryID,
                'title': $image.attr('title'),
                'data-caption': $image.attr('alt')
            });
        });

        stoplist += ', ' + gallerySelector;
    }

    /**
     * Set events by all image links
     */
    if (args.force) {
        $('a').not(stoplist).filter(isLinkToImage).each(function() {
            $(this).fancybox(libraryArgs);
        });
    }

    /**
     * Set events by selector
     */
    if (args.selector) {
        // back compatibility
        $(args.selector).each(function(index, el) {
            if (!$(this).data('fancybox') && $(this).attr('rel')) {
                $(this).data('fancybox', $(this).attr('rel'));
            }
        });

        $(args.selector).not(stoplist).fancybox(libraryArgs);
    }

    /**
     * Set events by modal posts
     */
    $.each(modals, function(modal_id, modalArgs) {
        switch (modalArgs.trigger_type) {
            case 'onclick':
                $(modalArgs.trigger).on('click', function(event) {
                    event.preventDefault();
                    new fancyboxModal(modal_id, modalArgs).open();
                });
                break;

            case 'onload':
                $(window).on('ready post-load', function(event) {
                    setTimeout(function() {
                        new fancyboxModal(modal_id, modalArgs).open();
                    }, modalArgs.trigger * 1000);
                }).trigger('ready');
                break;

            case 'onclose':
                $(document).one('mouseleave', function(event) {
                    new fancyboxModal(modal_id, modalArgs).open();
                });
                break;

            case 'shortcode':
            default:
                $('[data-modal-id="' + modal_id + '"]').on('click', function(event) {
                    event.preventDefault();
                    new fancyboxModal($(this).data('modal-id'), modalArgs).open();
                });
                break;
        }
    });
});