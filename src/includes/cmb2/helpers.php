<?php

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string $key     Options array key
 * @param  mixed  $default Optional default value
 * @return mixed           Option value
 */
function iande_get_option($key = '', $default = false)
{
    if (function_exists('cmb2_get_option')) {
        // Use cmb2_get_option as it passes through some key filters.
        return cmb2_get_option('iande', $key, $default);
    }

    // Fallback to get_option if CMB2 is not loaded yet.
    $opts = get_option('iande', $default);

    $val = $default;

    if ('all' == $key) {
        $val = $opts;
    } elseif (is_array($opts) && array_key_exists($key, $opts) && false !== $opts[$key]) {
        $val = $opts[$key];
    }

    return $val;
}

/**
 * The "group_title" will display the value of the 'title' sub-field, if it exists,
 * or fall back to the default CMB2 group title method.
 */
function iande_add_js_for_repeatable_titles()
{
    add_action(is_admin() ? 'admin_footer' : 'wp_footer', 'iande_add_js_for_repeatable_titles_to_footer');
    //add_action('admin_footer', 'iande_add_js_for_repeatable_titles_to_footer');
}

function iande_add_js_for_repeatable_titles_to_footer()
{ ?>
    <script type="text/javascript">
        jQuery(function($) {
            var $box = $(document.getElementById('institution_profile_repeat'));

            var replaceTitles = function() {
                $box.find('.cmb-group-title').each(function() {
                    var $this = $(this);
                    var txt = $this.next().find('[id$="title"]').val();
                    var rowindex;

                    if (!txt) {
                        txt = $box.find('[data-grouptitle]').data('grouptitle');
                        if (txt) {
                            rowindex = $this.parents('[data-iterator]').data('iterator');
                            txt = txt.replace('{#}', (rowindex + 1));
                        }
                    }

                    if (txt) {
                        $this.text(txt);
                    }
                });
            };

            var replaceOnKeyUp = function(evt) {
                var $this = $(evt.target);
                var id = 'title';

                if (evt.target.id.indexOf(id, evt.target.id.length - id.length) !== -1) {
                    $this.parents('.cmb-row.cmb-repeatable-grouping').find('.cmb-group-title').text($this.val());
                }
            };

            $box
                .on('cmb2_add_row cmb2_remove_row cmb2_shift_rows_complete', replaceTitles)
                .on('keyup', replaceOnKeyUp);

            replaceTitles();
        });
    </script>
<?php
}

function teste_option() {
    $option = iande_get_option( 'institution_profile' );
    echo '<pre>';
    var_dump( $option );
    echo '</pre>';
}
//add_action( 'wp_footer', 'teste_option' );