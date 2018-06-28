![wpNyarukoLive](https://raw.githubusercontent.com/kagurazakayashi/wpNyarukoLive/master/img/wpNyaruko.gif)  0.4

[介绍](#功能) | [功能](#功能) | [安装](#安装步骤) | [截图](#截图) | [兼容性](#兼容性) | [第三方](#使用的第三方软件) | [License](#许可协议-license)

- 警告：目前尚未开发完成，请勿使用。

## 介绍

- wpNyaruko 系列成员之一，一款可以为 WordPress 网站提供视频直播能力的插件，提供管理后台和弹幕功能，可以和 wpNyaruko-N 主题无缝集成。
  - 此分支虽然提供了很多自定义设置，但仍然主要是以定制网站开发的，因此不保证在其他网站上可以正确运行。
  - 目前尚未开发完成。
- 版权归属
  - 这是 [yaNyaruko Project](https://github.com/kagurazakayashi) 项目的一部分，神楽坂雅詩拥有其部分版权。
  - 这是 [北京未来赛车文化有限公司](https://www.futureracing.com.cn) 官网的一部分，北京未来赛车文化有限公司拥有其主要版权。
  - 这是 北京篝火网络科技有限公司 业务的一部分，北京篝火网络科技有限公司拥有其部分版权。
  - 非以上单位或个人，请勿将此项目用于商业用途，其他目的须遵守 GPL 协议。有关详细信息请前往[许可协议](#许可协议-license)节了解。

## 功能

- 多种直播流格式的播放
  - 支持 FLV 和 HLS 直播流播放。
  - 不需要客户端使用 Flash 插件，只需支持 HTML5 video 标签的视频的浏览器。
  - 集成三种第三方 JS 播放引擎，具体包括：flv.js, hls.min.js，video.js。
  - 支持引擎自动切换，默认使用 flv ，自动在不支持的平台上（例如 iOS）使用 hls。
  - 用户可以在前端播放器中手动切换引擎。
  - 只会加载选中引擎的 JS 脚本文件。
  - 支持直播线路（或清晰度）切换，可以在直播配置中自定义线路名称和跳转地址。
- 简单的配置文件
  - 数据库的配置文件与 WordPress 极为相似，以致于只需从 wp-config.php 复制相关行即可完成数据库连接配置。
  - 也可以通过自定义的数据库连接地址实现与 WordPress 不同表或者不同库。
  - 同样支持数据库表名前缀，可实现一个数据库对应多个网站。
- 直播平台接口支持
  - 提供播放状态回调接口，用于第三方直播平台调用（目前只支持第三方平台或测试页面调用）。
  - 目前支持的平台：
    - 阿里云：对应文件夹 `aliyun_livecallback` 。
- 前台播放器自动响应播放和停播（需要第三方直播平台调用播放状态回调接口）
  - 正在观看的视频停止推流后，自动停止播放并卸载加载的 JS 库，然后显示直播间指定的封面图片。
    - 如果视频处于停播状态，并且没有指定的封面图片，会显示一张默认的「彩条信号」图片，并且可以在后台进行更改默认的「彩条信号」图片样式（后台更换功能尚未实现）。目前彩条信号提供以下样式（位于 img 文件夹下）：
      - [SMPTE_1080P.png](https://github.com/kagurazakayashi/wpNyarukoLive/blob/master/img/SMPTE_1080P.png), [SMPTE_HD_1080P.png](https://github.com/kagurazakayashi/wpNyarukoLive/blob/master/img/SMPTE_HD_1080P.png): 标准 SMPTE 彩条信号，由第三方提供。
      - [wpNyarukoLive_LikeEBU.svg](https://github.com/kagurazakayashi/wpNyarukoLive/blob/master/img/wpNyarukoLive_LikeEBU_1080P.png), [wpNyarukoLive_LikeEBU_RGB.svg](https://github.com/kagurazakayashi/wpNyarukoLive/blob/master/img/wpNyarukoLive_LikeEBU_RGB1080P.png): 仿 EBU 彩条信号，提供普通色彩和 RGB 色彩，使用 svg 矢量编写。
      - [wpNyarukoLive_LikeSMPTE.svg](https://github.com/kagurazakayashi/wpNyarukoLive/blob/master/img/wpNyarukoLive_LikeSMPTE_1080P.png), [wpNyarukoLive_LikeSMPTE_RGB.svg](https://github.com/kagurazakayashi/wpNyarukoLive/blob/master/img/wpNyarukoLive_LikeSMPTE_RGB1080P.png): 仿 SMPTE 彩条信号，提供普通色彩和 RGB 色彩，使用 svg 矢量编写。
      - [wpNyarukoLive_YashiColorBar_RGB.svg](https://github.com/kagurazakayashi/wpNyarukoLive/blob/master/img/wpNyarukoLive_YashiColorBar_RGB_1080P.png): wpNyarukoLive 原创彩条样式，使用 svg 矢量编写。
  - 如果视频源开始推流，播放器会自动开始加载相关 JS 库，并进入播放状态。
- 弹幕功能
  - 可以允许用户发送弹幕。
  - 用户可以是游客账户和 WordPress 账户。
    - 可以在后台限制可以发送弹幕的用户等级（尚未实现），权限等级设置如下：
      - 禁止任何人发送弹幕
      - 只允许实名注册认证用户发送弹幕
      - 允许所有注册用户发送弹幕
      - 允许所有人发送弹幕（默认）
    - 允许非 WordPress 注册账户（游客账户）发送弹幕
      - 采用和 WordPress 评论功能类似的游客信息登记方法：填写昵称、邮件、网址，然后发送弹幕。
      - 在后台可以设置为简化的游客评论模式，不显示网址输入框和注册/登录按钮（默认开启，后台设置尚未实现）。
    - 可以读取当前已登录的 WordPress 账户信息进行评论（尚未实现）。
- 后台直播间管理功能
  - 可以在「推流记录」中看到每个直播间的状况，包括： 
    - 序号,播放状态,节点,资源,应用,名称,推流IP,回调时间,推流参数,手动播放控制,弹幕管理,记录删除。
    - 可以无视回调接口返回的信息，强制让直播插件视为正在直播或者停播。
    - 可以删除直播状态记录，回调接口再次触发时重新写入记录。
  - 可以在「弹幕管理」中浏览每个直播间的弹幕，包括：
    - 弹幕序号,当前会话编码,浏览器会话编码,发送者用户信息,发送者IP,发送时间,弹幕内容,弹幕样式,浏览器UA,WordPress用户ID,弹幕。
    - 可以单独开关指定直播间的弹幕功能（尚未实现）。
    - 可以删除这条弹幕，如果需要屏蔽IP地址：
  - 可以在「权限设置」中查询当前的屏蔽列表，这些设置同时生效于直播播放和弹幕：
    - 序号,类型,条件,起始时间,结束时间,执行开关,原因。
    - 通过指定 类型,条件,起始时间(自动设置为当前时间),结束时间,执行开关,原因 来新建屏蔽列表。
    - 类型目前只支持 IP地址 。
    - 可以开关也可以完全删除屏蔽条目。

## 安装步骤

1. 准备一个安装好的 WordPress 站点，并将本项目克隆到本地。
2. 根据回调方式，复制相关回调文件夹（以 `_livecallback` 结尾的文件夹）到网站根目录。
  - 例如，阿里云直播的回调文件夹名称是 `aliyun_livecallback` 。
  - 出于安全起见，建议将文件夹内的 `index.php` 修改为其他文件名。
3. 前往直播平台的管理控制台，填写该回调网址。
  - 例如 `https://www.yashi.moe/aliyun_livecallback/index.php` 。
4. 复制 `nyarukolive-config.simple.php` 到网站根目录，并重命名为 `nyarukolive-config.php` 。
5. 修改 `nyarukolive-config.php` ，直接复制 `wp-config.php` 中的相关条目即可（不要复制多余的条目），也可以根据实际需要进行自定义。
6. 将 `mysql` 文件夹中的数据库文件导入到 `nyarukolive-config.php` 中指定的数据库来新建相关数据表。
7. 复制所有其他文件到 `<网站文件夹>/wp-content/plugins/wpNyarukoLive` 中（不要改名）。
8. 进入 `<你的网址>/wp-admin/tools.php?page=nyarukolive-options` 登录管理员账户并对直播进行设置。
  - 也可以在后台管理中 `工具 → wpNyaruko 直播选项` 中打开此设置。
  - 如果网站采用了 wpNyaruko 的兼容主题（如 [wpNyaruko-F](https://github.com/kagurazakayashi/wpNyaruko-F) ），可以在主题自带的控制面板中找到 `视频直播` 图标。
9. 尝试进行推流并停止，以触发回调方法，若成功，你可以在后台「推流记录」中看到刚才的推流。
10. 新建直播用的页面或者文章：
  - 请按下面的代码块说明和示例代码块填写。
  - 你可以在其后面继续写其他东西。
  - 名称类条目需和「推流记录」中看到的一致。
  - 如果没有写可以留空的话，均为必填项（尽管代码中处理了部分未指定设置的情况，但最好还是不要忽略设置，版本更迭时注意此处变化）。

```
[nyarukolive
title="直播标题"
res="资源名称"
app="应用名称"
id="推流名称"
rtmp="RTMP网址（如非需要，建议留空）"
flv="FLV 观看地址 *.flv"
hls="HLS 观看地址 *.m3u8"
timezone="世界时钟显示的时区"
timedst="夏令时，没有则设为0，可以为正数或者负数"
zonename="世界时钟显示名称"
stoppic="没有直播的时候显示的图片网址"
protocol="填写 http 或者 https 或者留空，如果填写则会在播放时强制切换到指定协议，注意不要和服务器相关设置冲突"
lines="各种线路地址，链接到各清晰度的直播文章或页面，格式为：「清晰度名称,网址|清晰度名称,网址|清晰度名称,网址,now」，其中一个后面加「,now」来让播放器视为当前清晰度。"
][/nyarukolive]
在之后可以继续写你自己的内容。
```

以下是一份示例：

```
[nyarukolive
title="2018勒芒24小时正赛"
res="live.futureracing.com.cn"
app="18l24"
id="B7L5"
rtmp=""
flv="http://www.futureracing.com.cn/18l24/B7L5.flv"
hls="http://www.futureracing.com.cn/18l24/B7L5.m3u8"
timezone="1"
timedst="1"
zonename="法国勒芒"
stoppic="http://www.futureracing.com.cn/img/2018/06/LeMans2018_Poster_1080LC.jpg"
protocol="http"
lines="高清,http://www.futureracing.com.cn/20180616/a/|流畅,http://www.futureracing.com.cn/20180616/b/,now"
][/nyarukolive]
<h1>2018勒芒24小时正赛</h1>
<p>第86届勒芒24小时耐力赛将在 6月16-17日 举行。将有36支车队派出60台赛车出战，7支厂商车队，共180名车手，来自35个国家和地区。现在为您带来现场直播！</p>
```

## 截图

![未来赛车正在使用本产品进行26小时持续视频直播](https://raw.githubusercontent.com/kagurazakayashi/wpNyarukoLive/master/screenshots/20180617182550.jpg)
未来赛车正在使用本产品进行26小时持续视频直播，该网站在 [wpNyaruko-F](https://github.com/kagurazakayashi/wpNyaruko-F) 主题中工作。 © 图中直播内容版权为 北京未来赛车文化有限公司 和 LE MANS 24h ，请勿再发布此图片。

![后台流管理页面](https://raw.githubusercontent.com/kagurazakayashi/wpNyarukoLive/master/screenshots/20180617002017.jpg)
后台流管理页面

![后台弹幕管理页面](https://raw.githubusercontent.com/kagurazakayashi/wpNyarukoLive/master/screenshots/20180620163018.jpg)
后台弹幕管理页面

![后台黑名单管理页面](https://raw.githubusercontent.com/kagurazakayashi/wpNyarukoLive/master/screenshots/20180620163124.jpg)
后台黑名单管理页面

## 兼容性

- 建议使用 WordPress 4.9.x ，4.9.4 是该主题的开发环境。
- 欢迎使用最新版 Chrome 浏览器，以及 PHP7 。这是该主题的开发环境。
- 支持其他带有最新版 webkit 内核浏览器。
- 可以适配最新版 Firefox 浏览器。
- 欢迎使用最新版 iOS 里的 Safari ，这是该主题的开发环境。
- Android 请使用最新版 Chrome 浏览器。
- 要正常使用，浏览器必须开启 JavaScript 。
- 要保存浏览器令牌和游客保存用户名邮箱等信息，浏览器需要开启 Cookie 。

## 使用的第三方软件

- [jquery](https://github.com/jquery) / [jQuery](https://github.com/jquery/jquery)
- [bilibili](https://github.com/Bilibili) / [flv.js](https://github.com/Bilibili/flv.js)
- [video-dev](https://github.com/video-dev) / [hls.js](https://github.com/video-dev/hls.js)
- [videojs](https://github.com/videojs) / [video.js](https://github.com/videojs/video.js)
- [videojs](https://github.com/videojs) / [videojs-contrib-hls](https://github.com/videojs/videojs-contrib-hls)

## 许可协议 License

### For users in China:

- 从 commit 65396af400bb718ed03be22751de42ec22a747a7 之后的版本开始（不包括该版本），本 repository 的版权已被 北京未来赛车文化有限公司 购买。在此版本之后发布的任何 commit 版本，均不可以用于商业目的。
- 北京未来赛车文化有限公司、北京篝火网络科技有限公司、kagurazakayashi 共享本作品版权。
- 如果需要将这些代码用于商业目的，必须得到 北京未来赛车文化有限公司 的允许，并且您可能需要同时为该公司和 北京篝火网络科技有限公司 或 kagurazakayashi 支付授权费用。您可以[通过 issues 请求购买](https://github.com/kagurazakayashi/wpNyaruko-N/issues)。
- 对于非商业目的的使用，遵循 GPL License 条款发布，所有链接到本产品的软件均应遵循 GPL License 。如果您不能接受 GPL License ，则需要按照上述商业方式购买许可。

### For users in other areas:

- This repository is released under the GPL (GPLv2 or GPLv3). So any link to this repository must follow GPL. If you cannot accept GPL, you need to be licensed from Beijing Future Racing Culture Co.Ltd. and @kagurazakayashi.
- Free Use for Those Who Never Copy, Modify or Distribute. Commercial Use for Everyone Else. To all commercial organizations, we do recommend the commercial license.