# Skebby Symfony bundle
This is an unofficial Symfony4 bundle for the [Skebby](https://www.skebby.it) sms service provider.

Installation
------------
The suggested installation method is via [composer](https://getcomposer.org/):

```sh
$ composer require szopen/skebby-bundle
```

Configuration
-------------
Enable the bundle in ```config/bundles.php```

```php
return [
    // ...
    // ...
    Szopen\SkebbyBundle\SkebbyBundle::class => ['all' => true],
];
```
 
In your ```config/packages/skebby_bundle.yaml```:
```yaml
skebby:

  # Add your skebby account credentials
  username: 'yourskebbyusername'
  password: 'yourskebbypassword'
  
  # Skebby provides two kinds of authentication:
  #   - Getting a Session Id that expires in 5 minutes if no request is sent
  #   - Getting a Token always valid
  # Allowed values are: 
  #   - token (default)
  #   - session
  #
  # auth_type: 'token'
  
  # You can choose which kind of sms send between:
  #   - "GP" for Classic+ (limited to 1530 chars, delivery warranty, delivery report)
  #   - "TI" for Classic (limited to 1530 chars, delivery warranty)
  #   - "SI" for Basic (limited to 160 chars, no delivery warranty)
  #
  # message_type: 'TI'
  
  # You can also add a default sender alias used to send SMS.
  # This overrides the default alias set in Skebby account but must be one of the alias 
  # already registered. 
  # If the message type allows a custom TPOA and the field is left empty, the user’s preferred TPOA
  # is used. Must be empty if the message type does not allow a custom TPOA.
  #
  # default_sender_alias: ~
```
Simple Symfony Usage
-------------
You have access to the ```SkebbyManager``` service in your controller. 

#####Check your account status
```php
/**
 * @Route("/skebby/user", name="skebby.user")
 */
public function userAction(SkebbyManager $skebby)
{

    $s = $skebby->getStatus();

    return $this->render('skebby/index.html.twig', [
        'status' => $s,
    ]);
}
```

```SkebbyManager::getStatus``` returns a [```Szopen\SkebbyBundle\Model\Response```](src/Model/Response/Status.php)
