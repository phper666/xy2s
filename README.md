# 校园淘物站

如果想在此系统进行二次开发，请先去了解thinkphp5.0怎么使用。欢迎大家访问我的博客：http://www.liyuzhao.cn.此项目的演示地址在：http://www.xy2s.top

## 环境要求:
* PHP >= 5.4.0(注意：PHP5.4dev版本和PHP6均不支持)
* PDO PHP Extension
* MBstring PHP Extension
* CURL PHP Extension
* 开启静态重写(方法参考:http://www.kancloud.cn/manual/thinkphp5/177576)
* 要求环境支持pathinfo

## 重写设置
### [Apache]
httpd.conf配置文件中加载了mod_rewrite.so模块
AllowOverride None 将None改为 All
把下面的内容保存为.htaccess文件放到应用入口文件的同级目录下
 
```
<IfModule mod_rewrite.c>
Options +FollowSymlinks -Multiviews
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
</IfModule>
```
如果为phpstudy

```
<IfModule mod_rewrite.c>
Options +FollowSymlinks -Multiviews
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php [L,E=PATH_INFO:$1]
</IfModule>
```
如果还是不行,请添加"?"

```
<IfModule mod_rewrite.c>
Options +FollowSymlinks -Multiviews
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php?/$1 [QSA,PT,L]
</IfModule>
```
### [IIS]
如果你的服务器环境支持ISAPI_Rewrite的话，可以配置httpd.ini文件，添加下面的内容：
```
RewriteRule (.*)$ /index\.php\?s=$1 [I]
```
在IIS的高版本下面可以配置web.Config，在中间添加rewrite节点：

```
<rewrite>
 <rules>
 <rule name="OrgPage" stopProcessing="true">
 <match url="^(.*)$" />
 <conditions logicalGrouping="MatchAll">
 <add input="{HTTP_HOST}" pattern="^(.*)$" />
 <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
 <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
 </conditions>
 <action type="Rewrite" url="index.php/{R:1}" />
 </rule>
 </rules>
 </rewrite>
```
### [Nginx]
在Nginx低版本中，是不支持PATHINFO的，但是可以通过在Nginx.conf中配置转发规则实现：
```
location / { // …..省略部分代码
   if (!-e $request_filename) {
   rewrite  ^(.*)$  /index.php?s=/$1  last;
   break;
    }
 }
```
其实内部是转发到了ThinkPHP提供的兼容URL，利用这种方式，可以解决其他不支持PATHINFO的WEB服务器环境。
如果你的应用安装在二级目录，Nginx的伪静态方法设置如下，其中youdomain是所在的目录名称。
```
location /youdomain/ {
    if (!-e $request_filename){
        rewrite  ^/youdomain/(.*)$  /youdomain/index.php?s=/$1  last;
    }
}
```

## [UPDATE]

# 系统介绍
##功能
- 后台主要功能：
  1：账号管理：里面有权限管理，角色管理，管理员管理，用户管理。超级管理员可以自由的添加权限和角色等
  2：信息管理：前台反馈信息管理，站内发送通知等
  3：公告管理：发布站内公告
  4：学校管理：添加学校
  5：链接管理：尾部显示的友情链接
  6：商品管理：查看商品举报信息并对商品进行管理 
  7：栏目管理：对商品的栏目进行分类
  8：论坛管理：对论坛发帖进行顶置，删除等
  前台主要功能：
  1：用户可以就浏览发布的商品信息，和查看论坛帖子。不登陆无法看到发布人的联系方式，无法进入个人中心，无法发帖子和评论帖子
  2：登录的用户可以进入个人中心修改个人信息和修改密码查看发布的商品，修改发布的商品，查看发布的帖子，和发布商品等
  3：用户可以搜索商品，可以按学校搜索，也可以按栏目搜索
##数据库表
数据库设计（一共设计了21个表）：
1，用户表 es_user 2，商品信息表 es_goods 3，评论表 es_ comments
4，栏目分类表 es_category 5，校园公告表 es_gonggao 6，管理员表 es_admin  7，角色表 es_role 8，权限表 es_auth 9，学校分类表s_school 10，商品举报 es_goodsreport 11，商品信息管理表 es_goodsmsg_manage 12，链接管理表 es_link 13，地区表  es_region（此表是我直接网上找的）14，反馈表 es_msg 15，举报次数记录表 es_repornum 16，论坛总栏目表  es_forum_all_column 17，论坛子栏目表 es_forum_sub_column 18，论坛发帖表 es_forum_post 19，论坛回帖表 es_forum_reply 20，论坛评论回帖人表 es_forum_comment 21，发送通知表 es_tongzhi

##前台
- 前台采用百分比来制作的模版，手机电脑均可使用。

##后台管理

- 后台采用了古老的模版写的，不太兼容手机端。



# 许可协议

校园淘物站系统遵循Apache2开源协议发布。Apache Licence是著名的非盈利开源组织Apache采用的协议。该协议和BSD类似，鼓励代码共享和尊重原作者的著作权，同样允许代码修改，再作为开源或商业软件发布。需要满足的条件:

1. 需要给代码的用户一份Apache Licence ；
2. 如果你修改了代码，需要在被修改的文件中说明；
3. 在延伸的代码中（修改和有源代码衍生的代码中）需要带有原来代码中的协议，商标，专利声明和其他原来作者规定需要包含的说明；
4. 如果再发布的产品中包含一个Notice文件，则在Notice文件中需要带有Apache Licence。你可以在Notice中增加自己的许可，但不可以表现为对Apache Licence构成更改。

具体的协议参考：[http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0)。