# JsonRevisions plugin for CakePHP 3.x +

# Intro
I created this from a dream I had. I was curious to see which would preform better in a CMS, a table blasted full of revision records or a single column.
So far the only bottleneck i see is when returning a record with A LOT of revisions 500 +. 

Maybe a polymorphic archive table is in the future?
Until then enjoy and let me know if you found this useful.

## Installation
You can install this plugin into your CakePHP application using Plugin::load('JsonRevisions);

##Usage
This plugin requires MySql 5.7.x +
To make a row store its own revisions you will need to create a new Json column in that database table.
You can do so using cakes built in migration engine like so
(Where TableName is the name of your table.)
```
./bin/cake bake migration addRevisionsToTableName revisions:json
```
Then run migrations
```
./bin/cake migrations migrate
```
Now we need to add the behavior to the table and entity.
in your table
```
$this->addBehavior('JsonRevisions.Revisable');
```

in your entity
```
use JsonRevisions\Model\Entity\Traits\Revisable;
```
then under the trait decloration add
```
    use RevisableTrait;
```

## Excluding Revisions column from queries
simply use the ``` 
->selectAllExcept(['tableName.revisions'])
```
query object function
Read the docs to find out more about this method.

The trait contains some nice entity level functionality that will help you restore your data to a previous point in time.
You can also overload default behavior configurations to limit the fields you want to save in the revision as well as the total number of revisions you want to keep.

Currently the behavior is set to store an unlimited number of revisions and omit the following fields
id, revisions, created, modified

## some notes.
JSON columns by default are treated simillary to LongText and LongBlob data types in mysql and have a very large storage ccapacity by nature. However if your column data will force the database to excede the allowed table size the new revision will not save.

enjoy!