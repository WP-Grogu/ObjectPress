# Configuration

## Configuration folder

ObjectPress reads plain PHP array configuration files to setup your project (initiate post types, taxonomies, roles...) and configure your development environment.

By default, ObjectPress will look for a `config/` directory at your theme root folder, which can be changed by passing a path to the boot method. 

You can copy the ObjectPress configuration files from [the official repository](https://github.com/WP-Grogu/ObjectPress/tree/main/config) and place them inside your local directory. Each option is self-documented, so feel free to look through the files and get familiar with the options available to you.

You can also dynamically add configuration paths using the `Config::addPath()` method.

```php
use OP\Support\Facades\Config;

Config::addPath([
    '/path/to/directory',
]);
```

## Configuration files

| File  | Usage
|:---:|---|
| [setup.php](https://github.com/WP-Grogu/ObjectPress/blob/main/config/setup.php) | Your app/theme configuration. Quicky enable Post types, taxonomies, Commands, Roles, API routes.. |
| [object-press.php](https://github.com/WP-Grogu/ObjectPress/blob/main/config/object-press.php) | ObjectPress configuration. Configure how the framework should work in your project. |

## Constants

If you wan to force some parameters, the framework also make use of some constants.

| Constant  | Description  | Default value  |
|:---:|---|:---:|
| `OP_DEFAULT_APP_LOCALE`  | Default app lang (slug format: 'en' or 'fr')  | (none)  |
| `OP_DEFAULT_I18N_DOMAIN_CPTS` | Default custom post types i18n translation domain  |  `"op-theme-cpts"` |
| `OP_DEFAULT_I18N_DOMAIN_TAXOS` | Default taxonomies i18n translation domain  |  `"op-theme-taxos"` |
