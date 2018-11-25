jQuery(document).ready(function($) {
    var triggerTypeID = '_trigger_type',
        $triggerTypeElem = $('#' + triggerTypeID),

        scTxtID = 'shortcode_tpl',
        $scTxtElem = $('#'+scTxtID),
        selectText = function(elm) {
            var range, selection;

            if (window.getSelection) {
                selection = window.getSelection();
                range = document.createRange();
                range.selectNodeContents(elm);
                selection.removeAllRanges();
                selection.addRange(range);
            } else if (document.body.createTextRange) {
                range = document.body.createTextRange();
                range.moveToElementText(elm);
                range.select();
            }
        };

    $triggerTypeElem.on('change', function(event) {
        $('#_shortcode-wrap, #_onclick-wrap, #_onfocus-wrap, #_onload-wrap, #_disable_ontime-wrap')
            .hide()
            .attr('disable', 'true');

        $( '#_' + $(this).val() + '-wrap' )
            .show()
            .removeAttr('disable');

        if( $(this).val() == 'onclose' || $(this).val() == 'onload' ) {
            $( '#_disable_ontime-wrap' )
                .show()
                .removeAttr('disable');
        }
    }).trigger('change');

    // Select the shortcode on click
    $scTxtElem.on('click', function () {
        selectText(this);
    });

    document.getElementById(scTxtID)
        .addEventListener('copy', function(event) {
            var text = window.getSelection()
                .toString().split("'").map(function(string, index) {
                    return (index === 1) ? string.replace(/\s/g, '') : string;
                }).join("'");

            event.clipboardData.setData('text/plain', text);
            event.preventDefault();
        }, true);
});
