# slim-api
Basic slim api project and generator

![travis-ci status](https://travis-ci.org/gabriel403/slim-api.svg)

Status
==
pre-alpha, init and create models is mostly complete, controllers, migrations and 'scaffold' is still to do, develop relations in model migrations.

What?
==
A simple generator for producing simplified controllers/models and migrations, using Slim, Phinx and eloquent. It should be relatively simple to replace the controller|model template, and use something other than Phinx for migration.

Why?
==
I wanted to be able to create API end points as easily as possible, and I love the simplicity of Slim, and after a sordid time with RoR this seemed like a fun thing to do!

How?
==
Basic useage is simple, we first have to initiate the project, this creates a default skeleton for the project and initiates the phinx configuration.

```
slimapi init <project name> [location]
```

Location defaults to the cwd if not specified.

When can then generate a model, this creates a migration and a simple model class.

```
slimapi generate model <model name> <model definitions>
```

Model definitions are a space seperated list of column definitions, of the form `name:type:limit:null:unique`, so

```
slimapi generate model Foo bar:integer baz:string:128:false bazbar:string:128::true
```

Would create a migration of 3 columns, baz would have a character limit and can't be null, bazbar would have a character limit and must be unique.
