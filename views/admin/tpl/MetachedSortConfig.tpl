[{**
  * This piece of software is released under the MIT license. Take a look at the LICENSE file.
  *
  * Feel free to copy and change the code, but never remove the original author! Pull requests are also welcome.
  *
  * @version 1.0.0
  * @author  Stefan Krenz <krenz.stefan@googlemail.com>
  *}]
<!DOCTYPE html>
<html>
<head>
    <title>[{oxmultilang ident="METACHED_TITLE_CONFIG"}]</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/normalize/6.0.0/normalize.min.css" rel="stylesheet"
          type="text/css"
          integrity="sha384-Jselos8nGM89PT4COWBP/c2/Lj9sjMJ6IpQZD64CWQGd/c+Ks8MdS2kIWPHiRwiq"
          crossorigin="anonymous">
    <link href="[{$oViewConf->getModuleUrl('kyoya-de/metached', 'public/css/style.dist.css')}]?[{php}]echo time();[{/php}]"
          rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>

    <script src="[{$oViewConf->getModuleUrl('kyoya-de/metached', 'public/js/Sortable.min.js')}]?[{php}]echo time();[{/php}]"></script>
    <script src="[{$oViewConf->getModuleUrl('kyoya-de/metached', 'public/js/emitter.js')}]?[{php}]echo time();[{/php}]"></script>
    <script src="[{$oViewConf->getModuleUrl('kyoya-de/metached', 'public/js/metached.js')}]?[{php}]echo time();[{/php}]"></script>
</head>
<body>
<div class="container">
    <h2 class="page-title">[{oxmultilang ident="METACHED_TITLE_CONFIG"}]</h2>
    <div class="group-config">
        <div class="group-config-type">
            <label>[{oxmultilang ident="METACHED_TITLE_GROUP_BY"}]
                <select class="group-config-type-group-by">
                    <option value="alpha"[{if $grouping == 'alpha'}] selected="selected"[{/if}]>[{oxmultilang ident="METACHED_CAPTION_GROUP_BY_ALPHA"}]</option>
                    <option value="object"[{if $grouping == 'object'}] selected="selected"[{/if}]>[{oxmultilang ident="METACHED_CAPTION_GROUP_BY_OBJECT"}]</option>
                </select>
            </label>
        </div>
        <div class="group-config-links">
        [{foreach from=$moduleGroups|@array_keys item="group"}]
            <div class="group-config-links-link">
                <a href="#group-[{$group|@rawurlencode}]">[{oxmultilang ident=$group noerror=true}]</a>
            </div>
        [{/foreach}]
        </div>
    </div>
    [{foreach from=$moduleGroups key="group" item="groupClasses"}]
        <div id="group-[{$group}]" class="group-title">[{oxmultilang ident=$group noerror=true}]</div>
        <div class="sort-container" id="sortContainer">
            [{foreach from=$groupClasses item="extendedClass"}]
                [{assign var="sortDefinition" value=$moduleList.$extendedClass}]
                [{assign var="unknownPos" value=$sortConfig.$extendedClass.unknownPosition|default:$defaultUnknownPosition}]
                <div class="sort-extended-class">
                    <h3 class="sort-title">[{$extendedClass}]</h3>
                    <div class="sort-overview">
                        <div class="config-container">
                            <label>[{oxmultilang ident="METACHED_TITLE_UNKNOWN_POS"}]
                                <select class="sort-type" data-extended-class="[{$extendedClass}]">
                                    <option value="-1"[{if -1 == $unknownPos}] selected="selected"[{/if}]>
                                        [{oxmultilang ident="METACHED_CAPTION_UNKNOWN_POS_BEGINNING"}]
                                    </option>
                                    <option value="1"[{if 1 == $unknownPos}] selected="selected"[{/if}]>
                                        [{oxmultilang ident="METACHED_CAPTION_UNKNOWN_POS_END"}]
                                    </option>
                                </select>
                            </label>
                        </div>
                        <ol class="sort-sortable" data-extended-class="[{$extendedClass}]">
                            [{foreach from=$sortDefinition key="order" item="moduleClass"}]
                                <li class="sort-overview-order"
                                    data-module-class="[{$moduleClass}]">[{$moduleTitles.$extendedClass.$moduleClass|default:$moduleClass}]</li>
                            [{/foreach}]
                        </ol>
                    </div>
                </div>
            [{/foreach}]
        </div>
    [{/foreach}]
</div>

<div id="responseStatus" class="response-status">
    <div class="title"></div>
</div>

<script type="text/javascript">
    var baseUrl = [{$oViewConf->getSslSelfLink()|@html_entity_decode|@json_encode}] + 'cl=MetachedSortConfig';
    (function () {
        var m = new Metached(baseUrl + '&fnc=save');
        m.on('request.finished', function (response) {
            var statusElement = document.querySelector('#responseStatus');
            var classList = statusElement.classList;

            statusElement.querySelector('.title').innerHTML = response.message;

            if (response.success) {
                classList.contains('error') && classList.remove('error');
                classList.add('success');
            } else {
                classList.contains('success') && classList.remove('success');
                classList.add('error');
            }

            statusElement.style.opacity = 1;
            window.setTimeout(function () {
                statusElement.style.opacity = 0;
            }, 3000);
        });

        /**
         * @see http://rubaxa.github.io/Sortable/
         * @see https://github.com/RubaXa/Sortable
         * @type {NodeList}
         */
        const sortables = document.querySelectorAll('.sort-sortable');

        document.querySelector('.sort-container').onchange = function (e) {
            if (!e.target.classList.contains('sort-type')) {
                return;
            }

            m.save(e.target.dataset.extendedClass, {
                'unknownPos': e.target.value
            });
        };

        document.querySelector('.group-config-type-group-by').onchange = function(e) {
            window.location.href = baseUrl + '&grouping=' + e.target.value;
        };

        m.createSortables(sortables);
    })();
</script>
</body>
</html>
