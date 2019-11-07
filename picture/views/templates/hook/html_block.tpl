{if $html_blocks}
    {foreach from=$html_blocks item=html_block}
        <!-- picture module id {$html_block.id_picture} -->
        <img src="{$html_block.image nofilter}"></a>
        <!-- /picture module -->
    {/foreach}
{/if}