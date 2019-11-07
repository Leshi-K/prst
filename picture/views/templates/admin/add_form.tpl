<div class="row">
    <div class="col-lg-12">

        {if isset($success) AND $success}
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                Сохранено
            </div>
        {/if}

        <div class="panel">

            <div class="panel-heading">
                Картинка
            </div>

            <form id="pwblockhtml_form" class="form-horizontal" method="post" enctype="multipart/form-data">

                <input type="hidden" name="savePicture" value="1">
                <input type="hidden" name="id_picture" value="{if !empty($block_html->id)}{$block_html->id}{/if}">

                <div class="form-wrapper">

                    <div class="form-group">
                        <label for="image" class="control-label col-lg-3 required">
                            Картинка
                        </label>
                        <img src="{$block_html->image}" class="img-thumbnail" style="height: 150px;">
                        <div class="col-lg-9">
                            <input type="file" name="image">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-3 required" for="hooks">
                            Хук
                        </label>
                        <div class="col-lg-9">
                            <select name="hook" class="hooks" id="hooks">
                                {if $hooks}
                                    {foreach from=$hooks item=hook}
                                        <option value="{$hook|strtolower}" {if $current_hook == strtolower($hook)} selected {/if}>{$hook}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="control-label col-lg-3" for="position">
                            Порядок
                        </label>
                        <div class="col-lg-9">
                            <input type="text" name="position" id="position" value="{if isset($block_html->position) AND $block_html->position}{$block_html->position}{else}0{/if}">
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="control-label col-lg-3">
                            Статус
                        </label>
                        <div class="col-lg-9">
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input type="radio" name="active" id="active_on" value="1" {if !isset($block_html->active) OR $block_html->active}checked{/if}>
                                <label for="active_on" class="radioCheck">Вкл</label>
                                <input type="radio" name="active" id="active_off" value="0" {if isset($block_html->active) AND !$block_html->active}checked{/if}>
                                <label for="active_off" class="radioCheck">Выкл</label>
                                <a class="slide-button btn"></a>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="panel-footer">
                    <button type="submit" value="1" name="submitAddpicture" class="btn btn-default pull-right">
                        <i class="process-icon-save"></i> Сохранить
                    </button>
                    <a href="index.php?controller=AdminPicture{if isset($token) && $token}&amp;token={$token|escape:'html':'UTF-8'}{/if}"
                       class="btn btn-default pull-right" onclick="window.history.back();">
                        <i class="process-icon-cancel"></i> Отмена
                    </a>
                </div>
                <input type="hidden" name="smarty" value="0">
            </form>

        </div>
    </div>
</div>
