# Key features and restrictions

* Can serialize Doctrine 2 entities with single column key called id to JSON
* It is really fast. Uses progressive caching and uses approach allowing to benefit from OpCache
* Can serialize entity according to sample (serialization group). You can create any sample (group) at runtime
* Easy configurable. You can control samples in one file instead of configuring multiple entities in
multiple places

# Installation

Install via composer:
```json
{
    "require": {
        "jett/entity_serializer": "^1.0"
    }
}
```
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

Serializer supports serialization groups. They are called samples. They can be created at runtime. You can also specify samples in config and use them by name:

```yaml
jett_json_entity_serializer:
    AppBundle\Entity\User:
        samples:
            # Default sample
            default: >
                {
                    "id": "-",
                    "username":"-",
                    "firstname": "-",
                    "lastname": "-",
                    "fathername": "-",
                    "phone":"-",
                    "email":"-",
                    "roles": {
                        "id": "-",
                        "title": "-"
                    }
                }
            # Another sample
            autocomplete: >
                {
                    "id": "-",
                    "firstname": "-",
                    "lastname": "-",
                    "fathername": "-"
                }
```
If you have not specified any sample in your config, special sample called "all" will be used.
It includes every field of entity except ignored ones and replaces any relation field with objects which contains only id field.
