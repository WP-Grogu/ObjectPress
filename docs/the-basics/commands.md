# Commands

Wordpress comes with [WP-CLI](https://wp-cli.org/fr/) which brings various commands to help you in your development process.

ObjectPress help you extending WP-CLI by adding your own commands.

> The framework also comes with a set of available commands, listed [here](digging-deeper/cli-commands.md).


## Defining properties

You'll most likely define your commands inside the `app/Wordpress/Commands/` directory.  

Defining a command is trivial and is done by extending the `OP\Framework\Wordpress\Command` class :  

```php
<?php

namespace App\Wordpress\Commands;

use App\Classes\AlgoliaApi;
use OP\Framework\Wordpress\Command;

class WelcomeNewUsers extends Command
{
    /**
     * The command name called with "wp {name}"
     *
     * @var string
     * @access protected
     */
    protected string $name = 'mails:welcome-new-users';

    /**
     * Index coaches into algolia for website search.
     *
     * @param array $args The command arguments as returned by WpCLI.
     * @return void
     */
    public function execute(array $args = [])
    {
        // do something great.
    }
}
```

This class expects a `$name` property, used to execute the command, and an `execute` method which will be called when the command is executed.

The execute methad takes the command arguments as an array, and returns nothing.

### Additional arguments on registration

WP-CLI allow you to [pass arguments](https://make.wordpress.org/cli/handbook/references/internal-api/wp-cli-add-command/) to your command when registering it.

This can be done by overiding the `getArgs()` method on your class : 

```php
    /**
     * Returns the arguments to pass to the add_command function.
     * An associative array with additional registration parameters.
     * 
     * @return array
     */
    protected function getArgs(): array
    {
        return [
            'shortdesc' => 'Welcome new users with a mail.',
            'when' => 'after_wp_load',
        ];
    }
```

### Initiate your command

ObjectPress manage the Commands initialisation out of the box for you. You simply need to add your Command inside the `commands` key in the `config/setup.php` configuration file : 

```php
    /*
    |--------------------------------------------------------------------------
    | App CLI Commands
    |--------------------------------------------------------------------------
    |
    | Insert here your WP-CLI commands.
    |
    */
    'commands' => [
        App\Wordpress\Commands\WelcomeNewUsers::class,
    ],
```

If you prefer, you can also initiate your commands manually :  

```php
<?php

use OP\Support\Facades\Theme;

Theme::on(
  'init', 
  fn () => (new App\Wordpress\Commands\WelcomeNewUsers)->boot()
);
```


## Outputing

You can display input in the terminal using one of the following methods : 

```php
    public function execute(array $args = [])
    {
        $this->log('Starting command...');
        $this->line('Found 5 users to welcome.');
        $this->warning('Skipping user because the email is not confirmed.');
        $this->error('Sending email to user 1 failed.');
        $this->success('Sent welcome email to 2 users.');
        $this->debug('user email is ' . $user->email);
    }
```

- The `log` and `line` method won't display anything if the `--quiet` flag is passed to the command.
- The `debug` method will only display anything if the `--debug` flag is passed to the command.

#### Silent mode
Sometimes, you need the command to not output anything, for example when used in a cron job.

This can be done by setting the silent attribute to true : 

```php
class WelcomeNewUsers extends Command
{
    /**
     * @var bool
     */
    protected $silent = true;

    ...
```

You can also set this property dynamically by overriding the `__construct` method : 

```php
    public function __construct(bool $silent = false)
    {
        parent::__construct();

        $this->silent = $silent || $this->isCronJob();
    }
```

Note that scheduled commands are alway silent.


## Scheduling commands

ObjectPress comes with a built-in scheduler, which allows you to schedule your events to run at specific times.

To make a command schedulable, you use include the `Schedulable` trait in your class : 

```php
<?php

namespace App\Wordpress\Commands;

use OP\Framework\Wordpress\Command;
use OP\Framework\Wordpress\Concerns\Schedulable;

class AlgoliaIndex extends Command
{
    use Schedulable;
}
```

Then, use your command as the callback for the event. Learn more on the [Events & Schedule](the-basics/schedules.md) section.