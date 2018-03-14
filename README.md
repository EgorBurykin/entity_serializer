# Key features and restrictions

* Can serialize Doctrine 2 entities with single column key called id to JSON
* It is really fast. Uses progressive caching and uses approach allowing to get benefit from OpCache
* Can serialize entity according to sample (group). You can create any sample (group) at runtime
* Easy configurable

# Installation

Install via composer:
```json
{
    "require": {
        "jett/entity_serializer": "^1.0"
    }
}
```
 Connect bundle on Symfony 3 like that:
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
* **`@VirtualProperty`** - Method's call result will be included to JSON
* **`@TypeInfo`** - Allows to specify additional information for virtual property
* **`@GetterName`** - Specifies getter name for property:

You can also force serializer to use other annotations. For example:
```yaml
jett_json_entity_serializer:
    ignore_annotation: JMS\Serializer\Annotation\Exclude
    name_annotation: JMS\Serializer\Annotation\SerializedName
    virtual_annotation: JMS\Serializer\Annotation\VitrualProperty
```

Serializer supports serialization groups. We call them samples due to we allow to create
them at runtime. Also we can specify samples in config and use them by name:

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
            # И еще одну
            autocomplete: >
                {
                    "id": "-",
                    "firstname": "-",
                    "lastname": "-",
                    "fathername": "-"
                }
```
If you have not specified any sample in your config will be used special sample called "all".
This sample includes every field of entity except ignored ones and replaces any relation to their
dummy objects which contain only id field.

