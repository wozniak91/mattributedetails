{capture name=path}{l s='Attributes details' mod='mattributedetails'}{/capture}
{capture name=title}{l s='Attributes details' mod='mattributedetails'}{/capture}


<section class="attributes-details">
<h1 class="page-heading">{l s='Attributes details' mod='mattributedetails'}</h1>

<div class="form-group box">
    <label for="attributesSearch">{l s='Search' mod='mattributedetails'}</label>
    <input type="search" id="attributesSearch" class="form-control" placeholder="{l s='Search current fabric' mod='mattributedetails'}" />
</div>

<div id="attributesWrapper">

</div>

{* <div class="row">
{foreach from=$attributes_details item=attribute_detail}
    <div class=" col-lg-3 col-md-4 col-sm-6 col-xs-12">
        <article class="attribute-details">
            <figure class="attribute-details__image">
                <img class="img-responsive" src="/modules/mattributedetails/images/{$attribute_detail.cover_image}" />
            </figure>
            <h3 class="attribute-details__title">{$attribute_detail.title}</h3>
        </article>
    </div>
{/foreach}
</div> *}

</section>