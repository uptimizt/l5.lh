# Вводная

Ссылка для запуска приложения http://l5.lh/

# Исходные Требования

1. [VirtualBox](https://www.virtualbox.org/) - скачать и установить
2. [Vagrant](https://www.vagrantup.com/) - скачать и установить
3. [Vagrant::Hostsupdater](https://github.com/cogitatio/vagrant-hostsupdater) - установить плагин к vagrant

Заметка о п. 3: Vagrant Hostsupdater: он нужен для автоматической правки файла hosts, что обеспечивает работу сайта на локальном URL. Для плагина нужны права админа, поэтому на маках нужно будет ввести пароль, а на Win - запускать консоль или IDE с правами администратора перед тем, как выполнить vagrant up


# Использование

## первый старт

### Клонируем репо
и запускаем вагрант (нужно чтобы были выполнены Исходные Требования)
```
git clone git@github.com:uptimizt/l5.lh.git logger
cd logger
vagrant up
```
All Vagrant commands like `vagrant halt`, `vagrant destroy` and `vagrant suspend` are applicable.


### composer install

нужно установить зависимости php

```
cd /srv/www/
composer install
```

### Открываем консоль сайта

Ссылка на главную http://l5.lh/
Доступ к БД http://db.l5.lh/

вопросы: uptimizt@gmail.com


### поставить демо данные

#### можно восстановить из архива

Команды исполняются внутри ВМ `vagrant ssh`

```
sudo cp /srv/config/db/vagrant.sql.zip /tmp/
sudo unzip /tmp/vagrant.sql.zip -d /tmp/
mysql -uvagrant -ppassword vagrant < /tmp/vagrant.sql
```

#### можно сгенерировать через консоль

написана консольная команда Симфони для генерации 3 млн записей в лог:
```
cd /srv/www/
php bin/console app:create-log
```
будет запущена консольная обработка, которая создаст таблицу и сгенерирует 3 млн записей используя Faker
