# Config

ObjectPress's configurations are mainly done over constants and config files. By default, config files are located in a `config` folder inside your theme directory.

> You can add config folder paths using `OP\Support\Facades\Config::addPath(['path/to/folder'])`. Adding paths this way will always priorize the last added path(s).

## Constants

| Constant  | Description  | Default value  |
|:---:|---|:---:|
| `OP_DEFAULT_APP_LOCALE`  | Default app lang (slug format: 'en' or 'fr')  | (none)  |
| `OP_DEFAULT_I18N_DOMAIN_CPTS` | Default custom post types i18n translation domain  |  `"op-theme-cpts"` |
| `OP_DEFAULT_I18N_DOMAIN_TAXOS` | Default taxonomies i18n translation domain  |  `"op-theme-taxos"` |


## Configuration files

| File  | Usage
|:---:|---|
| `config/app.php` | Your app/theme configuration. This is where you enable your CPTs, taxonomies, API routes.. |