Bitpass
============

## 简介

Bitpass是一个基于不对称签名算法的身份验证器。

通常，身份认证需要提供用户名与密码等字段，服务端需要保存密码（哈希值或者明文），所以带来安全问题：1. 密码在通信过程中泄露；2. 服务端被攻击后大量账户数据泄露。Bitpass消除了身份认证过程中的密码字段，仅需要用户名（公钥地址）即可完成身份认证，极大提高了身份认证过程中的安全性。


### 下载客户端APP
* iOS，[http://bitpass.dearcoin.com/](http://bitpass.dearcoin.com/)
* Andriod，coming soon...


## 验证过程

### 参数
* 消息格式类似URL([RFC1738](http://www.ietf.org/rfc/rfc1738.txt))，参数的值均需要进行URL编码。
* 参数顺序无关


### 服务端

服务端需要按照下面格式提供出待验证消息：

    bitpass:?sm=[source message]&cbk=[callback url]&dt=[display title]
    
  参数 | 含义 | 是否必须
-----|-------|-----------
sm | source message，需要签名的消息内容 | 是 
cbk | callback url，HTTP POST回调URL | 是 
dt | display title，用于客户端显示标题 | 否
    

### 客户端

    address=[bitcoin address]&signature=[sign message with private key]&message=[source message]

签名消息时，采用兼容比特币消息签名格式。

  参数 | 含义 | 是否必须
-----|-------|-----------
address | 签名私钥所对应的比特币地址 | 是 
signature | 签名内容，采用Base64编码 | 是 
message | source message，需要签名的消息内容 | 是 

签名后将上述格式数据，使用HTTP GET/POST请求发送至`cbk`指定URL。

#### 扩展参考字段
客户端回发数据时，可以加一些额外字段。通常手机可以获取地理信息等，例如：

  参数 | 含义 | 是否必须
-----|-------|-----------
latitude | 纬度 | 否 
longitude | 经度 | 否 

#### 服务端Callback返回格式

  参数 | 含义 | 注释
-----|-------|-----------
code | 返回码 | 1为成功，非1为错误
message | 消息 | 成功时为"OK", 错误时显示对应信息。客户端通常会显示出该段文字。

支持三种格式：Text, XML, JSON。默认为Text，其他返回格式需在HTTP Response Header添加对应的声明，以便客户端识别。

##### TEXT格式：
repsonse header: 

    Content-type: text/plain
    
repsonse body:

    <int>\t<string>
    
##### XML格式：
repsonse header: 

    Content-type: text/xml
    
repsonse body:

    <result><code>1</code><message>OK</message></result>
##### JSON
repsonse header: 

    Content-type: application/json
    
repsonse body:

    {code: 1, message: "OK"}


#### 签名算法

![qq20130728-1](https://f.cloud.github.com/assets/514951/867985/5cef7d72-f760-11e2-8771-aaa629771f91.png)


## 作者

  * [Dearcoin.com](https://github.com/dearcoin)
  * Support By [Bitfund.pe](http://bitfund.pe).

* * *

## Demo

  * [http://bitpass.618.io](http://bitpass.618.io)


## Q&A


### Q: bitpass要解决什么问题？
A: 一句话，消除了身份认证过程中的密码字段。因为密码是造成安全隐患的根本，例如，可能在通信过程中被窃取，可能在服务端泄露明文/密文，一旦消除了密码字段，则彻底消除认证过程中的安全隐患。


### Q: 与Google Authenticator有什么区别？
A: Google Authenticator通常用于身份的二次验证，提高认证过程的安全性。但其依然需要同时在服务端、客户端保存一个种子，已完成核对。若服务端数据泄露，会导致Google Authenticator形同虚设，而bitpass不存在此问题。


### Q：bitpass有什么风险？
A: 其风险来自与私钥的管理。若私钥泄露或者遗失，则会被他人伪造登陆，窃取你的信息和财产。在客户端APP中，均使用AES(Advanced Encryption Standard)相关算法对关键数据进行加密保存，并借助iClound/Dropbox等完成云备份。像1Password一样，APP中通过主密码对所有数据进行保护，只需牢记好主密码即可。


### Q：bitpass APP中的数据如何保障安全？
A: 目前，我们设计的机制是非常安全的。首先，每位用户分配32字节的随机码(Salt)，用户输入自己的主密码（Master Password）后，结合Salt，采用哈希算法PBKDF2-SHA512迭代运算10000次，得到32字节的哈希密码。随后，使用该32字节的哈希密码，采用加密算法AES-256，对用户数据进行加密/解密。存储在APP/云盘等数据均为AES-256加密后的数据，这些算法的安全度目前都是顶级的。


### Q: bitpass需要中心服务器吗？
A: bitpass是一款客户端APP，不依赖中心服务器，只与目标验证网站进行通信，且私钥永远不会出现在通信数据中。


### Q: 云盘备份数据安全吗？
A: bitpass的数据都是经过多次高强度加密处理的，即使攻击者盗取了你云盘上的备份文件，依然是无法解开获取私钥的。

### Q: bitpass与比特币的关系？
A: bitpass与比特币采用相同的签名算法(基于ECDSA)，并且签名时头部硬编码了与比特币相同的消息，所以与比特币的签名是兼容的。



## Q&A (开发者)

### Q: 如果现有网站加上bitpass作为登录需要做哪些工作？
A：

* 首先，需要添加比特币的验证消息库(ECDSA的消息签名认证)，Demo里有PHP和Ruby的版本
  * 在用户表中新增一个比特币地址字段，每个用户对应一个唯一地址
* 然后，提供一个回调URL地址，用于接收与验证消息，并对验证后的消息做处理
* 对于未绑定bitpass用户，先让其使用普通方式登录，进入绑定页面，在网站上显示一个唯一Token（二维码），然后等待该Token的回发验证消息
  * 验证通过后，将此验证消息的签名地址绑定至该用户
* 对于已绑定的用户，显示Token，等待该Token的回发验证消息，通过后，查询其地址绑定的用户，认证通过执行登录操作，完成身份并登录


### Q：如何标识消息时间？

A: 比特币系统自身即为时间证明服务，可以在`source message`中放入Block信息即可证明，比如，`sm=<block_hash>:<block_height>:<content>`，这样即可证明这条消息产生与此Block之后，服务端可以只接受最近N个Block的消息。同理，也可以使用Transaction来替代Block，或者其他时间证明服务。


### Q：如何标识用户？

A：服务端在提供`source message`时，可以使用唯一字符串，例如一串随机数。

