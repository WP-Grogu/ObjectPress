# Models/CPTs Interface (ICpts.php)

## Basic usage

The ICpts interface (`app/Interfaces/ICpts.php`) contains an association of your postype-models.

```php
<?php

namespace App\Interfaces;

interface ICpts
{
    const MODELS = [
        'page' => 'App\Models\Page',
        'post' => 'App\Models\Post',
        'example-cpt' => 'App\Models\Example',
    ];
}
```

🤔 You're not forced to bind your custom-post-types to your models, as ObjectPress will guess your Model class name if you respect the naming convention & namespaces. [see models configuration guide](models.md).

However, it is very usefull for app consistence to keep this file updated as you keep growing your theme functionalities. 


## Models grouping

!> Work in progress. Below documentation is refering to a piece that is not yet available on current stable release.

In the Interface you can group your models into labelised groups.

```php
interface ICpts
{
    const MODELS = [
        ...
        'project'           => 'App\Models\Project',
        'case-study'        => 'App\Models\CaseStudy',
        'events'            => 'App\Models\Events',
        'corporate-news'    => 'App\Models\CorporateNews',
    ];

    const MODELS__CLIENTS = [
        'App\Models\Project',
        'App\Models\CaseStudy',
    ];
    
    const MODELS__BLOG = [
        'App\Models\Events',
        'App\Models\CorporateNews',
    ];
}
```

This way, you can identify a model group :

```php

$event->modelGroups();          // array('blog')
$event->modelInGroup('blog');   // true


use OP\Frameworks\Helpers\PostHelper;

PostHelper::modelBelongsToGroup($event, 'blog'); // true

```