<!DOCTYPE html>
<html>
<head>
    <title>Metached Configuration</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/normalize/6.0.0/normalize.min.css" rel="stylesheet"
          type="text/css"
          integrity="sha384-Jselos8nGM89PT4COWBP/c2/Lj9sjMJ6IpQZD64CWQGd/c+Ks8MdS2kIWPHiRwiq"
          crossorigin="anonymous">
    <link href="[{$oViewConf->getModuleUrl('kyoya-de/metached', 'public/css/style.css')}]?[{php}]echo time();[{/php}]"
          rel="stylesheet">

    <script src="[{$oViewConf->getModuleUrl('kyoya-de/metached', 'public/js/Sortable.min.js')}]?[{php}]echo time();[{/php}]"></script>

    [{*
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>
*}]
</head>
<body>
<div class="container">
    <h2>Metached Konfiguration</h2>
    <div class="sort-container" id="sortContainer">
        [{foreach from=$oView->getSortDefinition() key="extendedClass" item="sortDefinition"}]
            <div class="sort-extended-class" data-class="[{$extendedClass}]">
                <div class="title">[{$extendedClass}]</div>
                <div class="sort-overview">
                    <ul class="sort-sortable">
                        [{foreach from=$sortDefinition key="moduleClass" item="order"}]
                            <li class="sort-overview-order" data-module-class="[{$moduleClass}]">[{$moduleClass}]</li>
                        [{/foreach}]
                    </ul>
                </div>
            </div>
        [{/foreach}]
    </div>
</div>
<script type="text/javascript">
    (function () {
        /**
         * @see http://rubaxa.github.io/Sortable/
         * @see https://github.com/RubaXa/Sortable
         * @type {NodeList}
         */
        const sortables = document.querySelectorAll('.sort-sortable');
        for (var i = 0; i < sortables.length; i++) {
            console.log(sortables[i]);
            Sortable.create(sortables[i], {
                animation: 150,
                dataIdAttr:'module-class',
                onUpdate: function (evt) {
                    console.log(evt);
                },
            });
        }
    })();
</script>
</body>
</html>
