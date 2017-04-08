# Metached

This is an OXID module to automically manage the order of class extensions in OXID. It will create a new entry in the OXID admin navigation,
where you can configure the class extension order of your modules. 

## Installation

The module must be installed to `/path/to/modules/kyoya-de/metached`.
Please ensure that the file `/path/to/modules/kyoya-de/vendormetadata.php` exists,
otherwise OXID won't recognize the module.

### Using composer

1. Install `composer` (follow the [installation instructions](https://getcomposer.org/download/)).
2. Create a `composer.json` with this content (or add it, if you have the `composer.json` already):
    ```json
    {
      "require": {
          "kyoya-de/metached":"^1.0"
      },
      "extra":{
        "installer-paths": {
          "modules/{$vendor}/{$name}": ["type:oxid-module"]
        }
      }
    }
    ```
3. Run `composer install` (or `composer update` if you have already a `composer.json`).

### Manually

1. Download the release package from [the releases Page](https://github.com/kyoya-de/metached/releases).
2. Create the module directory `/path/to/modules/kyoya-de/metached`.
3. Extract all files from the archive to the module directory.

### Finally

Activate the module in the OXID admin.<br>
Now you can start to configure the order of OXID class extensions. 

## Configuration

There is no configuration required. But the module provides a global setting to define where to sort unconfigured module extensions.
This setting is also available for each overwritten class on the configuration page of the module.

### The configuration page

This page can be found in the OXID admin under *Extensions / Metached configuration*. Here you can configure the overwrite order of
all your extensions, plus where to put unknown overwritten classes. For better usability the classes are grouped. You can
select between two group types:

1. *First letter*: This will group the base classes by its first letter.
   For example: if the class name is `ManufacturerList` it will be found under `M`.
2. *Object type*: This will group the classes by there type. This type is defined as follows (will be detected in this order):
   * `oxAdminDetails`: Admin Details Controller
   * `oxAdminList`: Admin List Controller
   * `oxAdminView`: Admin Generic Controller
   * `ajaxListComponent`: Admin Ajax Component
   * `oxWidget`: Widget
   * `oxUBase`: Controller
   * `oxBase`: Model
   * `oxView`: Component
   * Any other class: Other

The groups itself are sorted alphabetically by the translated title.

In each group you can sort the overwrites for each class. It is also possible to set where to put unknown extensions per class.
OXIDs `extend` definitions are hidden, only module titles will be displayed.
If you change the order it will be saved without the need to press a button. Same for the position of unknown extensions.
A small box will be displayed to show you the result of the save action. It will automatically disappear after 3 seconds.

As long as you do not change anything, nothing will be saved. Only changes made to a class will be saved. The initial configuration
is created at the first module activation.

***Important:*** After you've changed the order, you must clear OXIDs module cache and re-activate one of the affected modules.

## Internals

When a module is going to be activated, OXID merges the extensions of the *new* module with the existing ones.
Metached sorts the resulting array after OXIDs merge. As the sorting algorithm [merge sort](https://en.wikipedia.org/wiki/Merge_sort) is used.
We can't use PHPs built-in [usort](https://secure.php.net/manual/en/function.usort.php) function, because it seems to be a
[quicksort](https://en.wikipedia.org/wiki/Quicksort) implementation. Quicksort is not a stable sorting algorithm, so it is **not** usable for us.
Quicksort does not guarantee that the order of equal elements is not changed. If we would use it, OXIDs extension may change on every module activation.

## License

This piece of software is released under the MIT license. Take a look at the [LICENSE](./LICENSE) file.

Feel free to copy and change the code, but never remove the original author! Pull requests are also welcome.
