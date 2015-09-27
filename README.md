#slim-api
Basic slim api project and generator

[![Build Status](https://travis-ci.org/slimphp-api/slim-api.svg)](https://travis-ci.org/slimphp-api/slim-api)
[![Coverage Status](https://coveralls.io/repos/slimphp-api/slim-api/badge.svg?branch=master&service=github)](https://coveralls.io/github/slimphp-api/slim-api?branch=master)
[![Code Climate](https://codeclimate.com/github/slimphp-api/slim-api/badges/gpa.svg)](https://codeclimate.com/github/slimphp-api/slim-api)

#Status

Alpha, init and create models/controllers/scaffolds is complete.

#What?

A simple command line app for producing simple controllers/models/migrations, routes and DI, using Slim and symfony console.

###External modules
By default the app uses phinx and eloquent for migrations and ORM, these are provided by external modules: [slim-eloquent](https://github.com/slimphp-api/slim-eloquent) and [slim-phinx](https://github.com/slimphp-api/slim-phinx)

#Why?

I wanted to be able to create API end points as easily as possible, and I love the simplicity of Slim, and after a sordid time with RoR this seemed like a fun thing to do!

#How?


###Init

Basic useage is simple, we first have to initiate the project, this creates a default skeleton for the project and initiates the phinx configuration.

```
slimapi init <project name> [location]
```

Location defaults to the cwd if not specified.

If you use a different migration/orm/structure module you'll then have to re-init the appropriate source, such as:

```
slimapi init:db
```

This must be done from the root or your project after the init.

###Models

We can then generate a model, this creates a migration, a simple model class and DI configuration.

```
slimapi generate model <model name> <model definitions>
```

Model definitions are a space seperated list of column definitions, of the form `name:type:limit:null:unique`, so

```
slimapi generate model Foo bar:integer baz:string:128:false bazbar:string:128::true
```

Would create a migration of 3 columns, baz would have a character limit and can't be null, bazbar would have a character limit and must be unique.

###Controllers

We can create a controller, this creates a simple controller, route and DI configuration.

```
slimapi generate controller <controller name> [methods]
```

Methods defaults to index, get, post, put and delete and are empty by default.
The controller name influences how the route is designed.

```
slimapi generate controller Foo index post
```

Would generate a controller named Foo with empty methods index and post. It would also create the GET/POST `/foo` route.

```
slimapi generate controller Foo
```

Would generate a controller named Foo with empty methods index, get, post, put, delete.
It would also create the GET/POST `/foo` routes and the GET/PUT/DELETE `/foo/{id}` routes.

###Scaffold

Scaffolding combines controller and model generation but with added jazz. It configures the controller to receive the model as a constructor param, configures the DI to inject the model to the controller and finally populates the normally empty controller methods with basic CRUD functionality. You can't provide arguments to specify controller methods (it creates them all), but you can supply your methods definition.

```
slimapi generate scaffold foo field1:integer field2:string
```

This would generate the Foo controller and appropriate routes, the Foo model/migration with field1/field2 as fillables and any required DI configuration.
