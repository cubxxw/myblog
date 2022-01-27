=== Smart SEO Tool - SEO优化插件 ===
Contributors: wbolt,mrkwong
Donate link: https://www.wbolt.com/
Tags: Baidu, SEO, Keyword, Description, Title, Alt, URL rewrite
Requires at least: 5.6
Tested up to: 5.8
Stable tag: 3.1.1
License: GNU General Public License v2.0 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Smart SEO Tool是一款专门针对WordPress开发的智能SEO优化插件，与众多WordPress的SEO插件不一样的是，Smart SEO Tool更加简单易用，帮助站长快速完成WordPress博客/网站的SEO基础优化。

== Description ==

Smart SEO Tool是一款专门针对WordPress开发的智能SEO优化插件，与众多WordPress的SEO插件不一样的是，Smart SEO Tool更加简单易用，帮助站长快速完成WordPress博客/网站的SEO基础优化。提供TITLES&METAS优化、图片Title&Alt优化、链接优化、robots.txt及Sitemap生成五大功能模块。

### 功能模块一：TDK优化-页面Title/Description/Keywords优化设置

* 站点首页优化-通过配置WordPress博客/网站的首页标题、关键词和描述，实现首页优化。
* 分类列表页优化-支持站长选择WordPress博客/网站已设置的分类，对每一个分类单独设置标题、关键词和描述。
* 文章页面优化-默认分别读取文章标题、Tag和文章内容(前200个字符)为Title、Keyword和Description。
* 独立页面优化-默认分别读取Page页面标题和文章内容(前200个字符)为Title和Description。
* 搜索列表页优化-使用智能优化规则优化搜索结果页，优化规则详见插件说明文档。
* 标签页优化-使用智能优化规则优化标签结果页，优化规则详见插件说明文档。
* 作者页优化-使用智能优化规则优化标签作者页，优化规则详见插件说明文档。

### 功能模块二：图片优化-图片Title/ALT属性优化设置

* 文章图片Title和ALT元描述优化-支持配置图片优化规则，按规则优化图片的标题和ALT元描述；
* 支持关闭、或者以补充和全覆盖的形式对详情图片和特色图片应用新的Title和Alt替代文本规则；
* 支持以站点名称、图像文件名称、文章标题、文章子类别及序号等组合生成Alt和Title。

### 功能模块三：链接优化-站内外链接改写优化设置

* 支持改写Tag标签URL地址，使WordPress标签的URL地址对搜索引擎更佳友好，提升页面收录；
* 支持改写WordPress分类URL地址，隐藏默认的category；
* 支持所有Page页面及Post文章内的站外链接增加rel=noopener noreferrer nofollow，降低网站页面权重流失；
* 支持站外链接改写为https://www.yourdomain.com/go?=*格式；
* 支持站外链接优化例外域名列表，支持站长设置不执行外链改写及nofollow优化规则域名地址。

### 功能模块四：404监测-依赖蜘蛛爬虫日志对404链接进行跟踪

该功能需要另外安装<a href='https://www.wbolt.com/plugins/spider-analyser?utm_source=wp&utm_medium=link&utm_campaign=sst' rel='friend' title='蜘蛛统计插件'>蜘蛛统计插件</a>，对所有蜘蛛爬取的URL链接404错误响应状态进行跟踪，以便于站长快速了解网站存在哪些404链接。

404监测列表数据包括URL地址、响应码、反馈蜘蛛、访问时间及操作项（刷新状态及忽略）。后续插件也将会增加URL重定向功能，敬请期待。

### 功能模块五：失效URL-拒绝一切无效的站外链接

无效的站外链接包括服务器无法访问、404响应状态码、403响应状态码、503服务器错误等一切不可正常访问的站外链接。无效的站外链接既造成不良的用户体验，也影响网站SEO优化。

该功能支持站长设置指定的文章类型、文章状态，检测频率及排除域名，对出站链接进行检测，以便于站长跟踪处理异常状态或者重定向状态的站外链接。

### 功能模块六：robots.txt-搜索引擎爬虫权限设置
* 支持对robots.txt编辑管理，控制爬虫爬取页面权限

#功能模块七：Sitemap生成-用于生成符合搜索引擎标准的sitemap文件
* 支持站长通过设置参数及开关快速生成标准的sitemap地图；
* 支持推送sitemap更新通知到Google及Bing；
* 相对于其他sitemap插件，我们的插件sitemap配置更加容易理解及使用。
* 提供Nginx和Apache伪静态及URL重写规则参考，以帮助站解决插件无法生成sitemap问题。

