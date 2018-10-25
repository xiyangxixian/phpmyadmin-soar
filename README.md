# phpmyadmin-soar
phpmyadmin-soar 是基于小米 soar的phpmyadmin插件, 方便在phpmyadmin上进行sql调试与优化。

![soar](https://raw.githubusercontent.com/xiyangxixian/phpmyadmin-soar/master/doc/img/example.png)

## 环境需求
* php >= 5.4
* phpmyadmin >= 4.0
* php.ini 中未禁用 proc_open 函数

## 安装
```php
php install.php phpmyadmin路径 --version=phpmyadmin版本
chmod -R a+x phpmyadmin路径/soar/bin
php install.php ~/phpmyadmin --version=4.8.3
chmod -R a+x ~/phpmyadmin/soar/bin
```

## 卸载
```php
php uninstall.php phpmyadmin路径 --version=phpmyadmin版本
php uninstall.php ~/phpmyadmin --version=4.8.3
```

## 感谢
感谢由小米人工智能与云平台的数据库团队开发与维护的SQL进行优化和改写的自动化工具 soar。

* [soar](https://github.com/XiaoMi/soar)
