# Redmine API BUNDLE

A simple bundle allowing the use of api redmine in symfony

## Step 1: Install
### A) Add Redmine API BUNDLE to your project

php composer.phar require php composer.phar require

### B) Enable the bundle

Enable the bundle in the kernel:

```php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Fluedis\RedmineBundle\FluedisRedmineBundle(),
    );
}
```

## Step 2: Configuring bundle
```yaml
fluedis_redmine:
    uri: http://redmine.com
    api_key: <GET IT on your redmine>
    tracker_id: 1 #Anomalie
    project_id: '<project name>'
    priority_id: 3 #HIGH
    status_id: 1 #new
    assigned_to: <user_id>
    watchers:
        - "<another user_id>"
        - "<another user_id>"
        - ...
```

## How it works

An event listner catches all uncaught exceptions and create an issue using config informations.
Issue title : exception message
Issue text: Exception stacktrace