Smart SEO Tool插件是闪电博专门为WordPress网站开发的SEO优化插件之一，帮助国内站长实现WordPress网站博客SEO基本优化。区别于大部分其他SEO优化插件，该插件简单易用及更符合国内站长需求。WordPress站长可以利用该插件，并结合<a href='https://www.wbolt.com/plugins/skt?utm_source=wp&utm_medium=link&utm_campaign=sst' rel='friend' title='热门关键词推荐插件'>热门关键词推荐插件</a>、<a href='https://www.wbolt.com/plugins/bsl?utm_source=wp&utm_medium=link&utm_campaign=sst' rel='friend' title='百度推送插件'>百度推送插件</a>和<a href='https://www.wbolt.com/plugins/spider-analyser?utm_source=wp&utm_medium=link&utm_campaign=sst' rel='friend' title='蜘蛛统计分析插件'>蜘蛛统计分析插件</a>，对WordPress网站内容的搜索引擎收录及排名优化可以做到事半功倍的效果！

== Installation ==

方式1：在线安装(推荐)
1. 进入WordPress仪表盘，点击“插件-安装插件”，关键词搜索“Smart SEO Tool”，找搜索结果中找到“Smart SEO Tool”插件，点击“现在安装”；
2. 安装完毕后，启用 `Smart SEO Tool` 插件.
3. 通过“设置”->“Smart SEO Tool” 进入插件设置界面进行相关设置.
4. 最后保存设置即可。

方式2：上传安装

FTP上传安装
1. 解压插件压缩包baidu-submit-link.zip，将解压获得文件夹上传至wordpress安装目录下的 `/wp-content/plugins/`目录.
2. 访问WordPress仪表盘，进入“插件”-“已安装插件”，在插件列表中找到“Smart SEO Tool”，点击“启用”.
3. 通过“设置”->“Smart SEO Tool” 进入插件设置界面进行相关设置.
4. 最后保存设置即可。

仪表盘上传安装
1. 进入WordPress仪表盘，点击“插件-安装插件”；
2. 点击界面左上方的“上传按钮”，选择本地提前下载好的插件压缩包baidu-submit-link.zip，点击“现在安装”；
3. 安装完毕后，启用 `Smart SEO Tool` 插件；
4. 通过“设置”->“Smart SEO Tool” 进入插件设置界面进行相关设置.
5. 最后保存设置即可。

关于本插件，你可以通过阅读<a href="https://www.wbolt.com/sst-plugin-documentation.html?utm_source=wp&utm_medium=link&utm_campaign=sst" rel="friend" title="插件教程">Smart SEO Tool插件教程</a>学习了解插件安装、设置等详细内容。

== Frequently Asked Questions ==

= 为什么在百度搜索资源平台提交插件所生成的sitemap提示“索引型不予以处理”？ =
由于百度搜索资源平台更新了sitemap提交机制，需分别提交sitemap子地图，否则不予以处理。参考<a href="https://www.wbolt.com/submit-sitemap-url-to-baidu.html?utm_source=wp&utm_medium=link&utm_campaign=sst" rel="friend" title="sitemap提交教程">非索引型sitemap提交教程</a>。

= 是否兼容国内热门主题? =
大部分热门中文主题已经做了兼容处理。但务必关闭主题本身的SEO功能（如果有），此外请勿安装启用多个SEO插件。

= 是否兼容WooCommerce插件? =
兼容。支持对WooCommerce分类及产品详情设置SEO信息。

= 如何无法生成sitemap及生成sitemap报错? =
无法生成sitemap或者生成sitemap报错。这一般是由于没有设置伪静态及URL重写规则导致。请参考sitemap生成设置的伪静态及重写规则修改服务器配置文件后重启Nginx或者Apache服务器，并且确保WordPress固定链接不是使用默认朴素类型链接。

= 为什么会出现多个title、keywords或者description信息? =
如果出现这种情况，说明你的主题启用SEO配置，又或者你安装了其他的SEO插件。建议关闭主题SEO配置及禁用其他SEO插件。

= 为什么部分页面出现title、keywords或者description信息丢失？ =
这有可能是由于插件与您安装的主题不兼容导致。出现这种情况，建议你提交<a href="https://www.wbolt.com/member?act=enquire?utm_source=wp&utm_medium=link&utm_campaign=sst rel="friend" title="联系工单">联系工单</a>反馈相关信息，我们进一步兼容。

