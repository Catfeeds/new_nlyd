<div class="wrap">
    <h1 class="wp-heading-inline">项目新增</h1>

    <hr class="wp-header-end">

    <form name="post" action="post.php" method="post" id="post"<?php
    /**
     * Fires inside the post editor form tag.
     *
     * @since 3.0.0
     *
     * @param WP_Post $post Post object.
     */
    do_action( 'post_edit_form_tag', $post );

    $referer = wp_get_referer();
    ?>>
        <?php wp_nonce_field($nonce_action); ?>
        <input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_ID ?>" />
        <input type="hidden" id="hiddenaction" name="action" value="<?php echo esc_attr( $form_action ) ?>" />
        <input type="hidden" id="originalaction" name="originalaction" value="<?php echo esc_attr( $form_action ) ?>" />
        <input type="hidden" id="post_author" name="post_author" value="<?php echo esc_attr( $post->post_author ); ?>" />
        <input type="hidden" id="post_type" name="post_type" value="<?php echo esc_attr( $post_type ) ?>" />
        <input type="hidden" id="original_post_status" name="original_post_status" value="<?php echo esc_attr( $post->post_status) ?>" />
        <input type="hidden" id="referredby" name="referredby" value="<?php echo $referer ? esc_url( $referer ) : ''; ?>" />
        <?php if ( ! empty( $active_post_lock ) ) { ?>
            <input type="hidden" id="active_post_lock" value="<?php echo esc_attr( implode( ':', $active_post_lock ) ); ?>" />
            <?php
        }
        if ( 'draft' != get_post_status( $post ) )
            wp_original_referer_field(true, 'previous');

        echo $form_extra;

        wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
        wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
        ?>

        <?php
        /**
         * Fires at the beginning of the edit form.
         *
         * At this point, the required hidden fields and nonces have already been output.
         *
         * @since 3.7.0
         *
         * @param WP_Post $post Post object.
         */
        do_action( 'edit_form_top', $post ); ?>

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">
                <div id="post-body-content">

                    <?php if ( post_type_supports($post_type, 'title') ) { ?>
                        <div id="titlediv">
                            <div id="titlewrap">
                                <?php
                                /**
                                 * Filters the title field placeholder text.
                                 *
                                 * @since 3.1.0
                                 *
                                 * @param string  $text Placeholder text. Default 'Enter title here'.
                                 * @param WP_Post $post Post object.
                                 */
                                $title_placeholder = apply_filters( 'enter_title_here', __( 'Enter title here' ), $post );
                                ?>
                                <label class="screen-reader-text" id="title-prompt-text" for="title"><?php echo $title_placeholder; ?></label>
                                <input type="text" name="post_title" size="30" value="<?php echo esc_attr( $post->post_title ); ?>" id="title" spellcheck="true" autocomplete="off" />
                            </div>
                            <?php
                            /**
                             * Fires before the permalink field in the edit form.
                             *
                             * @since 4.1.0
                             *
                             * @param WP_Post $post Post object.
                             */
                            do_action( 'edit_form_before_permalink', $post );
                            ?>
                            <div class="inside">
                                <?php
                                if ( $viewable ) :
                                    $sample_permalink_html = $post_type_object->public ? get_sample_permalink_html($post->ID) : '';

// As of 4.4, the Get Shortlink button is hidden by default.
                                    if ( has_filter( 'pre_get_shortlink' ) || has_filter( 'get_shortlink' ) ) {
                                        $shortlink = wp_get_shortlink($post->ID, 'post');

                                        if ( !empty( $shortlink ) && $shortlink !== $permalink && $permalink !== home_url('?page_id=' . $post->ID) ) {
                                            $sample_permalink_html .= '<input id="shortlink" type="hidden" value="' . esc_attr( $shortlink ) . '" /><button type="button" class="button button-small" onclick="prompt(&#39;URL:&#39;, jQuery(\'#shortlink\').val());">' . __( 'Get Shortlink' ) . '</button>';
                                        }
                                    }

                                    if ( $post_type_object->public && ! ( 'pending' == get_post_status( $post ) && !current_user_can( $post_type_object->cap->publish_posts ) ) ) {
                                        $has_sample_permalink = $sample_permalink_html && 'auto-draft' != $post->post_status;
                                        ?>
                                        <div id="edit-slug-box" class="hide-if-no-js">
                                            <?php
                                            if ( $has_sample_permalink )
                                                echo $sample_permalink_html;
                                            ?>
                                        </div>
                                        <?php
                                    }
                                endif;
                                ?>
                            </div>
                            <?php
                            wp_nonce_field( 'samplepermalink', 'samplepermalinknonce', false );
                            ?>
                        </div><!-- /titlediv -->
                        <?php
                    }
                    /**
                     * Fires after the title field.
                     *
                     * @since 3.5.0
                     *
                     * @param WP_Post $post Post object.
                     */
                    do_action( 'edit_form_after_title', $post );

                    if ( post_type_supports($post_type, 'editor') ) {
                        ?>
                        <div id="postdivrich" class="postarea<?php if ( $_wp_editor_expand ) { echo ' wp-editor-expand'; } ?>">

                            <?php wp_editor( $post->post_content, 'content', array(
                                '_content_editor_dfw' => $_content_editor_dfw,
                                'drag_drop_upload' => true,
                                'tabfocus_elements' => 'content-html,save-post',
                                'editor_height' => 300,
                                'tinymce' => array(
                                    'resize' => false,
                                    'wp_autoresize_on' => $_wp_editor_expand,
                                    'add_unload_trigger' => false,
                                    'wp_keep_scroll_position' => ! $is_IE,
                                ),
                            ) ); ?>
                            <table id="post-status-info"><tbody><tr>
                                    <td id="wp-word-count" class="hide-if-no-js"><?php printf( __( 'Word count: %s' ), '<span class="word-count">0</span>' ); ?></td>
                                    <td class="autosave-info">
                                        <span class="autosave-message">&nbsp;</span>
                                        <?php
                                        if ( 'auto-draft' != $post->post_status ) {
                                            echo '<span id="last-edit">';
                                            if ( $last_user = get_userdata( get_post_meta( $post_ID, '_edit_last', true ) ) ) {
                                                /* translators: 1: Name of most recent post author, 2: Post edited date, 3: Post edited time */
                                                printf( __( 'Last edited by %1$s on %2$s at %3$s' ), esc_html( $last_user->display_name ), mysql2date( __( 'F j, Y' ), $post->post_modified ), mysql2date( __( 'g:i a' ), $post->post_modified ) );
                                            } else {
                                                /* translators: 1: Post edited date, 2: Post edited time */
                                                printf( __( 'Last edited on %1$s at %2$s' ), mysql2date( __( 'F j, Y' ), $post->post_modified ), mysql2date( __( 'g:i a' ), $post->post_modified ) );
                                            }
                                            echo '</span>';
                                        } ?>
                                    </td>
                                    <td id="content-resize-handle" class="hide-if-no-js"><br /></td>
                                </tr></tbody></table>

                        </div>
                    <?php }
                    /**
                     * Fires after the content editor.
                     *
                     * @since 3.5.0
                     *
                     * @param WP_Post $post Post object.
                     */
                    do_action( 'edit_form_after_editor', $post );
                    ?>
                </div><!-- /post-body-content -->

                <div id="postbox-container-1" class="postbox-container">
                    <?php

                    if ( 'page' == $post_type ) {
                        /**
                         * Fires before meta boxes with 'side' context are output for the 'page' post type.
                         *
                         * The submitpage box is a meta box with 'side' context, so this hook fires just before it is output.
                         *
                         * @since 2.5.0
                         *
                         * @param WP_Post $post Post object.
                         */
                        do_action( 'submitpage_box', $post );
                    }
                    else {
                        /**
                         * Fires before meta boxes with 'side' context are output for all post types other than 'page'.
                         *
                         * The submitpost box is a meta box with 'side' context, so this hook fires just before it is output.
                         *
                         * @since 2.5.0
                         *
                         * @param WP_Post $post Post object.
                         */
                        do_action( 'submitpost_box', $post );
                    }


                    do_meta_boxes($post_type, 'side', $post);

                    ?>
                </div>
                <div id="postbox-container-2" class="postbox-container">
                    <?php

                    do_meta_boxes(null, 'normal', $post);

                    if ( 'page' == $post_type ) {
                        /**
                         * Fires after 'normal' context meta boxes have been output for the 'page' post type.
                         *
                         * @since 1.5.0
                         *
                         * @param WP_Post $post Post object.
                         */
                        do_action( 'edit_page_form', $post );
                    }
                    else {
                        /**
                         * Fires after 'normal' context meta boxes have been output for all post types other than 'page'.
                         *
                         * @since 1.5.0
                         *
                         * @param WP_Post $post Post object.
                         */
                        do_action( 'edit_form_advanced', $post );
                    }


                    do_meta_boxes(null, 'advanced', $post);

                    ?>
                </div>
                <?php
                /**
                 * Fires after all meta box sections have been output, before the closing #post-body div.
                 *
                 * @since 2.1.0
                 *
                 * @param WP_Post $post Post object.
                 */
                do_action( 'dbx_post_sidebar', $post );

                ?>
            </div><!-- /post-body -->
            <br class="clear" />
        </div><!-- /poststuff -->
    </form>
</div>
