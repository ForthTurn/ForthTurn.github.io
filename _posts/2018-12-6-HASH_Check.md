---
layout: post
title:  "Win10下利用PowerShell进行哈希校验"
date:   2018-12-6 22:00:00
categories: Windows
tags: Win10 PowerShwell Hash
excerpt: Win10下利用PowerShell进行哈希校验
mathjax: true
---

Win10系统中为了方便开发者，在windows原本的命令提示行`CMD`之外，又加入了使用`Linux`系统下命令的`PowerShell`，可以说是非常方便了。尤其是在`VSCode`中，可以直接在VSCode中打开PowerShell,调用`GCC`、`G++`等编译器编译程序，或者调用`Python`解释器运行Python程序。而除此以外，PowerShell还有一个用处，就是用来计算文件的哈希值。

===

一般我们计算文件哈希值，都需要单独使用一个软件，而在有了PowerShell之后，可以直接在命令行中完成了。

首先，我们以管理员权限运行PowerShell，或者在VSCode中打开终端，CD到目标目录，输入：

```shell
Get-FileHash 校验文件路径 -Algorithm 校验Hash值类型| Format-List
```

其中，如果已在目标目录下，那么文件路径直接输文件名即可。

而`校验Hash值类型`有以下几种：

`SHA1`,`SHA256`,`SHA384`,`SHA512`,`MACTripleDES`,`MD5`,`RIPEMD160`



可见，PowerShell功能还是很强大的。