= 安装启用了Smart SEO Tool，是否还可以安装其他SEO插件? =
不可以。WordPress博客网站仅支持启用一款SEO插件，同时启用多个SEO插件会造成冲突。Smart SEO Tool非常简单易用，且符合百度搜索优化规则，推荐安装使用。

= 为什么Smart SEO Tool插件不支持文章页、独立页面、搜索列表、标签页和作者页优化配置? =
Smart SEO Tool已经通过依据搜索引擎标准内置智能优化规则对这些页面执行了优化，我们希望WordPress站长可以通过最少的设置快速实现SEO优化。

= 插件生成的sitemap与其他的sitemap插件生成的有何区别？ =
Smart SEO Tool插件生成的Sitemap更加精简，对同类别的页面不会过于分散。

= 插件改写WordPress默认生成的标签URL地址有何意义？ =
WordPress默认生成的中文标签对应的URL地址对搜索引擎不太友好，通过改写后避免了这种情况的出现。如果你的WordPress纯粹为英文内容，则不建议开启该功能。

== Notes ==

<a href="https://www.wbolt.com/?utm_source=wp&utm_medium=link&utm_campaign=sst" rel="friend" title="SEO优化插件">Smart SEO Tool插件</a>是目前WordPress插件市场中最简单易用的SEO优化插件，但功能又非常强大的SEO插件. 

闪电博（<a href="https://www.wbolt.com/?utm_source=wp&utm_medium=link&utm_campaign=sst" rel="friend" title="闪电博官网">wbolt.com</a>）专注于WordPress主题和插件开发,为中文博客提供更多优质和符合国内需求的主题和插件。此外我们也会分享WordPress相关技巧和教程。

除了Smart SEO Tool插件外，目前我们还开发了以下WordPress插件：

