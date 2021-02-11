# Configuration

## Config folder

All of the configuration files for the ObjectPress framework are stored in the `config/` directory, at theme's folder root. You can duplicate the ObjectPress config files from [the official repository](https://gitlab.com/tgeorgel/object-press/-/tree/dev/config). Each option is documented, so feel free to look through the files and get familiar with the options available to you.


If you wish to use custom config folder location, you can append your own path(s). Adding paths this way will always priorize the latest added path.

```php
use OP\Support\Facades\Config;

Config::addPath([
    '/path/to/config/folder',
]);
```

## Configuration files

| File  | Usage
|:---:|---|
| `config/app.php` | Your app/theme configuration. This is where you enable your CPTs, taxonomies, API routes.. |

## Constants

| Constant  | Description  | Default value  |
|:---:|---|:---:|
| `OP_DEFAULT_APP_LOCALE`  | Default app lang (slug format: 'en' or 'fr')  | (none)  |
| `OP_DEFAULT_I18N_DOMAIN_CPTS` | Default custom post types i18n translation domain  |  `"op-theme-cpts"` |
| `OP_DEFAULT_I18N_DOMAIN_TAXOS` | Default taxonomies i18n translation domain  |  `"op-theme-taxos"` |
