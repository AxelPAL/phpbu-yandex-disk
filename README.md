# PHPBU with Yandex.Disk support

# Installation

To install this extension to phpbu, run this:
 
    composer create-project axelpal/phpbu-yandex-disk
    
Navigate to phpbu-yandex-disk folder:
 
    cd phpbu-yandex-disk    

# Usage

For running phpbu with Yandex.Disk support, you should run command:
 
    vendor/bin/phpbu --bootstrap=extend.php
    
For using Yandex.Disk as Sync, add these lines to your *phpbu.xml*:

```xml
    ...
  <sync type="yandex.disk">
      <option name="token" value="YANDEX_API_TOKEN"/>
      <option name="path" value="YANDEX_DISK_PATH"/>
  </sync>
    ...
```

Example of configuration you can find in phpbu.xml_example in this repo.

Getting API Token key:

Goto https://oauth.yandex.ru/client/new
create your app
- Check all Disks permissions
- generate access token:
1) Goto https://oauth.yandex.ru/authorize?response_type=token&client_id=APP_ID (replace APP_ID with ID giving to you)
2) Then you should get token parameter from GET-parameters of opened page

Other documentation you can find in:
* [phpbu repo](https://github.com/sebastianfeldmann/phpbu)
* [Yandex.Disk API repo](https://github.com/jack-theripper/Mackey)