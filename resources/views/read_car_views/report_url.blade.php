<?php
/**
 * @var TYPE_NAME $content
 */
?>
@if(isset($content))
    <div style="margin-top: 10px;">
        <a
            href="<?= $content ?>"
            style="padding:10px 45px;background-color:royalblue;color:white;border-radius:5px;"
        >View report</a>
    </div>
@else
    <span>Not available</span>
@endif
