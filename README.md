# Key features and restrictions

* It can serialize Doctrine 2 entities with single column key called "id" to JSON;
* It is really fast. Uses progressive caching and uses approach that allows to
 benefit from OpCache;
* You can configure which fields will be serialized at runtime by providing sample
 object (other serializers usually use groups).
* Easily configurable. You can control samples in one file instead of configuring multiple entities in
multiple places
* To tell serializer how to serialize a field or relation you can use transformers.
# Purpose
This serializer is created to perform continuous work and is optimized for working
in a daemon process. Though it is still faster in usual run-and-die processes.
So check out [benchmark](https://github.com/EgorBurykin/serializer_benchmark). For my device it prints:
```
Scenario 1:
 * Run-and-die process
 * One entity to serialize
 * Entity is not loaded to doctrine cache
Jett serializer is ~ 2.6x faster

Scenario 2:
 * Web-socket daemon (continuous execution)
 * One entity to serialize
 * Entity is loaded to doctrine cache once
Jett serializer is ~ 12.8x faster

Scenario 3:
 * Run-and-die process
 * Collection of entities to serialize
 * Collection is not loaded to doctrine cache
Jett serializer is ~ 4.6x faster

Scenario 4:
 * Web-socket daemon (continuous execution)
 * Collection of entities to serialize
 * Collection is loaded to doctrine cache once
Jett serializer is ~ 13.5x faster
```
Feel free to run it on your device.
# Installation

Install via composer:
```json
{
    "require": {
        "jett/entity_serializer": "^1.0"
    }
}
```
## Symfony 3
 Add the bundle to your AppKernel.php file (for Symfony 3):
```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            //...
            new \Jett\JSONEntitySerializerBundle\JettJSONEntitySerializerBundle(),
            //...
        ];
        //...
    }
}
```
Then add to config.yml:
```yaml
imports:
    - { resource: serializer.yml }
```
And create serializer.yml:
```yaml
jett_json_entity_serializer:
    entities:
        # FQCN of entities
        AppBundle\Entity\User:
        AppBundle\Entity\Role:
        # ...
```
## Symfony 4

Add to config\bundles.php
```php
return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    //...
    Jett\JSONEntitySerializerBundle\JettJSONEntitySerializerBundle::class => ['all' => true],
];
```
Then add to config\packages\serializer.yaml:
```yaml
jett_json_entity_serializer:
    entities:
        # FQCN of entities
        AppBundle\Entity\User:
        AppBundle\Entity\Role:
        # ...

```
The recipe is coming soon.
# Configuration
You can adjust behaviour of bundle by using annotations below:

* **`@Ignore`** - This field will be ignored
* **`@SerializedName`** - Specifies field name in JSON
* **`@VirtualProperty`** - Result of method call  will be included in JSON
* **`@TypeInfo`** - Allows to specify additional information for virtual property
* **`@GetterName`** - Specifies getter name for property:

You can also force serializer to use other annotations. For example:
```yaml
jett_json_entity_serializer:
    ignore_annotation: JMS\Serializer\Annotation\Exclude
    name_annotation: JMS\Serializer\Annotation\SerializedName
    virtual_annotation: JMS\Serializer\Annotation\VitrualProperty
    getter_annotation: ~
```
Serializer will be applicable only for entities listed in configuration.
It supports serialization samples - objects defining how the result should look like.
In configuration you can list samples and assign them names to use them later.
Also at runtime you can create object and provide it as a sample.

You can specify sample with name "default" which is used when you have not provided
sample or sample name during serialization.
If you have not configured default sample special sample called 'all' will be used.
It exposes every property of entity except ignored and exposes every relation as ID or
collection of IDs.
While configuring samples you should mind few things:
* Entities which are not listed in configuration will be absent in result. In other words if you think 
why some related entity is absent in result you should probably just add them
to configuration.
* You can configure transformer for specific property or relation. Some transformers, like 
 'datetime' are applied every time even if you have not specified them explicitly.
* You can omit transformer as shown below.
* Sample for every element of collection should be provided the same way
 as for single valued relation.
```yaml
jett_json_entity_serializer:
    # These entities will be exposed
    AppBundle\Entity\Role:
    AppBundle\Entity\User:
        samples:
        # Transformer datetime can be omitted
        # Roles property is a collection, but we provide sample for it as for single
        # valued relation.
            default: >
                {
                    "id": "",
                    "username":"",
                    "firstname": "",
                    "lastname": "",
                    "fathername": "",
                    "phone":"",
                    "birthdate": "datetime",
                    "email":"",
                    "roles": {
                        "id": "",
                        "title": ""
                    }
                }
            # Another sample
            autocomplete: >
                {
                    "id": "",
                    "firstname": "",
                    "lastname": "",
                    "fathername": ""
                }
            # Username will be lowered. To see all available transformers see section below
            # roles: ['ROLE_ONE','ROLE_TWO']
            short: >
                {
                    "id":"",
                    "username:"lower",
                    "roles": "title"
                }
```
# Transformers
Serializer uses transformers as a way to define how a property or relation should be
serialized. There are few transformers included:
* datetime - used by default for datetime/date fields
* lower - transforms text
* upper - transforms text
* id - used by default if special sample called "all" is used. It replaces entity with its ID
* title - replaces entity with its title

Also you can define your own transformer easily. Just implement `TransformerInterface` and 
define tagged
service with tag 'entity_serializer.transformer' or use `CallbackTransformer` and register it manually:
```php
$call = function($data) {
    return strtoupper($data);
};
$serializer->addTransformer(new CallbackTransformer($call,'upper'));
```

Right now there is no check if transformer can be applied to the property.
It's probably coming soon.
## Inheritance
There is few ways how you can provide sample for entities which inherit each other:
```yaml
App\Entity\Programmer:
App\Entity\Manager:
App\Entity\Department:
    samples:
        # you can provide different samples for each entity type
        default: >
            {
                "id": "",
                "title": "",
                "employees": {
                    "App\\Entity\\Programmer": {
                        "id": "",
                        "name": "",
                        "level": "",
                        "team" : "title"
                    },
                    "App\\Entity\\Manager": {
                        "id": "",
                        "name": "",
                        "projects": "title"
                    }
                }
            }
        # or you can use sample "all"
        simple: >
            {
                "id": "",
                "title": "",
                "employees": "all"
            }
        # or you can provide merged sample which contains all fields
        merged: >
            {
                "id": "",
                "title": "",
                "employees": {
                    "id": "",
                    "name": "",
                    "level": "",
                    "team": "title",
                    "projects": "title"
                }
            }
```