- [百度搜索推送管理-历史下载安装数130,000+](https://wordpress.org/plugins/baidu-submit-link/)
- [热门关键词推荐插件-最佳关键词布局插件](https://wordpress.org/plugins/smart-keywords-tool/)
- [IMGspider-轻量外链图片采集插件](https://wordpress.org/plugins/imgspider/)
- [WP资源下载管理-快速打造资源下载博客网站](https://wordpress.org/plugins/download-info-page/)
- [博客社交分享组件-打赏/点赞/微海报/社交分享四合一](https://wordpress.org/plugins/donate-with-qrcode/)
- [HTML代码代码优化工具-一键清洗转载文章多余代码](https://wordpress.org/plugins/imgspider.zip/)
- [WP VK-WordPress知识付费插件](https://wordpress.org/plugins/wp-vk/)
- 更多主题和插件，请访问<a href="https://www.wbolt.com/?utm_source=wp&utm_medium=link&utm_campaign=sst" rel="friend" title="闪电博官网">wbolt.com</a>!

如果你在WordPress主题和插件上有更多的需求，也希望您可以向我们提出意见建议，我们将会记录下来并根据实际情况，推出更多符合大家需求的主题和插件。

致谢！

闪电博团队

== Screenshots ==

1. TDK优化设置界面截图.
2. 图片优化设置界面截图.
3. 链接优化设置界面截图.
4. Sitemap生成设置界面.
5. robots.txt设置界面截图.
6. 插件设置界面截图.
7. 插件配置向导界面截图.

== Changelog ==

= 3.1.1 =
* 修复tdk变量插入异常bug；
* 修复robots.txt换行bug.

= 3.1.0 =
* 新增数据验证步骤以确保数据输入的安全性；
* 新增输出数据转义以防管理员屏幕被劫持；
* 新增wp_enqueue命令以规范化包含JS/CSS；
* 新增_FILE _变量使用以规范化调用文件路径；
* 解决与XSS或CSRF相关的跨站点脚本安全问题。

= 3.0.5 =
* 新增tag旧链接301重定向新URL规则；
* 修复失效URL模块操作失效bug；
* 修复部分列表翻页失效bug；
* 修复谷歌和bing搜索引擎sitemap更新无通知bug；
* 修复特色图ALT和标题优化替换bug。

= 3.0.4 =
* 新增tag原链接301重定向至rewrite链接规则；
* 修复失效URL功能模块编辑/未失效/取消链接重新检测操作及翻页bug；
* 修复sitemap更新未能通知Google和Bing搜索引擎；
* 解决文章特色图alt替代文本覆盖性问题；
* 解决插件向导缺失链接问题。

= 3.0.3 =
* 修复部分站点Sitemap模块报错无法正常使用问题；
* 修复外链优化原链接存在中文时无法正常跳转bug。

= 3.0.2 =
* 优化与SKT插件的关联逻辑，以更好地实现关键词推荐调用。

= 3.0.1 =
* 修复文章详情SEO设置不可调用SKT插件数据问题；
* 修复TDK变量引入无数据及末尾变量逗号问题；
* 优化图片SEO变量引入范围；
* 优化robots规则及子sitemap输入框默认高度；
* 优化失效URL列表设置默认展现部分选项；
* 优化移动端输入框、变量插入等交互体验。

= 3.0.0 =
* 重构插件后台，增强功能及用户体验；
* 新增插件安装配置向导，方便新安装用户快速配置插件；
* 新增插件设置功能，支持关闭开启各个模块；
* 优化TDK模块设置功能，支持变量、功能开关及索引开关等；
* 优化图片模块设置功能，支持变量插入；
* 优化链接模块，支持链接改写、404检测和失效URL分块管理；
* 优化网站地图模块，作为独立菜单管理；
* 优化robots.txt模块，支持自主编辑规则或采用默认规则。

= 2.4.6 =
* 进一步优化robots.txt默认规则以适用于大部分站长；
* 精简robots功能文字说明以易于理解。

= 2.4.5 =
* 优化插件默认robots.txt规则设置，增加Noindex规则及优化disallow规则；
* 修改插件部分功能文字描述及使用说明；
* 修复其他已知bug及体验问题。

= 2.4.4 =
* 新增404监测功能，快速定位解决404错误URL地址；
* 新增失效站外URL地址检测，帮助站长修复错误的站外链接；
* 优化图片SEO功能，支持站长自定义优化规则；
* 优化sitemap地图功能，支持自定义文章类型分类、post及tag子地图。

= 2.4.3 =
* 新增文章&页面详情SEO信息输入功能；
* 新增插件版本更新提示功能；
* 解决国内热门主题兼容性问题；
* 兼容兼容WooCommerce产品及分类链接SEO信息设置；
* 解决自定义分类文章SEO信息不全的问题；
* 解决SEO标题连接符编码问题；
* 统一外部链接nofollow代码规范为rel="noopener noreferrer nofollow"。

= 2.4.2 =
* 新增子sitemap列表，以便站长快速复制粘贴至百度搜索资源平台提交；
* 修复日主题文章&页面title无站点名称bug；
* 优化插件设置移动端交互界面。

= 2.4.1 =
* 修复主页TDK优化重复读取设置副标题bug；
* 修复sitemap地图生成tag链接优先级数据读取bug；
* 优化单个sitemap文件URL地址数量限制，支持tag和post均不超过1w个地址，实现自动分拆。

= 2.4.0 =
* 新增sitemap生成失败说明，提供Nginx和Apache伪静态及URL重写规则；
* 新增站外链接优化例外域名列表功能；
* 优化sitemap网站地图xml文件样式；
* 优化单个sitemap文件的URL地址数量，最多不超过1w个地址；
* 优化图片优化规则，解决图片名称及ALT重复问题；
* 优化插件设置界面样式。

= 2.3.2 =
* 增加国内热门主题兼容，修正title/keywords/description重复问题.

= 2.3.1 =
* 修复SEO功能关闭失效问题

= 2.3.0 =
* 优化插件设置界面交互
* 增加插件冲突提醒关闭记录
* 新增sitemap地址生成展示
* 新增头条搜索sitemap提交教程入口
* 优化tag标签链接重写规则

= 2.2.1 =
* 修正部分主题文章详情页会出现"阅读更多"字样的bug

= 2.2.0 =
* 修正部分主题设置SEO优化规则不生效的Bug
* 优化已知Bug

= 2.0.0 =
* 新增Sitemap生成功能
* 新增robots.txt管理功能
* 新增站内链接优化功能
* 新增插件模块开关功能
* 优化插件配置界面
* 修正插件已知bug

= 1.1.0 =
* 新增搜索列表页优化功能
* 新增Tag列表页优化功能
* 新增作者页优化功能
* 新增图片SEO优化功能
* 增加插件教程/插件支持等链接入口
* 优化插件设置UI界面

= 1.0.0 =
* 新增SEO功能开关功能
* 新增WordPress首页SEO设置功能
* 新增WordPress文章页和Page页面自动优化规则
* 新增WordPress分类页面SEO设置功能