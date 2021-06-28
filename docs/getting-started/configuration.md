# Configuration

## Configuration folder

ObjectPress reads `.php` configuration files, to setup the app (initiate post types, taxonomies...) and configure your development env. By default, ObjectPress will look for a `config/` directory, at your theme root folder. You can duplicate the ObjectPress config files from [the official repository](https://gitlab.com/tgeorgel/object-press/-/tree/dev/config). Each option is documented, so feel free to look through the files and get familiar with the options available to you.


If you wish to use custom config folder location, you can append your own path(s). Adding paths this way will always priorize the latest added path.


```php
use OP\Support\Facades\Config;

Config::addPath([
    '/path/to/config/folder',
]);
```

## Files

| File  | Usage
|:---:|---|
| `config/setup.php` | Your app/theme configuration. This is where you enable your CPTs, taxonomies, API routes.. |
| `config/object-press.php` | ObjectPress configuration. This is the place you configure how the frameworks handles things for you |

## Constants

You can also define constants for a couple of things : 

| Constant  | Description  | Default value  |
|:---:|---|:---:|
| `OP_DEFAULT_APP_LOCALE`  | Default app lang (slug format: 'en' or 'fr')  | (none)  |
| `OP_DEFAULT_I18N_DOMAIN_CPTS` | Default custom post types i18n translation domain  |  `"op-theme-cpts"` |
| `OP_DEFAULT_I18N_DOMAIN_TAXOS` | Default taxonomies i18n translation domain  |  `"op-theme-taxos"` |
