---
layout: post
title:  "C语言中const的用法详解"
date:   2019-1-10 12:55:20
categories: C语言
tags: const 指针 C语言
excerpt: 关于在C语言中const与指针结合的用法。
mathjax: true
---

const是c语言中的一个常用关键字，用来声明一个变量是不可改变的常量。  
这里我们看一下const的几种用法：  


# 一、惯用法： #
```c
    const int a = 233;
```
这样我们就声明了一个常量名为`a`的，值为233的整型常量。  
如果我们想要改变a的值，例如：
```c
    a = 2233;
```
那么编译器会报错，并且不予通过编译。  
那么如果真的需要改变一个const声明的常量的值，我们可以通过指针来实现：  
```c
    int *p;
    p = &a;
    *p = 2233;
```
