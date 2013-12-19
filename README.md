Bitpass
============

## 简介 / Introduction


Bitpass是一个基于不对称签名算法的身份验证器。

通常，身份认证需要提供用户名与密码等字段，服务端需要保存密码（哈希值或者明文），所以带来安全问题：1. 密码在通信过程中泄露；2. 服务端被攻击后大量账户数据泄露。Bitpass消除了身份认证过程中的密码字段，仅需要用户名（公钥地址）即可完成身份认证，极大提高了身份认证过程中的安全性。

Bitpass is an authenticator based on asymmetric key cryptography.

Typically, users need to provide a username and password to authenticate themselves to a server.  The server needs to save passwords, either as plain-text (bad) or hashes (better).  This results in security issues: 1) passwords can be stolen in man-in-the-middle attacks, and 2) passwords can be stolen from compromised servers.  With Bitpass, no password is needed, and authentication requires only a user name (or public key).

### 官方网站 / Official Website
* [http://bitpass.dearcoin.com/](http://bitpass.dearcoin.com/)

## 验证过程 / Authentication Procedure

![qq20131126-11](https://f.cloud.github.com/assets/514951/1711484/fa7daaf6-614f-11e3-9a96-0acd62eec9b2.png)

### 参数 / Parameters
* 消息格式类似URL([RFC1738](http://www.ietf.org/rfc/rfc1738.txt))，参数的值均需要进行URL编码。
* 参数顺序无关
URL standard message format.  Parameter values are required to be encoded in the URL.
Parameter values can be in any order.

### 服务端 / Server

服务端需要按照下面格式提供出待验证消息：

    bitpass:?sm=[source message]&cbk=[callback url]&dt=[display title]
    
  参数 | 含义 | 是否必须
-----|-------|-----------
sm | source message，需要签名的消息内容 | 是 
cbk | callback url，HTTP POST回调URL | 是 
dt | display title，用于客户端显示标题 | 否

The server needs to provide a validation message according to the following format:

    bitpass:?sm=[source message]&cbk=[callback url]&dt=[display title]
    
parameter | definition | required?
-----|-------|-----------
sm | source message to be signed | yes 
cbk | callback url，HTTP POST request callback URL | yes
dt | display title to be displayed in the client | no


### 客户端 / Client

    address=[bitcoin address]&signature=[sign message with private key]&message=[source message]

签名消息时，采用兼容比特币消息签名格式。

  参数 | 含义 | 是否必须
-----|-------|-----------
address | 签名私钥所对应的比特币地址 | 是 
signature | 签名内容，采用Base64编码 | 是 
message | source message，需要签名的消息内容 | 是 

签名后将上述格式数据，使用HTTP GET/POST请求发送至`cbk`指定URL。

Messages are signed using Bitcoin-compatible keys.

  parameter | meaning | required?
-----|-------|-----------
address | bitcoin address corresponding to signing key | yes 
signature | signature (Base64 encoding) | yes 
message | source message | yes

After signing the data using the above method, use HTTP GET/POST requests to the specified callback URL.

#### 扩展参考字段 / Extended Reference Field
客户端回发数据时，可以加一些额外字段。通常手机可以获取地理信息等，例如：

  参数 | 含义 | 是否必须
-----|-------|-----------
latitude | 纬度 | 否 
longitude | 经度 | 否 

Extra data can be returned from the client.  For example, a mobile phone can add location information, such as:

  parameter | meaning | required
-----|-------|-----------
latitude | latitude | no 
longitude | longitude | no 


#### 服务端Callback返回格式 / Server Callback Return Formats

参数 | 含义 | 注释
-----|-------|-----------
code | 返回码 | 1为成功，非1为错误
message | 消息 | 成功时为"OK", 错误时显示对应信息。客户端通常会显示出该段文字。

支持三种格式：Text, XML, JSON。默认为Text，其他返回格式需在HTTP Response Header添加对应的声明，以便客户端识别。

parameter | meaning | comment
-----|-------|-----------
code | return code | 1 is success，other values mean error
message | message | Success returns `OK`, errors return the corresponding error message. Messages are usually displayed by the client.

Three supported formats: Text, XML, JSON. The default is text.  To use other formats, add the format in the HTTP Response Header.

##### TEXT格式 / Text Formats
response header: 

    Content-type: text/plain
    
response body:

    <int>\t<string>
    
##### XML格式 / XML Formats
response header: 

    Content-type: text/xml
    
response body:

    <result><code>1</code><message>OK</message></result>
##### JSON 
response header: 

    Content-type: application/json
    
response body:

    {code: 1, message: "OK"}


#### 签名算法 / Signature Algorithms

![qq20130728-1](https://f.cloud.github.com/assets/514951/867985/5cef7d72-f760-11e2-8771-aaa629771f91.png)


## 作者 / Authors

* [Dearcoin.com](https://github.com/dearcoin), supported by [Bitfund.pe](http://bitfund.pe).

* * *

## Demo

* [http://bitpass.618.io](http://bitpass.618.io)
* [http://bitpass-rails.herokuapp.com](http://bitpass-rails.herokuapp.com)


## Q&A


### Q: Bitpass要解决什么问题？
### Q: What problem does Bitpass solve?
A: 一句话，消除了身份认证过程中的密码字段。因为密码是造成安全隐患的根本，例如，可能在通信过程中被窃取，可能在服务端泄露明文/密文，一旦消除了密码字段，则彻底消除认证过程中的安全隐患。
In short, Bitpass eliminates the need for login passwords.  Secure logins can be provided by using Bitcoin-based digital signatures.  Security risks such as man-in-the-middle attacks, hacked servers, or brute-force attacks.


### Q: 与Google Authenticator有什么区别？
### Q: How is Bitpass different from Google Authenticator?
A: Google Authenticator通常用于身份的二次验证，提高认证过程的安全性。但其依然需要同时在服务端、客户端保存一个种子，已完成核对。若服务端数据泄露，会导致Google Authenticator形同虚设，而Bitpass不存在此问题。Bitpass需要连接网络发送签名验证数据，Google Authenticater则不需要。
Google Authenticator provides 2-factor authentication, and the user's credentials are still required.  User secrets are still stored on the server.


### Q：Bitpass有什么风险？
### Q : What risks are there with Bitpass?
A: 其风险来自与私钥的管理。若私钥泄露或者遗失，则会被他人伪造登陆，窃取你的信息和财产。在客户端APP中，均使用AES(Advanced Encryption Standard)相关算法对关键数据进行加密保存，并借助iCloud/Dropbox等完成云备份。APP中通过主密码对所有数据进行保护，只需牢记好主密码即可。
With Bitpass, you have to manage your private keys, just as with Bitcoin.  If your private keys are leaked or lost, then others will be able to access your accounts associated with those keys.  Your keys are protected using AES on the client, and backed up on iCloud, Dropbox, or other cloud service, accessible only with your master password.  Do not lose your master password!

### Q：Bitpass APP中的数据如何保障安全？
### Q: How safe is Bitpass?
A: 我们设计的机制是非常安全的。首先，每位用户分配32字节的随机码(Salt)，用户输入自己的主密码（Master Password）后，结合Salt，采用哈希算法PBKDF2-SHA512迭代运算10000次，得到32字节的哈希密码。随后，使用该32字节的哈希密码，采用加密算法AES-256，对用户数据进行加密/解密。存储在APP/云盘等数据均为AES-256加密后的数据，这些算法的安全度目前都是顶级的。

Bitpass's design is extremely secure.  Each user's master password is salted with a random 32-byte salt, and then hashed using PBKDF2-SHA512 and 10,000 iterations.  The result is then encrypted using AES-256 before it is stored.  All the local and backup copies of your data are encrypted using AES-256 as well.


### Q: Bitpass需要中心服务器吗？
### Q: Does Bitpass require a central server?
A: Bitpass是一款客户端APP，不依赖中心服务器，只与目标验证网站进行通信，且私钥永远不会出现在通信数据中。
Bitpass does not require a central server.  The only communication is between your phone and the website to which you wish to sign in.  Furthermore, your private keys never appear in any communication data.

### Q: 云盘备份数据安全吗？
### Q: Are the cloud backups secure?
A: Bitpass的数据都是经过多次高强度加密处理的，即使攻击者盗取了你云盘上的备份文件，依然是无法解开获取私钥的。
All data stored by Bitpass is encrypted using extremely secure algorithms, so even if your backups are stolen, the data won't be accessible.

### Q: Bitpass与比特币的关系？
### Q: What's the relationship between Bitpass and Bitcoin?
A: Bitpass与比特币采用相同的签名算法(基于ECDSA)，并且签名时头部硬编码了与比特币相同的消息，所以与比特币的签名是兼容的。
Bitpass uses the same digital signature algorithm as Bitcoin (ECDSA) and encode the resulting keys using the same algorithm, so Bitpass keys are actually valid Bitcoin addresses.


## Q&A (开发者) / Q&A (Developers)

### Q: 如果现有网站加上Bitpass作为登录需要做哪些工作？
### Q: If I have a website and want to add Bitpass, what do I need to do?
A：

* 首先，需要添加比特币的验证消息库(ECDSA的消息签名认证)，Demo里有PHP和Ruby的版本
  * 在用户表中新增一个比特币地址字段，每个用户对应一个唯一地址
* 然后，提供一个回调URL地址，用于接收与验证消息，并对验证后的消息做处理
* 对于未绑定Bitpass用户，先让其使用普通方式登录，进入绑定页面，在网站上显示一个唯一Token（二维码），然后等待该Token的回发验证消息
  * 验证通过后，将此验证消息的签名地址绑定至该用户
* 对于已绑定的用户，显示Token，等待该Token的回发验证消息，通过后，查询其地址绑定的用户，认证通过执行登录操作，完成身份并登录
* 强烈建议回调URL采用HTTPS，以增强安全性

- Add a Bitcoin message validation library (ECDSA signature verification). Take a look at our demo for PHP and Ruby versions.
- Add a Bitcoin address field for each user in your database.
- Provide a callback URL address to receive signed messages and process information after authorization
- To bind a Bitcoin address to a user, allow a user to log in traditionally and provide a page with a Bitcoin address displayed as a QR code.  When the user scans this code, you will receive a validation message.
- After validation, the user will be bound to this address.
- For users who are already bound, display the token to the user and wait for the token validation message.  After you've received the message, check the message against the user's bound address.

