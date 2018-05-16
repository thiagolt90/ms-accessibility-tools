<div class="wrap">
    <h1>Microsoft SCC Accessibility Tools - Preferences</h1>
    <form method="post" novalidate="novalidate">
        <input name="action" value="save" type="hidden" method="POST">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="<?= MSAT_AccessibilityTools::MSAT_BING_SPEECH_API; ?>">Bing Speech API</label>
                    </th>
                    <td>
                        <input name="<?= MSAT_AccessibilityTools::MSAT_BING_SPEECH_API; ?>" id="<?= MSAT_AccessibilityTools::MSAT_BING_SPEECH_API; ?>" value="<?= $msat_bing_speech_api; ?>" class="regular-text" type="text">
                        <p class="description" id="tagline-description">Click <a href="https://azure.microsoft.com/en-us/services/cognitive-services/speech/" target="_blank">here</a> to grab your Keys.</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="<?= MSAT_AccessibilityTools::MSAT_COMPUTER_VISION_API; ?>">Computer Vision API</label>
                    </th>
                    <td>
                        <input name="<?= MSAT_AccessibilityTools::MSAT_COMPUTER_VISION_API; ?>" id="<?= MSAT_AccessibilityTools::MSAT_COMPUTER_VISION_API; ?>" value="<?= $msat_computer_vision_api; ?>" class="regular-text" type="text">
                        <p class="description" id="tagline-description">Click <a href="https://azure.microsoft.com/en-us/services/cognitive-services/computer-vision/" target="_blank">here</a> to grab your Keys.</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="<?= MSAT_AccessibilityTools::MSAT_TRANSLATOR_TEXT_API; ?>">Translator Text API </label>
                    </th>
                    <td>
                        <input name="<?= MSAT_AccessibilityTools::MSAT_TRANSLATOR_TEXT_API; ?>" id="<?= MSAT_AccessibilityTools::MSAT_TRANSLATOR_TEXT_API; ?>" value="<?= $msat_translator_text_api; ?>" class="regular-text" type="text">
                        <p class="description" id="tagline-description">Click <a href="https://azure.microsoft.com/en-us/services/cognitive-services/translator-text-api/" target="_blank">here</a> to grab your Keys. No need if you are using English language.</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="<?= MSAT_AccessibilityTools::MSAT_LANGUAGE; ?>">Language </label>
                    </th>
                    <td>
                        <select name="<?= MSAT_AccessibilityTools::MSAT_LANGUAGE; ?>" id="<?= MSAT_AccessibilityTools::MSAT_LANGUAGE; ?>" class="regular-text" required>
                            <?= $msat_languages; ?>
                        </select>
                        <p class="description" id="tagline-description">No post text translate, changes only the speaker. Need to re-generate all content manually.</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="submit">
            <input name="submit" id="submit" class="button button-primary" value="Salvar alterações" type="submit">
        </p>
    </form>

</div>