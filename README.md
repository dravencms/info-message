# Dravencms info message module

This is a simple info message module or your dravencms web (Inform about holidays, planned web maintenance, etc)

## Instalation

The best way to install dravencms/info-message is using  [Composer](http://getcomposer.org/):


```sh
$ composer require dravencms/info-message
```

Then you have to register extension in `config.neon`.

```yaml
extensions:
	infoMessage : Dravencms\InfoMessage\DI\InfoMessageExtension
```
