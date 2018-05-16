<?php

/* Accessibility Tools - Preferences */
function AccessibilityToolsPreferences() {

    /* Save Data */
    if ($_REQUEST["action"] == "save") {
        update_option( MSAT_AccessibilityTools::MSAT_BING_SPEECH_API, $_REQUEST[MSAT_AccessibilityTools::MSAT_BING_SPEECH_API], true );
        update_option( MSAT_AccessibilityTools::MSAT_COMPUTER_VISION_API, $_REQUEST[MSAT_AccessibilityTools::MSAT_COMPUTER_VISION_API], true );
        update_option( MSAT_AccessibilityTools::MSAT_TRANSLATOR_TEXT_API, $_REQUEST[MSAT_AccessibilityTools::MSAT_TRANSLATOR_TEXT_API], true );
        update_option( MSAT_AccessibilityTools::MSAT_LANGUAGE, $_REQUEST[MSAT_AccessibilityTools::MSAT_LANGUAGE], true );
    }

    /* Load Data */
    $msat_options = array(
        MSAT_AccessibilityTools::MSAT_BING_SPEECH_API,
        MSAT_AccessibilityTools::MSAT_COMPUTER_VISION_API,
        MSAT_AccessibilityTools::MSAT_TRANSLATOR_TEXT_API,
        MSAT_AccessibilityTools::MSAT_LANGUAGE
    );

    for ($i = 0; $i < count($msat_options); $i ++) {
        eval("\$$msat_options[$i] = get_option( $msat_options[$i] );");
    }

    /* Languages */
    $languages = MSAT_AccessibilityTools::MSAT_LANGUAGE_LIST;

    foreach ($languages as $key => $value) {
        $selected = "";
        if ($msat_language == $key) {
            $selected = "selected";
        }
        $msat_languages .= "<option value='" . $key . "' " . $selected . ">" . $value["name"] . "</option>";
    }

    require_once __MSAT_SRC_PARTIALS_FOLDER__ . "AccessibilityTools" . DIRECTORY_SEPARATOR . 'preferences.php';
}

/* Accessibility Tools - Bulk */
function AccessibilityToolsBulk() {
    
    $total_images           = count( get_posts( array(
        'fields' => 'ids', 
        'post_type' => 'attachment',
        'post_mime_type' =>'image',
        'posts_per_page' => -1
    ) ) );
    
    $total_optimized_images = count( get_posts( array(
        'fields' => 'ids', 
        'post_type' => 'attachment',
        'post_mime_type' =>'image',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => MSAT_AccessibilityTools::MSAT_POST_META_OPTIMIZED,
                'compare' => 'IN',
                'value' => array(
                    MSAT_AccessibilityTools::MSAT_OPTIMIZED_STATUS_OK, 
                    MSAT_AccessibilityTools::MSAT_OPTIMIZED_STATUS_EXISTS   
                )
            )
        )
    ) ) );
    $total_images_progress  = round( ( $total_optimized_images / $total_images * 100 ), 2 );

    if ($total_images == $total_optimized_images) {
        $images_finished = "bg-success";
    }


    $total_posts            = count( get_posts( array( 
        'fields' => 'ids', 
        'post_type' => 'post',
        'posts_per_page' => -1
    ) ) );
    
    $total_optimized_posts  = count( get_posts( array( 
        'fields' => 'ids', 
        'post_type' => 'post',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => MSAT_AccessibilityTools::MSAT_POST_META_OPTIMIZED,
                'compare' => 'IN',
                'value' => array(
                    MSAT_AccessibilityTools::MSAT_OPTIMIZED_STATUS_OK, 
                    MSAT_AccessibilityTools::MSAT_OPTIMIZED_STATUS_EXISTS   
                )
            )
        )
    ) ) );
    $total_posts_progress   = round( ( $total_optimized_posts / $total_posts * 100 ), 2 );

    if ($total_posts == $total_optimized_posts) {
        $posts_finished = "bg-success";
    }

    require_once __MSAT_SRC_PARTIALS_FOLDER__ . "AccessibilityTools" . DIRECTORY_SEPARATOR . 'bulk.php';
}