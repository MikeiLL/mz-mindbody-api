<?php
/**
 * Added to class modal with SVGs.
 *
 * Over-ride-able template page for full event listing shortcode result.
 *
 * @package MzMindbody
 */

?>
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" style="width:0;height:0;position:absolute;overflow:hidden;">
    <defs>
        <symbol viewBox="0 0 1024 1024" aria-labelledby="bisi-ant-check-circle-o-title" id="si-ant-check-circle-o"><title id="bisi-ant-check-circle-o-title">icon check-circle-o</title><path d="M821 331q-9-9-22.5-9t-22.5 9L383 713 247 584q-9-10-22.5-10T202 584q-10 9-10 22.5t10 22.5l158 152q10 9 23 9t23-9l415-405q10-9 10-22.5T821 331zM512 64q91 0 174 36 80 33 142 96 63 62 96 142 36 83 36 174t-36 174q-33 80-96 142-62 63-142 96-83 36-174 36t-174-36q-80-33-143-96-61-61-95-142-36-83-36-174t36-174q33-80 96-142 62-63 142-96 83-36 174-36zm0-64Q408 0 313 41T150 150 41 313 0 512t41 199 109 163 163 109 199 41 199-41 163-109 109-163 41-199-41-199-109-163T711 41 512 0z"/></symbol>
        <symbol viewBox="0 0 1024 1024" aria-labelledby="dnsi-ant-frown-title" id="si-ant-frown"><title id="dnsi-ant-frown-title">icon frown</title><path d="M512 64q91 0 174 35 81 34 143 96t96 143q35 83 35 174t-35 174q-34 81-96 143t-143 96q-83 35-174 35t-174-35q-81-34-143-96T99 686q-35-83-35-174t35-174q34-81 96-143t143-96q83-35 174-35zm0-64Q373 0 255 68.5T68.5 255 0 512t68.5 257T255 955.5t257 68.5 257-68.5T955.5 769t68.5-257-68.5-257T769 68.5 512 0zM224 446q-13 0-22.5-9.5T192 414v-64q0-13 9.5-22.5T224 318t22.5 9.5T256 350v64q0 13-9.5 22.5T224 446zm575 0q-13 0-22.5-9.5T767 414v-64q0-13 9.5-22.5T799 318t22.5 9.5T831 350v64q0 13-9.5 22.5T799 446zM192 577zm640 0zm-320-65q-106 0-182 74-61 60-73 144-2 14 7.5 25t23.5 11q12 0 21-8t11-19q9-62 55-107 57-56 137-56t137 56q46 45 55 107 2 12 11 19.5t21 7.5q14 0 24-11t7-25q-12-83-73-144-76-74-182-74z"/></symbol>
        <symbol viewBox="0 0 1024 1024" aria-labelledby="fzsi-ant-smile-title" id="si-ant-smile"><title id="fzsi-ant-smile-title">icon smile</title><path d="M512 64q91 0 174 35 81 34 143 96t96 143q35 83 35 174t-35 174q-34 81-96 143t-143 96q-83 35-174 35t-174-35q-81-34-143-96T99 686q-35-83-35-174t35-174q34-81 96-143t143-96q83-35 174-35zm0-64Q373 0 255 68.5T68.5 255 0 512t68.5 257T255 955.5t257 68.5 257-68.5T955.5 769t68.5-257-68.5-257T769 68.5 512 0zM224 446q-13 0-22.5-9.5T192 414v-64q0-13 9.5-22.5T224 318t22.5 9.5T256 350v64q0 13-9.5 22.5T224 446zm575 0q-13 0-22.5-9.5T767 414v-64q0-13 9.5-22.5T799 318t22.5 9.5T831 350v64q0 13-9.5 22.5T799 446zM192 577zm640 0zM512 766q-106 0-182-74-61-60-73-144-2-14 7.5-25t23.5-11q12 0 21 8t11 19q9 62 55 107 57 56 137 56t137-56q46-45 55-107 2-12 11-19.5t21-7.5q14 0 24 11t7 25q-12 83-73 144-76 74-182 74z"/></symbol>
    </defs>
</svg>
<div class="modal__wrapper">
    <div class="modal__content" id="AddedToClass">

        <?php if ( 'success' === $data->type ) : ?>
            <svg><use xlink:href="#si-ant-check-circle-o"></use></svg>
        <?php elseif ( 'booked' === $data->type ) : ?>
            <svg><use xlink:href="#si-ant-smile"></use></svg>
        <?php else : ?>
            <svg><use xlink:href="#si-ant-frown"></use></svg>
        <?php endif; ?>

        <h3>
            <?php
            echo esc_html(
                $data->message
            );
            ?>
        </h3>

    </div>

</div>
