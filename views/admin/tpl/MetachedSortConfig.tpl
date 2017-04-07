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

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>

    <script src="[{$oViewConf->getModuleUrl('kyoya-de/metached', 'public/js/Sortable.min.js')}]?[{php}]echo time();[{/php}]"></script>
</head>
<body>
<div class="container">
    <h2>Metached Konfiguration</h2>
    <div class="sort-container" id="sortContainer">
        [{foreach from=$oView->getSortDefinition() key="extendedClass" item="sortDefinition"}]
            <div class="sort-extended-class">
                <div class="title">[{$extendedClass}]</div>
                <div class="sort-overview">
                    <ul class="sort-sortable" data-extended-class="[{$extendedClass}]">
                        [{foreach from=$sortDefinition key="order" item="moduleClass"}]
                            <li class="sort-overview-order" data-module-class="[{$moduleClass}]">[{$oView->getModuleTitle($extendedClass, $moduleClass)}]</li>
                        [{/foreach}]
                    </ul>
                </div>
            </div>
        [{/foreach}]
    </div>
</div>
<script type="text/javascript">
    var xhr = new XMLHttpRequest(),
        url = [{$oViewConf->getSslSelfLink()|@html_entity_decode|@json_encode}] + 'cl=MetachedSortConfig&fnc=saveOrder';
    xhr.onreadystatechange = function () {
        if(xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            console.log(xhr.responseText);
        }
    };
    (function () {
        /**
         * @see http://rubaxa.github.io/Sortable/
         * @see https://github.com/RubaXa/Sortable
         * @type {NodeList}
         */
        const sortables = document.querySelectorAll('.sort-sortable');
        for (var i = 0; i < sortables.length; i++) {
            Sortable.create(sortables[i], {
                animation: 150,
                dataIdAttr:'data-module-class',
                group: sortables[i].dataset.extendedClass,
                store: {
                    /**
                     * Get the order of elements. Called once during initialization.
                     * @param   {Sortable}  sortable
                     * @returns {Array}
                     */
                    get: function (sortable) {
                        return [];
                    },

                    /**
                     * Save the order of elements. Called onEnd (when the item is dropped).
                     * @param {Sortable}  sortable
                     */
                    set: function (sortable) {
                        var order = sortable.toArray();
                        var data = new FormData();

                        data.append('oxidClass', sortable.options.group.name);

                        for (var i = 0; i < order.length; i++) {
                            data.append('order[' + i + ']', order[i]);
                        }

                        xhr.open('POST', url, true);
                        xhr.send(data);

                        localStorage.setItem(sortable.options.group.name, order.join('|'));
                    }
                }
            });
        }
    })();
</script>
</body>
</html>
