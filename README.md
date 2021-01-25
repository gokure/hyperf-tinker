# Hyperf Tinker

[![Latest Stable Version](https://poser.pugx.org/gokure/hyperf-tinker/version.png)](https://packagist.org/packages/gokure/hyperf-tinker)
[![Total Downloads](https://poser.pugx.org/gokure/hyperf-tinker/d/total.png)](https://packagist.org/packages/gokure/hyperf-tinker)
[![GitHub license](https://img.shields.io/github/license/gokure/hyperf-tinker)](https://github.com/gokure/hyperf-tinker)

## Installation

```bash
composer require gokure/hyperf-tinker
```

## Publish Config

```bash
php bin/hyperf.php vendor:publish gokure/hyperf-tinker
```

## Usage

```bash
php bin/hyperf.php tinker
```

## Commnads

* run command

````bash
>>> $a=1
=> 1
>>> $a
=> 1
>>> define('VERSION', 'v1.0.1')
=> true
>>> VERSION
=> "v1.0.1"
>>>
````

* The help command

```bash
>>> help
  help       Show a list of commands. Type `help [foo]` for information about [foo].      Aliases: ?
  ls         List local, instance or class variables, methods and constants.              Aliases: dir
  dump       Dump an object or primitive.
  doc        Read the documentation for an object, class, constant, method or property.   Aliases: rtfm, man
  show       Show the code for an object, class, constant, method or property.
  wtf        Show the backtrace of the most recent exception.                             Aliases: last-exception, wtf?
  whereami   Show where you are in the code.
  throw-up   Throw an exception or error out of the Psy Shell.
  timeit     Profiles with a timer.
  trace      Show the current call stack.
  buffer     Show (or clear) the contents of the code input buffer.                       Aliases: buf
  clear      Clear the Psy Shell screen.
  edit       Open an external editor. Afterwards, get produced code in input buffer.
  sudo       Evaluate PHP code, bypassing visibility restrictions.
  history    Show the Psy Shell history.                                                  Aliases: hist
  exit       End the current session and return to caller.                                Aliases: quit, q
```

* get hyperf env

```bash
>>> env("APP_NAME")
=> "skeleton"
>>>
```

* query db

```bash
>>> $user = App\Model\User::find(1)
=> App\Model\User {#84118
     id: 1,
     name: "Gang Wu",
     email: "gokure@gmail.com",
     created_at: "2019-03-12 19:07:08",
     updated_at: "2021-01-25 10:35:22"
   }
```

* class alias auto-load

```bash
>>> $user = User::find(1)
[!] Aliasing 'User' to 'App\Model\User' for this Tinker session.
=> App\Model\User {#84120
     id: 1,
     name: "Gang Wu",
     email: "gokure@gmail.com",
     created_at: "2019-03-12 19:07:08",
     updated_at: "2021-01-25 10:35:22"
   }
>>> collect([1, 2])
=> Hyperf\Utils\Collection {#84084
     all: [
       1,
       2,
     ],
   }
```
