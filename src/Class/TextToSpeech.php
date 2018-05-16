<?php

class MSAT_TextToSpeech
{
    const MSAT_POST_META_AUDIO = "_msat_post_meta_audio_";
    const MSAT_TTS_OUTPUT_FORMAT = "audio-16khz-32kbitrate-mono-mp3";
    const MSAT_TTS_APP_ID = "34b0eaf04ab94405a88471a0c7ce443b";
    const MSAT_TTS_CLIENT_ID = "34b0eaf04ab94405a88471a0c7ce443b";
    const MSAT_TTS_USER_AGENT = "Microsoft-SCC-Accessibility-Tools";

    const MSAT_TTS_AZURE_SYNTHESIZE_URL = "https://speech.platform.bing.com/synthesize";

    /** Get audio from Azure */
    public static function msat_tts_get_audio( $authorization, $text ) {
    
        $audio_content = wp_remote_post(
            MSAT_TextToSpeech::MSAT_TTS_AZURE_SYNTHESIZE_URL,
            array(
                "headers" => array(
                    "Authorization" => "Bearer " . $authorization,
                    "Content-Type" => "application/xml",
                    "X-Microsoft-OutputFormat" => MSAT_TextToSpeech::MSAT_TTS_OUTPUT_FORMAT,
                    "X-Search-AppId" => MSAT_TextToSpeech::MSAT_TTS_APP_ID,
                    "X-Search-ClientID" => MSAT_TextToSpeech::MSAT_TTS_CLIENT_ID,
                    "User-Agent" => MSAT_TextToSpeech::MSAT_TTS_USER_AGENT,
                    "Content-length" => strlen($text)
                ),
                "body" => $text,
                "timeout" => 60
            )
        );
    
        return $audio_content;
    }

    /* WP Hook on post save */
    public static function msat_tts_text_to_speech_wp_save_post_hook( $post_id, $error = 0 ) {
        
        if ( wp_is_post_revision( $post_id ) )
            return;

        $result = MSAT_TextToSpeech::msat_tts_generate_audio_file( $post_id );
        if ( !$result ) {
            if ( $errors < 3 ) { /* Try more times (API's instabilities) */
                MSAT_TextToSpeech::msat_tts_text_to_speech_wp_save_post_hook( $post_id, $error ++ );
            } else {
                wp_die("Failed to generate audio. Please try again.");
            }
        }
    }

    /** Generate the audio file on Disk */
    public static function msat_tts_generate_audio_file( $post_id ) {
        /* Language */
        $language_speaker = MSAT_AccessibilityTools::MSAT_LANGUAGE_LIST[ get_option( MSAT_AccessibilityTools::MSAT_LANGUAGE ) ]["speaker"];

        /* Content */
        $content_post = get_post( $post_id );
        $content = strip_tags( strip_shortcodes( $content_post->post_content ), '<img>' );
        $content = preg_replace( '/<img.*?alt="([^"]+?)".*?>/', '. Imagem: $1.', $content );
        $content = wp_strip_all_tags( $content );
        
        /* Authorization */
        $authorization = MSAT_AzureAuthorization::msat_get_authorization( get_option( MSAT_AccessibilityTools::MSAT_BING_SPEECH_API ) );

        if ( is_wp_error( $authorization ) ) {
            return false;
        } else {
            $authorization = $authorization["body"];
        }
    
        $upload_dir = wp_upload_dir();
        $file_name = $content_post->post_name . ".mp3";
    
        if ( file_exists($upload_dir["path"] . "/" . $file_name) ) {
            unlink($upload_dir["path"] . "/" . $file_name);
        }
    
        $text_sparation = preg_split( "/[\?\!\.]/", $content );
        
        for ( $i = 0; $i < count($text_sparation); $i ++ ) {
    
            $content_text = $text_sparation[$i] . " ";
            
            $post_content = '<speak version="1.0" xmlns="' . MSAT_TextToSpeech::MSAT_TTS_AZURE_SYNTHESIZE_URL . '" xml:lang="' . get_option( MSAT_AccessibilityTools::MSAT_LANGUAGE ) . '">' .
                                '<voice name="Microsoft Server Speech Text to Speech Voice (' . $language_speaker . ')">' .
                                    $content_text . 
                                '</voice>' .
                            '</speak>';
            
            $audio_content = MSAT_TextToSpeech::msat_tts_get_audio( $authorization, $post_content );

            if ( is_wp_error( $audio_content ) ) {
                return false;
            } else {
                $audio_content = $audio_content["body"];
            }
        
            $file_obj = fopen( $upload_dir["path"] . "/" . $file_name, "a" ) or die( "Unable to open file!" );
            fwrite($file_obj, $audio_content);
            fclose($file_obj);
        }
    
        update_post_meta( $post_id, MSAT_TextToSpeech::MSAT_POST_META_AUDIO, $upload_dir["url"] . "/" . $file_name );
        update_post_meta( $post_id, MSAT_AccessibilityTools::MSAT_POST_META_OPTIMIZED, MSAT_AccessibilityTools::MSAT_OPTIMIZED_STATUS_OK );

        return $post_id;
    }

    /** Add the audio player before the content */
    public static function msat_tts_text_to_speech_wp_hook_the_content( $content ) {

        if ( is_single() ) {
            $audio = get_post_meta( get_the_ID(), MSAT_TextToSpeech::MSAT_POST_META_AUDIO, true );
            if ( $audio != null ) {

                /* Language */
                if (get_option( MSAT_AccessibilityTools::MSAT_LANGUAGE ) == "pt-br") {
                    $language_speaker = "Ouvir Texto";
                } elseif (get_option( MSAT_AccessibilityTools::MSAT_LANGUAGE ) == "en-us") {
                    $language_speaker = "Listen to Text";
                } elseif (get_option( MSAT_AccessibilityTools::MSAT_LANGUAGE ) == "es-es" || get_option( MSAT_AccessibilityTools::MSAT_LANGUAGE ) == "es-mx") {
                    $language_speaker = "Escuchar texto";
                }

                $custom_content = '
                                    <label for="ouvir-texto" style="font-size: 10px;">' . $language_speaker . '</label>
                                    <br>
                                    <audio id="ouvir-texto" controls style="width: 100%;">
                                        <source src="' . $audio . '" type="audio/mpeg">
                                        Your browser does not support the audio tag.
                                    </audio> 
                                ';
                $custom_content .= $content;
                return $custom_content;
            }
        }
    
        return $content;
    }

    /* Load the next post to generate the MP3 (used in bulk optimization) */
    public static function msat_bulk_optimize_posts() {
        global $wpdb;
        $post_id = 0;
        $return_id = 0;
        $error = false;
        $finished = false;

        $bulk_posts = get_posts( array(
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => MSAT_AccessibilityTools::MSAT_POST_META_OPTIMIZED,
                    'compare' => 'NOT EXISTS'
                )
            )
        ) );
        if (is_array($bulk_posts) && count($bulk_posts) > 0) {
            foreach ($bulk_posts as $post) {
                $post_id = $post->ID;
                $return_id = MSAT_TextToSpeech::msat_tts_generate_audio_file( $post_id );
                if ($return_id == 0 || $return_id === false) {
                    $error = true;
                }
            }
        } else {
            $finished = true;
        }
        
        header('Content-Type: application/json');
        
        echo json_encode( array(
            "id" => $image_id,
            "error" => $error,
            "finished" => $finished
        ) );

        wp_die();
    }
}
