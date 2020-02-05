# Skebby Symfony4 bundle
This is an unofficial Symfony5 bundle for the [Skebby](https://www.skebby.it) sms service provider.

Installation
------------
The suggested installation method is via [composer](https://getcomposer.org/):

```sh
$ composer require leandro980/skebby-bundle
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

  # Add your Skebby account credentials
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

#### Check your account status
```php
use Szopen\SkebbyBundle\Model\Manager\SkebbyManager;

// ..

/**
 * @Route("/skebby/status", name="skebby.status")
 */
public function statusAction(SkebbyManager $skebby)
{

    $s = $skebby->getStatus();

    return $this->render('skebby/index.html.twig', [
        'status' => $s,
    ]);
}
```

```SkebbyManager::getStatus``` returns a [```Szopen\SkebbyBundle\Model\Response\Status```](src/Model/Response/Status.php)

#### Send an SMS
```php
use Szopen\SkebbyBundle\Model\Manager\SkebbyManager;
use Szopen\SkebbyBundle\Model\Data\Recipient;

// ..

/**
 * @Route("/skebby/send", name="skebby.sendsms")
 */
public function sendSmsAction(SkebbyManager $skebby)
{

    $sms = $skebby->createDefaultSms('Hello, this is a test message',
    [new Recipient('3331234567'), new Recipient('3207654321')]);

    $response = $skebby->sendSms($sms);

    return $this->render('skebby/index.html.twig', [
        'response' => $response,
    ]);
}
```
```SkebbyManager::sendSms``` returns a [```Szopen\SkebbyBundle\Model\Response\SmsResponse```](src/Model/Response/SmsResponse.php)

## Documentation
The Skebby bundle uses a set of clients classes that perform their specific API calls. The ```SkebbyManager``` is a service that wraps all the clients methods.
Please refer to [Official Skebby Developer Documentation](https://developers.skebby.it/) for further info. 
* [Authenticators](#authenticators)
* [UserClient](#userclient)
* [SmsClient](#smsclient)
* [SkebbyManager](#skebbymanager)

#### Authenticators
You can choose which kind of authentication to use by ```AuthenticatorFactory``` class.
```php
use Szopen\SkebbyBundle\Model\Auth\AuthenticatorFactory;

//..

// Token authentication
$auth = AuthenticatorFactory::create('token')
// Session authentication
// $auth = AuthenticatorFactory::create('session')

// Used to authenticate in later API calls
$arrayAccess = $auth->login('username', 'password');
```
Session authentication lasts 5 minutes if no API call is performed.

#### UserClient
Class dedicated to the User/Account API calls.

```php
use Szopen\SkebbyBundle\Model\Auth\AuthenticatorFactory;
use Szopen\SkebbyBundle\Model\Client\UserClient;

//..

// Token authentication
$auth = AuthenticatorFactory::create('token')

$userClient = new UserClient('username', 'password', $auth);
```
You can read more on methods comments of [```Szopen\SkebbyBundle\Model\Client\UserClient```](src/Model/Client/UserClient.php)

#### SmsClient
Class dedicated to the Sms API calls.

Sending a Simple Sms.
```php
use Szopen\SkebbyBundle\Model\Auth\AuthenticatorFactory;
use Szopen\SkebbyBundle\Model\Client\SmsClient;
use Szopen\SkebbyBundle\Model\Data\Recipient;

//..

// Token authentication
$auth = AuthenticatorFactory::create('token')

$smsClient = new SmsClient('username', 'password', $auth);

// Creating an sms
// You must choose which kind of SMS type to send 
$sms = new Sms(Sms::SMS_CLASSIC_KEY);

// Add a message
// SmsClient choose wich kind of encoding to use, between UCS2 and GSM, 
// authomatically parsing the message.
// It also counts chars available based on encoding, if the ength of the message exceeds the limit
// it raises a MessageLengthException
$sms->setMessage("Hello, this is a GSM encoded message");
// Substitutes message
$sms->setMessage("Hello, this is a UCS2 encoded message because of ç char");

// Set sender
// You can add sender only if your not using BASIC SMS and the aliasis registered to your account
$sms->setSender('YourAlias');

// Creating and adding a Recipient
// When you create a "Recipient" it parses the phone number, if it's not valid it raises
// a \libphonenumber\NumberParseException 
$recipient = new Recipient("+393211234567");
$sms->addRecipient($recipient);

// Sending SMS
$smsResponse = $smsClient->sendSms($sms);
// You can choose to allow invalid recipients (that means that an invalid recipient 
// won't block the entire operation) and if you want have the remaining sms and credit
$smsResponse = $smsClient->sendSms($sms, 
    true, // Allow invalid recipients
    true, // Return remaining
    true, // Return credit
    );
```

You can send Sms to groups. In this case the recipients must be of type Group  
```php
use Szopen\SkebbyBundle\Model\Auth\AuthenticatorFactory;
use Szopen\SkebbyBundle\Model\Client\SmsClient;
use Szopen\SkebbyBundle\Model\Data\Group;

//..

// Creating and adding a recipient
$recipient = new Group("groupname");
$sms->addRecipient($recipient);

// Sending SMS
$smsResponse = $smsClient->sendGroupSms($sms);
// You can choose to allow invalid recipients (that means that an invalid recipient 
// won't block the entire operation) and if you want have the remaining sms and credit
$smsResponse = $smsClient->sendGroupSms($sms, 
    true, // Allow invalid recipients
    true, // Return remaining
    true, // Return credit
    );
```
Sending messages with parameters is also possible. You can't send parametric sms to groups 
```php
// Adds a message with parameters
// The system recognizes the parameters in the text 
$sms->setMessage('Hello ${name}, i know your surname is ${surname}');

// Creating and adding a Recipient with parameters
$recipient = new Recipient("+393211234567");
$recipient->addVariable('name', 'John');
$recipient->addVariable('surname', 'Dorian');
$sms->addRecipient($recipient);

// Sending SMS
// If just a Recipient does'nt contains all the parameters defined in message 
// it raises a MissingParameterException 
$smsResponse = $smsClient->sendSms($sms);
```
You can read more on methods comments of [```Szopen\SkebbyBundle\Model\Client\SmsClient```](src/Model/Client/SmsClient.php)

#### SkebbyManager
This class is configured as a Symfony4 service and wraps all the clients methods.
It adds the ```SkebbyManager::createDefaultSms``` that returns an Sms using all the default parameters configured in the yaml file.
```php

$sms = $skebby->createDefaultSms('Hello, this is a test message');
$sms->addRecipient(new Recipient("+393211234567"));
    
// You can also add recipients in constructor
$sms = $skebby->createDefaultSms('Hello, this is a test message',
    [new Recipient('3331234567'), new Recipient('3207654321')]);
```
You can read more on methods comments of [```Szopen\SkebbyBundle\Model\Manager\SkebbyManager```](src/Model/Manager/SkebbyManager.php)

## License
MIT License, please see [LICENSE](LICENSE) for more information.