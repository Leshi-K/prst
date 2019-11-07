{if $pictures}
	{foreach from=$pictures key=key item=picture}
		<img src="{$picture.image}" style="float: left; height: 150px; margin: 20px;">
	{/foreach}
{/if}

