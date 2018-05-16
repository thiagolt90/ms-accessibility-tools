<?php

class MSAT_AccessibilityTools
{
    /* Constants */
    const MSAT_BING_SPEECH_API          = "msat_bing_speech_api";
    const MSAT_COMPUTER_VISION_API      = "msat_computer_vision_api";
    const MSAT_TRANSLATOR_TEXT_API      = "msat_translator_text_api";
    const MSAT_LANGUAGE                 = "msat_language";
    const MSAT_POST_META_OPTIMIZED      = "_msat_optimized";

    const MSAT_OPTIMIZED_STATUS_OK      = "1";
    const MSAT_OPTIMIZED_STATUS_EXISTS  = "2";

    const MSAT_LANGUAGE_LIST            = array(
        "pt-br" => array(
            "name" => "Português (Brazil)",
            "speaker" => "pt-BR, HeloisaRUS"
        ),
        "es-es" => array(
            "name" => "Español (Spain)",
            "speaker" => "es-ES, Laura, Apollo"
        ),
        "es-mx" => array(
            "name" => "Español (Mexico)",
            "speaker" => "es-MX, HildaRUS"
        ),
        "en-us" => array(
            "name" => "English (United States)",
            "speaker" => "en-US, ZiraRUS"
        )
    );
    
    /* Constructor - Init Components */
    public function __construct() {
        $this->msat_add_menus();
        $this->msat_add_hooks_and_filters();
        $this->msat_load_modules();
    }

    /* Add Menus and Submenus */
    private function msat_add_menus() {

        add_action('admin_menu', 'msat_add_all_menus');

        function msat_add_all_menus() {
            add_menu_page( 'Accessibility Tools', 'Accessibility Tools', 'manage_options', 'AccessibilityToolsPreferences', 'AccessibilityToolsPreferences', 'dashicons-welcome-view-site', 80 );
            add_submenu_page( 'AccessibilityToolsPreferences', 'Bulk Optimize', 'Bulk Optimize', 'manage_options', 'AccessibilityToolsBulk', 'AccessibilityToolsBulk' );
        }
    }

    /* Add Hooks and Filters */
    private function msat_add_hooks_and_filters() {
        /* Text to Speech */
        add_action( 'save_post', array( 'MSAT_TextToSpeech', 'msat_tts_text_to_speech_wp_save_post_hook' ) );
        add_filter( 'the_content', array( 'MSAT_TextToSpeech', 'msat_tts_text_to_speech_wp_hook_the_content' ) );
        
        /** Ajax */
        add_action( 'wp_ajax_optimize_posts', 'MSAT_TextToSpeech::msat_bulk_optimize_posts' );
        /* \\ Text to Speech */

        /* Picture Subtitle */
        add_filter( 'add_attachment', array( 'MSAT_PictureSubtitle', 'msat_picture_subtitle_wp_add_attachment_hook' ) );

        /** Ajax */
        add_action( 'wp_ajax_optimize_images', array( 'MSAT_PictureSubtitle', 'msat_bulk_optimize_images' ) );
        /* \\ Picture Subtitle */
    }

    /* Load all Modules */
    private function msat_load_modules() {
        $files = scandir( __MSAT_SRC_MODULES_FOLDER__ );

        foreach ($files as $file) {
            if ( is_file( __MSAT_SRC_MODULES_FOLDER__ . $file ) ) {
                include_once __MSAT_SRC_MODULES_FOLDER__ . $file;
            }
        }
    }
}
